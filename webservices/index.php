<?php
	require('./init.php');		
	//����
	$_S['kaiguan']=$db->get_one("select * from {kaiguan} where id=1");
	//����
	$_S['canshu']=$db->get_one("select * from {canshu} where id=1");		
	//$_S['canshu']['webservip']='192.168.1.150:888';
	
	
	require('./include/global.func.php');
		
	
	$arr_type=array(	
	'0'=>'ѡ������',
	'1'=>'���³�ֵ���',
	'2'=>'���³�ֵ����',
	'3'=>'�һ�����',
	'4'=>'�һ��ֽ�',
	'5'=>'����������',
	'6'=>'���ֽ��',
	'7'=>'���ַ���',
	'8'=>'�����Ż�ȯ',
	'9'=>'�ɹ�����',
	'10'=>'�ɹ���Ʒ',
	'11'=>'�ɹ���ͨ��',
	'12'=>'�Ź���֤��',
	'13'=>'�Ź�����',
	'14'=>'�ⶳ�Ź���֤��',
	'15'=>'������Ʒ',
	'16'=>'������Ʒ',
	'17'=>'���۹�����Ʒ',
	'18'=>'ȡ������',
	'24'=>'��̨�����û��ʽ�',
	'25'=>'���ӱҿ��ʽ�',
	'26'=>'��ֵ�ɹ������ٱҿ�',
	'27'=>'�����û��ʽ𣬼��ٱҿ�',
	'28'=>'�����û��ʽ����ӿ��',	
	'29'=>'�����ײ�����',
	'31'=>'�����û��ʽ�',
	'32'=>'�����û�����',
	'36'=>'�����ײ�',
	'37'=>'�ײ�����',
	'38'=>'�Ƽ��˻�õĽ���',
	'39'=>'��վ����Ƽ�����',
	'40'=>'��������õ��Ƽ�����',
	'41'=>'���',
	'42'=>'����',
	'100'=>'���߳�ֵ',
	'101'=>'���߳�ֵ����',
	'102'=>'���߳�ֵ����ƽ̨',
	'103'=>'���߳�ֵ����ƽ̨�Ƽ���',
	'104'=>'�ɳ�����',
	'105'=>'�շⶥ����',
	'110'=>'�ӽ��ת��',
	'111'=>'�̳�ת����',
	'112'=>'�����ֵ������Ϣת���̳�',
	'119'=>'���̳�ת���ʽ�',
	'117'=>'�̳�ת���ʽ�',
	//'106'=>'�˶���',
);


	
	require('./include/lang.php');
	require("./include/admin.func.php");	
 	$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : "";
	if($act!="")	
	{
		if($act!="login")
		{
			if($_SESSION["admin_id"]=="" || $_SESSION["admin_userid"]=="")
			{
				//$_SESSION['referer_url'] = $_SERVER['HTTP_REFERER'];
				header("Location:?act=login");
			}
		}
		$a_id=$_SESSION["admin_id"];		
		$a_userid=$_SESSION["admin_userid"];
		$a_username=$_SESSION["admin_username"];
		$a_typeid=$_SESSION['admin_typeid'];
		$a_purview=$_SESSION["admin_purview"];


/*require_once(ROOT."/include/approvedpoint.class.php");
				$app=new approvedpoint();
				$app->doapproved('407acfed-35a6-4d0b-91d6-a403683208ab',2);*/
		
		
for($i=1;$i<100;$i++)
{
	$a_purview.=$i.',';	
}
		checkpurview();
		include $act.".php";
		echo "\n</body>\n</html>";
	}
	else
		header("Location:?act=login");		
//	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//	header("Cache-Control: no-store, no-cache, must-revalidate");
//	header("Cache-Control: post-check=0, pre-check=0", false);
//header('Cache-control:private,must-revalidate');
?>