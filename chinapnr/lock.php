<?
$path="../data/pay_cache/";
if (!file_exists($path)) 
{ 
	mkdir($path, 0777);
}
$OrdId=12334;
$file =$path.$OrdId;   
//判断缓存中是否有交易cache文件
//创建交易cache缓存文件
$fp = fopen($file , 'w+');
chmod($file, 0777);	  
if(flock($fp , LOCK_EX | LOCK_NB)) //设定模式独占锁定和不堵塞锁定
{
	echo "充值成功，请点击返回查看充";
	flock($fp , LOCK_UN);     
}
fclose($fp);  
?>