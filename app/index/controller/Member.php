<?php

namespace app\index\controller;


use think\facade\Config;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\View;
use think\exception\ValidateException;
use think\facade\Filesystem;
use app\common\model\Files;

class Member extends Base
{
    protected $middleware = ['CheckMember'];
    protected function initialize()
    {
        parent::initialize();
        if($this->member&&$this->member->enabled==0&&$this->request->action()!='check'&&$this->request->action()!='edit'){
            return redirect(url('index/member/check')->build())->send();
        }
    }
    public function index()
    {
        if (!$this->member) {
            return redirect(url('index/member/login')->build());
        }
        return view();
    }
    public function check(){
        if($this->member->enabled==1){
            return redirect(url('index/member/index')->build());
        }
        return view();
    }
    public function register()
    {
        if (!$this->request->isPost()) {
            return view();
        }
        $data = input();
        if ($res = \app\common\model\Member::create($data)) {
            $url = url('index/member/index')->build();
            Session::set('uid', $res->id);
            Cookie::set('uid', $res->id, 3600);
            $this->success('注册成功', ['url' => $url]);
        }
    }
    public function login()
    {
        if (!$this->request->isPost()) {
            return view();
        }
        $data = input();

        $map[] = ['username', '=', $data['username']];
        $map[] = ['password', '=', md5($data['password'] . Config::get('member.salt'))];

        if ($res = \app\common\model\Member::where($map)->find()) {
            $url = url('index/member/index')->build();
            Session::set('uid', $res->id);
            $expire = (int) strtotime($data['lifetime'] . ' 0 second', 0);
            Cookie::set('uid', $res->id, $expire);
            $this->success('登录成功', ['url' => $url]);
        } else {
            $this->error('登录失败');
        }
    }
    public function edit()
    {
        if (!$this->request->isPost()) {
            return view();
        }
        $data = input();
        $data['id'] = $this->uid;
        if ($res = \app\common\model\Member::update($data)) {
            $url = url('index/member/index')->build();
            Session::set('uid', $res->id);
            Cookie::set('uid', $res->id, 3600);
            $this->success('恭喜，编辑成功', ['url' => $url]);
        }
    }
    public function article($t = 0, $id = 0)
    {
        if (!$this->request->isPost()) {
            $template = [0 => 'article', 1 => 'article_post'];
            $r = [];
            if ($id) {
                $r = \app\common\model\Article::find($id);
            }
            View::assign([
                'r'  => $r,
            ]);

            return view($template[$t]);
        }
        $data = input();
    }
    public function loginOut()
    {
        Session::delete('uid');
        Cookie::delete('uid');
        return redirect(url('index/index/index')->build());
    }
    public function upload()
    {
        $file = [];
        $from = ''; //来源
        if ($file = $this->request->file('file')) {
            $from = 'yp';
        } elseif ($file = $this->request->file('upload')) {
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
            $data['user_id'] = $this->uid;
            if (input('tag') && input('tag') == 'userpic') {
                Files::destroy($this->member->userpic);
                $r = Files::create($data);
                $fileId = $r->id;
                \app\common\model\Member::update(['avatar' => $fileId, 'id' => $this->uid]);
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
}
