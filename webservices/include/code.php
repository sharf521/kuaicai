<?php 
//设置色session id的名字
/*ini_set('session.name', 'sid');
//不使用 GET/POST 变量方式
ini_set('session.use_trans_sid', 0);
//设置垃圾回收最大生存时间
ini_set('session.gc_maxlifetime', 3600);
//使用 COOKIE 保存 SESSION ID 的方式
ini_set('session.use_cookies', 1);
ini_set('session.cookie_path', '/');*/
//多主机共享保存 SESSION ID 的 COOKIE,注意此处域名为一级域名
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

$str=random(4); //随机生成的字符串 
$width = 35; //验证码图片的宽度 
$height = 15; //验证码图片的高度 

header("Content-Type:image/png"); 
$_SESSION["code"]=$str;
$im=imagecreate($width,$height); 

$back=imagecolorallocate($im,0xFF,0xFF,0xFF); //背景色 

$pix=imagecolorallocate($im,250,250,255); //模糊点颜色 

$font=imagecolorallocate($im,255,0,0); //字体色 

mt_srand();//绘模糊作用的点 

for($i=0;$i< 1000;$i++) { 
  imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pix); 
} 
imagestring($im, 4, 2, 0,$str, $font); 
//imagerectangle($im,0,0,$width-1,$height-1,$font); //边框
imagepng($im); 
imagedestroy($im);
?>