<?
require '../init.php';
require './include/global.func.php';

$fp =   fopen("aaa.txt","w");
			$time=date("H:i:s");

			fputs($fp,'asdfasdfasd');
			fclose($fp);



if(!empty($_FILES['filename']['name']))//����ϴ�ͼƬ. 
{	
	$module=strip_tags($_POST['module']);
	$text=strip_tags($_POST['text']);//ԭҳ��inputԪ��,Ҫ��ֵ�Ŀؼ�	
	$exts=strip_tags($_POST['ext']);
	if(empty($exts)) $exts='jpg|gif|png|jpeg|bmp|swf';
	$arr=explode('|',$exts);	
	$ext=strtolower(getextension($_FILES['filename']['name']));//�ϴ��ļ���չ��
	if(! in_array($ext,$arr))
	{
		alert('�ļ����ƣ�'.$exts);	exit();
	}	
	if($_FILES['filename']['size']>1048576*2)//2M  
	{
		alert('��С���ƣ�2M��');		exit();
	}
	$filename=time().rand(1000,9999).'.'.$ext;
	$filepath=ROOT.'/uploadfiles/'.date('Y').date('m').'/';
	if(!(file_exists($filepath) && is_dir($filepath)))
	{
		mkdir($filepath,0777);
	}
	$targetFile=$filepath.$filename;	//�ϴ�����λ��
	move_uploaded_file($_FILES['filename']['tmp_name'],$targetFile);	//�ϴ��ļ�
	$value='/uploadfiles/'.date('Y').date('m').'/'.$filename;
	callback($text,$value);
}
function callback($text,$value)
{
	echo "<script type='text/javascript'>";
	echo "window.parent.document.getElementById('$text').value='$value';";
	/*echo "if(window.parent.document.getElementById('$text').value=='')";
		echo "{window.parent.document.getElementById('$text').value='$value';}";
	echo "else";
		echo "{window.parent.document.getElementById('$text').value+='||'+'$value';}";*/
	echo "window.parent.cDialog();";
	echo "</script>";
}
function alert($str)
{
	echo "<script type='text/javascript'>window.parent.alert('$str');</script>";
}
?>