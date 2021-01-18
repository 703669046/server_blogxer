<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Collect extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        //
        $param = input();
        $where['user_collect_id'] = $param['user_id'];
        $where['collect'] = 1;
        $list = \app\common\model\Collect::alias('a')
            ->join('blogs_post b','a.post_id=b.id','left')
            ->join('blogs_auth c','b.category_title=c.auth_name','left')
            ->field('a.*,b.id,b.title,b.icon_src,b.publisher_icon,b.publisher_name,b.source,b.context,b.category_title,c.path')
            ->where($where)->order('create_time','desc')->select();
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
        //
        $params = input();
        $validate = $this->validate($params, [
            'collectType' => 'require',
            'post_id' => 'require',
            'user_ids' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }

        $params['user_collect_id']=$params['user_id'];
        $where = [
            'post_id'=>$params['post_id'],
            'user_collect_id'=>$params['user_id']
        ];
        $fineds = \app\common\model\Collect::where($where)->count();
        if($params['collectType']==true){
            $params['collect']=1;
        }else{
            $params['collect']=0;
        }
        if($fineds!=0) {
            $data = \app\common\model\Collect::update($params, $where,  true);

        }else{
            $data = \app\common\model\Collect::create($params, true);
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
