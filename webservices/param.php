<?php
$modulename='��������';
$page=intval($_GET['page']);
$url="?act=$act&page=$page";
$sqlW='1=1';

if(isset($_REQUEST['func']))
{
	$func=$_REQUEST['func'];
	if($func=='editkaiguan')
	{
		$webservice=$_POST['webservice'];
		$db->query("update {kaiguan} set webservice='$webservice' where id=1");
		adminlog('�޸Ŀ�������');
	}
	elseif($func=='editcanshu')
	{
		unset($_POST['act']);
		unset($_POST['func']);
		$db->update('{canshu}',$_POST,'id=1');
		adminlog('�޸Ĳ�������');
		showMsg('����ɹ���',$url);exit();	
	}
	header("location:$url");
	exit();
}
pageTop($modulename.'����');


if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename.'����'=>''))?>&nbsp;&nbsp;</div>
    
    <br><br>
    <h2>���ع���</h2>
	<?
    	$kaiguan=$db->get_one("select * from {kaiguan} where id=1");		
	?>
    <table style=" margin-left:50px">
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="editkaiguan">
    	<tr><td>�Ƿ���WebServices</td><td>
        	<input type="radio" name="webservice" value="yes" checked>����
            <input type="radio" name="webservice" value="no" <? if($kaiguan['webservice']=='no'){echo 'checked';}?>>������
         </td></tr>
         

         
         <tr><td colspan="2"><input type="submit" value="����"></td></tr>
    </form>
    </table>
    
    
    <h2>��������</h2>
	<?
	//duihuanjifenfeilv �һ����ֵķ���	 duihuanxianjinfeilv �һ��ֽ�ķ���	 tixianfeilv ���ַ���	 chongzhifeilv ��ֵ����	 tuijianjiangli �Ƽ��˻�õĽ���	 jlshuishou �Ƽ������ֽ�˰�ձ���	 tuijianjifen �Ƽ���������	
	// jfshuishou �Ƽ���������˰�ձ���	 jifenbili �����ѻ���ת�����û��ֱ���	 tg_fei �Ź�����	 tg_baozhengjin �Ź���֤��	 zong_jinbi �ܱҿ�	 yu_jinbi ʣ��ҿ�	 jifenxianjin �ֽ�����������	 daishou ���ۻ������ʱ�빩���̵ı���
    	$row=$db->get_one("select * from {canshu} where id=1");		
	?>
    <table style=" margin-left:50px">
    <form method="post">
    	<input type="hidden" name="act" value="<?=$act?>">
        <input type="hidden" name="func" value="editcanshu">
        
    	<tr><td>WebService��ַ��</td><td><input type="text" name="webservip" value="<?=$row['webservip']?>" /></td></tr>
        <tr><td>�һ����ֵķ���</td><td><input type="text" name="duihuanjifenfeilv" value="<?=$row['duihuanjifenfeilv']?>" /></td></tr>
        <tr><td>�һ��ֽ�۳�����</td><td><input type="text" name="duihuanxianjinfeilv" value="<?=$row['duihuanxianjinfeilv']?>" /></td></tr>
        
        <tr><td>���ַ���</td><td><input type="text" name="tixianfeilv" value="<?=$row['tixianfeilv']?>" /></td></tr>
        <tr><td>������ͷ���</td><td><input type="text" name="tixianfeimin" value="<?=$row['tixianfeimin']?>" /></td></tr>
        <tr><td>������߷���</td><td><input type="text" name="tixianfeimax" value="<?=$row['tixianfeimax']?>" /></td></tr>
        <tr><td>��ֵ����</td><td><input type="text" name="chongzhifeilv" value="<?=$row['chongzhifeilv']?>" /></td></tr>
        <tr><td>��ֵ��ͷ���</td><td><input type="text" name="chongzhifeimin" value="<?=$row['chongzhifeimin']?>" /></td></tr>
        <tr><td>��ֵ��߷���</td><td><input type="text" name="chongzhifeimax" value="<?=$row['chongzhifeimax']?>" /></td></tr>
        <tr><td>�Ƽ��˻�õĽ���</td><td><input type="text" name="tuijianjiangli" value="<?=$row['tuijianjiangli']?>" /></td></tr>
        <tr><td>�����ѻ���ת�����û��ֱ���</td><td><input type="text" name="jifenbili" value="<?=$row['jifenbili']?>" /></td></tr>
        <tr><td>�Ƽ���������</td><td><input type="text" name="tuijianjifen" value="<?=$row['tuijianjifen']?>" /></td></tr>
        <tr><td>�Ź�����</td><td><input type="text" name="tg_fei" value="<?=$row['tg_fei']?>" /></td></tr>
        <tr><td>�Ź���֤��</td><td><input type="text" name="tg_baozhengjin" value="<?=$row['tg_baozhengjin']?>" /></td></tr>
        
        <tr><td>�ֽ�����������</td><td><input type="text" name="jifenxianjin" value="<?=$row['jifenxianjin']?>" /></td></tr>
        <tr><td>���ۻ������ʱ�빩���̵ı���</td><td><input type="text" name="daishou" value="<?=$row['daishou']?>" /></td></tr>
        <tr><td>�������ֵ���С���</td><td><input type="text" name="tx_min" value="<?=$row['tx_min']?>" /></td></tr>
        <tr><td>�������ֵ������</td><td><input type="text" name="tx_max" value="<?=$row['tx_max']?>" /></td></tr>

 	  


	<tr style="display:none"><td>�㷨����</td><td><input type="text" name="plantype" value="<?=$row['plantype']?>" /></td></tr>
    <tr><td>�ֺ����</td><td><input type="text" name="fenhongbili" value="<?=$row['fenhongbili']?>" />
       <!-- ///  ����16�㷨�ֺ����<br />
        ///     1 ƽ̨�������� * 0.05m * 0.16m;<br />
        ///     2 ƽ̨�������� * 0.3m * 0.16m;<br />
        ///     3 ƽ̨�������� * 0.3333333m * 0.16m;<br />
 
        ///  ����㷨��16�㷨�ֺ����<br />
        ///     1 ƽ̨�������� * 0.05m * 0.16m;<br />
        ///     2 ƽ̨�������� * 0.3m * 0.16m;<br />
        ///     3 ƽ̨�������� * 0.3333333m * 0.16m;<br />-->
        
        16�㷨�ֺ����
        1 ƽ̨�������� * 0.005        
        2 ƽ̨�������� * 0.0083        
        3 ƽ̨�������� * 0.025
        
        
