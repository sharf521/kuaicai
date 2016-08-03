<?php
require('./include/process.class.php');
$tclass=new process();
$modulename='WebService收益汇总报表';
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
	$sqlW.=" and log_text like '%$word%' ";
	$url.='&word='.$word;
}
if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and b.user_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and b.user_id='$user_id'";
	$url.='&user_id='.$user_id;
}
if(!empty($_GET['fid']))
{
	$fid=intval($_GET['fid']);
	$row1=$db->get_one("select web_id from {member} where user_id='$fid' limit 1");	
	$sqlW.=" and a.FromUserID='".$row1['web_id']."'";
	$row1=null;
	$url.='&fid='.$fid;
}
if(!empty($_GET['real_name']))
{
	$real_name=trim(checkPost(strip_tags($_GET['real_name'])));
	$sqlW.=" and b.real_name like '%$real_name%'";
	$url.='&real_name='.$real_name;
}
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and b.city='$c'";
	$url.='&c='.$c;
}
$starttime=checkPost(strip_tags($_GET['starttime']));
	if(empty($starttime))
	{
		$starttime=date('Y-m-d',strtotime(date('Y-m-d'))-3600*24);
	}
	$sqlW.=" and a.IncomeTime>='".($starttime)."' and a.IncomeTime<='".$starttime." 23:59:59'";
	$url.='&starttime='.$starttime;
	
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

$arr_status=array('未结算','己结算');

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>

	<div style="margin-bottom:5px;">
	<form method="GET">   
    
        会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
    	用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        <select name="c">
        <option value="">选择分站</option>
        <?
        foreach($city as $i=>$k)
		{
			$ch=($c==$i)?'selected':'';
			echo "<option value='$i' $ch>$k</option>";
		}
		?>        
        </select>

    	<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
     </form>


    </div>
	<?	

	{		
		$sql="select a.*,b.*,sum(a.Mony) as Mony from {process} a left join {member} b on a.UserID=b.web_id where $sqlW group by a.UserID";
		//echo  $sql;

		$result=$db->get_all($sql);	
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('用户ID','用户名','收入','时间','分站','状态'));	
		$money_sum=0;
		foreach ($result as $row)
		{			
			$user_id=$row['user_id'];
			$user_name=$row['user_name'];
			$money=$row['Mony'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<? if(!empty($user_id)){echo getuserno($user_id);}?></td>
                <td align='left'>&nbsp;&nbsp;<?=$user_name?></td>            	
                <td align='left'><?=$money?></td>                                 
                <td align='center'><?=$starttime?> </td>  
              
                <td align="center"><?=$city[$row['city']];?></td>
                <td align='center'><?=$arr_status[$row['status']]?></td> 
    
			</tr>
			<?	
			$money_sum=$money_sum+$money;	
		}
		?>
        </form></table>
       	<div><?=$money_sum?></div>
		
		<?php
	}

}

?>