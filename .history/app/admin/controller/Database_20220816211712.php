<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\exception\ValidateException;
use yp\Database as DatabaseModel;

/**
 * 数据库管理
 */
class Database extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->backupPath = realpath(Config::get('Database.backup_folder'));
        if (!is_dir(Config::get('Database.backup_folder'))) {
            mkdir(Config::get('Database.backup_folder'), 0755, true);
        }
        $this->backConfig = array(
            'path' => $this->backupPath . DIRECTORY_SEPARATOR,
            'part' => Config::get('Database.backup_size') * 1024 * 1000,
            'compress' => Config::get('Database.compress'), //是否压缩，1压缩，0不压缩
            'level' => Config::get('Database.level'), //压缩等级123级1最低
        );
    }
    //数据表列表
    public function index($type = 'export')
    {
        $tableList = Db::query("SHOW TABLE STATUS");
        $tableList = array_map('array_change_key_case', $tableList);
        $this->success('获取表获取成功', $tableList);
    }
    //备份数据表
    public function export($tables = null, $start = 0)
    {
        if ($this->request->isPost() && $tables) {
            // 检查进程
            $lock = $this->backupPath . DIRECTORY_SEPARATOR . "backup.lock";
            if (is_file($lock)) {
                $this->error('有备份任务正在执行，稍后再试！');
            } else {
                file_put_contents($lock, $this->request->time());
            }
            is_writeable(Config::get('Database.backup_folder')) || $this->error('备份目录不存在或者不可写！');
            $file = array(
                'name' => date('Ymd-His', $this->request->time()),
                'part' => 1,
            );
            $Database = new DatabaseModel($file, $this->backConfig);
            if (false !== $Database->create()) {
                // 备份指定表
                foreach ($tables as $table) {
                    $start = $Database->backup($table, $start);
                    while (0 !== $start) {
                        if (false === $start) {
                            // 出错
                            $this->error('备份出错！');
                        }
                        $start = $Database->backup($table, $start[0]);
                    }
                }
                // 备份完成，删除锁定文件
                unlink($lock);
                $this->success('备份完成！');
            } else {
                $this->error('初始化失败，备份文件创建失败！');
            }
        } else {
            $this->error('请选择数据表');
        }
    }
    //己备份列表
    public function importList()
    {
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($this->backupPath, $flag);
        $data_list = [];
        foreach ($glob as $name => $file) {
            if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
                $filename = $name;
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];

                if (isset($data_list["{$date} {$time}"])) {
                    $info = $data_list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                $info['time'] = strtotime("{$date} {$time}");
                $info['name'] = $info['time'];
                $info['filename'] = $filename;
                $info['url'] = '//' . $this->site['siteurl'] . DIRECTORY_SEPARATOR . Config::get('Database.backup_folder') . DIRECTORY_SEPARATOR . $filename;
                $info['nameDate'] = date("Y-m-d H:i:s", $info['time']);

                $data_list["{$date} {$time}"] = $info;
            }
        }
        $dataList = !empty($data_list) ? array_values($data_list) : $data_list;
        $this->success('己获取备份列表', $dataList);
    }

    //还原数据表
    public function import($time = 0)
    {
        if ($time === 0) {
            $this->error('参数错误！');
        }

        // 初始化
        $name = date('Ymd-His', (int) $time) . '-*.sql*';
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $name;
        $files = glob($path);
        $list = array();
        foreach ($files as $name) {
            $basename = basename($name);
            $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
            $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
            $list[$match[6]] = array($match[6], $name, $gz);
        }
        ksort($list);
        // 检测文件正确性
        $last = end($list);
        if (count($list) === $last[0]) {

            foreach ($list as $item) {
                $config = [
                    'path' => $this->backupPath . DIRECTORY_SEPARATOR,
                    'compress' => $item[2],
                ];
                $Database = new DatabaseModel($item, $config);
                $start = $Database->import(0);

                // 循环导入数据
                while (is_array($start)) {
                    if (false === $start) {
                        // 出错
                        $this->error('还原数据出错！');
                    }
                    $start = $Database->import($start[0]);
                }
            }

            // 记录行为
            Cache::clear();
            $this->success('还原完毕！');
        } else {
            $this->error('备份文件可能已经损坏，请检查！');
        }
    }
    //数据列管理
    public function column($tables)
    {
        $db = Config::get('database.connections.mysql.database');
        $colList = Db::query("select * from information_schema.columns where table_schema='$db' and table_name='$tables'");
        $colList = array_map('array_change_key_case', $colList);
        $this->success('字段获取成功', $colList);
    }
    //优化数据表
    public function optimize($tables = null)
    {
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");

                if ($list) {
                    $this->success("数据表优化完成！");
                } else {
                    $this->error("数据表优化出错请重试！");
                }
            } else {
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表'{$tables}'优化完成！");
                } else {
                    $this->error("数据表'{$tables}'优化出错请重试！");
                }
            }
        } else {
            $this->error("请选择要优化的表！");
        }
    }
    //修复数据表
    public function repair($tables = null)
    {
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("REPAIR TABLE `{$tables}`");

                if ($list) {
                    $this->success("数据表修复完成！");
                } else {
                    $this->error("数据表修复出错请重试！");
                }
            } else {
                $list = Db::query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表'{$tables}'修复完成！");
                } else {
                    $this->error("数据表'{$tables}'修复出错请重试！");
                }
            }
        } else {
            $this->error("请指定要修复的表！");
        }
    }
    //执行SQ	L
    public function dosql()
    {
        if ($this->request->isPost()) {
            $query = $this->request->param('query');
            $query = str_replace('[!pre!]', Config::get("database.connections.mysql.prefix"), $query);

            try {
                $r = Db::query($query);
                $this->success('执行成功', $r);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            } catch (\Exception $e) {

                $this->error($e->getMessage());
            }
        }
    }
    //删除备份文件
    public function delete(int $name = 0)
    {
        if ($name == 0) {
            $this->error('参数错误！');
        }
        $name = date('Ymd-His', $name) . '-*.sql*';
        $path = realpath($this->backupPath) . DIRECTORY_SEPARATOR . $name;
        array_map("unlink", glob($path));
        if (count(glob($path))) {
            $this->error('备份文件删除失败，请检查权限！');
        } else {
            // 记录行为
            $this->success('备份文件删除成功！');
        }
    }
    //下载备份文件
    public function download(int $name = 0)
    {
        if ($name == 0) {
            $this->error('参数错误！');
        }
        
    }
}
