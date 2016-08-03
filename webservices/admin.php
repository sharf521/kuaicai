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
pageTop('����Ա����');
if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array('����Ա����'=>''))?>&nbsp;&nbsp;<a href="?act=admin&ui=add">��ӹ���Ա</a></div>	
	<?	
		$result=$admin->getall();
		?>	
		<table cellpadding="4" cellspacing="1" width="90%" bgcolor="#CCCCCC">
		<tr <?=getChangeTr()?>><td class="th">����Ա</td><td class="th">��¼�ʺ�</td><td class="th">���ʱ��</td><td class="th">��¼����</td><td class="th">״̬</td><td class="th">����</td></tr>
		<?		
		foreach ($result as $row)
		{
			$id=$row['id'];
			if($row['status']==1)
			{
				$status='����';
				$astr="<a onclick='return confirm(\"ȷ��Ҫ���ø��ʺ���\")' href='?act=admin&func=change&status=0&id=$id'>����</a>";
			}
			else
			{
				$status='������';	
				$astr="<a href='?act=admin&func=change&status=1&id=$id'>����</a>";
			}
			?>
			<tr <?=getChangeTr()?>>
				<td align='left'>&nbsp;&nbsp;<?=$row["username"]?></td>
				<td align="center"><?=$row['userid']?></td>
				<td align="center"><?=$row['createdate']?></td>
				<td align="center"><?=$row['times']?> ��</td>
				<td align="center"><?=$status?></td>
				<td align="center"><?=$astr?>&nbsp;<a href="?act=admin&ui=edit&id=<?=$row["id"]?>">�༭</a></td>
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
		$arr=array('����Ա����'=>'?act=admin','��ӹ���Ա'=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array('����Ա����'=>'?act=admin','�༭����Ա'=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		$admin->id=$id;
		$row=$admin->getone();
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="?act=admin">���ع���</a></div>	<br>
		<table border="0" cellpadding="3" cellspacing="1">
			<tr><td>����Ա������</td><td><input type="text" name="username" value="<?=$row['username']?>"></td></tr>
            <!--<tr><td>�ʺ����ͣ�</td><td><? echoTypeRadio($type_admin,$row['typeid']);?></td></tr>-->
			<tr><td>��¼�ʺţ�</td><td><input type="text" name="userid" value="<?=$row['userid']?>">*5��18λ</td></tr>
			<tr><td>��¼���룺</td><td><input type="password" name="password"/>����Ϊԭ����,����������6-18���ַ�</td></tr>
			<tr><td>ȷ�����룺</td><td><input type="password" name="password1" />*</td></tr>
            <tr><td>Ȩ�ޣ�</td><td>
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
				$str.= '&nbsp;����&nbsp;';	
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
			<tr><td colspan="2"><input type="submit" value=" �� �� ">&nbsp;&nbsp;<input type="button" value=" �� �� " onclick="window.location='?act=admin'"></td></tr>
		</table>
		<input type="hidden" name="act" value="admin">
		</form>		
		<?
}
?>