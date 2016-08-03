<?
require './include/system.class.php';
$system=new system();


if(!empty($_REQUEST["func"]))
{
	$filedir = "data/dbbackup";
	if($_REQUEST["func"]=="revert")
	{
		if (isset($_REQUEST['nameid']))
		{
			$data['nameid'] = $_REQUEST['nameid'];
			$data['filedir'] = $filedir;
			$data['table'] = $_SESSION['dbbackup']['vtable'];
			$result = $system->RevertTables($data);
			if ($result!="")
			{
				$nameid= $data['nameid']+1;
				echo "正在还原（".$result."）表 数据，请不要关闭浏览器。。。";
				$url = "./?act=revertok&func=revert&nameid={$nameid}";
				echo "<script>location.href='{$url}';</script>";
				exit;
			}
			else
			{
				showMsg('还原成功！',"./?act=$act");	
				adminlog('还原数据库',3);
				exit();
			}
		
		}
		elseif (isset($_POST['name']))
		{
			
			$_SESSION['dbbackup']['vtable'] = !isset($_POST['name'])?"":$_POST['name'];
			if ( file_exists(ROOT.'/'.$filedir."/show_table.sql"))
			{
				$sql = file_get_contents(ROOT.'/'.$filedir."/show_table.sql");
				$_sql = explode("\r\n",$sql);
				foreach ($_sql as $val)
				{
					if ($val!="")
					{						
						$db->query($val);
					}
				}
			}
			$url = "./?act=revertok&func=revert&nameid=0";
			echo "<script>location.href='{$url}';</script>";
			exit;
		}
	}
}
pageTop('修改密码');
?>
<div class="div_title"><?=getHeadTitle(array('还原数据库'=>''))?></div>
<br>
<form method="post">
<?
$result = get_file('data/dbbackup',"file");
?>
<table width="100%" border="0" cellspacing="1" bgcolor="#CCCCCC">
	<input type="hidden" name="total" value="{ $total}"/>
	<tr  class="main_td1">
	  <td width="10%" class="main_td"><input type="checkbox" name="allcheck" onclick="selectAll()" checked="checked"/></td>
	  <td class="main_td">还原的文件</td>
      <td class="main_td">修改时间</td>
	</tr>
    <?
    if($result!='')
	{
		foreach($result as $item)
		{
	?>
	<tr >
	  <td  class="main_td1"><input type="checkbox" name="name[]" value="<?=$item?>" <? if($item!='dbbackup.zip'){echo 'checked';}?> /> </td>
	  <td align="left"  class="main_td1">&nbsp;&nbsp;<?=$item?></td>
      <td><?=date('Y-m-d H:i:s',filemtime('data/dbbackup/'.$item));?></td>
	  </tr>
		<?
		}
		?>

	<tr class="main_td1">
	  <td colspan="2" class="submit"> <input type="submit" value="开始还原数据" /></td>
	</tr>
    <?
	}
	else
	{
	?>
	<tr   >
	  <td width="10%" colspan="2" align="left" class="main_td1">&nbsp;&nbsp;<strong>找不到备份文件</strong></td>
	</tr>
	<?
	}
	?>
</table>

<input type="hidden" name="act" value="<?=$act?>">
<input type="hidden" name="func" value="revert"/>
</form>
<script>
function selectAll(){   //全选
	 var m = document.getElementsByName('name[]');
	for ( var i=0; i< m.length ; i++ )
	{
		m[i].checked == true
			? m[i].checked = false
			: m[i].checked = true;
	}
}


</script>