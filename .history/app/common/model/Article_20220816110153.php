<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-14 10:58:21
 * @LastEditTime: 2022-08-16 11:01:50
 * @FilePath: \web\app\common\model\Article.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Article extends Model
{
    use \mod\Article;
    use SoftDelete;
    //自定义内容

}
