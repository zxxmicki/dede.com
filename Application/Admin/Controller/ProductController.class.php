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
class ProductController extends AdminController {

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){

        $map['status']  =   array('egt',0);
        $list   = $this->lists('Product',$map);

        $this->mynum = M('Product')->where($map)->count();
		$level=M(UcenterMember)->where('id='.UID)->getField('level');
        $this->fid=M(UcenterMember)->where('id='.UID)->getField('fid');
        $this->money=M(UcenterMember)->where('id='.UID)->getField('money');
        $this->level=M(UcenterMember)->where('id='.UID)->getField('level');
		foreach($list as $key => $value){
            $list[$key]['num']=M(ProductToUser)->where('product_id='.$list[$key]['id'].' and user_id='.UID)->getField('qty');
            $list[$key]['box_n']=floor($list[$key]['num']/$list[$key]['box']);
            $list[$key]['pie_n']=$list[$key]['num']%$list[$key]['box'];
			$list[$key]['price']=$list[$key]['price_'.$level];
		}
        $this->assign('_list', $list);
        $this->meta_title = '产品信息';

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
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('Product', $map );
                break;
            case 'resumeuser':
                $this->resume('Product', $map );
                break;
            case 'deleteuser':
                $this->delete('Product', $map );
                break;
            case 'ship_c':
                $data=array('status'=>1,'update_time'=>time());
                M('Ship')->where('id='.$id)->save($data);
                $this->success('发货成功');
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function buy(){
        $id = I('id',0);
        $box = I('box');
        $pie = I('pie');
        $p = M('Product')->where('id='.$id)->find();
        $u = M('UcenterMember')->where('id='.UID)->find();
        $num = $box*$p['box']+$pie;
        if($num<=0)$this->error('请输入正确的购买数量');
        if(!is_int($num))$this->error('请输入正确的购买数量');
        $money_totle = $num*$p['price_'.$u['level']];
        if($money_totle>$u['money'])$this->error('余额不足,请充值');
        $p_u=M('ProductToUser')->where('product_id='.$id.' and user_id='.UID)->getField('id');
        if($p_u){
            M('ProductToUser')->where('id='.$p_u)->setInc('qty',$num);
        }else{
            M('ProductToUser')->add(array('product_id'=>$id,'user_id'=>UID,'qty'=>$num));
        }
        M('UcenterMember')->where('id='.UID)->setDec('money',$money_totle);
        $data=array('u_id'=>UID,'o_id'=>'','type'=>'money','num'=>$money_totle,'comment'=>'购买产品id:'.$id,'time'=>time());
        M('Log')->add($data);
        $data=array('u_id'=>1,'o_id'=>UID,'type'=>'send','num'=>$num,'comment'=>'公司配货产品id:'.$id,'time'=>time());
        M('Log')->add($data);
        $this->success('购买成功,金额:'.$money_totle);

    }

    public function send(){
        $id = I('id');
        $this->name =  M('Product')->where('id='.$id)->getField('name');
        $this->box =  M('Product')->where('id='.$id)->getField('box');
        $own_n = M('ProductToUser')->where('product_id='.$id.' and user_id='.UID)->getField('qty');
        if(IS_POST){
            $uid = I('uid');if($uid=='')$this->error('参数错误');
            $box = I('box');
            $pie = I('pie');
            $num = $box*$this->box+$pie;
            if($num<=0)$this->error('请输入要配送的数量');
            if(!is_int($num))$this->error('请输入正确的配送数量');
            if($own_n < $num)$this->error('您的存货不足,请先购买');
            $p_u=M('ProductToUser')->where('product_id='.$id.' and user_id='.$uid)->getField('id');
            if($p_u){
                M('ProductToUser')->where('id='.$p_u)->setInc('qty',$num);
            }else{
                M('ProductToUser')->add(array('product_id'=>$id,'user_id'=>$uid,'qty'=>$num));
            }
            M('ProductToUser')->where('product_id='.$id.' and user_id='.UID)->setDec('qty',$num);
            $data=array('u_id'=>UID,'o_id'=>$uid,'type'=>'send','num'=>$num,'comment'=>'代理配货产品id:'.$id,'time'=>time());
            M('Log')->add($data);
            $this->success('配货成功');
        } else {
            $this->id=$id;
            $this->box_n=floor($own_n/$this->box);
            $this->pie_n=$own_n%$this->box;
            $this->slt = '';
            $list = M('UcenterMember')->where('fid='.UID.' and status>0')->select();
            foreach($list as $key => $value){
                $this->slt .= '<option value="'.$list[$key]['id'].'">'.$list[$key]['username'].'</option>';
            }
            $this->meta_title = '代理配货';

            $this->display();
        }
    }

    public function ship(){
        $id = I('id');
        $this->name =  M('Product')->where('id='.$id)->getField('name');
        $this->box =  M('Product')->where('id='.$id)->getField('box');
        $own_n = M('ProductToUser')->where('product_id='.$id.' and user_id='.UID)->getField('qty');
        if(IS_POST){
            $box = I('box');
            $pie = I('pie');
            $tel = I('tel');
            $name = I('name');
            $address = I('address');
            $comment = I('comment');
            $num = $box*$this->box+$pie;
            if($num<=0)$this->error('请输入要配送的数量');
            if(!is_int($num))$this->error('请输入正确的配送数量');
            if($own_n < $num)$this->error('您的存货不足');
            if($tel==''||$name==''||$address=='')$this->error('收货人信息不完整');
            $data = array('product_id'=>$id,'product_num'=>$num,'name'=>$name,'tel'=>$tel,'address'=>$address,'comment'=>$comment,'user_id'=>UID,'status'=>0,'time'=>time());
            M('Ship')->add($data);
            M('ProductToUser')->where('product_id='.$id.' and user_id='.UID)->setDec('qty',$num);
            $data=array('u_id'=>UID,'o_id'=>'','type'=>'send','num'=>$num,'comment'=>'发货申请产品id:'.$id.'客户:'.$name,'time'=>time());
            M('Log')->add($data);
            $this->success('发货申请成功');
        } else {
            $this->id=$id;
            $this->box_n=floor($own_n/$this->box);
            $this->pie_n=$own_n%$this->box;
            $this->slt = '';
            $list = M('UcenterMember')->where('fid='.UID.' and status>0')->select();
            foreach($list as $key => $value){
                $this->slt .= '<option value="'.$list[$key]['id'].'">'.$list[$key]['username'].'</option>';
            }
            $this->meta_title = '发货申请';

            $this->display();
        }
    }

    public function add(){
        if(IS_POST){
            $data = array();
            $data['name'] = I('name');
            $data['img'] = I('img');
            $data['price_1'] = I('price_1');
            $data['price_2'] = I('price_2');
            $data['price_3'] = I('price_3');
            $data['price_4'] = I('price_4');
            $data['price_5'] = I('price_5');
            $data['box'] = I('box');
            if($data['name']==''||$data['img']==''||$data['price_1']==''||$data['price_2']==''||$data['price_3']==''||$data['price_4']==''||$data['price_5']==''||$data['box']=='')$this->error('信息不完善！');
            $rs = M('Product')->add($data);
            if($rs){ //注册成功
                $this->success('产品添加成功！',U('index'));
            } else { //注册失败，显示错误信息
                $this->error('产品添加失败！');
            }
        } else {
            $this->slt = '';
            $arr = array(1=>'销售代表',2=>'区域经理',3=>'省级经理',4=>'总经理',5=>'董事');
            $level = M('UcenterMember')->where('id='.UID)->getField('level');
            foreach($arr as $key => $value){
                if($key < $level)$this->slt .= '<option value="'.$key.'">'.$value.'</option>';
            }
            $this->meta_title = '添加新产品';
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
            $data = array();
            $data['name'] = I('name');
            $data['img'] = I('img');
            $data['price_1'] = I('price_1');
            $data['price_2'] = I('price_2');
            $data['price_3'] = I('price_3');
            $data['price_4'] = I('price_4');
            $data['price_5'] = I('price_5');
            $data['box'] = I('box');
            if($data['name']==''||$data['img']==''||$data['price_1']==''||$data['price_2']==''||$data['price_3']==''||$data['price_4']==''||$data['price_5']==''||$data['box']=='')$this->error('信息不完善！');
            $res = M('Product')->where('id='.I('id'))->save($data);
            if($res){
                    $this->success('修改信息成功！',U('index'));
            } else {
                $this->error("没有更改!");
            }
        } else {
            $id = I('id');
            $this->data = M('Product')->where('id='.$id)->find();

            $this->meta_title = '编辑产品';
            $this->display();
        }
    }
    public function log(){
        if(UID != 1)$map['o_id|u_id']  =  UID;
        $map['type']  =  'send';
        $list   = $this->lists('Log', $map, 'time desc');

        $this->assign('_list', $list);
        $this->meta_title = '产品记录';

        $this->display();
    }
    public function slog(){
        if(UID != 1)$map['user_id']  =  UID;

        $list   = $this->lists('Ship', $map, 'time desc');
        $this->uid=UID;
        $this->assign('_list', $list);
        $this->meta_title = '发货管理';

        $this->display();
    }
}
