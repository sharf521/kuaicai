<?
if(!empty($_POST["func"]) && $_POST["func"]=="edit")
{
	require_once("./include/mysql.class.php");
	//require_once("../include/function.php");
	$password="".checkPost(strip_tags($_POST["password"]));
	$password1="".checkPost(strip_tags($_POST["password1"]));
	if(strlen($password)<6 || strlen($password)>18)
	{
		showMsg('���볤���������6��18λ��');		die();
	}
	if($password!=$password1)
	{
		showMsg('������������벻һ�£�');		die();
	}
	$db->query("update {admin} set password='".md5($password.'art')."' where id=$a_id limit 1");
	showMsg('�޸ĳɹ���');
	exit();
}
pageTop('�޸�����');
?>
<div class="div_title"><?=getHeadTitle(array('�޸�����'=>''))?></div>
<br>
<form method="post">
<table cellpadding="4" cellspacing="1">
<tr><td>�� �� ����</td><td><input type="text" name="userid" disabled value="<?=$a_userid?>"/>*</td></tr>
<tr><td>���룺</td><td><input type="password" name="password" />*����������6-18���ַ�</td></tr>
<tr><td>ȷ�����룺</td><td><input type="password" name="password1" />*</td></tr>
<tr><td colspan="2"><input type="hidden" name="func" value="edit"/><input type="hidden" name="act" value="modifypwd">
<input type="submit" value=" �� �� " />&nbsp;&nbsp;<input type="reset" value=" �� �� "/></td></tr>
</table>
</form>
