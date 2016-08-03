<?php
require './include/module.class.php';
$typeid=intval($_GET['typeid']);
$modulename='模块';
$url="?act=$act&typeid=$typeid";
$tclass=new module();
$parentid=intval($_REQUEST['parentid']);
if(isset($_REQUEST['func']))
{	
	if ($_GET['func']=='del')
	{
		$tclass->id=intval($_GET['id']);
		$tclass->fields=array('id');
		$result=$tclass->getSubCategory($tclass->id);
		if($result)
		{
			showMsg('存在下级分类，先删除子分类！'); exit();
		}
		else 
		{
			$tclass->delete();	
		}
	}
	elseif($_POST['func']=='setorder')
	{		
		$tclass->setorder($_POST['showorder']);	
	}
	else
	{
		if($tclass->pass($_POST))
		{
			if($_POST['func']=='add')
				$tclass->add($_POST);	
			elseif($_POST['func']=='edit')
				$tclass->edit($_POST);								
		}
		else
		{
			showMsg($tclass->errmsg);exit();	
		}
	}
	header("location:$url");
	exit();
}
pageTop('管理');
$tclass->id=$parentid;
$row=$tclass->getone();//获取上级分类的parent,level
$parentpath=$row["path"];
if($parentpath=='') $parentpath='0,';
$level=$row["level"]+1;
$parentfullpath=$row["fullpath"];
$row=null;
$arr1=array();
$arr1[$modulename]=$url;
/*if($parentpath!='0,')
{		
	$arr_ids=explode(",",$parentpath);
	array_shift($arr_ids);//除移第一个元素0
	array_pop($arr_ids);
	foreach($arr_ids as $id)
	{		
		$tclass->id=$id;
		$tclass->fields=array('id','name');
		$row=$tclass->getone();
		$arr1[$row['name']]=$url.'&parentid='.$row['id'];
		$row=null;
	}		
}*/
if(empty($_GET['ui']))
{
	$arr1['当前分类']='';
?>
	<div class="div_title"><?=getHeadTitle($arr1)?>&nbsp;&nbsp;
	<?
		if($parentpath!='0,')
		{
			echo "<a href='$url'>返回</a>";
		}
	?>
	&nbsp;&nbsp;<a href="<?=$url?>&ui=add&parentid=<?=$parentid?>">添加<?=$modulename?></a></div>	
	<?			
		//$tclass->fields=array('id','name','code','remark','parentid','path','fullpath','level','template','purview','link','showorder');

		?>	
		<form method="POST">
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<?	
		
	function echoTrList($pid,$id=0,$path='')
	{
		global $tclass,$url;
		$result=$tclass->getSubCategory($pid);
		$count=count($result);
		$num=1;
		foreach($result as $row)
		{
			$str='';
			for($i=1;$i<$row['level'];$i++)
			{
				$str.= '&nbsp;　　&nbsp;';	
			}		
			if($row['level']==1)
				$name=$row['name'];
			else
			{
					$name=$str.$row['name'];
			}	
			$sel=$row['id']	 ==$id	?'selected':'';
			
			?>
            <tr <?=getChangeTr()?>>            
				<td align="left">&nbsp;&nbsp;<?=$str?><input type="text" size="2" name="showorder[<?=$row['id']?>]" value="<?=$row['showorder']?>">
                
                <?=$row["name"]?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row["code"]?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row["file"]?></td>
	
       
				<td align="center"> 
                <a href="<?=$url?>&ui=add&parentid=<?=$row['id']?>">添加子类</a>&nbsp;
                <a href="<?=$url?>&ui=edit&parentid=<?=$parentid?>&id=<?=$row["id"]?>">编辑</a> &nbsp;
                <a onclick="return confirm('确定要删除该分类吗？')" href='<?=$url?>&func=del&id=<?=$row["id"]?>&parentid=<?=$pid?>'>删除</a></td>
			</tr>
            <?
			echoTrList($row['id'],$id,$path);
			$num++;
		}
		$result=null;
	}
		
		
		echoTh(array('名称','code','file','操作'));	
		echoTrList(0);
		?>
			<input type="hidden" name="func" value="setorder">
			<tr><td colspan="7"  bgcolor='#ffffff'>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="更新排序"></td></tr>
</table></form>
        <?
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST">';
	if(empty($id))
	{
		$arr1['添加'.$modulename]='';
		echo '<input type="hidden" name="func" value="add">';
		echo "<input type='hidden' name='typeid' value='$typeid'>";
		echo "<input type='hidden' name='level' value='$level'>";
		echo "<input type='hidden' name='parentid' value='$parentid'>";
		echo "<input type='hidden' name='parentpath' value='$parentpath'>";
		echo "<input type='hidden' name='parentfullpath' value='$parentfullpath'>";
	}
	else
	{
		$arr1['编辑'.$modulename]='';
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		$tclass->id=$id;
		
		$row=$tclass->getone();
		echo "<input type='hidden' name='oldname' value='$row[name]'>";
		echo "<input type='hidden' name='path1' value='$row[path]'>";//更新子fullpath要用
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr1)?>&nbsp;&nbsp;<a href="<?=$url?>&parentid=<?=$parentid?>">返回管理</a></div>	<br>
      <table border="0" cellpadding="3" cellspacing="1">
      

      
          <tr><td>名称：</td><td><input type="text" name="name" class="w400" value="<?=$row['name']?>"></td></tr>
          <tr><td>code：</td><td><input type="text" name="code" class="w400" value="<?=$row['code']?>"></td></tr>
          <tr><td>file：</td><td><input type="text" name="file" class="w400" value="<?=$row['file']?>"></td></tr>
          
          
          <tr><td>模板：</td><td><input type="text" name="template" class="w400" value="<?=$row['template']?>"></td></tr>
          <tr><td>权限：</td><td>
          <textarea name="purview" class="w400" rows="6"><?=$row['purview']?></textarea> </td></tr>
         
          <tr><td>跳转页面：</td><td><input type="text" name="link" class="w400" value="<?=$row['link']?>"></td></tr>		
          
          <tr><td>描述：</td><td><textarea name="remark" class="w400" rows="6"><?=$row['remark']?></textarea></td></tr>	
          <tr><td colspan="2"><input type="submit" value=" 保 存 ">&nbsp;&nbsp;<input type="button" value=" 返 回 " onclick="window.location='<?=$url?>&parentid=0'">
          <input type="hidden" name="act" value="<?=$act?>" />
          </td></tr>
      </table>
      </form>		
		<?
}


?>
