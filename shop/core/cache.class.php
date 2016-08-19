<?php
class Cache 
{
	private $dir = "data/cache/";//定义缓存目录	
	private $key='c_a_sss';	// 文件名 md5加密 密钥
	
	function set_dir($dirpath)
	{
		$this->dir=$dirpath;
		$this->make_dir($this->dir);
	}
	function read($key,$minutes=1)
	{
		$filename=$this->get_filename($key);
		if($datas = @file_get_contents($filename))
		{
		  $datas = unserialize($datas);
		  if(time() - $datas['time'] < $minutes*60)
		  {
		  	return $datas['data'];
		  }
		}
		return false;
	}
 
	function write($key,$data)
	{		
		$filename=$this->get_filename($key);
		if($handle = fopen($filename,'w+'))
		{
			$datas = array('data'=>$data,'time'=>time());
			flock($handle,LOCK_EX);
			$rs = fputs($handle,serialize($datas));
			flock($handle,LOCK_UN);
			fclose($handle);
			if($rs!==false){return true;  }
		}
		return false;
	}
	function clear_all()
	{
		$dir=$this->dir;
		$this->del_file($dir);	
	}
 
 	private function get_filename($key)
	{
		return $this->dir.$key.'_'.md5($key.$this->key);
	}
	private function make_dir($path)
	{
		if (! file_exists ( $path ))
		{
			if (! mkdir ( $path, 0777,true)) die ( '无法创建缓存文件夹' . $path );
		}
	}
	private function del_file($dir)
	{ 
		if (is_dir($dir)) 
		{ 
			$dh=opendir($dir);//打开目录 //列出目录中的所有文件并去掉 . 和 .. 
			while (false !== ( $file = readdir ($dh))) { 
				if($file!="." && $file!="..") {
					$fullpath=$dir."/".$file; 
					if(!is_dir($fullpath)) { 
						unlink($fullpath);
					} else { 
						$this->del_file($fullpath); 
					} 
				}
			}
			closedir($dh); 
		} 
	}
}


/*

//分库分表算法 
function calc_hash_db($u, $s = 4)
{ 
$h = sprintf("%u", crc32($u)); 
$h1 = intval(fmod($h, $s)); 
return $h1; 
}

echo calc_hash_db('dfasdkfjakdsjf111');
echo '<hr>';

echo calc_hash_tbl('dfasdkfjakdsjf111');

function calc_hash_tbl($u, $n = 256, $m = 16) 
{ 
$h = sprintf("%u", crc32($u)); 
$h1 = intval($h / $n); 
$h2 = $h1 % $n; 
$h3 = base_convert($h2, 10, $m); 
$h4 = sprintf("%02s", $h3); 
return $h4; 
}
*/