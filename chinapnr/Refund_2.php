<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�˿�-�ڶ���</title>
</head>

<body>
<?
	$Version		= trim($_POST['Version']);
	$CmdId			= "Refund";
	$MerId			= trim($_POST['MerId']);
	$DivDetails		= trim($_POST['DivDetails']);
	$RefAmt			= trim($_POST['RefAmt']);
	$OrdId			= trim($_POST['OrdId']);
	$OldOrdId		= trim($_POST['OldOrdId']);
	$BgRetUrl		= trim($_POST['BgRetUrl']);
	
	//ǩ��
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $Version.$CmdId.$MerId.$DivDetails.$RefAmt.$OrdId.$OldOrdId.$BgRetUrl;
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/MerPrK871746.key";
	$ChkValue = $SignObject->SignMsg0($MerId,$MerFile,$MsgData,strlen($MsgData));
	
	if(strlen($ChkValue)< 10){
		//ǩ��ʧ��
		echo "ǩ��ʧ��[".$ChkValue."]";
		exit();
	}

?>
<form name="form" method="post" action="http://test.chinapnr.com/gar/entry.do" target="_blank">
<br>
<table align="center" border=1 width=725>
	<th colspan=3 height=27>�˿�(�ڶ���)</th>
	<tr><td width=161 height=27>�汾</td><td width=87>Version</td><td width=455 align="left"><?=$Version?></td></tr>
	<tr><td width=161 height=27>��������</td><td width=87>CmdId</td><td width=455 align="left"><?=$CmdId?></td></tr>
	<tr><td width=161 height=27>�̻���</td><td>MerId</td><td width=455 align="left"><?=$MerId?></td></tr>
	<tr><td width=161 height=27>�˿������ϸ</td><td>DivDetails</td><td><?=$DivDetails?></td></tr>
	<tr><td width=161 height=27>�˿���</td><td>RefAmt</td><td><?=$RefAmt?></td></tr>
	<tr><td width=161 height=27>�˿����</td><td>OrdId</td><td><?=$OrdId?></td></tr>
	<tr><td width=161 height=27>���׶�����</td><td>OldOrdId</td><td><?=$OldOrdId?></td></tr>
	<tr><td width=161 height=27>��̨���ص�ַ</td><td>BgRetUrl</td><td><?=$BgRetUrl?></td></tr>
	<tr><td width=150 height=27>����ǩ��</td><td width=80>ChkValue</td><td width=350> <textarea name="ChkValue" cols="60" rows="6"><?=$ChkValue?></textarea></td></tr>
	<tr><td colspan="3" align="center" height=27><input type="submit" name="Submit" value="�ύ"></td></tr>
</table>
<input type=hidden name="Version" value="<?=$Version?>">
<input type=hidden name="CmdId" value="<?=$CmdId?>">
<input type=hidden name="MerId" value="<?=$MerId?>">
<input type=hidden name="DivDetails" value="<?=$DivDetails?>">
<input type=hidden name="RefAmt" value="<?=$RefAmt?>">
<input type=hidden name="OrdId" value="<?=$OrdId?>">
<input type=hidden name="OldOrdId" value="<?=$OldOrdId?>">
<input type=hidden name="BgRetUrl" value="<?=$BgRetUrl?>">
</form>
</body>
</html>
