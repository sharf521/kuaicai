<?php
$modulename='�û������ײ�';
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
	$modulename=$s==1?'����FBB':'����FBB';	
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
		$user_money=$row['money'];//�û��ʽ�
		$user_moneydj=$row['money_dj'];
		$user_duihuanjifen=$row['duihuanjifen'];
		$user_dongjiejifen=$row['dongjiejifen'];
		$user_suoding_money=$row['suoding_money'];//�û��������
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
			showMsg('���Ŀ������㣬���ܹ���');
			exit();	
		}
		if($type==1)//FBB
		{
			if($s==0)//����
			{				
				if($_POST['fbb']==1)//Сfbb
				{					
					$fbb_s=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>2000));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=2000,fbb_s='$fbb_s' where user_id=$user_id limit 1");
					adminlog("$user_name[$user_id]����СFBB");
					$beizhu="$user_name[$user_id]��$moneyԪ����СFBB";
					$beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ���СFBB$moneyԪ";
					
				}
				else//��fbb
				{
					$fbb_s=webService('Fbb_Regist_Money',array("ID"=>$web_id,"Money"=>20000));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=20000,fbb_s='$fbb_s' where user_id=$user_id limit 1");
					adminlog("$user_name[$user_id]�����FBB");
					$beizhu="$user_name[$user_id]��$money�����FBB";
					$beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ����FBB$moneyԪ";
					
				}
			}
			else//������fbb
			{
				$fbb_s=webService('Fbb_Update_Regist_Money',array("ID"=>$web_id));
				$db->query("update {my_webserv} set paymoney=paymoney+$money,fbb=20000 where user_id=$user_id limit 1");
				adminlog("$user_name[$user_id]������FBB");
				$beizhu="$user_name[$user_id]��$money������FBB";
				$beizhu1="��ȡ�û�$user_name[$user_id]��������FBB$moneyԪ";
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
					$beizhu="$user_name[$user_id]��$money����С׿";
				    $beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ���С׿��$moneyԪ";
				}
				else
				{
					$zhuo_s=webService('Z_Static_Regist',array("ID"=>$web_id,"Money"=>20000,"Month"=>$zmonth));
					$db->query("update {my_webserv} set paymoney=paymoney+$money,zhuo=20000,zhuo_s='$zhuo_s' where user_id=$user_id limit 1");
					$beizhu="$user_name[$user_id]��$money�����׿";
				    $beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ����׿��$moneyԪ";
				}
			}
			else
			{
				$zhuo_s=webService('Z_Static_Update_Regist',array("ID"=>$web_id,"Month"=>$zmonth));
				$db->query("update {my_webserv} set paymoney=paymoney+$money,zhuo=20000,zhuo_s='$zhuo_s' where user_id=$user_id limit 1");
				$beizhu="$user_name[$user_id]��$money������׿";
				$beizhu1="��ȡ�û�$user_name[$user_id]��������׿��$moneyԪ";
			}	
			
			
			
		}
		else if($type==3)
		{
			$lishuid=$_POST['lishuid'];
			if(empty($lishuid))
			{
				showMsg('�����˲���Ϊ�գ�');exit();		
			}
			$result=$db->get_one("select count(*) as count from {my_webserv} a join {member} b on a.user_id=b.user_id where a.status=1 and b.lishuid='$lishuid'");
			if($result['count']>2)
			{
				showMsg("�û�{$lishuid}ֻ������������!");exit();	
			}	
			$result=null;			
			$result=$db->get_one("select web_id from {member} where user_id='$lishuid' limit 1 ");
			$post_data=array("ID"=>$web_id,"DPID"=>$result['web_id'],'Weights'=>$user_id);
			$dpid_s=webService('RegistAddDParent',$post_data);
			$result=null;
			

			$liubao_s=webService('JD_Regist_Money',array("ID"=>$web_id,"Money"=>5500));
			$db->query("update {my_webserv} set paymoney=paymoney+$money,liubao=5500,liubao_s='$liubao_s' where user_id=$user_id limit 1");
			
			$beizhu="$user_name[$user_id]��$money��������";
			$beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ���������$moneyԪ";
			
			
		}
		else if($type==4)
		{			
	
			$zengjin_s=webService('Vip_Money',array("ID"=>$web_id,"Money"=>2000));	
			$db->query("update {my_webserv} set paymoney=paymoney+$money,zengjin=2000,zengjin_s='$zengjin_s' where user_id=$user_id limit 1");
			
			$beizhu="$user_name[$user_id]��$money��������";
			$beizhu1="��ȡ�û�$user_name[$user_id]�Ĺ���������$moneyԪ";
			
		}
		$db->query("insert into {moneylog} set user_name='$user_name',user_id='$user_id',money='-$money',time='$riqi',zcity='$zcity',type=37,s_and_z=2,dq_money='$new_money',dq_money_dj='$user_moneydj',dq_jifen='$user_duihuanjifen',dq_jifen_dj='$user_dongjiejifen',beizhu='$beizhu'");//�����û��ʽ���ˮ
		$db->query("update {my_money} set money=$new_money where user_id=$user_id limit 1");//�����û��˻��ʽ�
		
		
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
			$db->query("update {canshu} set zong_jifen=$dq_jifen,zong_money=$dq_money where id=1 limit 1");//�������˻��ʽ�
		
		
		
		$consume_id=webService('C_Consume',array("ID"=>$web_id,"Money"=>getjifen($money),"MoneyType"=>2,"Count"=>1));//����ȫ��
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

