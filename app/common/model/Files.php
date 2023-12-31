<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-24 07:53:52
 * @LastEditTime: 2022-08-09 13:09:26
 * @FilePath: \web\app\common\model\Files.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\common\model;

use think\Model;
use think\facade\Cache;
//本模型为系统生成,具体功能需要您来配置
class files extends Model
{
    public static function onAfterRead($data)
    {
        $site = Cache::get('sitepro');
        $data->filepath = '//' . $site['siteurl'] . $data->filepath;
        return $data;
    }
    //模型事件-删除后
    public static function onAfterDelete($files)
    {
        $site = Cache::get('sitepro');
        $prestr='//' . $site['siteurl'];
        //删除数据后删除附件
        $filepath=$files['filepath'];
        $filepath=str_replace($prestr,'',$filepath);
        $sourcefile=root_path() .'public'. $filepath;
        if (file_exists($sourcefile)) {
            unlink($sourcefile);
        }
    }
    //绑定信息
    public static function bindInfo($ids, $id, $category_id = 0, $type, $tag)
    {
        //绑定信息前解除当前绑定的信息
        self::where(['ypcms_id' => $id, 'ypcms_type' => $type, 'tag' => $tag])->update(['ypcms_id' => '', 'ypcms_type' => '', 'tag' => '']);
        //绑定多条
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                self::update(['id' => $value, 'category_id' => $category_id, 'ypcms_id' => $id, 'ypcms_type' => $type, 'tag' => $tag]);
            }
        } else {
            //绑定单条
            self::update(['id' => $ids, 'category_id' => $category_id, 'ypcms_id' => $id, 'ypcms_type' => $type, 'tag' => $tag]);
        }
    }
    //绑定内容中图片
    public static function bindEditor($id, $category_id = 0, $type, $tag, $content)
    {
        //内容图片绑定
        self::where(['ypcms_id' => $id, 'ypcms_type' => $type, 'tag' => $tag])->update(['ypcms_id' => '', 'ypcms_type' => '', 'tag' => '']);
        preg_match_all('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i', $content, $res);
        foreach ($res[1] as $key => $value) {
            self::where('filepath', $value)->update(['category_id' => $category_id, 'ypcms_id' => $id, 'ypcms_type' => $type, 'tag' => $tag]);
        }
    }
    public function ypcms()
    {
        return $this->morphTo('ypcms', [
            'Download' => 'app\common\model\Download',
            'Article' => 'app\common\model\Article',
            'System' => 'app\common\model\System',
        ]);
    }
}
