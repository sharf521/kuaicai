<?
require '../init.php';
require './func.php';

$MerPriv=$_POST['MerPriv'];
$OrdAmt = (float)$_POST['OrdAmt'];
$UsrSn=$_POST['UsrSn'];

$arr=array(
	"OrdAmt"=>sprintf("%.2f",$_POST['OrdAmt']),
	"Pid"=>(int)$_POST['Pid'],
	"MerPriv"=>$MerPriv,
	"UsrSn"=>$UsrSn
);

if($_POST['Sign'] != md5_sign($arr,'620b979f83bb60bceaa0fc033e212f2a'))
{
	echo 'error,ǩ������';
	exit();
}





	  //�жϻ������Ƿ��� ��������cache�����ļ�	
	  $path="../../data/pay_cache/".date("Y-m")."/";
	  if (!file_exists($path)) 
	  { 
		  mkdir($path, 0777);
	  }
	  $file =$path.$UsrSn;
	  $fp = fopen($file , 'w+');
	  chmod($file, 0777);	  
	  if(flock($fp , LOCK_EX | LOCK_NB)) //�趨ģʽ��ռ�����Ͳ���������
	  {
		////////////start����////////////////////////////
		
		$MerPriv=explode('#',$MerPriv);
		$user_id=(int)$MerPriv[0];
		$host=$MerPriv[1];	
		$row=$db->get_one("select id from {moneylog} where user_id=$user_id and orderid='$UsrSn' limit 1");
		if($row)
		{
			echo "<!--";
			echo "RECV_ORD_ID_".$UsrSn;
			echo "-->";
			echo "<script>window.location='../../index.php?app=my_money&act=paylog';</script>";
			exit();
		}
		else
		{			
			$money=$OrdAmt;	
			if($money<200)
			{
				$fei	=$money*0.01;	
				$amoney =0;			
			}
			else
			{
				
				if($GateId=='61')//PNRǮ�ܼ�
				{
					$fei=$money*0.009;	
				}
				/*elseif(in_array($GateId,array(25,29,27,28,12,13,33)))
				{
					$fei=$money*0.006;
				}*/
				else
				{
					$fei=$money*0.006;	
				}			
				
				$row=$db->get_one("select recharge from {city}  where city_yuming like '%".$host."%'  limit 1");
				
				$amoney	=$money*(float)$row['recharge'];//��ֵ����
				
				chongzhi_award($money,$user_id,$UsrSn,$host);	//����ƽ̨�Ƽ���			
			}
			$gate_ar=array(69,70,71,72,74,75,76,78,81);
			if(in_array($GateId,$gate_ar))
			{
				if($fei<6) $fei=6;
			}			
			$relmoney=$money-$fei;	
					
			$row=$db->get_one("select user_name,money,duihuanjifen,dongjiejifen,money_dj,qianbiku,city from {my_money} where user_id='$user_id' limit 1");
				$user_name=$row['user_name'];
				$dq_money=$row['money'];
				$dq_money_dj=$row['money_dj'];
				$dq_jifen=$row['duihuanjifen'];
				$dq_jifen_dj=$row['dongjiejifen'];
				$qianbiku=$row['qianbiku'];
				$city=$row['city'];
			$row=null;
			//��ֵ��ˮ��־
			$arr=array(
				'money'=>$relmoney,
				'jifen'=>0,
				'money_dj'=>0,
				'jifen_dj'=>0,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>100,
				's_and_z'=>1,
				'time'=>date('Y-m-d H:i:s'),
				'zcity'=>$city,
				'dq_money'=>$dq_money+$relmoney,
				'dq_money_dj'=>$dq_money_dj,
				'dq_jifen'=>$dq_jifen,				
				'dq_jifen_dj'=>$dq_jifen_dj,
				'orderid'=>$UsrSn,
				'beizhu'=>"��ֵ{$money}Ԫ"
			);			
			$db->insert('{moneylog}',$arr);				
			
			//����my_moneylog��				
			$add_mymoneylog=array(
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'add_time'=>time(),
				'leixing'=>30,
				'log_text'=>"��ֵ{$money}Ԫ",
				//'caozuo'=>50,
				's_and_z'=>1,	
				'money'=>$relmoney,
				'riqi'=>date('Y-m-d H:i:s'),
				's_and_z'=>1,
				'type'=>2,	
				'status'=>1,	
				'money_feiyong'=>'-'.$fei,
				'city'=>$city,
				'dq_money'=>$dq_money+$relmoney,
				'dq_money_dj'=>$dq_money_dj,
				'dq_jifen'=>$dq_jifen,				
				'dq_jifen_dj'=>$dq_jifen_dj												
			);						
			$db->insert('{my_moneylog}',$add_mymoneylog);
			
			if(empty($amoney))
			{
				 // û�н���				
				$db->query("update {my_money} set money=money+$relmoney where user_id='$user_id' limit 1");//�����ʻ��ʽ�
			}
			else
			{				
				$arr=array(
					'money'=>$amoney,
					'jifen'=>0,
					'money_dj'=>0,
					'jifen_dj'=>0,
					'user_id'=>$user_id,
					'user_name'=>$user_name,
					'type'=>101,
					's_and_z'=>1,
					'time'=>date('Y-m-d H:i:s'),
					'zcity'=>$city,
					'dq_money'=>$dq_money+$relmoney+$amoney,
					'dq_money_dj'=>$dq_money_dj,
					'dq_jifen'=>$dq_jifen,				
					'dq_jifen_dj'=>$dq_jifen_dj,
					'orderid'=>$UsrSn,
					'beizhu'=>"��ֵ����"
				);			
				$db->insert('{moneylog}',$arr);//��ֵ����				
				$db->query("update {my_money} set money=money+$relmoney+$amoney where user_id='$user_id' limit 1");//�����ʻ��ʽ�
			}				
			
			$db->query("update {canshu} set yu_jinbi=yu_jinbi-$money where id=1");							
			$arr=array(
				'money'=>'-'.$money,
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'type'=>100,
				's_and_z'=>2,
				'riqi'=>date('Y-m-d H:i:s'),
				'biku_city'=>$city,
				'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
				'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$money,
				'beizhu'=>'�����ţ�'.$UsrSn
			);
			$db->insert('{bikulog}',$arr);				
		}
		echo "<!--";
		echo "RECV_ORD_ID_".$UsrSn;
		echo "-->";
		//header("location:../index.php?app=my_money&act=paylog");		
		//exit();echo "֧���ɹ�";
		echo "<script>window.location='../../index.php?app=my_money&act=paylog';</script>";
		exit();
		
		///////////��������/////////////////////////////
	  flock($fp , LOCK_UN);     
  }
  fclose($fp);