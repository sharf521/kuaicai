<?php
require('./include/moneylog.class.php');
$tclass=new moneylog();
$modulename='会员资金流水';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';


/*if(isset($_GET['date']))
{
	$date=checkPost(strip_tags($_GET['date']));
	$url.='&date='.$date;
}
else
{
	$date=date('Y-m-d');	
	$url.='&date='.$date;
}*/

$date1=checkPost(strip_tags($_GET['date1']));
$date2=checkPost(strip_tags($_GET['date2']));
if(!empty($date1))
{	
	$sqlW.=" and time>='".($date1)."'";
	$url.='&date1='.$date1;
}

if(!empty($date2))
{
	$sqlW.=" and time<='".$date2."'";	
	$url.='&date2='.$date2;
}



if($_GET['money']!='')
{
	$ty1=$_GET['ty1'];
	$money=(float)$_GET['money'];
	$sqlW.=" and $ty1=$money";
	$url.='&ty1='.$ty1."&money=".$money;	
}

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
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and user_id='$user_id'";
	$url.='&user_id='.$user_id;
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


$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}
$arr_zs=array('选择收支','收','支');

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='excel')
	{
		$savename = date("Y-m-j H:i:s");
		$file_type = "vnd.ms-excel";      
		$file_ending = "xls";  
		header("Content-Type: application/$file_type;charset=big5");   
		header("Content-Disposition: attachment; filename=".$savename.".$file_ending");      
		//header("Pragma: no-cache");		  
		$title = "会员资金流水"; 			   
		echo("$title\n");       
		$sep = "\t";   
		$fields=array('用户ID','用户名','资金','冻结资金','积分','冻结积分','操作时间','当前总资金','当前总冻结资金','当前总积分','当前总冻结积分','所属站','类型','收/支','备注');
		foreach($fields as $v)
		{
		  echo $v."\t";	
		}      
		echo ("\n");	
		$result=$tclass->getall(0,1000000,'id desc',$sqlW);	
		foreach($result as $row)
		{		
			$schema_insert = "";
			$schema_insert .= '　'.getuserno($row['user_id']).$sep; 	
			$schema_insert .= '　'.$row["user_name"].$sep; 		  
			$schema_insert .= $row['money'].$sep;
			
			$schema_insert .= $row['money_dj'].$sep;
			$schema_insert .= $row['jifen'].$sep;
			$schema_insert .= $row['jifen_dj'].$sep;
			$schema_insert .= $row['time'].$sep;
			$schema_insert .= $row['dq_money'].$sep;
			
			$schema_insert .= $row['dq_money_dj'].$sep;
			$schema_insert .= $row['dq_jifen'].$sep;
			$schema_insert .= $row['dq_jifen_dj'].$sep;
			
			$schema_insert .= $city[$row['zcity']].$sep;
			$schema_insert .= $arr_type[$row['type']].$sep;
			
			$schema_insert .= $arr_zs[$row['s_and_z']].$sep; 	
			
			$schema_insert .= $row['beizhu'].$sep;			
				  
			  
			$schema_insert = str_replace($sep."$", "", $schema_insert);       
			$schema_insert .= "\t";       
			echo $schema_insert;       
			echo "\n";       
		}            	
		$result=null;
		exit();
	}
}

pageTop($modulename.'管理');




if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>
    <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>
	<div style="margin-bottom:5px;">
	<form method="GET">
    	用户ID：<input type="text" name="user_id" value="<?=$user_id?>" size="8"/>
        用户名：<input type="text" name="user_name" value="<?=$user_name?>" size='8'/>
        <select name="ty1">
        	<option value="money" <? if($ty1=='money'){echo 'selected';}?>>资金</option>
            <option value="money_dj" <? if($ty1=='money_dj'){echo 'selected';}?>>冻结资金</option>
            <option value="jifen" <? if($ty1=='jifen'){echo 'selected';}?>>积分</option>
            <option value="jifen_dj" <? if($ty1=='jifen_dj'){echo 'selected';}?>>冻结积分</option>
        </select>
        <input type="text" name="money" value="<?=$money?>" size="4"/>
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
    	<input id="date1"  name="date1" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'date1'});" value="<?=$date1?>">
        <input id="date2"  name="date2" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'date2'});" value="<?=$date2?>">
        
        <input type="text" name="word" value="<?=$word?>">&nbsp;
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
        <a href="<?=$url?>&func=excel">导出EXCEL</a>
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
		echoTh(array('用户ID','用户名','资金','冻结资金','积分','冻结积分','操作时间','当前总资金','当前总冻结资金','当前总积分','当前总冻结积分','所属站','类型','收/支','备注'));	
		
		foreach ($result as $row)
		{
			$user_id=$row['user_id'];			
		
			?>
			<tr <?=getChangeTr()?>>
            	<td><?=getuserno($user_id)?></td>
            	<td align='left'>&nbsp;&nbsp;<?=$row['user_name']?></td>
            	
                <td align='left'><?=$row['money']?></td>
                <td align='left'><?=$row['money_dj']?></td>
                 <td align='left'><?=$row['jifen']?></td>
                 <td align='left'><?=$row['jifen_dj']?></td>
                 <td align="center"><?=$row['time']?></td>
                 <td align='left'><?=$row['dq_money']?></td>
                 <td align='left'><?=$row['dq_money_dj']?></td>
                 <td align='left'><?=$row['dq_jifen']?></td>
                 <td align='left'><?=$row['dq_jifen_dj']?></td>               
               
                
                <td align="left">&nbsp;&nbsp;<?=$city[$row['zcity']];?></td>
                 <td align='left'><?=$arr_type[$row['type']]?></td>

                <td align='left'>&nbsp;&nbsp;<?=$arr_zs[$row['s_and_z']]?></td>
				
				<td align='left'><?=$row['beizhu']?></td>
				
			</tr>
			<?		
		}
		?>
        <tr>
        <?
        $row=$db->get_one("select sum(money) as money, sum(jifen) jifen,sum(money_dj) money_dj, sum(jifen_dj) jifen_dj from {moneylog} where $sqlW");
		
		
		?>
        <td></td>
        <td>总计：</td><td><?=$row['money']?>
        <td><?=$row['money_dj']?></td></td><td><?=$row['jifen']?></td><td><?=$row['jifen_dj']?></td></tr>
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