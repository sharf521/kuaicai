<?php  
if (!defined('ROOT'))  die('no allowed');
class URI
{
	public $param=array();
	public function get($key)
	{
		return isset($this->param[$key])?$this->param[$key]:'';
	}
	private function safe_str($str)
	{
		if(!get_magic_quotes_gpc())	{
			if( is_array($str) ) {
				foreach($str as $key => $value) {
					$str[$key] = safe_str($value);
				}
			}else{
				$str = addslashes($str);
			}
		}
		return $str;
	}
	function __construct()
	{
		//index.php/class/func
		$_path=(isset($_SERVER['PATH_INFO']))?$_SERVER['PATH_INFO']:@getenv('PATH_INFO');
		$arr=explode("/",trim($_path,'/'));
		$pre='';
		//index.php/class/func/a/1/b/2  --> $_GET[a]=1 $_GET[b]=2
		foreach($arr as $i=>$v)
		{
			$v=strip_tags(trim($v));
			$par[$i]=$v;
			//index.php/class/func/a/1/b/2
			//a和b位置 不能为数字
			if($i>1 && $i%2==0 && !is_numeric($v))
			{
				$v=addslashes(strip_tags(trim($arr[$i+1])));
				$par[$arr[$i]]=$v;
				$_GET[$arr[$i]] =$v;
			}
		}
		$this->param=$par;
		foreach(array('_GET','_POST','_COOKIE','_REQUEST') as $key)
		{
			if (isset($$key)){
				foreach($$key as $_key => $_value){
					$_value=strip_tags($_value);
					$$key[$_key] = $this->safe_str($_value);
				}
			}
		}		
		/*define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
		echo $_SERVER['PATH_INFO'].'<br>';
		echo strip_tags($_SERVER['REQUEST_URI']);
		exit;
		preg_replace_callback('/(\w+)\/([^\/]+)/', array($this, 'param_call'), $path);
		$_GET   =  array_merge($this->arr,$_GET);*/
		
/*		//获取url中参数?aa=1&bb=2&cc=3
		$request_uri = explode("?",$_SERVER['REQUEST_URI']);
		if(isset($request_uri[1])){
			$rewrite_url = explode("&",$request_uri[1]);
			foreach ($rewrite_url as $key => $value){
				$_value = explode("=",$value);
				if (isset($_value[1])){
					$_GET[$_value[0]] = strip_tags($_value[1]);
				}
			}
		}*/
	}	
	function param_call($match)
	{	
		$this->arr[$match[1]]=strip_tags($match[2]);
	} 
}

/*

$_path_info = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');	
	echo $_path_info;
	$var  =  array();
	function aaa($match){global $var;
	
	 $var[$match[1]]=strip_tags($match[2]);}
	 preg_replace_callback('/(\w+)\/([^\/]+)/', 'aaa', $_path_info);  
*/