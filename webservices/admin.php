<?php
require './include/admin.class.php';
$admin=new admin();
if(isset($_REQUEST['func']))
{
	if($_GET['func']=='change')
	{
		$admin->id=intval($_GET['id']);
		$admin->status(intval($_GET['status']));
	}
	elseif($_POST['func']=='add')
	{
		if($admin->pass($_POST))
		{
			$admin->add($_POST);
		}
		else
		{			
			showMsg($admin->errmsg);exit();	
		}		
	}
	elseif ($_POST['func']=='edit')
	{
		if($admin->pass($_POST))
		{
			$admin->edit($_POST);
		}
		else
		{
			showMsg($admin->errmsg);exit();	
		}
	}
	header('location:?act=admin');
	exit();
}
pageTop('管理员管理');
if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array('管理员管理'=>''))?>&nbsp;&nbsp;<a href="?act=admin&ui=add">添加管理员</a></div>	
	<?	
		$result=$admin->getall();
		?>	
		<table cellpadding="4" cellspacing="1" width="90%" bgcolor="#CCCCCC">
		<tr <?=getChangeTr()?>><td class="th">管理员</td><td class="th">登录帐号</td><td class="th">添加时间</td><td class="th">登录次数</td><td class="th">状态</td><td class="th">操作</td></tr>
		<?		
		foreach ($result as $row)
		{
			$id=$row['id'];
			if($row['status']==1)
			{
				$status='正常';
				$astr="<a onclick='return confirm(\"确定要禁用该帐号吗？\")' href='?act=admin&func=change&status=0&id=$id'>禁用</a>";
			}
			else
			{
				$status='己禁用';	
				$astr="<a href='?act=admin&func=change&status=1&id=$id'>启用</a>";
			}
			?>
			<tr <?=getChangeTr()?>>
				<td align='left'>&nbsp;&nbsp;<?=$row["username"]?></td>
				<td align="center"><?=$row['userid']?></td>
				<td align="center"><?=$row['createdate']?></td>
				<td align="center"><?=$row['times']?> 次</td>
				<td align="center"><?=$status?></td>
				<td align="center"><?=$astr?>&nbsp;<a href="?act=admin&ui=edit&id=<?=$row["id"]?>">编辑</a></td>
			</tr>
			<?		
		}
		echo '</table>';
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST">';
	if(empty($id))
	{
		$arr=array('管理员管理'=>'?act=admin','添加管理员'=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array('管理员管理'=>'?act=admin','编辑管理员'=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		$admin->id=$id;
		$row=$admin->getone();
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="?act=admin">返回管理</a></div>	<br>
		<table border="0" cellpadding="3" cellspacing="1">
			<tr><td>管理员姓名：</td><td><input type="text" name="username" value="<?=$row['username']?>"></td></tr>
            <!--<tr><td>帐号类型：</td><td><? echoTypeRadio($type_admin,$row['typeid']);?></td></tr>-->
			<tr><td>登录帐号：</td><td><input type="text" name="userid" value="<?=$row['userid']?>">*5到18位</td></tr>
			<tr><td>登录密码：</td><td><input type="password" name="password"/>不填为原密码,长度限制在6-18个字符</td></tr>
			<tr><td>确认密码：</td><td><input type="password" name="password1" />*</td></tr>
            <tr><td>权限：</td><td>
            	<?
    function echoPurviewList($pid,$purview='')
	{
		include_once './include/module.class.php';
		$tclass=new module();
		$result=$tclass->getSubCategory($pid);
		$count=count($result);
		$num=1;
		foreach($result as $row)
		{
			$id=$row['id'];
			$name=$row['name'];
			$str='';
			for($i=1;$i<$row['level'];$i++)
			{
				$str.= '&nbsp;　　&nbsp;';	
			}
			if(strpos($purview,$row['id'])===false)
			{
				$chk='';	
			}
			else
			{
				$chk='checked';	
			}
			if($row['level']==1)
			{
				echo "<div><input type='checkbox' name='purview[]' $chk value='$id'/><b>$name</b></div>";	
			}
			else
			{
				echo $str."<input type='checkbox' name='purview[]' $chk value='$id'/>$name";	
			}
			echoPurviewList($row['id'],$purview);
			$num++;

		}
		$result=null;
	}
	echoPurviewList(0,$row['purview']);
				?>
            </td></tr>
			<tr><td colspan="2"><input type="submit" value=" 保 存 ">&nbsp;&nbsp;<input type="button" value=" 返 回 " onclick="window.location='?act=admin'"></td></tr>
		</table>
		<input type="hidden" name="act" value="admin">
		</form>		
		<?
}
?>