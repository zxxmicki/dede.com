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
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class UserController extends AdminController {

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $nickname       =   I('nickname');
        $map['status']  =   array('egt',0);
        if(UID != 1)$map['fid']  =  UID;
        $map['mobile|username']    =   array('like', '%'.(string)$nickname.'%');
        $list   = $this->lists('UcenterMember', $map, 'fid');
        $map['status']  =  1;
        $this->mynum = M('UcenterMember')->where($map)->count();
        $this->totlenum=0;
        foreach($list as $key => $value){
            $list[$key]['own'] = M('UcenterMember')->where('fid='.$list[$key]['id'])->count();
            $list[$key]['ownr'] = M('UcenterMember')->where('fid='.$list[$key]['id'].' and status=1')->count();
            $this->totlenum += $list[$key]['ownr'];
            if(UID == 1)$list[$key]['top'] = M('UcenterMember')->where('id='.$list[$key]['fid'])->getField('username');
        }
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $fid =  M('UcenterMember')->where('id='.UID)->getField('fid');
        if($fid){
            $top = M('UcenterMember')->where('id='.$fid)->find();
            $this->top = $top['username'].' 手机:'.$top['mobile'].' 微信:'.$top['weixin'];
        }

        $this->display();
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname(){
        $nickname = M('UcenterMember')->getFieldById(UID, 'username');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname(){
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User   =   new UserApi();
        $uid    =   $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $data   =   array('username'=>$nickname);

        $res = M('UcenterMember')->where(array('id'=>$uid))->save($data);

        if($res){
            $user               =   session('user_auth');
            $user['username']   =   $data['username'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        }else{
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword(){
        $this->meta_title = '修改密码';
        $this->display();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UserApi();
        $res    =   $Api->updateInfo(UID, $password, $data);
        if($res['status']){
            $this->success('修改密码成功！');
        }else{
            $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action(){
        //获取列表数据
        $Action =   M('Action')->where(array('status'=>array('gt',-1)));
        $list   =   $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction(){
        $this->meta_title = '新增行为';
        $this->assign('data',null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign('data',$data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction(){
        $res = D('Action')->update();
        if(!$res){
            $this->error(D('Action')->getError());
        }else{
            $this->success($res['id']?'更新成功！':'新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        if( in_array(C('USER_ADMINISTRATOR'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] =   array('in',$id);
        $level=M('UcenterMember')->where('id='.$id)->getField('level');
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('UcenterMember', $map );
                break;
            case 'resumeuser':
                $this->resume('UcenterMember', $map );
                break;
            case 'deleteuser':
                $this->delete('UcenterMember', $map );
                break;
            case 'addm':
                if($level!=5)$this->error('非董事不能加款');
                M('UcenterMember')->where('id='.$id)->setInc('money',I('money'));
                $data=array('u_id'=>UID,'o_id'=>$id,'type'=>'money','num'=>I('money'),'comment'=>'充值','time'=>time());
                M('Log')->add($data);$this->success('加款成功!');
                break;
            case 'subm':
                if($level!=5)$this->error('非董事不能加款');
                M('UcenterMember')->where('id='.$id)->setDec('money',I('money'));
                $data=array('u_id'=>UID,'o_id'=>$id,'type'=>'money','num'=>I('money'),'comment'=>'管理员扣款','time'=>time());
                M('Log')->add($data);
                $this->success('减款成功!');
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function add($username = '', $password = '', $repassword = '', $email = '', $mobile = '', $fid = '', $level=''){
        if(IS_POST){
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }

            /* 调用注册接口注册用户 */
            $User   =   new UserApi;
            $levelo = M('UcenterMember')->where('id='.UID)->getField('level');
            if(($level != ($levelo-1))||($level<1||$level>5)){
                $this->error('只能申请比自己小一级的代理！');
            }
            $uid    =   $User->register($username, $password, $email, $mobile, $fid, $level);
            if(0 < $uid){ //注册成功
                $AuthGroup = D('AuthGroup');
                if ( $AuthGroup->addToGroup($uid,3) ){
                    $this->success('用户添加成功！',U('index'));
                }else{
                    $this->error('用户添加失败！');
                }
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            }
        } else {
            $this->slt = '';
            $arr = array(1=>'销售代表',2=>'区域经理',3=>'省级经理',4=>'总经理',5=>'董事');
            $level = M('UcenterMember')->where('id='.UID)->getField('level');
            //foreach($arr as $key => $value){
                if($level!=1)$this->slt .= '<option value="'.($level-1).'">'.$arr[$level-1].'</option>';
            //}
            $this->meta_title = '申请下级代理';
            $this->fid = UID;
            $this->display();
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }

    public function edit(){
        if(IS_POST){
            if(UID != 1)$this->error('您没有修改权限！如有需求请联系管理员.');
            $id = I('id');
            $level = I('level');
            $auth_pic = I('auth_pic');
            $end_time = strtotime(I('end_time'));
            $id_num = I('id_num');
            $weixin =I('weixin');
            $city = I('city');
            $address = I('address');
            $data = array(level=>$level,auth_pic=>$auth_pic,mobile=>I('mobile'),username=>I('username'),email=>I('email'),end_time=>$end_time,id_num=>$id_num,weixin=>$weixin,city=>$city,address=>$address);
            if(I('password') != ''){
                $data['password'] = md5(sha1(I('password')) . 'I_y.5C2ZBo+Ut>s<]zQOe3Y8b~09fk7vwT}RaPJ#');
            }
            $res = M('UcenterMember')->where('id='.$id)->save($data);
            if($res){
                    $this->success('修改信息成功！',U('index'));
            } else {
                $this->error("没有更改!");
            }
        } else {
            if(UID == 1)$this->upload='<input type="file" id="upload_picture_auth_id">';
            $id = I('id');
            $data[1] = M('UcenterMember')->where('id='.$id)->find();
            int_to_string($data);
            $this->data = $data[1];
            $this->slt = '';
            $arr = array(1=>'销售代表',2=>'区域经理',3=>'省级经理',4=>'总经理',5=>'董事');
            $level = M('UcenterMember')->where('id='.UID)->getField('level');
            if(UID == 1)$level=6;
            foreach($arr as $key => $value){
                if($key < $level)$this->slt .= '<option value="'.$key.'" '.($key == $this->data['level'] ? 'selected="selected"' : '').'>'.$value.'</option>';
            }
            $this->meta_title = '查看资料';
            $this->display();
        }
    }

    public function log(){
        if(UID != 1)$map['o_id|u_id']  =  UID;
        $map['type']  =  'money';
        $list   = $this->lists('Log', $map, 'time desc');

        $this->assign('_list', $list);
        $this->meta_title = '资金记录';

        $this->display();
    }
}
