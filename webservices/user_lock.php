<?php
$modulename='�û��ʽ�';
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
			showMsg('�û�Ա�����ڣ�');exit();		
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
		if($type==1)//�����ʽ�
		{
			$db->query("update {my_money} set suoding_money=$money where user_id='$user_id' limit 1");
			$arr['type']=31;
			$arr['beizhu']=$log_text.'�����ʽ�'.$money.'Ԫ';			
			$db->insert('{moneylog}',$arr);
			adminlog("������Ա[{$user_name}]�ʽ�{$money}Ԫ");
		}
		elseif($type==2)
		{
			$db->query("update {my_money} set suoding_jifen=$money where user_id='$user_id' limit 1");
			$arr['type']=32;
			$arr['beizhu']=$log_text.'��������'.$money;		
			$db->insert('{moneylog}',$arr);	
			adminlog("������Ա[{$user_name}]���֣�{$money}");
		}

		showMsg('�����ɹ���');exit();	
	}	
	header("location:$url");
	exit();
}
pageTop($modulename.'����');

require './include/member.class.php';
$member=new member();
$result=$member->getone("user_id=".$user_id);

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'����'=>''))?>&nbsp;&nbsp;</div>
<br>
    <table>
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="edit">
        <input type="hidden" name="user_id" value="<?=$user_id?>" />
    	<tr><td>�û���</td>        
        <td><input type="text" name="user_name" value="<?=$result['user_name']?>"/></td></tr>
        
       
        <tr><td>��������</td><td><input type="radio" checked value="1" name="type">�����ʽ�
		   <input type="radio" value="2" name="type">��������</td></tr>
            <tr><td>������</td><td><input type="text" name="money" value="" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/> Ԫ(�����)</td></tr>
        <tr><td>��¼��ˮ</td><td><input type="text" size="60" value="����Ա�ֹ������û��ʽ�" name="log_text"></td></tr>
         
         <tr><td colspan="2"><input type="submit" value="����"></td></tr>
    </form>
    </table>
    
<?
}
?>
