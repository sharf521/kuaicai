<?php 
//����ɫsession id������
/*ini_set('session.name', 'sid');
//��ʹ�� GET/POST ������ʽ
ini_set('session.use_trans_sid', 0);
//�������������������ʱ��
ini_set('session.gc_maxlifetime', 3600);
//ʹ�� COOKIE ���� SESSION ID �ķ�ʽ
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/');*/
//������������ SESSION ID �� COOKIE,ע��˴�����Ϊһ������
//ini_set('session.cookie_domain', 'art.cn');
session_start();
function random($len)
{ 
  $srcstr="0123456789"; 
  $strs=""; 
  for($i=0;$i<$len;$i++){ 
    $strs.=$srcstr[rand(0,9)]; 
  } 
  return strtoupper($strs); 
} 

$str=random(4); //������ɵ��ַ��� 
$width = 35; //��֤��ͼƬ�Ŀ�� 
$height = 15; //��֤��ͼƬ�ĸ߶� 

header("Content-Type:image/png"); 
$_SESSION["code"]=$str;
$im=imagecreate($width,$height); 

$back=imagecolorallocate($im,0xFF,0xFF,0xFF); //����ɫ 

$pix=imagecolorallocate($im,250,250,255); //ģ������ɫ 

$font=imagecolorallocate($im,255,0,0); //����ɫ 

mt_srand();//��ģ�����õĵ� 

for($i=0;$i< 1000;$i++) { 
  imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pix); 
} 
imagestring($im, 4, 2, 0,$str, $font); 
//imagerectangle($im,0,0,$width-1,$height-1,$font); //�߿�
imagepng($im); 
imagedestroy($im);
?>