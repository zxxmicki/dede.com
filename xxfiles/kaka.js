// JavaScript Document
$(document).ready(function() {
	if($.fn.fullpage){
		$("#fullpage").fullpage({
			navigation:true,
			anchors:['#page1','#page2','#page3','#page4','#page5','#page6'],
			afterLoad: function(anchorLink, index){
				if(index == 1){
					//$(".section1 p.animated").addClass("fadeInUp");
					setTimeout(function(){$(".section1 p.animated").eq(0).addClass("fadeInUp")},0);
					setTimeout(function(){$(".section1 p.animated").eq(1).addClass("fadeInUp")},500);
					setTimeout(function(){$(".section1 p.animated").eq(2).addClass("fadeInUp")},1000);
					setTimeout(function(){$(".section1 p.animated").eq(3).addClass("fadeInUp")},1500);

				}
				if(index == 2){
					$(".section2 p.animated").addClass("fadeInUp");
				}
				if(index == 3){
					$(".section3 p.animated").addClass("fadeInUp");
				}
				if(index == 4){
					$(".section4 p.animated").addClass("fadeInUp");
				}
				if(index == 5){
					$(".section5 p.animated").addClass("fadeInUp");
				}
				if(index == 6){
					$(".section6 p.animated").addClass("fadeInUp");
				}
			},
			onLeave: function(index, direction){
				$(".section p.animated").removeClass("fadeInUp");
			}
		});
	}
});
/*以下  走进kaka   控制器*/
function Nearkk($scope){
	$scope.nearA=1;
	$scope.togNearA=function(a){
		$scope.nearA=a;
	};
};
/*以下  微商资讯  控制器*/
function wszxCtrl($scope){
	$scope.wsDate=[
		{date:'2014-12-31'},
		{date:'2014-12-30'},
		{date:'2014-12-29'},
		{date:'2014-12-28'},
	];
	/**
	$scope.wsDateInfo=[
		{
			title:'“台湾咔咔寿”瘦身梅再创微商传播神话',
			coutent:''
		}
	];
	*/
	$scope.selected=function(record)
	{
		for(var i in $scope.wsDate)
		{
			$scope.wsDate[i].selected = false;
		}
		record.selected=true;
	};
	//$scope.wsA=;
};
/*以下  kaka代理  控制器*/
function agentKK($scope){
	$scope.agentA=1;
	$scope.togAgent=function(b){
		$scope.agentA=b;
	};
};
function kkContact($scope){
	$scope.title=['北京微管家网络科技有限公司'];
	$scope.info=[
		{name:'电话', value:'400-6527518'},
		{name:'传真', value:'400-6527-518'},
		{name:'邮箱', value:'3201958134@qq.com'},
		{name:'微信公众账号', value:'kakaso'},
	];
};

