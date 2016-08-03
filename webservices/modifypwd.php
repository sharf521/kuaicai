<?
if(!empty($_POST["func"]) && $_POST["func"]=="edit")
{
	require_once("./include/mysql.class.php");
	//require_once("../include/function.php");
	$password="".checkPost(strip_tags($_POST["password"]));
	$password1="".checkPost(strip_tags($_POST["password1"]));
	if(strlen($password)<6 || strlen($password)>18)
	{
		showMsg('密码长度请控制在6到18位！');		die();
	}
	if($password!=$password1)
	{
		showMsg('输入的两次密码不一致！');		die();
	}
	$db->query("update {admin} set password='".md5($password.'art')."' where id=$a_id limit 1");
	showMsg('修改成功！');
	exit();
}
pageTop('修改密码');
?>
<div class="div_title"><?=getHeadTitle(array('修改密码'=>''))?></div>
<br>
<form method="post">
<table cellpadding="4" cellspacing="1">
<tr><td>用 户 名：</td><td><input type="text" name="userid" disabled value="<?=$a_userid?>"/>*</td></tr>
<tr><td>密码：</td><td><input type="password" name="password" />*长度限制在6-18个字符</td></tr>
<tr><td>确认密码：</td><td><input type="password" name="password1" />*</td></tr>
<tr><td colspan="2"><input type="hidden" name="func" value="edit"/><input type="hidden" name="act" value="modifypwd">
<input type="submit" value=" 提 交 " />&nbsp;&nbsp;<input type="reset" value=" 重 置 "/></td></tr>
</table>
</form>
