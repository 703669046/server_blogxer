<?php

namespace app\index\controller;

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

    public function indexPage()
    {
        $params = input();
        $validate = $this->validate($params, [
            'user_id' => 'require',
            'id|类别' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        $where=[];
        if(isset($params['title']) && !empty($params['title'])){
            $keyword=$params['keyword'];
            $where['title']=['like',"%$keyword%"];

        }
        $where['type']=3;
        $data = \app\common\model\Post::alias('a')
            ->join('blogs_praise b','a.id=b.post_id','left')
            ->join('blogs_collect c','b.post_id=c.post_id','left')
            ->field('a.*,b.praise,b.post_id,b.user_parise_id,b.user_id,c.collect,c.post_id,c.user_collect_id,c.user_id')
            ->where($where)->paginate($params['pageSize']);

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
}
