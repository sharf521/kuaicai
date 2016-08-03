<?php
	require('./init.php');		
	//开关
	$_S['kaiguan']=$db->get_one("select * from {kaiguan} where id=1");
	//参数
	$_S['canshu']=$db->get_one("select * from {canshu} where id=1");		
	//$_S['canshu']['webservip']='192.168.1.150:888';
	
	
	require('./include/global.func.php');
		
	
	$arr_type=array(	
	'0'=>'选择类型',
	'1'=>'线下充值金额',
	'2'=>'线下充值费用',
	'3'=>'兑换积分',
	'4'=>'兑换现金',
	'5'=>'提现申请金额',
	'6'=>'提现金额',
	'7'=>'提现费用',
	'8'=>'购买优惠券',
	'9'=>'采购申请',
	'10'=>'采购商品',
	'11'=>'采购不通过',
	'12'=>'团购保证金',
	'13'=>'团购费用',
	'14'=>'解冻团购保证金',
	'15'=>'购买商品',
	'16'=>'出售商品',
	'17'=>'出售供货商品',
	'18'=>'取消订单',
	'24'=>'后台增减用户资金',
	'25'=>'增加币库资金',
	'26'=>'充值成功，减少币库',
	'27'=>'增加用户资金，减少币库',
	'28'=>'减少用户资金，增加库币',	
	'29'=>'购买套餐申请',
	'31'=>'锁定用户资金',
	'32'=>'锁定用户积分',
	'36'=>'购买套餐',
	'37'=>'套餐升级',
	'38'=>'推荐人获得的奖励',
	'39'=>'分站获得推荐奖励',
	'40'=>'区域代理获得的推荐奖励',
	'41'=>'借款',
	'42'=>'还款',
	'100'=>'在线充值',
	'101'=>'在线充值奖励',
	'102'=>'在线充值奖励平台',
	'103'=>'在线充值奖励平台推荐人',
	'104'=>'成长积分',
	'105'=>'日封顶收益',
	'110'=>'从借贷转入',
	'111'=>'商城转入借贷',
	'112'=>'借贷充值奖励利息转向商城',
	'119'=>'向商城转出资金',
	'117'=>'商城转入资金',
	//'106'=>'核定点',
);


	
	require('./include/lang.php');
	require("./include/admin.func.php");	
 	$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : "";
	if($act!="")	
	{
		if($act!="login")
		{
			if($_SESSION["admin_id"]=="" || $_SESSION["admin_userid"]=="")
			{
				//$_SESSION['referer_url'] = $_SERVER['HTTP_REFERER'];
				header("Location:?act=login");
			}
		}
		$a_id=$_SESSION["admin_id"];		
		$a_userid=$_SESSION["admin_userid"];
		$a_username=$_SESSION["admin_username"];
		$a_typeid=$_SESSION['admin_typeid'];
		$a_purview=$_SESSION["admin_purview"];


/*require_once(ROOT."/include/approvedpoint.class.php");
				$app=new approvedpoint();
				$app->doapproved('407acfed-35a6-4d0b-91d6-a403683208ab',2);*/
		
		
for($i=1;$i<100;$i++)
{
	$a_purview.=$i.',';	
}
		checkpurview();
		include $act.".php";
		echo "\n</body>\n</html>";
	}
	else
		header("Location:?act=login");		
//	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//	header("Cache-Control: no-store, no-cache, must-revalidate");
//	header("Cache-Control: post-check=0, pre-check=0", false);
//header('Cache-control:private,must-revalidate');
?>