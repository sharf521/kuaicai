<?php

function getHTML($page,$host='keyword.discuz.com')
{
	/*$cnt=0;
	$result=file_get_contents($page);
	while($cnt < 3 && empty($result))
	{
	 	$cnt++;
		$result=file_get_contents($page);
	}
	return $result;*/
	$fp = fsockopen($host, 80, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{ 
		$out = "GET $page HTTP/1.0\r\n"; 
		$out .= "Host: $host\r\n"; 
		$out .= "Connection: Close\r\n\r\n"; 
		fputs($fp, $out); 
		$status = stream_get_meta_data($fp);
		while (!feof($fp)) {
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
		  break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		} 
		fclose($fp); 
	} 
	return $tmp;
}

	$func=$_REQUEST['func'];
	if(empty($func)) exit();
	if($func=='addjob')
	{
		include('./include/job.class.php');
		$job=new job();		
		if($job->pass($_POST))
		{
			$job->add($_POST);
			//showMsg('您的信息己提交成功！');exit();
			header("location:job.php");	
		}
		else
		{
			showMsg($gust->errmsg);exit();		
		}
		exit();
	}
	elseif($func=='gettag')
	{
		$q=urlencode(iconv('utf-8','gbk','女包'));
		$str="http://s.taobao.com/search?q=$q&commend=all&ssid=s5-e&search_type=item&sourceId=tb.index&initiative_id=tbindexz_20130221";
		
		$str=getHTML($str,'s.taobao.com');
		//echo $str;
		$str=iconv('gbk','utf-8',$str);

		$str=substr($str,strpos($str,'<div class="prop-item">')); 
		
		$str=substr($str,0,strpos($str,'<!-- Filter toolbar -->')); 


		//$split=strrpos($str,'<dt>你是不是想找：</dt>');
		$split=explode('<dl class="related-search related-byshop">',$str);
				
		echo $split[0];
		echo '[#]';
		echo $split[1];
		exit();	
	}
?>
  	