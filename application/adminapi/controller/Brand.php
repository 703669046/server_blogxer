<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Brand extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收参数 cate_id; keywrod page
        $params=input();
        $where=[];
        //分类下品牌列表
        if(isset($params['cate_id']) && !empty($params['cate_id'])){
            //分类下的品牌列表
            $where['cate_id']=$params['cate_id'];
            //查询数据
            $list=\app\common\model\Brand::where($where)->field('id,name')->select();
            //查询数据
            //SELECT t1.*,t2.cate_name FROM pyg_brand t1 left join pyg_category t2 on t1.cate_id =t2.id WHERE cate_id=72;
            //$list=\app\common\model\Brand::alias('t1')
            //    ->join(config('database.prefix').'category t2','t1.cate_id=t2.id','left')
            //    ->field('t1.*,t2.cate_name')
            //    ->where($where)->select();
        }else{
            //分页+搜索列表


            //SELECT t1.*,t2.cate_name FROM pyg_brand t1 left join pyg_category t2 on t1.cate_id =t2.id where name like '%亚%' limit 0,10;
            if(isset($params['keyword']) && !empty($params['keyword'])){
                $keyword=$params['keyword'];
                $where['name']=['like',"%$keyword%"];
            }
            //查询数据
            //$list= \app\common\model\Brand::where($where)->paginate(10);
            $list= \app\common\model\Brand::alias('t1')
                ->join('pyg_category t2','t1.cate_id=t2.id','left')
                ->field('t1.*,t2.cate_name')
                ->where($where)->paginate(10);
        }
        $this->ok($list);
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
        $param=input();
        //参书检测
        $validate=$this->validate($param,[
            'name'=>'require',
            'cate_id'=>'require|integer|gt:0',
            'is_hot'=>'require|integer|in:0,1',
            'sort'=>'require|between:0,9999',
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //成功缩略图
        if(isset($param['logo']) && !empty($param['logo']) && is_file('.'.$param['logo'])){
            \think\Image::open('.'.$param['logo'])->thumb(200,100)->save('.'.$param['logo']);
        }
        //添加数据
        $brand=\app\common\model\Brand::create($param,true);
        $info=\app\common\model\Brand::find($brand['id']);
        //返回数据
        $this->ok($info);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询一条数据
        $info =\app\common\model\Brand::find($id);
        //返回结果
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
        //接收参数
        $params=input();
        //参数检测
        $validate=$this->validate($params,[
            'name'=>'require',
            'cate_id'=>'require|integer|gt:0',
            'is_hot'=>'require|integer|in:0,1',
            'sort'=>'require|between:0,9999',
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //修改数据（logo图片 缩略图）
        if(!$params['logo'] && !empty($params['logo']) && is_file('.'.$params['logo'])){
            \think\Image::open('.'.$params['logo'])->thumb(200,100)->save('.'.$params['logo']);
        }
        \app\common\model\Brand::update($params,['id'=>$id],true);
        $info=\app\common\model\Brand::find($id);
        //返回数据
        $this->ok($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断品牌下是否有商品
        $total=\app\common\model\Goods::where('brand_id',$id)->count();
        if($total>0){
            $this->fail('品牌下有商品，不能删除');
        }
        //删除品牌
        \app\common\model\Brand::destroy($id);
        //返回结果
        $this->ok();
    }
}
