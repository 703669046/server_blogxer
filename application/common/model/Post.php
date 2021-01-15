<?php

namespace app\common\model;

use think\Model;

class Post extends Model
{
    //
    public function praise(){
        //hasMany 第二个参数是外键 默认category_id; 第三个参数主键默认id
        return $this->hasMany('Praise','post_id','id');
    }
}
