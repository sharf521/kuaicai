<?
require './include/system.class.php';
$system=new system();



if(!empty($_REQUEST["func"]))
{
	if($_REQUEST['func']=='show')
	{
		$table =$_REQUEST['table'];
		$sql = "show create table $table";
		$result = $db->get_one($sql);
		showMsg($result['Create Table']);exit();
		exit();
	}
	elseif($_REQUEST["func"]=="back")
	{
		$filedir = "data/dbbackup";
		if (isset($_POST['name']))
		{
			$table = $_POST['name'];
			$size = $_POST['size'];
			if ($table=="")
			{
				showMsg('请选择表进行备份！');exit();
			}
			if ($size<50 || $size >2000)
			{
				showMsg('备份大小必须在50k和2000k之间！');exit();
			}
			else
			{
				del_file($filedir);
				mk_dir($filedir);
				$_SESSION['dbbackup']['table'] = $table;
				$_SESSION['dbbackup']['size'] = $size;
				
				$data['table'] = $_SESSION['dbbackup']['table'];
				$data['size'] = $_SESSION['dbbackup']['size'];
				$data['tid'] = isset($_REQUEST['tid'])?$_REQUEST['tid']:0;
			
				$data['limit'] = isset($_REQUEST['limit'])?$_REQUEST['limit']:0;
				$data['filedir'] = $filedir;
				$data['table_page'] = isset($_REQUEST['table_page'])?$_REQUEST['table_page']:0;
	
				$result = $system->BackupTables($data);
				if ($result!="")
				{
					echo "正在备份（".$data['table'][$data['tid']]."）表 第 （{$data['limit']}） 条数据，请不要关闭浏览器。。。";
					$url = "./?act=dbbackup&func=back&tid={$result['tid']}&limit={$result['limit']}&table_page={$result['table_page']}";
					echo "<script>location.href='{$url}';</script>";
					exit;
				}else{
					include_once("./include/pclzip.class.php");
					$archive = new PclZip(ROOT.'/data/dbbackup/dbbackup.zip');
					$v_list = $archive->create('data/dbbackup');
					if ($v_list == 0) 
					{
						die("Error : ".$archive->errorInfo(true));
					}
					showMsg('备份成功！','./?act=dbbackup');	
					adminlog('备份数据库',1);
					exit();
				}
			}
		}
		elseif (isset($_REQUEST['tid']))
		{
			$data['table'] = $_SESSION['dbbackup']['table'];
			$data['size'] = $_SESSION['dbbackup']['size'];
			$data['tid'] = isset($_REQUEST['tid'])?$_REQUEST['tid']:0;
			$data['limit'] = isset($_REQUEST['limit'])?$_REQUEST['limit']:0;
			$data['filedir'] = $filedir;
			$data['table_page'] = isset($_REQUEST['table_page'])?$_REQUEST['table_page']:0;
			$result = $system->BackupTables($data);
			if ($result!="")
			{
				echo "正在备份（".$data['table'][$data['tid']]."）表 第 （{$data['limit']}） 条数据，请不要关闭浏览器。。。";
				$url = "./?act=dbbackup&func=back&tid={$result['tid']}&limit={$result['limit']}&table_page={$result['table_page']}";
				echo "<script>location.href='{$url}';</script>";
				exit;
			}
			else
			{
				include_once("./include/pclzip.class.php");
				$archive = new PclZip(ROOT.'/data/dbbackup/dbbackup.zip');
				$v_list = $archive->create('data/dbbackup');
				if ($v_list == 0) 
				{
					die("Error : ".$archive->errorInfo(true));
				}
				showMsg('备份成功！','./?act=dbbackup');	
				adminlog('备份数据库',1);
				exit();
			}
		}
	}
}
pageTop('修改密码');
?>
<div class="div_title"><?=getHeadTitle(array('备份数据库'=>''))?></div>
<br>
<form method="post">

<table border="0" cellspacing="1" bgcolor="#CCCCCC" width="100%">

	
	<tr   >
	  <td width="10%" class="main_td"><input type="checkbox" name="allcheck" onclick="selectAll()" checked="checked" /></td>
	  <td width="*" class="main_td"><strong>表名</strong></td>
	  <td width="20%" class="main_td"><strong>记录数</strong></td>
	  <td width="28%" class="main_td"><strong>操作</strong></td>
	</tr>

<?
$result=$system->GetSystemTables();
foreach($result as $item)
{
	?>
    <tr  >
	  <td ><input type="checkbox" name="name[]" value="<?=$item['name']?>" checked="checked" /> </td>
	  <td align="left" >&nbsp;&nbsp;<?=$item['name']?></td>
	 
	  <td ><?=$item['num']?></td>
	   <td ><a href="./?act=<?=$act?>&func=show&table=<?=$item['name']?>">查看结构</a></td>
	</tr>
    <?	
}
?>

	<tr   >
	  <td colspan="4"  class="submit">&nbsp;&nbsp;<strong>备份操作</strong>  当前数据库版本 ：<?=mysql_get_server_info()?> 分卷大小：<input type="text" size="10" value="1024" name="size"/> K  <input type="submit" value="开始备份" /> <input type="button" onclick="location.href='data/dbbackup/dbbackup.zip'" value="下载到本地" /><input type="hidden" name="total" value="<?=count($result)?>" /></td>
	</tr>

</table>

<input type="hidden" name="act" value="<?=$act?>">
<input type="hidden" name="func" value="back"/>
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