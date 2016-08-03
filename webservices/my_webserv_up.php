<?php
$modulename='用户升级套餐';
$page=intval($_GET['page']);
$user_id=intval($_REQUEST['user_id']);
$type=intval($_REQUEST['type']);
$s=intval($_REQUEST['s']);
require './include/my_webserv.class.php';
$my_webserv=new my_webserv();
require('./include/member.class.php');
$member=new member();
if($type==1)
{
	$modulename=$s==1?'升级FBB':'购买FBB';	
}

$row=$db->get_one("select web_id,user_name,tuijianid,lishuid from {member} where user_id=$user_id limit 1");
$web_id=$row['web_id'];
$user_name=$row['user_name'];

$tuijianid=(int)$row['tuijianid'];
$lishuid=(int)$row['lishuid'];
$row=null;

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='update')
	{
		
		$row=$db->get_one("select * from {my_money} where user_id=$user_id limit 1");
		$user_money=$row['money'];//用户资金
		$user_moneydj=$row['money_dj'];
		$user_duihuanjifen=$row['duihuanjifen'];
		$user_dongjiejifen=$row['dongjiejifen'];
		$user_suoding_money=$row['suoding_money'];//用户锁定金额
		$user_dongjiejifen=$row['dongjiejifen'];
		$zcity=$row['city'];
		$money=$_POST['money'];
		$new_money=$user_money-$money;
		$riqi=date('Y-m-d H:i:s');
		$canshu=$db->get_one("select zong_money,zong_jifen from {canshu}");
		$zong_money=$canshu['zong_money'];
		$zong_jifen=$canshu['zong_jifen'];
		$new_zong_money=$zong_money+$money;
		$keyong_money=$user_money-$user_suoding_money;
		if($money>$keyong_money)
		{
			showMsg('您的可用余额不足，不能购买');
			exit();	
		}
		if($type==1)//FBB
		{
			if($s==0)//购买
			{				
				if($_POST['fbb']==1)//小fbb
				{					
					$fbb_s=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>2000));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=2000,fbb_s='$fbb_s' where user_id=$user_id limit 1");
					adminlog("$user_name[$user_id]购买小FBB");
					$beizhu="$user_name[$user_id]用$money元购买小FBB";
					$beizhu1="收取用户$user_name[$user_id]的购买小FBB$money元";
					
				}
				else//大fbb
				{
					$fbb_s=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>20000));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=20000,fbb_s='$fbb_s' where user_id=$user_id limit 1");
					adminlog("$user_name[$user_id]购买大FBB");
					$beizhu="$user_name[$user_id]用$money购买大FBB";
					$beizhu1="收取用户$user_name[$user_id]的购买大FBB$money元";
					
				}
			}
			else//升级大fbb
			{
				$fbb_s=webService('Fbb_Update_Regist_Money',array("ID"=>$web_id));
				$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=20000 where user_id=$user_id limit 1");
				adminlog("$user_name[$user_id]升级大FBB");
				$beizhu="$user_name[$user_id]用$money升级大FBB";
				$beizhu1="收取用户$user_name[$user_id]的升级大FBB$money元";
			}
	
		}
		else if($type==2)
		{
			$zmonth=$_POST['zmonth'];
			if($s==0)
			{				
				if($_POST['z100']==1)
				{
					
					$zhuo_s=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>2000,"Month"=>$zmonth));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,zhuo=2000,zhuo_s='$zhuo_s' where user_id=$user_id limit 1");
					$beizhu="$user_name[$user_id]用$money购买小卓";
				    $beizhu1="收取用户$user_name[$user_id]的购买小卓：$money元";
				}
				else
				{
					$zhuo_s=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>20000,"Month"=>$zmonth));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,zhuo=20000,zhuo_s='$zhuo_s' where user_id=$user_id limit 1");
					$beizhu="$user_name[$user_id]用$money购买大卓";
				    $beizhu1="收取用户$user_name[$user_id]的购买大卓：$money元";
				}
			}
			else
			{
				$zhuo_s=webService('Z_Static_Update_Regist',array("ID"=>$web_id,"Month"=>$zmonth));
				$db->query("update {my_webserv} set paymoney=paymoney+$money,zhuo=20000,zhuo_s='$zhuo_s' where user_id=$user_id limit 1");
				$beizhu="$user_name[$user_id]用$money升级大卓";
				$beizhu1="收取用户$user_name[$user_id]的升级大卓：$money元";
			}	
			
			
			
		}
		else if($type==3)
		{
			$lishuid=$_POST['lishuid'];
			if(empty($lishuid))
			{
				showMsg('隶属人不能为空！');exit();		
			}
			$result=$db->get_one("select count(*) as count from {my_webserv} a join {member} b on a.user_id=b.user_id where a.status=1 and b.lishuid='$lishuid'");
			if($result['count']>2)
			{
				showMsg("用户{$lishuid}只能有两个隶属!");exit();	
			}	
			$result=null;			
			$result=$db->get_one("select web_id from {member} where user_id='$lishuid' limit 1 ");
			$post_data=array("ID"=>$web_id,"DPID"=>$result['web_id'],'Weights'=>$user_id);
			$dpid_s=webService('RegistAddDParent',$post_data);
			$result=null;
			

			$liubao_s=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
			$db->query("update {my_webserv} set paymoney=paymoney+$money,liubao=5500,liubao_s='$liubao_s' where user_id=$user_id limit 1");
			
			$beizhu="$user_name[$user_id]用$money购买六保";
			$beizhu1="收取用户$user_name[$user_id]的购买六保：$money元";
			
			
		}
		else if($type==4)
		{			
	
			$zengjin_s=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));	
			$db->query("update {my_webserv} set paymoney=paymoney+$money,zengjin=2000,zengjin_s='$zengjin_s' where user_id=$user_id limit 1");
			
			$beizhu="$user_name[$user_id]用$money购买增进";
			$beizhu1="收取用户$user_name[$user_id]的购买增进：$money元";
			
		}
		$db->query("insert into {moneylog} set user_name='$user_name',user_id='$user_id',money='-$money',time='$riqi',zcity='$zcity',type=37,s_and_z=2,dq_money='$new_money',dq_money_dj='$user_moneydj',dq_jifen='$user_duihuanjifen',dq_jifen_dj='$user_dongjiejifen',beizhu='$beizhu'");//增加用户资金流水
		$db->query("update {my_money} set money=$new_money where user_id=$user_id limit 1");//更新用户账户资金
		
		
		$dq_jifen=$_S['canshu']['zong_jifen']+getjifen($money*0.31);
		$dq_money=$_S['canshu']['zong_money']+$money*0.69;
			$arr=array(
				'money'=>$money*0.69,
				'jifen'=>getjifen($money*0.31),
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>37,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$zcity,
				'dq_money'=>$_S['canshu']['zong_money'],
				'dq_jifen'=>$dq_jifen,
				'beizhu'=>''
			);
			$db->insert('{accountlog}',$arr);					
			$db->query("update {canshu} set zong_jifen=$dq_jifen,zong_money=$dq_money where id=1 limit 1");//更新总账户资金
		
		
		
		$consume_id=webService('C_Consume',array("ID"=>$web_id,"Money"=>getjifen($money),"MoneyType"=>2,"Count"=>1));//返利全部
		$arr=array(
			'cai_id'=>0,
			'gong_id'=>$user_id,
			'consume_id'=>$consume_id,
			'type'=>2,
			'time'=>date('Y-m-d H:i:s'),
			'status'=>0,
			'money'=>getjifen($money),
			'jifen'=>0,
			'city'=>$zcity,
			'gh_id'=>0
		);
		$db->insert('{webservice_list}',$arr);	
		
		header("location:?act=my_webserv&page=$page");
	}
}
$url="?act=$act&page=$page";
$sqlW='1=1';

