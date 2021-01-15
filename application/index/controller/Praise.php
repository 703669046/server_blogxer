<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Praise extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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
        //
        $params = input();
        $validate = $this->validate($params, [
            'praiseType' => 'require',
            'post_id' => 'require',
            'user_ids' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }

        $params['user_parise_id']=$params['user_id'];
        $where = [
            'post_id'=>$params['post_id'],
            'user_parise_id'=>$params['user_id']
        ];
        $fineds = \app\common\model\Praise::where($where)->count();
        if($params['praiseType']==true){
            $params['praise']=1;
        }else{
            $params['praise']=0;
        }
        if($fineds!=0) {
            $data = \app\common\model\Praise::update($params, $where,  true);

        }else{
            $data = \app\common\model\Praise::create($params, true);
        }
        $this->ok($data);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
}
