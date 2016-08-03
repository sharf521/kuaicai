<?php

require '../include/kucun.class.php';
$url="?act=$act&page=$page";
$sqlW='a.status>-1';
$tclass=new kucun();
$code=intval($_REQUEST['code']);
if(isset($_REQUEST['func']))
{	
	if ($_GET['func']=='del')
	{
		$tclass->id=intval($_GET['id']);
		$tclass->delete();	
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
pageTop('类别管理');


if(empty($_GET['ui']))
{
	$arr1['库存管理']='';
?>
	<div class="div_title"><?=getHeadTitle($arr1)?>&nbsp;&nbsp;
	<?
		if(!empty($code))
		{
			echo "<a href='$url'>返回</a>";
		}
	?>
	&nbsp;&nbsp;<a href="<?=$url?>&ui=add">添加</a></div>	
    
	<?			
		$PageSize = 15;  //每页显示记录数
	//$strWhere1="?act=keyword&typeid=$typeid";
	//$RecordCount = $tclass->getcount();//获取总记录数
	$row=$db->get_one("select count(id) as count from kucun a where $sqlW");
	$RecordCount = $row['count'];//获取总记录数
	if(!empty($page))
	{
		$StartRow=($page-1)*$PageSize;
	}
	else
	{
		$StartRow=0;
		$page=1;
	}
	if($RecordCount>0)
	{
		//$result=$tclass->getall($StartRow,$PageSize,'id desc','');
		$result=$db->get_all("select a.*,b.name,b.company,b.tel from kucun a left join customer b on a.customerid=b.id where $sqlW order by a.status desc limit $StartRow,$PageSize");
		?>	
		<form method="POST">
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<?	
		echoTh(array('ID','姓名','公司','电话','纸','尺寸','数量','时间','状态','操作'));	
		$arr_type=array('无库存','有库存');
		foreach ($result as $row)
		{
			$id=$row['id'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align="center"><?=$row['id']?></td>
				<td align="left">&nbsp;&nbsp;<?=$row['name']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row["company"]?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row["tel"]?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row["ptype"]?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row["sizex"]?> * <?=$row['sizey']?> mm</td>
                <td align='left'>&nbsp;&nbsp;<?=$row["num"]?></td>
				<td align='left'>&nbsp;&nbsp;<?=$row["createdate"]?></td>
                <td align='left'>&nbsp;&nbsp;<?=$arr_type[$row["status"]]?></td>
			
				<td align="center">

                
               
                <a href="<?=$url?>&ui=edit&id=<?=$row["id"]?>">编辑</a> &nbsp;
                <a onclick="return confirm('确定要删除该分类吗？')" href='<?=$url?>&func=del&id=<?=$id?>'>删除</a></td>
			</tr>
			<?		
		}
		?>
			<input type="hidden" name="func" value="setorder">
			
            </table>
            <div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
			<?
	}
	else
		{
			echo "<div>无分类！</div>";
		}
		?></form>
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
	}
	else
	{
		$arr1['编辑'.$modulename]='';
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";
		$tclass->id=$id;
		$row=$db->get_one("select a.*,b.name,b.company,b.tel from kucun a left join customer b on a.customerid=b.id where a.id=$id limit 1");
		
	}
		?>
		<div class="div_title"><?=getHeadTitle($arr1)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>
        <script language="javascript" src="../include/js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="../include/js/artDialog5.0/skins/default.css" />
<script type="text/javascript" src="../include/js/artDialog5.0/artDialog.min.js"></script><script type="text/javascript" src="../include/js/artDialog5.0/artDialog.plugins.min.js"></script>	<br>
      <table border="0" cellpadding="3" cellspacing="1">
          <tr><td>客户：</td><td>
          <input type="text" name="customername" id="customername" value="<?=$row['name']?>" onClick="customer()" readonly>
          <input type="hidden" name="customerid" id="customerid" value="<?=$row['customerid']?>"><span id="customertel"></span></td></tr>
          
          <tr><td>纸张类型：</td><td><input type="text" name="ptype" id="ptype" value="<?=$row['ptype']?>" onClick="paper()"></td></tr>
          <tr><td>张数：</td><td><input type="text" name="num" class="w400" value="<?=$row['num']?>"></td></tr>
          <tr><td>尺寸：</td><td><input type="text" name="sizex" value="<?=$row['sizex']?>"> * <input type="text" name="sizey" value="<?=$row['sizey']?>"> mm</td></tr>
          <tr><td>态状</td><td><input type="radio" name="status" value="1" checked="checked"/>有库存 
          <input type="radio" name="status" value="0" <? if($row['status']=='0'){echo 'checked="checked"';}?> />无库存
          </td></tr>
          <tr><td>备注：</td><td><textarea name="remark" rows="5" cols="50"><?=$row['remark']?></textarea></td></tr>
         
          <tr><td colspan="2"><input type="submit" value=" 保 存 ">&nbsp;&nbsp;<input type="button" value=" 返 回 " onclick="window.location='<?=$url?>'"></td></tr>
      </table>
      </form>		
		<?
}
?>
<style type="text/css">
body,ul,li{padding:0px; margin:0px; list-style:none}
.list{ width:500px}
.list li{float:left;}
.list li a{display:block; width:100px; overflow:hidden; line-height:30px}
</style>

  <div id="paper_dialog" style="display:none;">
		<ul class="list">
        	<?
            	foreach($_G['linkpage']['paper'] as $v)
				{
					?>
                    <li><a href="javascript:selpaper('<?=$v?>')"><?=$v?></a></li>
					<?	
				}
			?>
        </ul>
  </div>  

<script language="javascript">
var dia;
function customer()
{
	dia=art.dialog({
		id:'d_cu',
		title:'选择客户',
		content:'<iframe src="./?act=customer_win" width="500" height="330"  frameborder="0" marginheight="0" marginwidth="0"></iframe>',
		lock:true
	});
}
function c_close()
{
	dia.close();	
}
function paper()
{
	dia=art.dialog({ 
		id:'login',
		title:'选择纸张类型',
		content:$('#paper_dialog').html(),
		lock:true
	});
}
function selpaper(v)
{
	$('#ptype').val(v);	
	c_close();
}
</script>