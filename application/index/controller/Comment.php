<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Comment extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = input();
        $data=$params;
        $where['post_id']=$params['post_id'];
        $data = \app\common\model\Comment::alias('a')
            ->join('blogs_user b','a.comment_user_id=b.id','left')
            ->join('blogs_user c','a.reply_user_id=c.id','left')
            ->field('a.*,b.nickname,b.figure_url,c.nickname as nickname2,c.figure_url as figure_url2')
            ->where($where)->select();
        foreach ($data as $item){
            if($item['reply_user_id']==$item['comment_user_id']){
                $item['children']=$item;
            }
        }

        //先转化为标准的二维数组
        $data = (new \think\Collection($data))->toArray();
        $data = get_router_tree_list($data);
        $this->ok($data);
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
        $params = input();
        $validate = $this->validate($params, [
            //'comment_user_id|评论内容' => 'require',
            'post_id|评论内容' => 'require',
            'comment_content|评论内容' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate);
        }
        $params['comment_user_id']=$params['user_id'];
        \app\common\model\Comment::create($params,true);
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

    public function  mycomment ()
    {
        $param = input();
        $where['comment_user_id'] = $param['user_id'];
        $list = \app\common\model\Comment::alias('a')
            ->join('blogs_post b','a.post_id=b.id','left')
            ->join('blogs_user c','b.publisher_id=c.id','left')
            ->join('blogs_auth d','b.category_title=d.auth_name','left')
            ->field('a.*,b.id,b.title,b.icon_src,b.publisher_icon,b.publisher_name,b.source,b.context,b.category_title,d.path')
            ->where($where)->select();
        $this->ok($list);
    }

    public function  getcomment ()
    {
        $param = input();
        $userId = $param['user_id'];
        $list = \app\common\model\Comment::alias('a')
            ->join('blogs_post b',"a.post_id=b.id",'left')
            ->join('blogs_user c','a.comment_user_id=c.id','left')
            ->join('blogs_auth d','b.category_title=d.auth_name','left')
            ->field('a.*,b.title,b.title,b.icon_src,b.publisher_icon,b.publisher_name,b.source,b.context,b.category_title,c.id as user_ids,c.nickname,d.path')
            ->where('comment_user_id','<>',$userId)->select();
        $this->ok($list);
    }
}
