<?php

namespace app\index\middleware;

use think\facade\Cookie;

class CheckMember
{
    public function handle($request, \Closure $next)
    {
        if (Cookie::has('uid') && in_array($request->action(), ['register', 'login'])) {
            return redirect(url('index/member/index')->build());
        }
        if (!Cookie::has('uid') && !in_array($request->action(), ['register', 'login'])) {
            return redirect(url('index/member/login')->build());
        }
        return $next($request);
    }
    public function end(\think\Response $response)
    {
        // 回调行为
    }
}
