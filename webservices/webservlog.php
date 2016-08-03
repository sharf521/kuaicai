<?php
require('./include/webservlog.class.php');
$tclass=new webservlog();
$modulename='WebServices结算日志';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if($_GET['status']!='')
{	
	$status=$_GET['status'];	
	$sqlW.=' and status='.$status;
	$url.='&status='.$status;
}	

if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and user_id='$user_id'";
	$url.='&user_id='.$user_id;
}

if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}

if(!empty($_GET['starttime']))
{
	$starttime=checkPost(strip_tags($_GET['starttime']));
	$sqlW.=" and date>='".($starttime)."'";
	$url.='&starttime='.$starttime;
}
if(!empty($_GET['endtime']))
{
	$endtime=checkPost(strip_tags($_GET['endtime']));
	$sqlW.=" and date<='".($endtime)."'";
	$url.='&endtime='.$endtime;
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




if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    
        <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>

	<div style="margin-bottom:5px;">
	<form method="GET">
    	用户ID：<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>"/>
        用户名：<input type="text" name="user_name" value="<?=$user_name?>"/>
    	<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
        
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

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('用户ID','用户名','积分','信用额度','扣除日封顶','收入日期','结算时间'));	
		
		$jifen=0;
		$zengjin=0;
		$yujifen=0;
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];
			
			$jifen+=$row['jifen'];
			$zengjin+=$row['zengjin'];
			$yujifen+=$row['yujifen'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['user_name']?></td>
            
                <td align='left'><?=$row['jifen']?></td>
                <td align='left'><?=$row['zengjin']/$_S['canshu']['jifenxianjin']?>元</td>
                 <td align='left'><?=$row['yujifen']?></td>
                 <td align='left'><?=$row['date']?></td>
                
                
               
                <td align="center"><?=$row['createdate']?></td>

                
				
			</tr>
			<?		
		}
		?>
        
        <tr><td></td><td></td><td><?=$jifen?></td><td><?=$zengjin/$_S['canshu']['jifenxianjin']?>元</td><td><?=$yujifen?></td>
        </form>
        </table>
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