<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>鑫玺控股集团</title>

</head>
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/login.css" media="all">
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
<body id="case">
<!--头部--开始-->
<div class="wrapper">


    <link rel="stylesheet" href="xxfiles/reset.css">
    <link rel="stylesheet" href="xxfiles/style.css">
    <script type="text/javascript" src="./xxfiles/jquery-1.10.2.min.js"></script>
    <title>顺为资本</title>


    <div class="top clearfix">
        <h1 class="logo"><a href="index.html"></a></h1>
        <div class="brk_crmb">

            <div class="top_sub clearfix"><a href="mailto:271055144@qq.com">提交商业计划书&nbsp;&nbsp;&nbsp;></a></div>
            <!-- <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="goLogin()">登录&nbsp;&nbsp;&nbsp;></a></div>
            <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="goRegister()">注册&nbsp;&nbsp;&nbsp;></a></div>
            <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="goPlanList()">计划书列表&nbsp;&nbsp;&nbsp;></a></div>
            <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="goUpdatePlan()">修改计划书&nbsp;&nbsp;&nbsp;></a></div>
            <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="goUpdateUser()">修改个人信息&nbsp;&nbsp;&nbsp;></a></div>
            <div class="top_sub clearfix"><a href="javascript:void(0);" onclick="forgetPwd()">忘记密码&nbsp;&nbsp;&nbsp;></a></div> -->
            <div class="nav clearfix">
                <ul class="right">
                    <li><a href="index.html">首页</a></li>

                    <li><a href="case.html">投资组合</a></li>
                    <li><a href="news.html">新闻动态</a></li>
                    <li><a href="xxagent.html">鑫玺代理</a></li>
                    <li class="current"><a href="login.html">经销商登陆</a></li>
                </ul>
            </div>
        </div>
    </div>

    </head>
    <body id="login-page">
        <div id="main-content">

            <!-- 主体 -->
            <div class="login-body">
                <div class="login-main pr">
                    <form action="<?php echo U('login');?>" method="post" class="login-form">
                        <h3 class="welcome"><i class="login-logo"></i>鑫玺代理平台</h3>
                        <div id="itemBox" class="item-box">
                            <div class="item">
                                <i class="icon-login-user"></i>
                                <input type="text" name="username" placeholder="请填写用户名" autocomplete="off" />
                            </div>
                            <span class="placeholder_copy placeholder_un">请填写登陆手机号</span>
                            <div class="item b0">
                                <i class="icon-login-pwd"></i>
                                <input type="password" name="password" placeholder="请填写密码" autocomplete="off" />
                            </div>
                            <span class="placeholder_copy placeholder_pwd">请填写密码</span>
                            <!--
                            <div class="item verifycode">
                                <i class="icon-login-verifycode"></i>
                                <input type="text" name="verify" placeholder="请填写验证码" autocomplete="off">
                                <a class="reloadverify" title="换一张" href="javascript:void(0)">换一张？</a>
                            </div>
                            <span class="placeholder_copy placeholder_check">请填写验证码</span>
                            <div>
                                <img class="verifyimg reloadverify" alt="点击切换" src="<?php echo U('Public/verify');?>">
                            </div>
                            -->
                        </div>
                        <div class="login_btn_panel">
                            <button class="login-btn" type="submit">
                                <span class="in"><i class="icon-loading"></i>登 录 中 ...</span>
                                <span class="on">登 录</span>
                            </button>
                            <div class="check-tips"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	<!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <!--<![endif]-->
    <script type="text/javascript">
    	/* 登陆表单获取焦点变色 */
    	$(".login-form").on("focus", "input", function(){
            $(this).closest('.item').addClass('focus');
        }).on("blur","input",function(){
            $(this).closest('.item').removeClass('focus');
        });

    	//表单提交
    	$(document)
	    	.ajaxStart(function(){
	    		$("button:submit").addClass("log-in").attr("disabled", true);
	    	})
	    	.ajaxStop(function(){
	    		$("button:submit").removeClass("log-in").attr("disabled", false);
	    	});

    	$("form").submit(function(){
    		var self = $(this);
    		$.post(self.attr("action"), self.serialize(), success, "json");
    		return false;

    		function success(data){
    			if(data.status){
    				window.location.href = data.url;
    			} else {
    				self.find(".check-tips").text(data.info);
    				//刷新验证码
    				$(".reloadverify").click();
    			}
    		}
    	});

		$(function(){
			//初始化选中用户名输入框
			$("#itemBox").find("input[name=username]").focus();
			//刷新验证码
			var verifyimg = $(".verifyimg").attr("src");
            $(".reloadverify").click(function(){
                if( verifyimg.indexOf('?')>0){
                    $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
                }else{
                    $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
                }
            });

            //placeholder兼容性
                //如果支持 
            function isPlaceholer(){
                var input = document.createElement('input');
                return "placeholder" in input;
            }
                //如果不支持
            if(!isPlaceholer()){
                $(".placeholder_copy").css({
                    display:'block'
                })
                $("#itemBox input").keydown(function(){
                    $(this).parents(".item").next(".placeholder_copy").css({
                        display:'none'
                    })                    
                })
                $("#itemBox input").blur(function(){
                    if($(this).val()==""){
                        $(this).parents(".item").next(".placeholder_copy").css({
                            display:'block'
                        })                      
                    }
                })
                
                
            }
		});
    </script>
        <!--底部--开始-->
        <div id="foot" class="foot clearfix">
            <div class="foot_right">
                <div class="foot_box">
                    <div class="box">
                        <p class="txt">联系我们</p>
                        <!-- <p class="font_col">Contact@shunwei.com</p>
                        <p class="font_col">(010) 4857 6793</p>
                        <p class="font_col">(010) 4768 7984</p> -->
                        <p class="font_col" style="height:65px;">
                            浙江省杭州市未来科技城安通大厦311</br>手机：13067782335</br>电话：400-728-0299</p>
                    </div>
                    <div class="box">
                        <p class="txt">关注我们</p>
                        <p class="icon">
                            <a class="sina" href="http://weibo.com/xinxiss" target="_blank"></a>
                            <!--<a class="weixin" href="javascript:;"></a>-->
                        </p>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="foot_left">
                <p class="copy">Copyright © 2015 Xin Xi All rights reserved. </p>
            </div>
        </div>
        <!--底部--结束-->

        <script type="text/javascript">
        </script>


  </body></html>