<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class User extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params =input();
        $userId = \tools\jwt\Token::getUserId($params['token']);
        $where['id']=$userId;
        $count = \app\common\model\User::where($where)->find();
        if(empty($count)){
            $this->fail('数据异常,请稍后再试');
        }
        $this->ok($count);
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
        $params=input();
        //参数检测（表单验证）
        $validate = $this->validate($params, [
            'username|用户名' => 'require',
            'password|密码' => 'require',
            'email|邮箱'=>'require|email',
            'nickname|昵称'=>'require',
//            'code|验证码' => 'require|captcha:'.$params['uniqid'], //验证码自动校验
            'uniqid|验证码标识' => 'require'
        ]);
        if($validate !== true){
            //参数验证失败
            $this->fail($validate, 401);
        }
        //校验验证码 手动校验
        //从缓存中根据uniqid获取session_id, 设置session_id, 用于验证码校验
        $session_id = cache('session_id_' . $params['uniqid']);
        if($session_id){
            session_id($session_id);
        }

        if(!captcha_check($params['code'], $params['uniqid']))
        {
            //验证码错误
            $this->fail('验证码错误', 402);
        }
        $length=\app\common\model\User::where('username',$params['username'])->count();
        if($length>1){
            $this->fail('该账户已存在', 500);
        }

        $params['password']=encrypt_password($params['password']);
        $params['phone']=$params['username'];
        $params['cip']=$params['city']['cip'];
        $params['cid']=$params['city']['cid'];
        $params['cname']=$params['city']['cname'];
        $cate=\app\common\model\User::create($params,true);
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

    public function edit()
    {
        //
        $param = input();
        $validate = $this->validate($param, [
            'phone|手机号' => 'require',
            'email|邮箱地址' => 'require',
        ]);
        if($validate !== true){
            $this->fail($validate, 401);
        }
        $id = $param['id'];
        \app\common\model\User::update($param, ['id'=>$id], true);
        $this->ok();
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
