<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>֧���տ�-�ڶ���</title>
</head>

<body>
<?php

	$Version = trim($_POST['Version']);
	$CmdId = "Buy";
	$MerId = trim($_POST['MerId']);
	$OrdId = trim($_POST['OrdId']);
	$OrdAmt = trim($_POST['OrdAmt']);
	$CurCode = "RMB";
	$Pid = trim($_POST['Pid']);
	$RetUrl = trim($_POST['RetUrl']);
	$BgRetUrl = trim($_POST['BgRetUrl']);
	$MerPriv = trim($_POST['MerPriv']);
	$GateId = trim($_POST['GateId']);
	$UsrMp = trim($_POST['UsrMp']);
	$DivDetails = trim($_POST['DivDetails']);
	$PayUsrId = trim($_POST['PayUsrId']);
	
	
	//��ǩ
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $Version.$CmdId.$MerId.$OrdId.$OrdAmt.$CurCode.$Pid.$RetUrl.$MerPriv.$GateId.$UsrMp.$DivDetails.$PayUsrId.$BgRetUrl;
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/MerPrK871746.key";
	$ChkValue = $SignObject->SignMsg0($MerId,$MerFile,$MsgData,strlen($MsgData));

?>
<form name="form" method="post" action="http://mas.chinapnr.com/gar/RecvMerchant.do" target="_blank">
<table border=1 width=650 align="center">
<th colspan=3 height=27>֧���տ�-������(�ڶ���)</th>
<tr><td width=150 height=27>�汾��</td><td width=80>Version</td><td width=350><?=$Version?></td></tr>
<tr><td width=150 height=27>��Ϣ����</td><td width=80>CmdId</td><td width=350><?=$CmdId?></td></tr>
<tr><td width=150 height=27>�̻���</td><td width=80>MerId</td><td width=350><?=$MerId?></td></tr>
<tr><td width=150 height=27>������</td><td width=80>OrdId</td><td width=350><?=$OrdId?></td></tr>
<tr><td width=150 height=27>�������</td><td width=80>OrdAmt</td><td width=350><?=$OrdAmt?></td></tr>
<tr><td width=150 height=27>����</td><td width=80>CurCode</td><td width=350><?=$CurCode?></td></tr>
<tr><td width=150 height=27>��Ʒ���</td><td width=80>Pid</td><td width=350><?=$Pid?></td></tr>
<tr><td width=150 height=27>ҳ�淵�ص�ַ</td><td width=80>RetUrl</td><td width=350><?=$RetUrl?></td></tr>
<tr><td width=150 height=27>��̨���ص�ַ</td><td width=80>BgRetUrl</td><td width=350><?=$BgRetUrl?></td></tr>
<tr><td width=150 height=27>�̻�˽��������</td><td width=80>MerPriv</td><td width=350><?=$MerPriv?></td></tr>
<tr><td width=150 height=27>���غ�</td><td width=80>GateId</td><td width=350><?=$GateId?></td></tr>
<tr><td width=150 height=27>�û��ֻ���</td><td width=80>UsrMp</td><td width=350><?=$UsrMp?></td></tr>
<tr><td width=150 height=27>������ϸ</td><td width=80>DivDetails</td><td width=350><?=$DivDetails?></td></tr>
<tr><td width=150 height=27>�������û���</td><td width=80>PayUsrId</td><td width=350><?= $PayUsrId ?></td></tr>
<tr><td width=150 height=27>����ǩ��</td><td width=80>ChkValue</td><td width=350> <textarea name="ChkValue" cols="60" rows="6"><?=$ChkValue?></textarea></td></tr>
<tr><td colspan="3" align="center" height=27><input type="submit" name="Submit" value="�ύ"></td></tr>
</table>
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
</body>
</html>
