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
            'title|标题' => 'require|length:2,60',
            'source|来源' => 'require',
            'category|类别' => 'require',
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
            ->field('a.*,b.praise,c.collect')
            ->where($where)->paginate($params['pageSize']);
        static $list = array();
        foreach($data as $item){
            $pieces = explode(",", $item['category']);
            if(in_array($params['id'], $pieces)){
                $list[]=$item;
            }
        }
        $this->ok($list);
    }

    public function searchs()
    {
        $params = input();
        $where=[];
        if(isset($params['title']) && !empty($params['title'])){
            $keyword=$params['keyword'];
            $where['title']=['like',"%$keyword%"];

        }
        $where['type']=3;
        $data = \app\common\model\Post::alias('a')
            ->join('blogs_praise b','a.id=b.post_id','left')
            ->join('blogs_collect c','b.post_id=c.post_id','left')
            ->join('blogs_auth d','a.category_title=d.auth_name','left')
            ->field('a.id,a.title,a.category_title,b.praise,c.collect,d.path')
            ->where($where)->select();
        static $list = array();
        foreach($data as $item){
            $pieces = explode(",", $item['category']);
            if(in_array($params['id'], $pieces)){
                $list[]=$item;
            }
        }
        $this->ok($list);
    }

    public function postinfo($id)
    {
        $param = input();
        $where['id']=$id;
        $obj = \app\common\model\Post::where($where)->find();
        $wheres['post_id']=$id;
        $wheres['user_parise_id']=$param['user_id'];
        $wheres['praise']=1;
        $praise = \app\common\model\Praise::where($wheres)->find();
        unset($wheres['user_parise_id']);
        unset($wheres['praise']);
        $wheres['user_collect_id']=$param['user_id'];
        $wheres['collect']=1;
        $collect = \app\common\model\Collect::where($wheres)->find();
        unset($wheres['user_collect_id']);
        $collects = \app\common\model\Collect::where($wheres)->count();
        $add['browse']=$obj['browse']+1;
        \app\common\model\Post::update($add,$where,true);
        $obj['praise']=$praise['praise'];
        $obj['collect']=$collect['collect'];
        $obj['collects']=$collects;
        $this->ok($obj);

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
