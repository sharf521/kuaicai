<?php

class AdApp extends MallbaseApp
{
    function index()
    {
	
	$this->adv_mod=& m('adv');
	$type = empty($_GET['type']) ? 0 : (int)$_GET['type'];	
	$row=$this->adv_mod->get_cityrow();
	$city_id=$row['city_id'];
	$time=date('Y-m-d H:i:s');
	$result=$this->adv_mod->getrow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type=$type and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $im=$result['image'];
	   $lianjie=$result['lianjie'];
	   if(empty($result['image']))
	   {
			if($type==4)//顶部横幅广告
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/top.jpg'/></a>";
			if($type==5)//中间横幅广告
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/hf.jpg'/></a>";
			if($type==6 || $type==7  || $type==8 || $type==12)//四个图片广告
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/demo.jpg'/></a>";
			if($type==2 || $type==3)//2F 4F
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/ggt.jpg'/></a>";
			
	   }
	   else
	   {
	   		$url="<a href='$lianjie' target='_blank'><img src='$im'/></a>";
	   }
	    $content=AddSlashes($url); 
	  	echo $this->htmltojs($content);
	  	//echo "document.write($url)";
}
function htmltojs($str)
{
  $re='';
  $str= split("\r\n",$str);
  foreach($str as $v)
  {
	  $re.="document.writeln('".trim($v)."');\r\n";
  }  
  return $re;
}



}
?>