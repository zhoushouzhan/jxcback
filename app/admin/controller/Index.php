<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-09 10:49:25
 * @FilePath: \web\app\admin\controller\index.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use yp\Ypdate;
use think\exception\ValidateException;
use think\facade\Filesystem;
use app\common\model\Files;

use think\facade\Cache;

class Index extends Base
{
    public function index()
    {
        return view();
    }
    public function getsite()
    {
        $this->success('基础数据获取成功', $this->site);
    }


    public function myTree()
    {
        //$result['route'] = $this->getMenu();
        $rule = new Rule($this->app);
        $result['route'] = $rule->index();
        return $result;
    }
    public function date($ypdate = '')
    {
        $calendar = Ypdate::getNow($ypdate);

        $this->success('网站属性',  $calendar);
        //return $calendar;
    }
    public function upload($id = 0)
    {
        $file = $this->request->file('file') ? $this->request->file('file') : $this->request->file('upload');
        $from = ''; //来源
        if ($this->request->file('file')) {
            $from = 'yp';
        }
        if ($this->request->file('upload')) {
            $from = 'ck';
        }
        if (!$file) {
            return 'no files';
        }
        $fileSize = $this->site['uploadsize'] * 1024 * 1000;
        $fileExt = $this->site['filetype'];
        $filesDir = $this->site['filesdir'];
        $checkFile = ['files' => ['filesize' => $fileSize, 'fileExt' => $fileExt]];
        try {
            validate($checkFile)->check(['files' => $file]);
            $savename = Filesystem::disk('public')->putFile($filesDir, $file);
            $path = Filesystem::getDiskConfig('public', 'url') . '/' . str_replace('\\', '/', $savename);
            //附件入库
            $data['name'] = $file->getOriginalName();
            $data['filepath'] = $path;
            $data['addtime'] = time();
            $data['ftype'] = $file->getMime();
            $data['fsize'] = $file->getSize();
            $data['user_id'] = 0;
            $data['admin_id'] = $this->admin->id;
            $data['ypcms_id'] = 0;
            $data['category_id']=0;
            $data['isq'] = 0;
            if (isset($this->user->userpic->id)) {
                Files::destroy($this->user->userpic->id);
                $r = Files::create($data);
                $fileId = $r->id;
            } else {
                $r = Files::create($data);
                $fileId = $r->id;
            }

            $result = [
                'uploaded' => 1,
                'url' =>  '//' . $this->site['siteurl'] . $data['filepath'],
                'fileName' => $data['name'],
                'fileId' => $fileId,
            ];
            if ($from == 'yp') {
                $this->success('上传成功', $result);
            } else {
                return json_encode(['url' => $result['url']]);
            }
        } catch (ValidateException $e) {

            $result = [
                'uploaded' => 0,
                'error' => ["message" => $e->getError()],
            ];

            $this->error($e->getError());
        }
    }
    public function test()
    {
        return root_path();
    }
    public function clearCache()
    {
        Cache::clear();
        $this->success('清除缓存成功');
    }
}