</td></tr>    
    <tr><td>��Ȩ��С</td><td><input type="text" name="guquandaxiao" value="<?=$row['guquandaxiao']?>" />
    
    [0] ����120�㷨 ��Ȩ��С��
        ///  [1] ����16�㷨 ��Ȩ��С��
        ///  [2] ����㷨�� 120 ��Ȩ��С��
        ///  [3] ����㷨�� 16 ��Ȩ��С��

    </td></tr>
    <tr><td>����ֵ</td><td><input type="text" name="Probability" value="<?=$row['Probability']?>" />
    
    16%60��2�Ŷ���60������������(��Чֵ:1,2,3,4)������ŷָ�

    </td></tr>
    
    
    
    <tr style="display:none"><td>�շⶥ���</td><td><input type="text" name="daymaxmoney" value="<?=$row['daymaxmoney']?>" /></td></tr>
    <tr style="display:none"><td>�շⶥ�������</td><td>
    <input type="radio" name="daymaxmoneytype" value="1" checked="checked"/>�������û�
    <input type="radio" name="daymaxmoneytype" value="2" <? if($row['daymaxmoneytype']==2){echo 'checked';}?>/>�û�������</td></tr>
      

        
         
         <tr><td colspan="2"><input type="submit" value="����"></td></tr>
    </form>
    </table>
    
<?
}
?>