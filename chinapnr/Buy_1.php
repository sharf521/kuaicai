<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>支付收款-第一步</title>
</head>
<body>
<form name=form action="Buy_2.php" method="post" OnSubmit="true">
<br>
<table align="center" border=1 width=725>
	<th colspan=3 height=27>支付收款-单订单(第一步)</th>
	<tr><td width=161 height=27>版本</td><td width=87>Version</td><td width=455 align="left"><input type="text" name="Version" value="10" size="22" maxlength="2"></td></tr>
	<tr><td width=161 height=27>商户号</td><td>MerId</td><td width=455 align="left"><input type="text" name="MerId" value="871746" size="10"></td></tr>
	<tr><td width=161 height=27>订单号</td><td>OrdId</td><td><input type="text" name="OrdId" value="<?=date(Ymdhms)?>" size="22" maxlength="20"></td></tr>
	<tr><td width=161 height=27>订单金额</td><td>OrdAmt</td><td><input type="text" name="OrdAmt" value="1.01" size="22" maxlength="12"></td></tr>
	<tr><td width=161 height=27>商品编号</td><td>Pid</td><td><input type="text" name="Pid" value="" size="6" maxlength="10"></td></tr>
	<tr><td width=161 height=27>页面返回地址</td><td>RetUrl</td><td><input type="text" name="RetUrl" value="http://www.imm023.com/chinapnr/Buy_return_url.php" size="60" maxlength="60"></td></tr>
	<tr><td width=161 height=27>后台返回地址</td><td>BgRetUrl</td><td><input type="text" name="BgRetUrl" value="http://www.imm023.com/chinapnr/Buy_notify_url.php" size="60" maxlength="260"></td></tr>
	<tr><td width=161 height=27>商户私有数据项</td><td>MerPriv</td><td><input type="text" name="MerPriv" value="" size="60" maxlength="60"></td></tr>
	<tr><td width=161 height=27>网关号</td><td>GateId</td><td><input type="text" name="GateId" value="" size="4" maxlength="2"></td></tr>
	<tr><td width=161 height=27>用户手机号</td><td>UsrMp</td><td><input name="UsrMp" type="text" id="UsrMp" value="" size="15" maxlength="11"></td></tr>
	<tr><td width=161 height=27>分账明细</td><td>DivDetails</td><td><input type="text" name="DivDetails" size="60" maxlength="60" value="Agent:000004662788:1.00"></td></tr>
	<tr><td width=161 height=27>付款人用户号</td><td>PayUsrId</td><td><input type="text" name="PayUsrId" value="" size="40" maxlength="40"></td></tr>
	<tr><td colspan="3" align="center" height=27><input type="submit" name="Submit" value="提交"></td></tr>
	<tr>
	  <td colspan="3" align="center" height=27><div align="left">说明:<br>
	  &nbsp;&nbsp;1.商户号请填入你从汇付获得的六位商户号.<br>
	  &nbsp;&nbsp;2.订单号请与贵网站系统中的唯一订单号匹配.<br>
	  &nbsp;&nbsp;3.页面返回地址--当支付完成后跳转回贵网站系统的页面地址.<br>
	  &nbsp;&nbsp;4.后台返回地址--当支付完成后异步返回贵网站系统的地页面地址.<br>
	  &nbsp;&nbsp;5.网关号--当输入此项后，支付页面将直接跳转到网关号所对应的银行支付页面（您可向客服人员索取网关号）.<br>
	  &nbsp;&nbsp;6.分账明细--当存在一笔支付金额分发给不同的商户时使用，无此情况请留空.<br>
	  &nbsp;&nbsp;7.付款人用户号--如果付款人在汇付有账号，请填入此项.
	  <br>
	  <br>
	  </div></td>
	</tr>
</table>
</form>
</body>
</html>
