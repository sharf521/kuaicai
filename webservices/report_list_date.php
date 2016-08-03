<?php
require('./include/process.class.php');
$tclass=new process();
$modulename='WebService收益结算';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if($_GET['status']!='')
{	
	$status=$_GET['status'];	
	$sqlW.=' and status='.$status;
	$url.='&status='.$status;
}	

if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and b.city='$c'";
	$url.='&c='.$c;
}
$starttime=checkPost(strip_tags($_GET['starttime']));
	if(empty($starttime)) $starttime=date('Y-m-d',strtotime(date('Y-m-d'))-3600*24);
	$sqlW.=" and a.IncomeTime>='".($starttime)."'";
	$url.='&starttime='.$starttime;
	
$endtime=checkPost(strip_tags($_GET['endtime']));
if(!empty($endtime))
{
	$sqlW.=" and a.IncomeTime<='".$endtime." 23:59:59'";
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

$arr_status=array('未结算','己结算');

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
    <script language="javascript" src="include/js/jquery.js"></script>
    <script language="javascript">
		function jiesuan(date)
		{
			if(window.confirm('确定要结算【'+date+'】的收益吗？'))
			{
				$.post("ajax.php?func=jiesuan",{date:date},function(result){
					alert(result);
				});	
			}
		}
		function vip_up()
		{
			$.post("ajax.php?func=vip_up",{},function(result){
					alert(result);
				});	
		}
    </script>

	<div style="margin-bottom:5px;">
	<form method="GET">   
    
        
       <!-- <select name="c">
        <option value="">选择分站</option>
        <?
        foreach($city as $i=>$k)
		{
			$ch=($c==$i)?'selected':'';
			echo "<option value='$i' $ch>$k</option>";
		}
		?>        
        </select>-->

    	开始：<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        结束：<input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
        
        <input type="button" value="更新vip等级" onclick="vip_up();"/>
     </form>


    </div>
	<?	

	{	
		
		
		
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		
		$sql="select substring(IncomeTime,1,10) as date,sum(a.Mony) as Mony,a.status from {process} a where $sqlW group by date";
	

		$result=$db->get_all($sql);	


		
		echoTh(array('时间','结算积分','状态','操作'));	
		
		foreach($result as $row)
		{			
			
			$status=$row['status'];
			?>
		   <tr <?=getChangeTr()?>>
			   
				<td align='left'>&nbsp;&nbsp;<?=$row['date']?></td>            	
				<td align='left'><?=$row['Mony']?></td>                                 
				<td align='left'><?=$arr_status[$row['status']]?></td>  
				<td align="center">
				<? 
					if($status==0 && $row['date']<=date('Y-m-d'))
					{
						echo "<a href=\"javascript:jiesuan('".$row['date']."')\">结算</a>";
					}?></td>   
			</tr>
			<?	
		
		}
		$result=null;
		
		
		?>
        </form></table>
		
		<?php
	}

}

?>