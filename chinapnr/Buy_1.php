<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>֧���տ�-��һ��</title>
</head>
<body>
<form name=form action="Buy_2.php" method="post" OnSubmit="true">
<br>
<table align="center" border=1 width=725>
	<th colspan=3 height=27>֧���տ�-������(��һ��)</th>
	<tr><td width=161 height=27>�汾</td><td width=87>Version</td><td width=455 align="left"><input type="text" name="Version" value="10" size="22" maxlength="2"></td></tr>
	<tr><td width=161 height=27>�̻���</td><td>MerId</td><td width=455 align="left"><input type="text" name="MerId" value="871746" size="10"></td></tr>
	<tr><td width=161 height=27>������</td><td>OrdId</td><td><input type="text" name="OrdId" value="<?=date(Ymdhms)?>" size="22" maxlength="20"></td></tr>
	<tr><td width=161 height=27>�������</td><td>OrdAmt</td><td><input type="text" name="OrdAmt" value="1.01" size="22" maxlength="12"></td></tr>
	<tr><td width=161 height=27>��Ʒ���</td><td>Pid</td><td><input type="text" name="Pid" value="" size="6" maxlength="10"></td></tr>
	<tr><td width=161 height=27>ҳ�淵�ص�ַ</td><td>RetUrl</td><td><input type="text" name="RetUrl" value="http://www.imm023.com/chinapnr/Buy_return_url.php" size="60" maxlength="60"></td></tr>
	<tr><td width=161 height=27>��̨���ص�ַ</td><td>BgRetUrl</td><td><input type="text" name="BgRetUrl" value="http://www.imm023.com/chinapnr/Buy_notify_url.php" size="60" maxlength="260"></td></tr>
	<tr><td width=161 height=27>�̻�˽��������</td><td>MerPriv</td><td><input type="text" name="MerPriv" value="" size="60" maxlength="60"></td></tr>
	<tr><td width=161 height=27>���غ�</td><td>GateId</td><td><input type="text" name="GateId" value="" size="4" maxlength="2"></td></tr>
	<tr><td width=161 height=27>�û��ֻ���</td><td>UsrMp</td><td><input name="UsrMp" type="text" id="UsrMp" value="" size="15" maxlength="11"></td></tr>
	<tr><td width=161 height=27>������ϸ</td><td>DivDetails</td><td><input type="text" name="DivDetails" size="60" maxlength="60" value="Agent:000004662788:1.00"></td></tr>
	<tr><td width=161 height=27>�������û���</td><td>PayUsrId</td><td><input type="text" name="PayUsrId" value="" size="40" maxlength="40"></td></tr>
	<tr><td colspan="3" align="center" height=27><input type="submit" name="Submit" value="�ύ"></td></tr>
	<tr>
	  <td colspan="3" align="center" height=27><div align="left">˵��:<br>
	  &nbsp;&nbsp;1.�̻�����������ӻ㸶��õ���λ�̻���.<br>
	  &nbsp;&nbsp;2.�������������վϵͳ�е�Ψһ������ƥ��.<br>
	  &nbsp;&nbsp;3.ҳ�淵�ص�ַ--��֧����ɺ���ת�ع���վϵͳ��ҳ���ַ.<br>
	  &nbsp;&nbsp;4.��̨���ص�ַ--��֧����ɺ��첽���ع���վϵͳ�ĵ�ҳ���ַ.<br>
	  &nbsp;&nbsp;5.���غ�--����������֧��ҳ�潫ֱ����ת�����غ�����Ӧ������֧��ҳ�棨������ͷ���Ա��ȡ���غţ�.<br>
	  &nbsp;&nbsp;6.������ϸ--������һ��֧�����ַ�����ͬ���̻�ʱʹ�ã��޴����������.<br>
	  &nbsp;&nbsp;7.�������û���--����������ڻ㸶���˺ţ����������.
	  <br>
	  <br>
	  </div></td>
	</tr>
</table>
</form>
</body>
</html>
