<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            /* 检测验证码 TODO: */
            //if(!check_verify($verify)){
            //    $this->error('验证码输入错误！');
            //}

            /* 调用UC登录接口登录 */
            $User = new UserApi;
            $uid = $User->login($username, $password);
            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                //$Member = D('Member');
                //if($Member->login($uid)){ //登录用户
                $username=M('UcenterMember')->where('id='.$uid)->getField('username');
                $auth = array(
                    'uid'             => $uid,
                    'username'        => $username,
                );
                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));
                    //TODO:跳转到登录前页面
                    $this->success('登录成功！', U('User/index'));
                //} else {
                //    $this->error($Member->getError());
                //}

            } else { //登录失败
                switch($uid) {
                    case -1: $error = '手机号不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	=	S('DB_CONFIG_DATA');
                if(!$config){
                    $config	=	D('Config')->lists();
                    S('DB_CONFIG_DATA',$config);
                }
                C($config); //添加配置
                
                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('Member')->logout();
            session('[destroy]');
            header('location:login.html');
            //$this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }
    public function xxagent(){
        $q = I('q');
        if($q != ''){
            $q = I('q');
            $result = M('UcenterMember')->where('mobile='.$q)->find();
            $result1[1] = $result;
            int_to_string($result1);
            $result = $result1[1];
            if($result){
                if($result['status']!=1||$result['end_time'] <= time()||$result['auth_pic']==''){
                    $q='查询结果:<br/>代理状态异常或授权已过期';
                }else{
                    //$q="查询结果:<br/>代理姓名:".$result['username']."<br/>代理级别:".$result['level_text']."<br/>授权到期时间:".date('Y-m-d', $result['end_time']);
                    $q='<img src="'.$result['auth_pic'].'" width="800px;">';
                }

            }else{
                $q='查询结果:<br/>没有找到对应代理的授权，请检查输入信息是否错误';
            }
            $this->result = $q;
        }
        $this->display();
    }

}
