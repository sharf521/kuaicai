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
		
		$money=$_POST['money'];
		if($_POST['money']>$_S['canshu']['yu_jinbi']  && $type==1)
		{
			showMsg('���ñҿⲻ�㣡');exit();	
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
				showMsg('�û�Ա�����ڣ�');exit();		
			}
			$city=intval($row['city']);
			$dq_money=$row['money'];
			$dq_money_dj=$row['money_dj'];
			$dq_jifen=$row['duihuanjifen'];
			$dq_jifen_dj=$row['dongjiejifen'];
			$qianbiku=$row['qianbiku'];
			$row=null;			
			
			if($type==1)//����û��ʽ�
			{	
/*				if($qianbiku>0)//�����ǰ�����ײε�ʱ��໮���˱ҿ�,���ڻ����ҿ�
				{
					if($money>=$qianbiku)
					{
						//�����ʻ����
						$db->query("update {my_money} set money=money+$money,qianbiku=0 where user_id='$user_id' limit 1");
						
						$q=$money-$qianbiku;//���Ǯ����Ƿ�ıҿ⣬��Ҫ�����ҿ�
						$db->query("update {canshu} set yu_jinbi=yu_jinbi-$q where id=1");							
						//�����û��ʽ𣬼�С���
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
							'beizhu'=>$log_text.'�����ײ͵�ʱ��໮���˱ҿ�,�˴�ֻ������'.$q
						);
						$db->insert('{bikulog}',$arr);						
					}
					else
					{
						$q=$qianbiku-$money;
						//�����ʻ����
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
							'beizhu'=>$log_text.'�˴β�����,�໮���ҿ⣺'.$q
						);
						$db->insert('{bikulog}',$arr);	
					}
				}*/
				//else
				{	
					//�����ʻ����
					$db->query("update {my_money} set money=money+$money where user_id='$user_id' limit 1");
					//$db->query("update {my_money} set duihuanjifen =duihuanjifen +$money where user_id='$user_id' limit 1");				
					//���Ŀ��
					$db->query("update {canshu} set yu_jinbi=yu_jinbi-$money where id=1");							
					//�����û��ʽ𣬼�С���
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
				//�û���Ǯ��ˮ
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
					showMsg('�������㣡');exit();	
				}
				$row=null;
				//���Ŀ��
				$db->query("update {canshu} set yu_jinbi=yu_jinbi+$money where id=1");
				$row=$db->get_one("select zong_jinbi,yu_jinbi from {canshu} where id=1");
			    //�����û��ʽ����ӿ��
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
    <br /><a href="/webservices/?act=user_jifenadd">�û����ֹ���</a>
    <br /><br />
    �ܱҿ⣺<?=$_S['canshu']['zong_jinbi']?>  &nbsp;ʣ��ҿ⣺<?=$_S['canshu']['yu_jinbi']?>
    <br><br>
    <table>
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="edit">
        
       
        <tr><td>�û�ID</td>        
        <td><input type="text" name="user_id" value="<? if($user_id!=0){echo $user_id;}?>" /></td></tr>
    	<tr><td>�û���</td>        
        <td><input type="text" name="user_name" value="<?=$result['user_name']?>"/></td></tr>
        
        <tr><td>�������</td><td><input type="text" name="money" value="" onKeyPress="inputMoney(this)" onKeyUp="inputMoney(this)" onBlur="inputMoney(this)"/> Ԫ</td></tr>
        <tr><td>��������</td><td><input type="radio" checked value="1" name="type">����
		   <input type="radio" value="2" name="type">����  <font color="#FF0000">ע��ѡ��!</font></td></tr>
        <tr><td>��¼��ˮ</td><td><input type="text" size="60" value="����Ա�����û��ʽ�" name="log_text"></td></tr>
         
         <tr><td colspan="2"><input type="submit" value="����"></td></tr>
    </form>
    </table>
    
<?
}
?>
