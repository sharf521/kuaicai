<?php
require('./include/adminlog.class.php');
$tclass=new adminlog();
$modulename='����Ա������־';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if($_GET['status']!='')
{	
	$status=$_GET['status'];	
	$sqlW.=' and status='.$status;
	$url.='&status='.$status;
}	
if(!empty($_GET['word']))
{
	$word=trim(checkPost(strip_tags($_GET['word'])));
	$sqlW.=" and (remark like '%$word%' or a_name like '%$word%')";
	$url.='&word='.$word;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['type']))
{
	$s_and_z=intval($_GET['type']);
	$sqlW.=" and type=$type";
	$url.='&type='.$type;
}
pageTop($modulename.'����');


if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
	<div style="margin-bottom:5px;">
	<form method="GET">


    	
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //ÿҳ��ʾ��¼��	
	$RecordCount = $tclass->getcount($sqlW);//��ȡ�ܼ�¼��
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

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('����Ա[ID]','��ע','ʱ��'));	
		
		foreach ($result as $row)
		{
			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['a_name']?> [<?=$row['a_id']?>]</td>
            	
  
   
				
				<td align='left'><?=$row['remark']?></td>
                <td align='left'><?=$row['createdate']?></td>
				
			</tr>
			<?		
		}
		?>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;�����ݣ�</div>";
	}
}
else
{
	
}
?>