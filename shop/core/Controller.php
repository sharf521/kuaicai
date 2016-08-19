<?php  
if (!defined('ROOT'))  die('no allowed');
//import("ORG/User/Info.class.php");
//ORG/User/Info.class.php
function import($file)
{
	static $_file = array();
	if(file_exists(ROOT.'model/'.$file.'.class.php'))
	{
		$file=ROOT.'model/'.$file.'.class.php';
	}
	else
	{
		$file=$file;
	}
    if (isset($_file[$file]))
        return true;
    else
        $_file[$file] = true;
	require($file);
}
function new_model($name)
{
    static $_model = array();
    if(isset($_model[$name])){
        return $_model[$name];}
	import($name);
	$classname = $name."Class";
    if(class_exists($classname))
	{		
        $model             =   new $classname();
        $_model[$name]     =   $model;
        return $model;
    }else{
        //return false;
		die("error new_model {$name}");
    }
}
//模块
function m($url,$vars=array())
{
	$_url=explode('/',$url);
	
    $class	=new_model($_url[0]);
	$func	=$_url[1];
	if($func=='') $func='index';
    if($class && method_exists($class,$func))
	{
        return call_user_func(array(&$class,$func),$vars);
    }
	else
	{
		//return false;
        die("error class or method {$url}");
    }	
}

class Model
{
	public function __construct()
	{
		global $mysql,$dbfix;
		$this->mysql=$mysql;
		$this->dbfix=$dbfix;		
	}	
}

class Control
{
	public $base_url;
	public $template;
	public $user_id;
	public $username;
	public function __construct()
	{
		global $mysql,$dbfix,$uriClass;
		$this->mysql=$mysql;
		$this->dbfix=$dbfix;
		
		$this->uri=$uriClass;
		$this->base_url='/index.php/';
		if(strtolower(substr($_SERVER['HTTP_HOST'],0,4))=='wap.')
		{
			$this->is_wap=true;
		}
		else
		{
			$this->is_wap=false;	
		}
		$this->template=($this->is_wap===true)?'default_wap':'default';		
		$this->control		=($this->uri->get(0)!='')?$this->uri->get(0):'index';
		$this->func			=($this->uri->get(1)!='')?$this->uri->get(1):'index';
		$this->user_id		=getSession('user_id');
		if($this->user_id!="")
		{
			$this->user=m('user/one',array('user_id'=>$this->user_id));
		}
		$this->username		=getSession('username');
		$this->user_typeid	=getSession('usertype');
	}
	//显示模板
	public function view($tpl,$data=array())
	{
		if(!empty($data))
		{
			extract($data);	
		}
		global $_G;
		$tpldir='/themes/'.$this->template.'/';
		if(file_exists(ROOT.'themes/'.$this->template.'/'.$tpl.'.tpl.php'))
			require ROOT.'themes/'.$this->template.'/'.$tpl.'.tpl.php';
		else
			require ROOT.'themes/'.$this->template.'/'.$tpl.'.php';
	}
	public function base_url($control='')
	{
		return $this->base_url.$control;
	}
	
	public function anchor($control,$title='',$attributes = '')
	{
		$url=$this->base_url($control);
		if($attributes!='')
		{	
			if(is_array($attributes))
			{
				$str='';
				foreach($attributes as $k=>$v)
				{
					$str.=" {$k}=\"{$v}\"";		
				}
			}
			else
			{
				$str=$attributes;	
			}
		}
		
		return '<a href="'.$url.'" '.$str.'>'.$title.'</a>';
	}
	public function redirect($control)
	{
		$url=$this->base_url($control);
		header("location:$url");
		exit;
	}
	
	public function error()
	{
		echo '找不到当前网页';
	}
}
/*class Controller extends FrameWork
{
	private $control;
	private $func;
	public function __construct()
	{
		parent::__construct();
		
		//echo URI::get(0);			
	}
	public function display($template='',$data=array())
	{
		require ROOT.'core/libs/smarty/Smarty.class.php';
		$smarty = new Smarty;
		$smarty->setConfigDir(ROOT."core/libs/smarty/configs");					        //../../Smarty/configs;
		//$smarty->setCompileDir(ROOT."data/templates_c/default");
		//$smarty->setTemplateDir(APP_PATH."themes/default");
		//$smarty->force_compile = true;
		$smarty->debugging = false;
		$smarty->caching = false;
		$smarty->cache_lifetime = 120;
		//$smarty->setAllow_php_tag(true)   #设置开启识别php的标签
		require ROOT.'core/libs/smarty/smarty.func.php';		
		$smarty->setCompileDir(ROOT."data/templates_c/admin");
		$smarty->setTemplateDir(ROOT."");
		//$smarty->template_dir = ROOT.'themes/admin/';
		$smarty->assign("tpldir",'/themes/admin');
		
		if($data)
			$this->_G=array_merge($this->_G,$data);
		$smarty->assign('_G',$this->_G);
		$smarty->display($template);
	}
	//后台模板
	public function view($tpl,$data=array())
	{
		if(!empty($data))
		{
			extract($data);	
		}
		require ROOT.'themes/admin/'.$tpl.'.php';
	}
	public function show($tpl_main='',$data=array())
	{
		if(is_array($tpl_main))
		{
			$data=$tpl_main;
			$tpl_main='';
		}
		if($tpl_main=='')
		{
			$this->_G['module_tpl']='modules/'.$this->_G['class'].'/'.$this->_G['class'].'.html';
		}
		else
		{
			$this->_G['module_tpl']='themes/admin/'.$tpl_main;		
		}
		//extract($data);
		$this->display('themes/admin/main.html',$data);
	}

	
}*/