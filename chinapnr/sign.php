<?
$UsrId=$_GET['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>支付收款-第二步</title>
</head>
<script language="javascript">
function init()
{
	document.getElementById('form1').submit();	
}
</script>
<body>
<h2>签约银帐户</h2>
<?php

	$Version = 10;
	$CmdId = "Sign";	
	$MerId = '871746';
	//$UsrId='000004662788';	
	
	$MerDate=date('Ymd');	

	$MerTime=date('Hsi');

	$BgRetUrl = 'http://'.$_SERVER['HTTP_HOST'].'/chinapnr/sign_notify_url.php';
	//加签
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $Version.$CmdId.$MerId.$UsrId.$MerDate.$MerTime.$BgRetUrl;
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/MerPrK871746.key";
	$ChkValue = $SignObject->SignMsg0($MerId,$MerFile,$MsgData,strlen($MsgData));

?>
<form name="form" id="form1" method="post" action="http://mas.chinapnr.com/gau/UnifiedServlet">
  Version<input type=text name="Version" value="<?=$Version?>"><br />
  CmdId<input type=text name="CmdId" value="<?=$CmdId?>"><br />
  MerId<input type=text name="MerId" value="<?=$MerId?>"><br />
  银帐户ID：<input type=text name="UsrId" value="<?=$UsrId?>"><br />
  MerDate<input type=text name="MerDate" value="<?=$MerDate?>"><br />
  MerTime<input type=text name="MerTime" value="<?=$MerTime?>"><br />
  BgRetUrl<input type=text name="BgRetUrl" value="<?=$BgRetUrl?>"><br />
  OperId<input type="text" name="OperId" value="" /><br />
  
  <textarea name="ChkValue" cols="60" rows="6"><?=$ChkValue?></textarea>
  <input type="submit" name="Submit" value="提交">
</form>
<script language="javascript">
<?
if(!empty($UsrId)){echo 'init();';}
?>
</script>
</body>
</html>
