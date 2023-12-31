<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\model\Files;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\facade\Cache;

class Api extends Base
{
    protected function initialize()
    {
        parent::initialize();
    }
    //清除无效附件
    public function oldFileRemove($id = 0)
    {
        if (!$id) {
            return 0;
        }
        $r = Files::get($id);
        unlink(root_path()  . $r->filepath);
        $r->delete();
    }
    //生成菜单
    public function getTree($pid, $sid = 0)
    {
        $data = [];
        foreach ($this->menu as $k => $v) {
            if ($pid == $v['id']) {
                $data = $v;
            }
        }
        if ($data) {
            $this->success('获取成功', '', $data);
        } else {
            $this->error('未找到菜单');
        }
    }
    //上传文件
    public function upload($id = "0")
    {
        $file = $this->request->file('file');
        $fileSize = $this->site['uploadsize'] * 1024 * 1000;
        $fileExt = $this->site['filetype'];
        $checkFile = ['files' => ['filesize' => $fileSize, 'fileExt' => $fileExt]];
        try {
            validate($checkFile)->check(['files' => $file]);
            $savename = Filesystem::disk('public')->putFile('images', $file);
            $path = Filesystem::getDiskConfig('public', 'url') . '/' . str_replace('\\', '/', $savename);
            //附件入库
            $data['name'] = $file->getOriginalName();
            $data['filepath'] = $path;
            $data['addtime'] = time();
            $data['ftype'] = $file->getMime();
            $data['fsize'] = $file->getSize();
            $data['user_id'] = $this->admin;
            $data['isq'] = 0;
            if ($id) {
                $r = Files::find($id);
                unlink(YP_ROOT . $r->filepath);
                $r::update($data, ["id" => $id]);
            } else {
                $r = Files::create($data);
                $fileId = $r->id;
            }
            $result = [
                'code' => 1,
                'msg' => '上传成功',

            ];
            return json($result);
        } catch (ValidateException $e) {

            $result = [
                'code' => 0,
                'msg' => $e->getError(),

            ];

            return json($result);
        }
    }
    //编辑器上传
    public function ckupload($id = "0")
    {
        $file = $this->request->file('upload');
        if (!$file) {
            return '';
        }
        $fileSize = $this->site['uploadsize'] * 1024 * 1000;
        $fileExt = $this->site['filetype'];
        $checkFile = ['files' => ['filesize' => $fileSize, 'fileExt' => $fileExt]];
        try {
            validate($checkFile)->check(['files' => $file]);
            $savename = Filesystem::disk('public')->putFile('images', $file);
            $path = Filesystem::getDiskConfig('public', 'url') . '/' . str_replace('\\', '/', $savename);
            //附件入库
            $data['name'] = $file->getOriginalName();
            $data['filepath'] = $path;
            $data['addtime'] = time();
            $data['ftype'] = $file->getMime();
            $data['fsize'] = $file->getSize();
            $data['user_id'] = $this->admin;
            $data['isq'] = 0;
            if ($id) {
                $r = Files::find($id);
                unlink(YP_ROOT . $r->filepath);
                $r::update($data, ["id" => $id]);
                $fileId = $id;
            } else {
                $r = Files::create($data);
                $fileId = $r->id;
            }
            $result = [
                'uploaded' => 1,
                'url' => $data['filepath'],
                'fileName' => $data['name'],
                'fileId' => $fileId,
                'error' => ["message" => "上传成功"],

            ];
            return json($result);
        } catch (ValidateException $e) {

            $result = [
                'uploaded' => 0,
                'error' => ["message" => $e->getError()],
            ];

            return json($result);
        }
    }
    //删除缓存
    public function clear()
    {
        //清缓存
        Cache::clear();
        $this->success('清除缓存成功');
    }
}
