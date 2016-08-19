<?php
session_start();
//session_register("randcode");
$width=50;    //先定义图片的长、宽 
$height= isset($_REQUEST['height'])?$_REQUEST['height']:18;
$rand_str = "";
 $codeSet = '346789ABCDEFGHJKLMNPQRTUVWXY';  
 $fontSize = 25;     // 验证码字体大小(px)  
 $useCurve = true;   // 是否画混淆曲线  
 $useNoise = true;   // 是否添加杂点   
 $imageH = 0;        // 验证码图片宽  
 $imageL = 0;        // 验证码图片长  
 $length = 4;        // 验证码位数  

for($i=0;$i<4;$i++){
	$rand_str .= chr(mt_rand(48,57));
}
if(function_exists("imagecreate")){

	$_SESSION["randcode"]=strtolower($rand_str);//注册session
	
	$img = imagecreate($width,$height);//生成图片
    imagecolorallocate($img, 255,255,255);  //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了 
//    $black = imagecolorallocate($img,127,157,185);        
	$black = imagecolorallocate($img, 0,0,0);     //此条及以下三条为设置的颜色
	$white = imagecolorallocate($img, 255,255,255);
	$gray = imagecolorallocate($img, 200,200,200);
	$red = imagecolorallocate($img, 255, 0, 0);
	
 
	
  	for($i = 0; $i < 10; $i++){  
          //杂点颜色  
           $noiseColor = imagecolorallocate($img,mt_rand(150,190),mt_rand(150,180), mt_rand(150,180));  
           for($j = 0; $j < 5; $j++) {  
               // 绘杂点  
       	 imagestring($img,$j,mt_rand(-10,$height), mt_rand(-20,$width),$codeSet[mt_rand(0, 27)],$noiseColor);  
			
			// 杂点文本为随机的字母或数字  
                 
           }  
      }  
	   
	   
	   
	for($i=0;$i<4;$i++){ //加入文字
		imagestring($img, mt_rand(3,6), $i*10+6, mt_rand(2,5), $rand_str[$i],imagecolorallocate($img,mt_rand(0,89),mt_rand(0,89),mt_rand(0,89)));
	 }


	
  //	imagerectangle($img,0,0,$width-1,$height-1,$black);//先成一黑色的矩形把图片包围
	
	if(function_exists("imagejpeg")){
		header("content-type:image/jpeg\r\n"); imagejpeg($img); 
	}else{ 
		header("content-type:image/png\r\n"); imagepng($img); 
	}
	
	imagedestroy($img);
	
}else{
	$_SESSION["randcode"]="1234";
	header("content-type:image/jpeg\r\n");
	$fp = fopen("./randcode.bmp","r");
	echo fread($fp,filesize("./validate.bmp"));
	fclose($fp);
}

