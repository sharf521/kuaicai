<?php
$modulename='商城日报表';	
$date=checkPost(strip_tags($_GET['date']));
if(empty($date)) 	$date=date('Y-m-d');
$sqlW="time>='".($date)."' and time<='".$date." 23:59:59'";


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
if(!empty($_GET['type']))
{
	$type=intval($_GET['type']);
	$sqlW.=" and type=$type";
	$url.='&type='.$type;
}
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and zcity='$c'";
	$url.='&c='.$c;
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
    
        会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
    	用户名：<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
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

    	<input id="date"  name="date" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'date'});" value="<?=$date?>">
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
     </form>


    </div>
	<?
	{		
		$sql="select sum(money) as money, sum(jifen) jifen,sum(money_dj) money_dj, sum(jifen_dj) jifen_dj, user_name ,user_id , zcity ,type , s_and_z  from {moneylog} where $sqlW group by user_id";
		$result=$db->get_all($sql);	
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('用户ID','用户名','金额','积分','冻结金额','冻结积分','时间','分站'));	
		$money_sum=0;
		$money_dj_sum=0;
		$jifen_sum=0;
		$jifen_dj_sum=0;
		foreach ($result as $row)
		{			
			$user_id=$row['user_id'];
			$user_name=$row['user_name'];
			
			$money_sum+=$row['money'];
			$money_dj_sum+=$row['money_dj'];
			$jifen_sum+=$row['jifen'];
			$jifen_dj_sum+=$row['jifen_dj'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<? if(!empty($user_id)){echo getuserno($user_id);}?></td>
                <td align='left'>&nbsp;&nbsp;<?=$user_name?></td>            	
                <td align='left'><?=$row['money']?></td>  
                <td align='left'><?=$row['jifen']?></td>  
                <td align='left'><?=$row['money_dj']?></td>  
                <td align='left'><?=$row['jifen_dj']?></td>                                    
                <td align='center'><?=$date?> </td>  
              
                <td align="center"><?=$city[$row['zcity']];?></td>
			</tr>
			<?
		}
		?>
        <tr <?=getChangeTr()?>>
            	<td align='left' colspan="2"></td>            	
                <td align='left'><?=$money_sum?></td>  
                 
                <td align='left'><?=$jifen_sum?></td> 
                 <td align='left'><?=$money_dj_sum?></td> 
                <td align='left'><?=$jifen_dj_sum?></td>                                    
                <td align='center' colspan="2"></td>
			</tr>
        </form></table>
       	<div><?=$money_sum?></div>
		
		<?php
	}

}

?>