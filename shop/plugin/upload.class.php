<?php
/*
	$data=array('field'=>'upfile',
		'path'=>$u,
		'name'=>''
		);
	$up=new upload($data);
	$arr=$up->uploadfile();
	if($arr['status']==1)
*/
//define('ROOT', dirname($_SERVER['SCRIPT_FILENAME']).'/');
//$_SERVER['DOCUMENT_ROOT'];
//echo realpath(dirname(__FILE__).'/../');

class upload
{
	//var $posturl='http://img.test.cn/upload.php';
	//var $url='http://img.zhuzhan.cn/';
	var $posturl='http://pictures.5-58.com/upload.php';
	var $url='http://img.5-58.com/';
	var $maxsize=2;// 上传图片最大2M
	//var $exts =array(".gif", ".png", ".jpg", ".jpeg", ".bmp");
	var $exts =array();
	var $field;
	var $path;
	var $name;
	var $sign='fenecll_upload_img';
	var $tempath='data';//本网站上传目录
	var $thumb=0;
	var $width=100;
	var $height=100;
	function upload($data)
	{
		$this->field=$data['field'];
		$this->path=$data['path'];
		$this->name=$data['name'];
		if(isset($data['exts']))	$this->exts=$data['exts'];
		if(isset($data['maxsize']))	$this->maxsize=(float)$data['maxsize'];
		if(intval($data['thumb'])===1)
		{
			$this->thumb=1;
			if(intval($data['width'])>0)  $this->width =intval($data['width']);
			if(intval($data['height'])>0) $this->height=intval($data['height']);
		}		
		/*$this->tempath=$_SERVER['DOCUMENT_ROOT'];
		if($this->path !='')
		{
			$this->tempath.='/'.trim($this->path,'/');
		}*/	
		$this->tempath=$_SERVER['DOCUMENT_ROOT'].'/'.trim($this->tempath,'/');
	}	
	private function check()
	{
		$arr['status']=0;
		if(empty($this->field))
		{
			$arr['error']="Upload field is empty ";
			return $arr;
		}
		if(empty($this->exts))
		{
			/*if(exif_imagetype($_FILES[$this->field]['tmp_name'])<1)
			{						
				$arr['error']="not picture type ";
				return $arr;
			}*/
		}
		else
		{
			$ext=$this->getext($_FILES[$this->field]['name']);
			if(!in_array($ext,$this->exts))
			{
				$arr['error']="error file type ";
				return $arr;
			}
		}
		if($_FILES[$this->field]['size']>1048576 * $this->maxsize)//2M
		{
			$arr['error']="Upload file is too large！max is 2M";
			return $arr;
		}
		if(!file_exists($this->tempath))
		{
			if(!mkdir($this->tempath,0777,true))
			{
				$arr['status']=0;
				$arr['error']='Can not create tempath directory';
				return $arr;	
			}
		}
		return true;
	}
	private function getext($filename)
	{
		return 	strtolower(strrchr($filename,"."));
	}
	private function getfilename()
	{
		$name	=empty($this->name)?time().rand(1000,9999):$this->name;
		$ext	=$this->getext($_FILES[$this->field]['name']);
		return $name.$ext;
	}
	function save()
	{		
		$check=$this->check();
		if($check !==true){return $check;}		
		$file=$this->tempath.'/'.$this->getfilename();
		if(! move_uploaded_file($_FILES[$this->field]['tmp_name'],$file))
		{
			$arr['status']=0;
			$arr['error']='can not move to tempath';
			return $arr;
		}
		return $this->curl_file($file,1);
	}
	private function curl_file($file,$is_del=1)
	{
		$post=array();
		$post['act']		='upload';
		$post['sign']		=$this->sign;
		$post['field']		='@'.$file;			
		$post['filename']	=basename($file);
		$post['filepath']	=$this->path;
		if($this->thumb===1)
		{
			$post['thumb'] =1;
			$post['width'] =$this->width;
			$post['height']=$this->height;	
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->posturl);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl);
		curl_close($curl);
		if($is_del)	unlink($file);
		$result=json_decode($result,true);
		if($result['status']==1)
		{
			$arr['status']=1;
			$arr['file']=$this->url.$result['file'];
			if($this->thumb===1) 
				$arr['thumb']=$this->url.$result['thumb'];
			return $arr;
		}
		else
		{
			$arr['status']=0;
			$arr['error']=$result['error'];
			return $arr;
		}	
	}
	
	function getfilelist()
	{			
		$post=array();
		$post['act']		='getlist';
		$post['sign']		=$this->sign;	
		$post['filepath']	=$this->path;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->posturl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl);
		curl_close($curl);
		$files=json_decode($result,true);		
		foreach($files as $i=>$v)
		{
			$files[$i]=$this->url.$v;
		}
		return $files;	
	}	
}