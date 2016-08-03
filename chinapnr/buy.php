<?
require './init.php';
$user_id=(int)$_POST['user_id'];
$host=$_SERVER['HTTP_HOST'];
$money=(float)trim($_POST['cz_money']);
if(empty($user_id))
{
	header("location:/");
	echo '参数错误';
	exit();	
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>在线支付</title>
</head>
<script language="javascript">
function init()
{
	document.getElementById('form1').submit();	
}
</script>
<body onLoad="init()">
<?php
	
	$MerPriv = $user_id.'#'.$host;//商户私有数据项
	
	$OrdAmt = sprintf("%.2f",$money);
	$GateId = $_POST['GateId'];//网关号
	if(!in_array($GateId,array(25,29,27,28,12,13,33)))
	{
		include "95epay/RecvMerchant.php";
		exit();	
	}
	
	$Version = '10';
	$CmdId = "Buy";
	$MerId = '871746';
	$OrdId = date('YmdHms').rand(100,999);
	
	$CurCode = "RMB";
	$Pid = '';//商品编号
	
	//$RetUrl   = "http://$host/chinapnr/Buy_return_url.php";
	//$BgRetUrl = "http://$host/chinapnr/Buy_notify_url.php";
	
	$RetUrl   = "http://$host/chinapnr/chinapnr_return.php";
	$BgRetUrl = "http://$host/chinapnr/chinapnr_return.php";
	
	
	
	$UsrMp = '';//用户手机号
	$DivDetails = '';//Agent:000004662788:1.00
/*	if($OrdAmt>=200)//分站分润
	{
		$row=$db->get_one("select chinapnr from {city} where city_yuming like '%$host%' limit 1");
		$pnr=$row['chinapnr'];
		$row=null;
		if(!empty($pnr))
		{
			$pmoney=sprintf("%.2f",$OrdAmt*0.0004);
			$DivDetails = "Agent:$pnr:$pmoney";//Agent:000004662788:1.00	
		}
	}*/	
	$PayUsrId = '';//付款人用户号
	
	//加签
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $Version.$CmdId.$MerId.$OrdId.$OrdAmt.$CurCode.$Pid.$RetUrl.$MerPriv.$GateId.$UsrMp.$DivDetails.$PayUsrId.$BgRetUrl;
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/MerPrK871746.key";
	$ChkValue = $SignObject->SignMsg0($MerId,$MerFile,$MsgData,strlen($MsgData));

?>
<form name="form" id="form1" style="display:none" method="post" action="http://mas.chinapnr.com/gar/RecvMerchant.do">
<input type="submit" name="Submit" value="提交">
<input type="hidden" name="ChkValue" value="<?=$ChkValue?>" />
  <input type=hidden name="Version" value="<?=$Version?>">
  <input type=hidden name="CmdId" value="<?=$CmdId?>">
  <input type=hidden name="MerId" value="<?=$MerId?>">
  <input type=hidden name="OrdId" value="<?=$OrdId?>">
  <input type=hidden name="OrdAmt" value="<?=$OrdAmt?>">
  <input type=hidden name="CurCode" value="<?=$CurCode?>">
  <input type=hidden name="Pid" value="<?=$Pid?>">
  <input type=hidden name="RetUrl" value="<?=$RetUrl?>">
  <input type=hidden name="BgRetUrl" value="<?=$BgRetUrl?>">
  <input type=hidden name="MerPriv" value="<?=$MerPriv?>">
  <input type=hidden name="GateId" value="<?=$GateId?>">
  <input type=hidden name="UsrMp" value="<?=$UsrMp?>">
  <input type=hidden name="DivDetails" value="<?=$DivDetails?>">
  <input type=hidden name="PayUsrId" value="<?= $PayUsrId ?>">
</form>
<script language="javascript">
init();
</script>
</body>
</html>
