<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>退款-第一步</title>
</head>

<body>
<form name=form action="refund_2.php" method="post" OnSubmit="true">
<br>
<table align="center" border=1 width=725>
	<th colspan=3 height=27>退款(第一步)</th>
	<tr><td width=161 height=27>版本</td><td width=87>Version</td><td width=455 align="left"><input type="text" name="Version" value="10" size="22" maxlength="2"></td></tr>
	<tr><td width=161 height=27>商户号</td><td>MerId</td><td width=455 align="left"><input type="text" name="MerId" value="" size="10" maxlength="6"></td></tr>
	<tr><td width=161 height=27>退款分账明细</td><td>DivDetails</td><td><input type="text" name="DivDetails" size="60" maxlength="60"></td></tr>
	<tr><td width=161 height=27>退款金额</td><td>RefAmt</td><td><input name="RefAmt" type="text" id="RefAmt" value="0.01" size="22" maxlength="12"></td></tr>
	<tr><td width=161 height=27>退款订单号</td><td>OrdId</td><td><input type="text" name="OrdId" value="<?=date(Ymdhms)?>" size="22" maxlength="20"></td></tr>
	<tr><td width=161 height=27>交易订单号</td><td>OldOrdId</td><td><input name="OldOrdId" type="text" id="OldOrdId" value="" size="22" maxlength="20"></td></tr>
	<tr><td width=161 height=27>后台返回地址</td><td>BgRetUrl</td><td><input type="text" name="BgRetUrl" value="http://www.xxx.cn/php_example/Refund_return_url.php" size="60" maxlength="260"></td></tr>
	<tr><td colspan="3" align="center" height=27><input type="submit" name="Submit" value="提交"></td></tr>
</table>
</form>
</body>
</html>
