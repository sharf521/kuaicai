<?
require './init.php';
$user_id=(int)$_POST['user_id'];
$host=$_SERVER['HTTP_HOST'];
$money=(float)trim($_POST['cz_money']);
if(empty($user_id))
{
	header("location:/");	
	echo '��������'; 
	exit();	
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>����֧��</title>
</head>
<script language="javascript">
function init()
{
	document.getElementById('form1').submit();	
}
</script>
<body onLoad="init()">
<?php
	
	$Version = '10';
	$CmdId = "Buy";
	$MerId = '871746';
	$OrdId = date('YmdHms').rand(100,999);
	$OrdAmt = sprintf("%.2f",$money);
	$CurCode = "RMB";
	$Pid = '';//��Ʒ���
	
	//$RetUrl   = "http://$host/chinapnr/Buy_return_url.php";
	//$BgRetUrl = "http://$host/chinapnr/Buy_notify_url.php";
	
	$RetUrl   = "http://$host/chinapnr/chinapnr_return.php";
	$BgRetUrl = "http://$host/chinapnr/chinapnr_return.php";
	
	$MerPriv = $user_id.'#'.$host;//�̻�˽��������
	$GateId = $_POST['GateId'];//���غ�
	$UsrMp = '';//�û��ֻ���
	$DivDetails = '';//Agent:000004662788:1.00
/*	if($OrdAmt>=200)//��վ����
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
	$PayUsrId = '';//�������û���
	
	//��ǩ
	/*$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $Version.$CmdId.$MerId.$OrdId.$OrdAmt.$CurCode.$Pid.$RetUrl.$MerPriv.$GateId.$UsrMp.$DivDetails.$PayUsrId.$BgRetUrl;
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/MerPrK871746.key";
	$ChkValue = $SignObject->SignMsg0($MerId,$MerFile,$MsgData,strlen($MsgData));*/
	
	
	
	
	$fp = fsockopen("127.0.0.1", '8733', $errno, $errstr, 10);
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		
		$MsgData = $Version.$CmdId.$MerId.$OrdId.$OrdAmt.$CurCode.$Pid.$RetUrl.$MerPriv.$GateId.$UsrMp.$DivDetails.$PayUsrId.$BgRetUrl;
		$MsgData_len =strlen($MsgData);
		if($MsgData_len < 100 ){
			$MsgData_len = '00'.$MsgData_len;
		}
		elseif($MsgData_len < 1000 ){
			$MsgData_len = '0'.$MsgData_len;
		}

		$out = 'S'.$MerId.$MsgData_len.$MsgData;
		
		$out_len = strlen($out);
		if($out_len < 100 ){
			$out_len = '00'.$out_len;
		}
		elseif($out_len < 1000 ){
			$out_len = '0'.$out_len;
		}		
		$out =$out_len.$out;

		//echo $MsgData_len;exit;
		//$out = '0021S87052400101234567890';
		fputs($fp, $out);

		$ChkValue ='';
		while (!feof($fp)) {
			$ChkValue .= fgets($fp, 128);
		}
		//$ChkValue = substr($ChkValue, -264,-8);
		$ChkValue = substr($ChkValue, -256);
		fclose($fp);
	}
	
	

?>
<form name="form" id="form1" style="display:none" method="post" action="http://mas.chinapnr.com/gar/RecvMerchant.do">
<input type="submit" name="Submit" value="�ύ">
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
  <input type="submit">
</form>
<script language="javascript">
init();
</script>
</body>
</html>
