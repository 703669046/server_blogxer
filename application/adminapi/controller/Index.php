<?php
namespace app\adminapi\controller;

class Index extends BaseApi
{
    public function index()
    {
        //测试关联模型
        /*$info=\app\common\model\Admin::find(1);
        $info=\app\common\model\Admin::with('profile')->find(1);
        //dump($info);
        $this->ok($info);
        echo encrypt_password('123456');die;*/

        //以档案为主：档案到管理员
        /*$info=\app\common\model\Profile::find(1);
        dump($info->admin->username);
        $info=\app\common\model\Profile::with('admin')->find(1);
        $this->ok($info);*/

        //一对多关联
        //以分类表为主 查询一条
        /*$info=\app\common\model\Category::find(72);
        $info=\app\common\model\Category::with('brands')->find(72);
        $this->ok($info);*/
        //查询多条
        /*$data=\app\common\model\Category::with('brands')->select();
        $this->ok($data);*/
        //以品牌表为主
        $info=\app\common\model\Brand::with('category')->select();
        $this->ok($info);

        //测试Token工具类
        //生成token
        $token = \tools\jwt\Token::getToken(200);
        dump($token);
        //解析token 得到用户id
        $user_id = \tools\jwt\Token::getUserId($token);
//        dump($user_id);die;

        //测试响应方法
        //$this->response();
        //$this->response(200, 'success', ['id' => 100, 'name' => 'zhangsan']);
        //$this->ok(['id' => 100, 'name' => 'zhangsan']);
        //$this->response(400, '参数错误');
        //$this->fail('参数错误');
        //$this->fail('参数错误', 401);
        //测试数据库配置
//        $goods = \think\Db::table('pyg_goods')->find();
//        dump($goods);die;
        //return 'hello, 这里是adminapi的index的index方法';
    }
}
