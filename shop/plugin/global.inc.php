<?php
/**
 * 获取IP地址
 */
function ip() 
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip_address = $_SERVER["HTTP_CLIENT_IP"];
	}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip_address = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
	}else if(!empty($_SERVER["REMOTE_ADDR"])){
		$ip_address = $_SERVER["REMOTE_ADDR"];
	}else{
		$ip_address = '';
	}
	return $ip_address;
}
//将秒（非时间戳）转化成 ** 小时 ** 分
function changeTimeType($seconds)
{
	if($seconds==0) return '';
	if ($seconds>3600){
	$hours = intval($seconds/3600);
	$minutes = $seconds600;
	$time = $hours.":".gmstrftime('%M:%S', $minutes);
	}else{
	$time = gmstrftime('%H:%M:%S', $seconds);
	}
	return $time;
}

function check_var($post,$fields)
{
	foreach($post as $i=>$v)
	{
		if(!in_array($i,$fields))
		{
			unset($post[$i]);	
		}
	}
	return $post;
}

