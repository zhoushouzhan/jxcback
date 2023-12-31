<?php
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class Article extends Model {
use \mod\Article;
//自定义内容

use SoftDelete;
protected $deleteTime = 'delete_time';
protected $defaultSoftDelete = 0;
}
