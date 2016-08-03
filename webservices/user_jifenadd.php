<?php
$modulename='用户积分';
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
		$jifen=$_POST['jifen'];
		$log_text=$a_username.'|'.$a_userid.$_POST['log_text'];
		
		if(empty($user_id))
		{
			$row=$db->get_one("select money,duihuanjifen,dongjiejifen,money_dj,user_id,city from {my_money} where user_name='$user_name' limit 1");
			$user_id=$row['user_id'];
		}
		else
		{
			$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,city from {my_money} where user_id='$user_id' limit 1");
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
		$row=null;			
		
		if($type==1)//添加用户积分
		{	
			//更改帐户积分
			$db->query("update {my_money} set duihuanjifen =duihuanjifen +$jifen where user_id='$user_id' limit 1");				
			
			//用户积分流水
			$dq_jifen=$dq_jifen+$jifen;					
			$arr=array(
				'money'=>0,
				'jifen'=>$jifen,
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
			//更改帐户积分
			$db->query("update {my_money} set duihuanjifen =duihuanjifen -$jifen where user_id='$user_id' limit 1");	
			
			//用户积分流水
			$dq_jifen=$dq_jifen-$jifen;		
			$arr=array(
				'money'=>0,
				'jifen'=>'-'.$jifen,
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
    <br /><br />
    <table>
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="edit">
        
       
        <tr><td>用户ID</td>        
        <td><input type="text" name="user_id" value="<? if($user_id!=0){echo $user_id;}?>" /></td></tr>
    	<tr><td>用户名</td>        
        <td><input type="text" name="user_name" value="<?=$result['user_name']?>"/></td></tr>
        
        <tr><td>操作积分</td><td><input type="text" name="jifen" value="" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/> 积分</td></tr>
        <tr><td>操作类型</td><td><input type="radio" checked value="1" name="type">增加
		   <input type="radio" value="2" name="type">减少  <font color="#FF0000">注意选择!</font></td></tr>
        <tr><td>记录流水</td><td><input type="text" size="60" value="管理员更改用户积分" name="log_text"></td></tr>
         
         <tr><td colspan="2"><input type="submit" value="保存"></td></tr>
    </form>
    </table>
<?
}
?>
