<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Exception;
use think\Request;

class Type extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询数据
        $list=\app\common\model\Type::select();
        //返回数据
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
        $params=input();
        //参数检测
        $validate=$this->validate($params,[
            'type_name|模型名称'=>'require|max:20',
            'spec|规格'=>'require|array',
            'attr|属性'=>'require|array',
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //开启事务
        \think\Db::startTrans();
        try {
            //添加商品类型
            $type=\app\common\model\Type::create($params,true);
            //添加商品规格
            //添加商品规格名  去除没有值的规格名
            //外层遍历规格名
            foreach($params['spec'] as $i=>$spec){
                //判断规格名是否为空
                if(empty($spec['name'])){
                    unset($params['spec']);
                    continue;
                }
                //内存遍历规格名称
                foreach ($spec['value'] as $k=>$val){
                    //$val就是一个规格值，去除空的值
                    if(empty($val)){
                        unset($spec['value'][$k]);
                    }
                }
                //内存foreach结束，判断当前的规格名的规则值是不是空数组
                if(empty($spec['value'])){
                    unset($params['spec'][$i]);
                }
            }

            //遍历组装 数据表需要的数据
            $specs=[];
            foreach ($params['spec'] as $spec){
                $row=[
                    'type_id'=>$spec['id'],
                    'spec_name'=>$spec['name'],
                    'sort'=>$spec['sort'],
                ];
                $specs[]=$row;
            }
            //批量添加
            $spec_model=new \app\common\model\Spec();
            $spec_data=$spec_model->allowField(true)->saveAll($specs);

            // $spec_ids=array_column($spec_data,'id');
            //添加商品规格值
            $spec_values=[];
            foreach ($params['spec'] as $i=>$spec){
                //内层遍历规格值
                foreach ($spec['value'] as $value){
                    $row=[
                        'spec_id'=>$spec_data[$i]['id'],
                        'spec_value'=>$value,
                        'type_id'=>$type['id']
                    ];
                    $spec_values[]=$row;
                }

            }
            //批量添加规格值
            $spec_value_model=new \app\common\model\SpecValue();
            $spec_value_model->saveAll($spec_values);

            //添加商品属性
            //去除空的属性名和属性值
            foreach ($params['attr'] as $i=>&$attr){
                if(empty($attr['name'])){
                    unset($params['attr'][i]);
                }else{
                    foreach ($attr['value'] as $k=>$value){
                        if(empty($value)){
                            //unset($attr['value'][$k]);
                            unset($params['attr'][$i]['value'][$k]);
                        }
                    }
                }

            }
            unset($attr);
            //批量添加属性名称属性值
            $attrs=[];
            foreach($params['attr'] as $attr){
                $row=[
                    'attr_name'=>$attr['name'],
                    'attr_values'=>implode(',',$attr['value']),
                    'sort'=>$attr['sort'],
                    'type_id'=>$type['id'],
                ];
                $attrs[]=$row;
            }
            //批量添加
            $attr_model=new \app\common\model\Attribute();
            $attr_model->saveAll($attrs);
            //提交事务
            \think\Db::commit();
            $type=\app\common\model\Type::find($type['id']);
            $this->ok($type);
        }catch (\Exception $e){
            \think\Db::rollback();
            $msg=$e->getMessage();
            $this->fail('添加失败');
        }

        //返回数据
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询一条数据(包括规格信息、规格值、属性信息）
        $info=\app\common\model\Type::with('specs,specs.spec_values,attrs')->find($id);
        //返回数据
        $this->ok($info);

        $info=\app\common\model\Type::find($id);
        $specs=\app\common\model\Spec::where('type_id',$id)->select();
        foreach($specs as &$spec){
            //$spec['id']  对应规格值表的spec_id
            $spec['spec_values']=\app\common\model\SpecValue::where('spec_id',$spec['id'])->select();
        }
        unset($spec);
        $attrs=\app\common\model\Attribute::where('type_id',$id)->select();
        $info['specs']=$specs;
        $info['attrs']=$attrs;
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
        $params=input();
        $validate=$this->validate($params,[
            'type_name|模型名称'=>'require|array',
            'spec|规格'=>'require|array',
            'attr|属性'=>'require|array'
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        \think\Db::startTrans();
        try{
            \app\common\model\Type::update(['type_name'=>$params['type_name']],['id'=>$id],true);
            \app\common\model\Type::where('id',$id)->update(['type_name'=>$params['type_name']]);
        }catch (\Exception $e){
            \think\Db::rollback();
            $msg=$e->getMessage();
            $this->fail($msg);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断是否有商品在使用该商品类型
        $goods=\app\common\model\Goods::where('type_id',$id)->find();
        if($goods){
            $this->fail('正在使用中，不能删除');
        }
        //开启事务
        \think\Db::startTrans();
        try{
            //删除数据（商品类型、类型下的规格名、规格值、属性）
            \app\common\model\Type::destroy($id);
            \app\common\model\Type::destroy(['type_id',$id]);
            //\app\common\model\Spec::where('type_id',$id)->delete();
            \app\common\model\SpecValue::destroy(['type_id',$id]);
            \app\common\model\Attribute::destroy('type_id',$id);
            //提交事务
            \think\Db::commit();
            $this->ok();
        }catch(\Exception $e){
            //回滚事务
            \think\Db::rollback();
            //获取错误信息
            $msg=$e->getMessage();
            $this->fail();
        }


    }
}
