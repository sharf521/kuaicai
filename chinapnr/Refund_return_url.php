<?	
	$CmdId = $_POST['CmdId'];				//��Ϣ����
	$OrdId = $_POST['OrdId'];				//�˿����
	$OldOrdId = $_POST['OldOrdId'];			//ԭ���׶�����
	$RespCode = $_POST['RespCode']; 	 	//Ӧ����
	$ErrMsg = $_POST['ErrMsg'];				//Ӧ���������
	$ChkValue =$_POST['ChkValue'];			//��֤��
	
	//��֤ǩ��
	$SignObject = new COM("CHINAPNR.NetpayClient");
	$MsgData = $CmdId.$OrdId.$OldOrdId.$RespCode.$ErrMsg;  		//����˳���ܴ�
	$MerFile = $_SERVER["DOCUMENT_ROOT"]."/PgPubk.key";			//�̻���ǩ��Կ�ļ�
	$SignData = $SignObject->VeriSignMsg0($MerFile,$MsgData,strlen($MsgData),$ChkValue);
		
	if($SignData == "0"){
		if($RespCode == "000000"){
			//�˿�ɹ�
			//�����˿���� ������Ӧҵ�����
			//��Щ�������
			echo "�˿�ɹ�";
		}else{
			//�˿�ʧ��
			//�����˿���� ������Ӧҵ�����
			//��Щ�������
			echo "�˿�ʧ��";
		}
		echo "RECV_ORD_ID_".$OldOrdId;
	}else{
		//��ǩʧ��
		echo "��ǩʧ��[".$SignData."]";
	}
?>