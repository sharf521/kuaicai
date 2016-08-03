<?php
$modulename='用户资金';
$page=intval($_GET['page']);
$user_id=intval($_REQUEST['user_id']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if(isset($_REQUEST['func']))
{
	
	$func=$_REQUEST['func'];
	if($func=='edit')
	{
		$type=intval($_POST['type']);
		
		$user_name=$_POST['user_name'];	
		
		$money=$_POST['money'];
		if($_POST['money']>$_S['canshu']['yu_jinbi']  && $type==1)
		{
			showMsg('可用币库不足！');exit();	
		}
		else
		{
			$log_text=$a_username.'|'.$a_userid.$_POST['log_text'];
			if(empty($user_id))
			{
				$row=$db->get_one("select money,duihuanjifen,dongjiejifen,money_dj,qianbiku,user_id,city from {my_money} where user_name='$user_name' limit 1");
				$user_id=$row['user_id'];
				
			}
			else
			{
				$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,city from {my_money} where user_id='$user_id' limit 1");
				$user_name=$row['user_name'];
			}
			if(!$row)
			{
				showMsg('该会员不存在！');exit();		
			}
			$city=intval($row['city']);
			$dq_money=$row['money'];
			$dq_money_dj=$row['money_dj'];
			$dq_jifen=$row['duihuanjifen'];
			$dq_jifen_dj=$row['dongjiejifen'];
			$qianbiku=$row['qianbiku'];
			$row=null;			
			
			if($type==1)//添加用户资金
			{	
/*				if($qianbiku>0)//如果以前购买套参的时候多划拨了币库,不在划拨币库
				{
					if($money>=$qianbiku)
					{
						//更改帐户余额
						$db->query("update {my_money} set money=money+$money,qianbiku=0 where user_id='$user_id' limit 1");
						
						$q=$money-$qianbiku;//充的钱大于欠的币库，还要划拨币库
						$db->query("update {canshu} set yu_jinbi=yu_jinbi-$q where id=1");							
						//增加用户资金，减小库币
						$arr=array(
							'money'=>'-'.$q,
							'user_id'=>$user_id,
							'user_name'=>$user_name,
							'type'=>27,
							's_and_z'=>2,
							'riqi'=>date('Y-m-d H:i:s'),
							'biku_city'=>$city,
							'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
							'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$q,
							'beizhu'=>$log_text.'购买套餐的时候多划拨了币库,此次只划拨：'.$q
						);
						$db->insert('{bikulog}',$arr);						
					}
					else
					{
						$q=$qianbiku-$money;
						//更改帐户余额
						$db->query("update {my_money} set money=money+$money,qianbiku=$q where user_id='$user_id' limit 1");
						
						$arr=array(
							'money'=>'0',
							'user_id'=>$user_id,
							'user_name'=>$user_name,
							'type'=>27,
							's_and_z'=>2,
							'riqi'=>date('Y-m-d H:i:s'),
							'biku_city'=>$city,
							'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
							'dq_yujinbi'=>$_S['canshu']['yu_jinbi'],
							'beizhu'=>$log_text.'此次不划拨,多划拨币库：'.$q
						);
						$db->insert('{bikulog}',$arr);	
					}
				}*/
				//else
				{	
					//更改帐户余额
					$db->query("update {my_money} set money=money+$money where user_id='$user_id' limit 1");
					//$db->query("update {my_money} set duihuanjifen =duihuanjifen +$money where user_id='$user_id' limit 1");				
					//更改库币
					$db->query("update {canshu} set yu_jinbi=yu_jinbi-$money where id=1");							
					//增加用户资金，减小库币
					$arr=array(
						'money'=>'-'.$money,
						'user_id'=>$user_id,
						'user_name'=>$user_name,
						'type'=>27,
						's_and_z'=>2,
						'riqi'=>date('Y-m-d H:i:s'),
						'biku_city'=>$city,
						'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
						'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$money,
						'beizhu'=>$log_text
					);
					$db->insert('{bikulog}',$arr);				
				}
				//用户金钱流水
				$dq_money=$dq_money+$money;					
		
				
				$arr=array(
					'money'=>$money,
					'jifen'=>0,
					'money_dj'=>0,
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>27,
					's_and_z'=>1,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$dq_money,
					'dq_money_dj'=>$dq_money_dj,
					'dq_jifen'=>$dq_jifen,				
					'dq_jifen_dj'=>$dq_jifen_dj,
					'beizhu'=>$log_text
				);			
				$db->insert('{moneylog}',$arr);				
				
			}
			else
			{
				if($dq_money<$money)
				{
					showMsg('可用余额不足！');exit();	
				}
				$row=null;
				//更改库币
				$db->query("update {canshu} set yu_jinbi=yu_jinbi+$money where id=1");
				$row=$db->get_one("select zong_jinbi,yu_jinbi from {canshu} where id=1");
			    //减少用户资金，增加库币
				$arr=array(
					'money'=>$money,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>28,
					's_and_z'=>1,
					'riqi'=>date('Y-m-d H:i:s'),
					'biku_city'=>$city,
					'dq_jinbi'=>$row['zong_jinbi'],
					'dq_yujinbi'=>$row['yu_jinbi'],
					'beizhu'=>$log_text
				);
				$db->insert('{bikulog}',$arr);				
				
				$db->query("update {my_money} set money=money-$money where user_id='$user_id' limit 1");	
				
				$dq_money=$dq_money-$money;		

				
				$arr=array(
					'money'=>'-'.$money,
					'jifen'=>0,
					'money_dj'=>0,
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>28,
					's_and_z'=>2,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$dq_money,
					'dq_money_dj'=>$dq_money_dj,
					'dq_jifen'=>$dq_jifen,				
					'dq_jifen_dj'=>$dq_jifen_dj,
					'beizhu'=>$log_text
				);			
				$db->insert('{moneylog}',$arr);				
				
			}
		}
		showMsg('操作成功！');exit();	
	}	
	header("location:$url");
	exit();
}
pageTop($modulename.'管理');

require './include/member.class.php';
$member=new member();
$result=$member->getone("user_id=".$user_id);

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'管理'=>''))?>&nbsp;&nbsp;</div>
    <br /><a href="/webservices/?act=user_jifenadd">用户积分管理</a>
    <br /><br />
    总币库：<?=$_S['canshu']['zong_jinbi']?>  &nbsp;剩余币库：<?=$_S['canshu']['yu_jinbi']?>
    <br><br>
    <table>
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="edit">
        
       
        <tr><td>用户ID</td>        
        <td><input type="text" name="user_id" value="<? if($user_id!=0){echo $user_id;}?>" /></td></tr>
    	<tr><td>用户名</td>        
        <td><input type="text" name="user_name" value="<?=$result['user_name']?>"/></td></tr>
        
        <tr><td>操作金额</td><td><input type="text" name="money" value="" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/> 元</td></tr>
        <tr><td>操作类型</td><td><input type="radio" checked value="1" name="type">增加
		   <input type="radio" value="2" name="type">减少  <font color="#FF0000">注意选择!</font></td></tr>
        <tr><td>记录流水</td><td><input type="text" size="60" value="管理员更改用户资金" name="log_text"></td></tr>
         
         <tr><td colspan="2"><input type="submit" value="保存"></td></tr>
    </form>
    </table>
    
<?
}
?>