$row=$db->get_one("select money,money_dj,duihuanjifen,dongjiejifen,suoding_money,suoding_jifen,user_name,city from {my_money} where user_id=$user_id limit 1");
	$money=$row['money'];;
	$money_dj=$row['money_dj'];
	$duihuanjifen=$row['duihuanjifen'];
	$dongjiejifen=$row['dongjiejifen'];
	$suoding_jifen=$row['suoding_jifen'];
	$suoding_money=$row['suoding_money'];
	$user_name=$row['user_name'];
	$city=$row['city'];
	$keyongmoney=$money-$suoding_money;
$row=null;

pageTop($modulename.'管理');



$row=$my_webserv->getone("user_id=".$user_id);

$paytype=$row['paytype'];
switch($paytype)
{
	case 0:
		$lv=2.16*1.1*1.1*1.1;
		break;
	case 1:
		$lv=2.16*1.1*1.1;
		break;
	case 2:
		$lv=1.32*1.1*1.1*1.1;
		break;
	case 3:
		$lv=1.32*1.1*1.1;
		break;
	case 4:
		$lv=1.16*1.1*1.1*1.1;
		break;
	case 5:
		$lv=1.16*1.1*1.1;
		break;	
}
$zmonth=$row['zmonth'];

if(empty($_GET['ui']))
{
	//$money=$db->get_one("select ");
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=my_webserv">返回</a></div>
    <script language="javascript" src="include/js/jquery.js"></script>
    <script language="javascript">
		$(document).ready(function(){
		  	$("input[name='fbb']").click(function(){
				if(this.value==1)
				{
					$('#fmoney').val(2000*216*11*11*11/100000);
				}
				else
				{
					$('#fmoney').val(20000*216*11*11*11/100000);
				}
				
		  	});
		});
	</script>
    <br><br>
    <?=$user_name?> <br /><br />资金：<?=$money?> <br /><br />冻结：<?=$money_dj?>  <br /><br />锁定资金：<?=$suoding_money?><br /><br />
    
    
    

          
    
    <br><br />
	<form method="post" style="padding-left:30px; line-height:35px">
    
    
     推荐人ID:
        <?
        echo getuserno($tuijianid);
		$row1=$member->getone("user_id=".$tuijianid);
		echo '&nbsp;&nbsp;（'.$row1['user_name']."）";
		$row1=null;
		?>
<br /><br />
          隶属人ID:
        <?
        echo getuserno($lishuid);
		$row1=$member->getone("user_id=".$lishuid);
		echo '&nbsp;&nbsp;（'.$row1['user_name']."）";
		echo "<input type='hidden' name='lishuid' value='$lishuid'>";
		$row1=null;
		?>
    
    	<input type="hidden" name="func" value="update">
        <input type="hidden" name="act" value='<?=$act?>'>
        <input type="hidden" name="type" value='<?=$type?>'>
        <input type="hidden" name="s" value='<?=$s?>'>
        <?
        	if($type==1)
			{	
				$money2=20000*2.16*1.1*1.1*1.1;			
				if($s==0)
				{
					$money=2000*2.16*1.1*1.1*1.1;
					
					?>
                    <input type="radio" name="fbb" value="1" checked="checked"/>小FBB
                    <input type="radio" name="fbb" value="2"/>大FBB
                    费用<input type='text' name='money' id="fmoney" value='<?=$money?>'>元
                    <?			
				}
				else
				{
					$money=$money2-2000*$lv;
					echo "大FBB费用：{$money2}元，己交小FBB费用：".(2000*$lv)."元<br>";
					echo "升级大FBB，还需缴费：";
					echo "<input type='text' name='money' value='$money'>元";
				}
			}
			elseif($type==2)
			{
				$money2=20000*2.16*1.1*1.1*1.1;	
				?>
                <select name="zmonth">
                  <option value="9" selected="selected">9个月</option>
                  <option value="12" <? if($zmonth==12){echo 'selected';}?>>12个月</option>
                  </select>
                <?	
				if($s==0)
				{
					$money=2000*2.16*1.1*1.1*1.1;					
					?>
                    <input type="radio" name="z100" value="1" checked="checked"/>小卓
                    <input type="radio" name="z100" value="2" />大卓
                    费用<input type='text' name='money' id="fmoney" value='<?=$money?>'>元
                    <?
			
				}
				else
				{					
					$money=$money2-2000*$lv;
					echo "大卓费用：{$money2}元，己交小卓费用：".(2000*$lv)."元<br>";
					echo "升级大卓，还需缴费：";
					echo "<input type='text' name='money' value='$money'>元";
				}
			}
			elseif($type==3)
			{				
				$money=5500*2.16*1.1*1.1*1.1;
					
				echo "购买六保，费用：";	
				echo "<input type='text' name='money' value='$money'>元";
			}
			elseif($type==4)
			{
				$money=2000*2.16*1.1*1.1*1.1;

				echo "购买增进，费用：";
				echo "<input type='text' name='money' value='$money'>元";	
				
				
			}
		?><br />
        <input type="submit" value="确定"/>
    </form>
        
 
<?
}
?>
