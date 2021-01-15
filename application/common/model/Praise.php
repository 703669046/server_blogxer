<?php

namespace app\common\model;

use think\Model;

class Praise extends Model
{
    //
    public function collect(){
        //hasMany 第二个参数是外键 默认category_id; 第三个参数主键默认id
        return $this->hasMany('Collect','post_id','post_id');
    }
}
