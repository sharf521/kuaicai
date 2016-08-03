<?php
//参数排序
function argSort($para) 
{
	ksort($para);
	reset($para);
	return $para;
}
//验证签名
function md5Verify($para,$key='',$sign) 
{
	$mysgin = md5_sign($para,$key);
	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
function md5_sign($para,$key='')
{
	$prestr=getsignstr($para);
	$sign=md5($prestr.$key);
	return $sign;
}
function getsignstr($para)
{
	$para=argSort($para);
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		//$arg.=$key."=".$val."&";
		$arg.=$key."=".urlencode($val)."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);
	
	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
}
//流水，金额，钱管家交易唯一标识
function pay_rate_do($OrdId,$OrdAmt,$TrxId,$GateId)
{
	global $mysql;
	$row=$mysql->get_one("select pid,status,usrsn from {order} where orderid='$OrdId' limit 1");
	$pid=$row['pid'];
	$site=$mysql->get_one("select name,RetUrl,BgRetUrl,rtype,uid,users,pkey from {psite} where id=$pid limit 1");
	$site['usrsn']=$row['usrsn'];
	
	
	//判断缓存中是否有 创建交易cache缓存文件	
	$path="../data/pay_cache/".date("Y-m")."/";
	if (!file_exists($path)) 
	{ 
		mkdir($path, 0777);
	}
	$file =$path.$OrdId;
	$fp = fopen($file , 'w+');
	chmod($file, 0777);	  
	if(flock($fp , LOCK_EX | LOCK_NB)) //设定模式独占锁定和不堵塞锁定
	{
		//#############开始处理###########################
		if($row['status']==0)
		{	
			if($site['rtype']==1)
			{					
				if($OrdAmt<200)
				{
					$fei=$OrdAmt*0.01;				
				}
				else
				{	
					if($GateId=='61')//PNR钱管家
					{
						$fei=$OrdAmt*0.009;	
					}
					elseif(in_array($GateId,array(25,29,27,28,12,13,33)))
					{
						$fei=$OrdAmt*0.006;
					}
					else
					{
						$fei=$OrdAmt*0.008;	
					}							
				}
				if(in_array($GateId,array(69,70,71,72,74,75,76,78,81)))
				{
					if($fei<6) $fei=6;
				}
				$fei=round_money($fei,2);
				$sql="update {order} set status=1,fei=$fei,ordamt=$OrdAmt,trxid='$TrxId' where orderid=$OrdId limit 1";
				$mysql->query($sql);
				$m=$OrdAmt-$fei;
				userlog($site['uid'],$m,$OrdId,$pid,1);
				//分润
				if($OrdAmt>=200)
				{
					fen_users($site['users'],$OrdId,$pid,$OrdAmt);	
				}	
			}
			elseif($site['rtype']==2)
			{
				
				if($OrdAmt<200)
				{
					$fei=$OrdAmt*0.01;		
				}
				else
				{
					if(in_array($GateId,array(25,29,27,28,12,13,33)))
					{
						$fei=$OrdAmt*0.003;
					}
					else
					{
						$fei=$OrdAmt*0.01;//只开通了上面7家银行	
					}	
				}
				$fei=round_money($fei,2);			
				
				$sql="update {order} set status=1,fei=$fei,ordamt=$OrdAmt,trxid='$TrxId' where orderid=$OrdId limit 1";
				$mysql->query($sql);
				$m=$OrdAmt-$fei;
				userlog($site['uid'],$m,$OrdId,$pid,1);
				//分润
				if($OrdAmt>=200)
				{
					fen_users($site['users'],$OrdId,$pid,$OrdAmt);	
				}
			}		
			//更新网站充值总额
			$mysql->query("update {psite} set money=money+$OrdAmt where id=$pid limit 1");
		}
		//##############end################################
		flock($fp , LOCK_UN);  
	}
	fclose($fp);	
	return $site;	
}


//分润
function fen_users($users,$OrdId,$Pid,$OrdAmt)
{
	if(!empty($users))
	{
		$users=explode(';',$users);//id:0.94;id:0.001
		foreach($users as $user)
		{
			list($uid,$rate)=explode(':',$user);				
			$m=floor($OrdAmt*(float)$rate*100)/100;
			if($m!=0)
			{	
				userlog($uid,$m,$OrdId,$Pid,3);
			}
		}
		$users=null;
	}
}

function userlog($uid,$m,$OrdId,$Pid,$typeid=1)
{
	global $mysql;
	$uid=(int)$uid;
	$row_user=$mysql->get_one("select money from {user} where id=$uid limit 1");			
	if($row_user)
	{			
		$mysql->query("update {user} set money=money+$m where id=$uid limit 1");
		$arr=array(
			'pid'=>$Pid,
			'typeid'=>$typeid,
			'userid'=>$uid,		
			'money'=>$m,
			'money_dj'=>$m+$row_user['money'],
			'addtime'=>date('Y-m-d H:i:s'),
			'orderid'=>$OrdId
		);
		$mysql->db_insert('userlog',$arr);	
	}
	$row_user=null;	
}

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
function sock_open($url,$data=array())
{	
	$row = parse_url($url);
	$host = $row['host'];
	$port = isset($row['port']) ? $row['port']:80;
	
	$post='';//要提交的内容.
	foreach($data as $k=>$v)
	{
		//$post.=$k.'='.$v.'&';
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$fp = fsockopen($host, $port, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{
		$header = "POST $url HTTP/1.1\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: MSIE\r\n";
		$header .= "Host: $host\r\n"; 
		$header .= "Content-Length: ".strlen($post)."\r\n";
		$header .= "Connection: Close\r\n\r\n"; 
		$header .= $post."\r\n\r\n";		
		fputs($fp, $header); 
		//$status = stream_get_meta_data($fp);
		
		while (!feof($fp)) 
		{
			$tmp .= fgets($fp, 128);
		}
		fclose($fp);
		$tmp = explode("\r\n\r\n",$tmp);
		unset($tmp[0]);
		$tmp= implode("",$tmp);
		
		/*while (!feof($fp)) 
		{
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
			break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		}
		fclose($fp); */
	}
	return $tmp;
}
//处理小数位
function round_money($money,$type=1)
{
	$money=(float)$money;
	if($type==1)//舍去第3位
	{	
		$pri=substr(sprintf("%.3f", $money), 0, -1);		
	}
	else
	{
		$pri=ceil($money*100)/100;
		if($pri<0.01) $pri=0.01;
	}
	return $pri;
}