pageTop($modulename.'����');



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
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;<a href="?act=my_webserv">����</a></div>
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
    <?=$user_name?> <br /><br />�ʽ�<?=$money?> <br /><br />���᣺<?=$money_dj?>  <br /><br />�����ʽ�<?=$suoding_money?><br /><br />
    
    
    

          
    
    <br><br />
	<form method="post" style="padding-left:30px; line-height:35px">
    
    
     �Ƽ���ID:
        <?
        echo getuserno($tuijianid);
		$row1=$member->getone("user_id=".$tuijianid);
		echo '&nbsp;&nbsp;��'.$row1['user_name']."��";
		$row1=null;
		?>
<br /><br />
          ������ID:
        <?
        echo getuserno($lishuid);
		$row1=$member->getone("user_id=".$lishuid);
		echo '&nbsp;&nbsp;��'.$row1['user_name']."��";
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
                    <input type="radio" name="fbb" value="1" checked="checked"/>СFBB
                    <input type="radio" name="fbb" value="2"/>��FBB
                    ����<input type='text' name='money' id="fmoney" value='<?=$money?>'>Ԫ
                    <?			
				}
				else
				{
					$money=$money2-2000*$lv;
					echo "��FBB���ã�{$money2}Ԫ������СFBB���ã�".(2000*$lv)."Ԫ<br>";
					echo "������FBB������ɷѣ�";
					echo "<input type='text' name='money' value='$money'>Ԫ";
				}
			}
			elseif($type==2)
			{
				$money2=20000*2.16*1.1*1.1*1.1;	
				?>
                <select name="zmonth">
                  <option value="9" selected="selected">9����</option>
                  <option value="12" <? if($zmonth==12){echo 'selected';}?>>12����</option>
                  </select>
                <?	
				if($s==0)
				{
					$money=2000*2.16*1.1*1.1*1.1;					
					?>
                    <input type="radio" name="z100" value="1" checked="checked"/>С׿
                    <input type="radio" name="z100" value="2" />��׿
                    ����<input type='text' name='money' id="fmoney" value='<?=$money?>'>Ԫ
                    <?
			
				}
				else
				{					
					$money=$money2-2000*$lv;
					echo "��׿���ã�{$money2}Ԫ������С׿���ã�".(2000*$lv)."Ԫ<br>";
					echo "������׿������ɷѣ�";
					echo "<input type='text' name='money' value='$money'>Ԫ";
				}
			}
			elseif($type==3)
			{				
				$money=5500*2.16*1.1*1.1*1.1;
					
				echo "�������������ã�";	
				echo "<input type='text' name='money' value='$money'>Ԫ";
			}
			elseif($type==4)
			{
				$money=2000*2.16*1.1*1.1*1.1;

				echo "�������������ã�";
				echo "<input type='text' name='money' value='$money'>Ԫ";	
				
				
			}
		?><br />
        <input type="submit" value="ȷ��"/>
    </form>
        
 
<?
}
?>
