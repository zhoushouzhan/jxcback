<?php

namespace yp;

use think\facade\Db;

class Database
{

    private $fp;
    private $file;
    private $size = 0;
    private $config;
    public function __construct($file, $config, $type = 'export')
    {
        $this->file = $file;
        $this->config = $config;
    }
    private function open($size = 0)
    {
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config['part']) {
                $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->fp = null;
                $this->file['part']++;
                session('backup_file', $this->file);
                $this->create();
            }
        } else {
            $backup_path = $this->config['path'];
            $filename = "{$backup_path}{$this->file['name']}-{$this->file['part']}.sql";
            if ($this->config['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }

    public function create()
    {
        $sql = "-- -----------------------------\n";
        $sql .= "-- MySQL Data Transfer\n";
        $sql .= "--\n";
        $sql .= "-- Host     : " . config('database.hostname') . "\n";
        $sql .= "-- Port     : " . config('database.hostport') . "\n";
        $sql .= "-- Database : " . config('database.database') . "\n";
        $sql .= "--\n";
        $sql .= "-- Part : #{$this->file['part']}\n";
        $sql .= "-- Date : " . date("Y-m-d H:i:s") . "\n";
        $sql .= "-- -----------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        return $this->write($sql);
    }

    private function write($sql = '')
    {
        $size = strlen($sql);
        $size = $this->config['compress'] ? $size / 2 : $size;
        $this->open($size);
        return $this->config['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }


    public function backup($table = '', $start = 0)
    {
        // 备份表结构
        if (0 == $start) {
            $result = Db::query("SHOW CREATE TABLE `{$table}`");
            $result = array_map('array_change_key_case', $result);

            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= trim($result[0]['create table']) . ";\n\n";
            if (false === $this->write($sql)) {
                return false;
            }
        }

        // 数据总数
        $result = Db::query("SELECT COUNT(*) AS count FROM `{$table}`");
        $count = $result['0']['count'];

        //备份表数据
        if ($count) {
            // 写入数据注释
            if (0 == $start) {
                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n";
                $this->write($sql);
            }

            // 备份数据记录
            $result = Db::query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
            foreach ($result as $row) {
                $row = array_map('addslashes', $row);
                $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                if (false === $this->write($sql)) {
                    return false;
                }
            }

            //还有更多数据
            if ($count > $start + 1000) {
                return array($start + 1000, $count);
            }
        }

        // 备份下一表
        return 0;
    }


    public function import($start = 0)
    {
        return($this->config);
        if ($this->config['compress']) {
            $gz = gzopen($this->file[1], 'r');
            $size = 0;
        } else {
            $size = filesize($this->file[1]);
            $gz = fopen($this->file[1], 'r');
        }

        $sql = '';
        if ($start) {
            $this->config['compress'] ? gzseek($gz, $start) : fseek($gz, $start);
        }

        for ($i = 0; $i < 1000; $i++) {
            $sql .= $this->config['compress'] ? gzgets($gz) : fgets($gz);
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== Db::execute($sql)) {
                    $start += strlen($sql);
                } else {
                    return false;
                }
                $sql = '';
            } elseif ($this->config['compress'] ? gzeof($gz) : feof($gz)) {
                return 0;
            }
        }

        return array($start, $size);
    }

    public static function export($tables = [], $path = '', $prefix = '', $export_data = 1)
    {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
        $datetime = date('Y-m-d H:i:s', time());
        $sql = "-- -----------------------------\n";
        $sql .= "-- 导出时间 `{$datetime}`\n";
        $sql .= "-- -----------------------------\n";

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $sql .= self::getSql($prefix . $table, $export_data);
            }

            // 写入文件
            if (file_put_contents($path, $sql)) {
                return true;
            };
        }
        return false;
    }

    public static function exportUninstall($tables = [], $path = '', $prefix = '')
    {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
        $datetime = date('Y-m-d H:i:s', time());
        $sql = "-- -----------------------------\n";
        $sql .= "-- 导出时间 `{$datetime}`\n";
        $sql .= "-- -----------------------------\n";

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $sql .= "DROP TABLE IF EXISTS `{$prefix}{$table}`;\n";
            }

            // 写入文件
            if (file_put_contents($path, $sql)) {
                return true;
            };
        }
        return false;
    }


    private static function getSql($table = '', $export_data = 0, $start = 0)
    {
        $sql = "";
        if (Db::query("SHOW TABLES LIKE '%{$table}%'")) {
            // 表结构
            if ($start == 0) {
                $result = Db::query("SHOW CREATE TABLE `{$table}`");
                $sql .= "\n-- -----------------------------\n";
                $sql .= "-- 表结构 `{$table}`\n";
                $sql .= "-- -----------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= trim($result[0]['Create Table']) . ";\n\n";
            }

            // 表数据
            if ($export_data) {
                $sql .= "-- -----------------------------\n";
                $sql .= "-- 表数据 `{$table}`\n";
                $sql .= "-- -----------------------------\n";

                // 数据总数
                $result = Db::query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];

                // 备份数据记录
                $result = Db::query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
                foreach ($result as $row) {
                    $row = array_map('addslashes', $row);
                    $sql .= "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                }

                // 还有更多数据
                if ($count > $start + 1000) {
                    $sql .= self::getSql($table, $export_data, $start + 1000);
                }
            }
        }
        return $sql;
    }


    public function __destruct()
    {
        $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
    }
}
