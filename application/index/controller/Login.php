<?php

namespace app\index\controller;

use think\Controller;

class Login extends BaseApi
{
    /**
     * 验证码接口
     */
    public function captcha()
    {
//        session_destroy('session_id_');
//        Session::delete('session_id_');
//        dump(encrypt_password('Admin123@'));
        //
        if(isset($_SERVER)){
            if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            }else{
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }

        //验证码唯一标识
        $uniqid =uniqid(mt_rand(100000,999999));
        //生成验证码地址
        $src = captcha_src($uniqid);
        //返回数据
        $res = [
            'src' => $src,
            'uniqid' => $uniqid,
            'ipv4'=>$realip
        ];
        $this->ok($res);
    }

    /**
     * 登录接口
     */
    public function login()
    {
        //接收参数
        $params = input();

        //参数检测（表单验证）
        $validate = $this->validate($params, [
            'username|用户名' => 'require',
            'password|密码' => 'require',
            'code|验证码' => 'require',
            //'code|验证码' => 'require|captcha:'.$params['uniqid'], //验证码自动校验
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
        //查询用户表进行认证
        $password = encrypt_password($params['password']);
        $info = \app\common\model\User::where('username', $params['username'])->where('password', $password)->find();
        if(empty($info)){
            //用户名或者密码错误
            $this->fail('用户名或者密码错误', 403);
        }
        //生成token令牌
        $token = \tools\jwt\Token::getToken($info['id']);
        $menutList=$this::getMenutList();
        //返回数据
        $data = [
            'token' => $token,
            'user_id' => $info['id'],
            'username' => $info['username'],
            'nickname' => $info['nickname'],
            'figure_url' => $info['figure_url'],
            'email' => $info['email'],
            'menutList'=>$menutList
        ];
        $this->ok($data);
    }

    /**
     * 退出
     */
    public function logout()
    {
        //记录token 为已退出
        //获取当前请求中的token
        $token = \tools\jwt\Token::getRequestToken();
        //从缓存中取出 注销的token数组
        $delete_token = cache('delete_token') ?: [];
        //将当前的token 加入到数组中 ['dssfd','dsfds']
        $delete_token[] = $token;
        //将新的数组 重新存到缓存中  缓存1天
        cache('delete_token', $delete_token, 86400);
        //返回数据
        $this->ok();
    }

    private function getMenutList()
    {
        $where['menut_type'] = 0;
        $data = \app\common\model\Auth::where($where)->select();
        foreach ($data as $item) {
            if($item['is_d']==1||$item['isLogin']==1){
                $item['is_d']=true;
                $item['isLogin']=true;
            }else{
                $item['is_d']=false;
                $item['isLogin']=false;
            }
//            $item['meta']['is_login']=$item['is_login'];
        }
        //先转化为标准的二维数组
        $data = (new \think\Collection($data))->toArray();
        //再转化为 父子级树状结构
        $data = get_router_tree_list($data);
        return $data;
    }
}
