<?php
require('./include/accountlog.class.php');
$tclass=new accountlog();
$modulename='总帐户资金流水';
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
	$word=checkPost(strip_tags($_GET['word']));
	$sqlW.=" and beizhu like '%$word%' ";
	$url.='&word='.$word;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['s_and_z']))
{
	$s_and_z=intval($_GET['s_and_z']);
	$sqlW.=" and s_and_z=$s_and_z";
	$url.='&s_and_z='.$s_and_z;
}
if(!empty($_GET['type']))
{
	$type=intval($_GET['type']);
	$sqlW.=" and type=$type";
	$url.='&type='.$type;
}
if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='edit')
	{
		
		
	}
	
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

$arr_zs=array('选择收支','收','支');



if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    
    
    总帐户金额：<?=$_S['canshu']['zong_money']?>
    总帐户积分：<?=$_S['canshu']['zong_jifen']?>
	<div style="margin-bottom:5px;">
	<form method="GET">
    	用户名：<input type="text" name="user_name" value="<?=$user_name?>"/>
    	<select name="s_and_z">
            <?
            foreach($arr_zs as $i=>$v)
			{
				$ch='';
				if($s_and_z==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_zs[$i]?></option>
                <?	
			}
			?>
        </select>
        
        <select name="type">
            <?
            foreach($arr_type as $i=>$v)
			{
				$ch='';
				if($type==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_type[$i]?></option>
                <?	
			}
			?>
        </select>
    	
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?	
	$PageSize = 15;  //每页显示记录数	
	$RecordCount = $tclass->getcount($sqlW);//获取总记录数
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

		$result=$tclass->getall($StartRow,$PageSize,'account_id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('用户名[用户ID]','资金','积分','当前资金','当前积分','操作时间','所属站','类型','收/支','备注'));	
		
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];
			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['user_name']?> [<?=$row['user_id']?>]</td>
            	
                <td align='left'><?=$row['money']?></td>
                <td align='left'><?=$row['jifen']?></td>
                 <td align='left'><?=$row['dq_money']?></td>
                 <td align='left'><?=$row['dq_jifen']?></td>
                
                
               
                <td align="center"><?=$row['time']?></td>
                <td align="left">&nbsp;&nbsp;<?=$city[$row['zcity']];?></td>
                 <td align='left'><?=$arr_type[$row['type']]?>（<?=$row['type']?>）</td>

                <td align='left'>&nbsp;&nbsp;<?=$arr_zs[$row['s_and_z']]?></td>
				
				<td align='left'><?=$row['beizhu']?></td>
				
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
		echo "<div><br>&nbsp;&nbsp;无数据！</div>";
	}
}
else
{

}
?>