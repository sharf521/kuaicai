<?
require './init.php';
$_S['canshu']=$db->get_one("select * from {canshu} where id=1");

	$CmdId = $_POST['CmdId'];			//��Ϣ����
	$MerId = $_POST['MerId']; 	 		//�̻���
	$RespCode = $_POST['RespCode']; 	//Ӧ�𷵻���
	$TrxId = $_POST['TrxId'];  			//Ǯ�ܼҽ���Ψһ��ʶ
	$OrdAmt = $_POST['OrdAmt']; 		//���
	$CurCode = $_POST['CurCode']; 		//����
	$Pid = $_POST['Pid'];  				//��Ʒ���
	$OrdId = $_POST['OrdId'];  			//������
	$MerPriv = $_POST['MerPriv'];  		//�̻�˽����
	$RetType = $_POST['RetType'];  		//��������
	$DivDetails = $_POST['DivDetails']; //������ϸ
	$GateId = $_POST['GateId'];  		//����ID
	$ChkValue = $_POST['ChkValue']; 	//ǩ����Ϣ
	
	//��֤ǩ��
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $CmdId.$MerId.$RespCode.$TrxId.$OrdAmt.$CurCode.$Pid.$OrdId.$MerPriv.$RetType.$DivDetails.$GateId;  	//����˳���ܴ�
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/PgPubk.key";			//�̻���ǩ��Կ�ļ�
	$SignData = $SignObject->VeriSignMsg0($MerFile,$MsgData,strlen($MsgData),$ChkValue);
	
	if($SignData == "0"){
		if($RespCode == "000000")
		{
			$MerPriv=explode('#',$MerPriv);
			$user_id=(int)$MerPriv[0];
			$host=$MerPriv[1];				
			$row=$db->get_one("select id from {moneylog} where user_id=$user_id and orderid='$TrxId' limit 1");
			if($row)
			{
				echo "RECV_ORD_ID_".$OrdId;
				echo '�ظ��˰�';	
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
					$amoney	=$money*0.001;//��ֵ����
					if($GateId=='61')//PNRǮ�ܼ�
					{
						$fei=$money*0.009;	
					}
					elseif(in_array($GateId,array(25,29,27,28,12,13,33)))
					{
						$fei=$money*0.006;
					}
					else
					{
						$fei=$money*0.008;	
					}
					chongzhi_award($money,$user_id,$TrxId,$host);	//����ƽ̨�Ƽ���				
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
					'orderid'=>$TrxId,
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
						'orderid'=>$TrxId,
						'beizhu'=>"��ֵ����"
					);			
					$db->insert('{moneylog}',$arr);//��ֵ����				
					$db->query("update {my_money} set money=money+$relmoney+$amoney where user_id='$user_id' limit 1");//�����ʻ��ʽ�
				}
				
				if($qianbiku>0)//�����ǰ�����ײε�ʱ��໮���˱ҿ�,���ڻ����ҿ�
				{
					if($money>=$qianbiku)
					{
						$db->query("update {my_money} set qianbiku=0 where user_id='$user_id' limit 1");						
						$q=$money-$qianbiku;//���Ǯ����Ƿ�ıҿ⣬��Ҫ�����ҿ�
						$db->query("update {canshu} set yu_jinbi=yu_jinbi-$q where id=1");							
						$arr=array(
							'money'=>'-'.$q,
							'user_id'=>$user_id,
							'user_name'=>$user_name,
							'type'=>100,
							's_and_z'=>2,
							'riqi'=>date('Y-m-d H:i:s'),
							'biku_city'=>$city,
							'dq_jinbi'=>$_S['canshu']['zong_jinbi'],
							'dq_yujinbi'=>$_S['canshu']['yu_jinbi']-$q,
							'beizhu'=>'�����ţ�'.$TrxId
						);
						$db->insert('{bikulog}',$arr);						
					}
					else
					{
						$q=$qianbiku-$money;
						$db->query("update {my_money} set qianbiku=$q where user_id='$user_id' limit 1");
					}
				}
				else
				{	
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
						'beizhu'=>'�����ţ�'.$TrxId
					);
					$db->insert('{bikulog}',$arr);					
				}			
			}
			echo "֧���ɹ�";
		}else{
			//����ʧ��
			//���ݶ����� ������Ӧҵ�����
			//��Щ�������
			echo "֧��ʧ��";
		}
		echo "RECV_ORD_ID_".$OrdId;
	}else{
		//��ǩʧ��
		echo "��ǩʧ��[".$SignData."]";
	}
?>