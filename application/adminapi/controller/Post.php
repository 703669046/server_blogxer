<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Post extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $list =\app\common\model\Post::select();
        $this->ok($list);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收参数
        $params = input();
        //参数检测
        $validate = $this->validate($params, [
            'title|标题' => 'require|length:2,20',
            'source|来源' => 'require',
            'category|类别' => 'require',
            'icon_src|图标' => 'require',
            'context|内容' =>'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        \app\common\model\Post::create($params, true);
        $this->ok();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询数据
        $info = \app\common\model\Post::find($id);
        //返回数据
        $this->ok($info);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    public function approvalpost()
    {
        $params = input();
        //参数检测
        $validate = $this->validate($params, [
            'type|状态' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        $id=$params['id'];
        \app\common\model\Post::update($params,['id'=>$id], true);
        $this->ok();
    }
}
