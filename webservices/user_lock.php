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
		$log_text=$a_username.'|'.$a_userid.$_POST['log_text'];
		
		$money=$_POST['money'];
		
		if(empty($user_id))
		{
			$row=$db->get_one("select city,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,user_id from {my_money} where user_name='$user_name' limit 1");
			$user_id=$row['user_id'];
		}
		else
			$row=$db->get_one("select city,money,duihuanjifen,dongjiejifen,money_dj,qianbiku from {my_money} where user_id='$user_id' limit 1");
		if(!$row)
		{
			showMsg('该会员不存在！');exit();		
		}
		$dq_money=$row['money'];
		$dq_money_dj=$row['money_dj'];
		$dq_jifen=$row['duihuanjifen'];
		$dq_jifen_dj=$row['dongjiejifen'];
		$qianbiku=$row['qianbiku'];
		$city=$row['city'];
		$row=null;			
		$arr=array(
			'money'=>0,
			'jifen'=>0,
			'money_dj'=>0,
			'jifen_dj'=>0,
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			's_and_z'=>0,
			'time'=>date('Y-m-d H:i:s'),
			'zcity'=>$city,
			'dq_money'=>$dq_money,
			'dq_money_dj'=>$dq_money_dj,
			'dq_jifen'=>$dq_jifen,				
			'dq_jifen_dj'=>$dq_jifen_dj
		);			
		if($type==1)//锁定资金
		{
			$db->query("update {my_money} set suoding_money=$money where user_id='$user_id' limit 1");
			$arr['type']=31;
			$arr['beizhu']=$log_text.'锁定资金'.$money.'元';			
			$db->insert('{moneylog}',$arr);
			adminlog("锁定会员[{$user_name}]资金：{$money}元");
		}
		elseif($type==2)
		{
			$db->query("update {my_money} set suoding_jifen=$money where user_id='$user_id' limit 1");
			$arr['type']=32;
			$arr['beizhu']=$log_text.'锁定积分'.$money;		
			$db->insert('{moneylog}',$arr);	
			adminlog("锁定会员[{$user_name}]积分：{$money}");
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
<br>
    <table>
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="edit">
        <input type="hidden" name="user_id" value="<?=$user_id?>" />
    	<tr><td>用户名</td>        
        <td><input type="text" name="user_name" value="<?=$result['user_name']?>"/></td></tr>
        
       
        <tr><td>操作类型</td><td><input type="radio" checked value="1" name="type">锁定资金
		   <input type="radio" value="2" name="type">锁定积分</td></tr>
            <tr><td>锁定：</td><td><input type="text" name="money" value="" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/> 元(或积分)</td></tr>
        <tr><td>记录流水</td><td><input type="text" size="60" value="管理员手工锁定用户资金" name="log_text"></td></tr>
         
         <tr><td colspan="2"><input type="submit" value="保存"></td></tr>
    </form>
    </table>
    
<?
}
?>
