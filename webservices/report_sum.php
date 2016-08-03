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
if(!empty($_GET['starttime']))
{
	$starttime=checkPost(strip_tags($_GET['starttime']));
	$sqlW.=" and a.IncomeTime>='".($starttime)."'";
	$url.='&starttime='.$starttime;
}
if(!empty($_GET['endtime']))
{
	$endtime=checkPost(strip_tags($_GET['endtime']));
	$sqlW.=" and a.IncomeTime<='".($endtime)."'";
	$url.='&endtime='.$endtime;
}


if(!empty($_GET['Aside2']))
{
	$Aside2=intval($_GET['Aside2']);
	$sqlW.=" and a.Aside2=$Aside2";
	$url.='&Aside2='.$Aside2;
}
if(!empty($_GET['Aside1']))
{
	$Aside1=intval($_GET['Aside1']);
	$sqlW.=" and a.Aside1=$Aside1";

	$url.='&Aside1='.$Aside1;
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
$arr_Aside1=array('请选择','平台收入 5%  分红金额','天天返利','排队返利','Fbb过程数据','Z100过程数据','借贷平台过程数据');
$arr_Aside2=array(
	0=>'请选择',
	1=>'Fbb 用户入盘金额',
	2=>'Fbb 用户收益金额',
	3=>'Fbb 用户消费或提现金额',
	4=>'卓100 用户静态入盘金额',
	5=>'卓100 用户静态收益金额',
	6=>'卓100 用户静态收益消费或提现',
	7=>'卓100 用户动态入盘金额',
	8=>'返利商城用户消费金额',
	9=>'返利商城用户返利金额',
	10=>'卓100 用户动态收益金额',
	11=>'卓100 用户动态收益消费或提现',
	12=>'借贷平台注册资金',
	13=>'借贷平台收益金额',
	14=>'借贷平台25天计划',
	15=>'vip 买入'
);


$arr_tty=array();

$arr_tty[1]=array('请选择','120 平台分红','160 平台分红','两种都有分红');
$arr_tty[2]=array('请选择','160 天天返','两种都有天天返');
$arr_tty[3]=array('请选择','120 排队','160 排队','两种都有排队');
$arr_tty[4]=array('请选择','FBB1--15层奖励','FBB推荐奖励','FBB核定点数据');
$arr_tty[5]=array('请选择','静态计算数据','推荐奖励','达标奖励','1--45期','1---50期','核定点数据');
$arr_tty[6]=array('请选择','六保数据','滑落数据','25天计划数据','增进数据','核定点数据');
echo "<script>";
echo 'var arr_tty = new Array();';
foreach($arr_tty as $i=>$v)
{	
	$str=implode("','",$v);
	echo "arr_tty[$i]=new Array('$str');\r\n";
}
echo "</script>";



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
        来源ID：<input type="text" name="fid" value="<? if(!empty($fid)){echo getuserno($fid);}?>" size="8"/>
    	<select name="Aside1" onchange="selAside2(this.value)">
            <?
            foreach($arr_Aside1 as $i=>$v)
			{
				$ch='';
				if($Aside1==$i)  $ch='selected'; 
				?>
                <option value="<?=$i?>" <?=$ch?>><?=$arr_Aside1[$i]?></option>
                <?	
			}
			?>
        </select>
        <select id='Aside2' name="Aside2"></select>

    	<input id="starttime"  name="starttime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'starttime'});" value="<?=$starttime?>">
        <input id="endtime"  name="endtime" type="text"  class="Wdate" onclick="javascript:WdatePicker({el:'endtime'});" value="<?=$endtime?>">
        
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">   
     </form>
    </div>
    
    <script language="javascript" src="include/js/jquery.js"></script>
    <script language="javascript">
    	function selAside2(val)
		{
			sel=document.getElementById('Aside2');
			if(val!='0')
			{		
			    sel.options.length=0;		
				for(v in arr_tty[val])
				{
					sel.options.add(new Option(arr_tty[val][v],v));				
				}
			}
			else
			{
				sel.options.length=0;	
			}
		}
		<?
		if(!empty($Aside1))
		{
			echo "selAside2($Aside1);";	
			if(!empty($Aside2))
			{
				echo "document.getElementById('Aside2').value=$Aside2;";	
			}
		}
		?>
    	
    </script>
    
	<?	
	{		
		
		$sql="select a.*,b.*,sum(a.Mony) as Mony from {process} a join {member} b on a.UserID=b.web_id where $sqlW group by a.Aside1,a.Aside2";
		echo "<!--". $sql.'-->';
		//exit();
		$result=$db->get_all($sql);	
		
		/*
		Aside3  商城消费的 资金ID
Aside2 为 子类型

		*/
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('用户ID','用户名','收入','类型','子类','时间','状态','分站'));	
		
		$money_sum=0;
		foreach ($result as $row)
		{
			$money_sum+=$row['Mony'];
			$Aside1=$row['Aside1'];
			$Aside2=$row['Aside2'];
			if($Aside1<4)
			{
				$money=	$row['Mony'].'积分';
			}
			else
			{
				$money=$row['Mony']/$_S['canshu']['jifenxianjin'].'元';	
			}
		
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<? if(!empty($user_id)){echo getuserno($user_id);}?></td>
                <td align='left'>&nbsp;&nbsp;<?=$user_name?></td>            	
                <td align='left'><?=$money?></td>                
                <td align='left'><?=$arr_Aside1[$Aside1]?></td>      
                <td align='left'><?=$arr_tty[$Aside1][$Aside2]?>(<?=$row['Aside1']?>.<?=$row['Aside2']?>,<?=$row['Aside3']?>)</td>           
                <td align='center'><?=$starttime?> 至 <?=$endtime?></td>  
                <td align='center'><?=$arr_status[$row['status']]?></td>                
                <td align="center"><?=$city[$c];?></td>
			</tr>
			<?		
		}
		?>
        </form></table>
        <?=$money_sum?>积分（<?=$money_sum/$_S['canshu']['jifenxianjin']?>元）。
		<?php
		
	}

}

?>