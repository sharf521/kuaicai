<?

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
		if($RespCode == "000000"){

			echo "ǩԼ�ɹ�";
		}else{
			//����ʧ��
			//���ݶ����� ������Ӧҵ�����
			//��Щ�������
			echo "ǩԼʧ��";
		}
		echo "RECV_ORD_ID_".$OrdId;
	}else{
		//��ǩʧ��
		echo "��ǩʧ��[".$SignData."]";
	}
?>