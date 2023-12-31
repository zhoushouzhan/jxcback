<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-05-19 12:56:19
 * @LastEditTime: 2022-08-07 11:28:40
 * @FilePath: \web\extend\yp\Auth.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace yp;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;


class Auth
{
    /**
     * 封装管理员Token生成参数
     * @param int $id 登录的用户id
     * @param $mobile 登录用户手机号码
     */
    public static function getToken(array  $parameter = [])
    {
        $key = config('app.JWTAuth_key');
        $payload  = array(
            "iss" => config('app.JWTAuth_iss'),
            "aud" => config('app.JWTAuth_iss'),
            "iat" => time(),
            "nbf" => time() + 3,
            'exp' => time() + 00,
            'data' => $parameter
        );
        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
    }

    /**
     * 封装Toekn检验方法
     * @param string $token
     * @return array
     */
    public static function decodeToken(string $token)
    {

        $key = config('app.JWTAuth_key');
        try {
            JWT::$leeway = 60;
            $data = (array)JWT::decode($token, new Key($key, 'HS256'));

            //快到期时续期这里定义为10分钟
            $exp = $data['exp'];
            if ($exp - time() < 600 && $exp - time() > 0) {
                $result['token'] = self::getToken(['adminid' => $data['data']->adminid]);
            }


            $result['data'] = $data['data'];
            $result['status'] = 1001;
            return $result;
        } catch (SignatureInvalidException $exception) {
            $result['status'] = 1002;
            $result['data']   = 'Token无效';
            return $result;
        } catch (BeforeValidException $exception) {
            $result['status'] = 1003;
            $result['data']   = 'token异常';
            return $result;
        } catch (ExpiredException $exception) {
            $result['status'] = 1004;
            $result['data']   = '登录信息已超时，请重新登录';
            return $result;
        } catch (\Exception $exception) {
            $result['status'] = 1005;
            $result['data']   = '未知错误';
            return $result;
        }
    }
}
