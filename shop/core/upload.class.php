<?php

class upload
{
	var $maxsize=4;// 上传图片最大2M
	//var $exts =array(".gif", ".png", ".jpg", ".jpeg", ".bmp");
	var $exts =array();
	var $field;
	var $path;
	var $name;
	var $sign='';
	var $tempath='data';//本网站上传目录
	var $thumb=0;
	var $width=100;
	var $height=100;
	function upload($data)
	{
		$this->field=$data['field'];
		$this->path=trim($data['path'],'/');
		$this->name=$data['name'];
		if(isset($data['exts']))	$this->exts=$data['exts'];
		if(isset($data['maxsize']))	$this->maxsize=(float)$data['maxsize'];

		$this->tempath=$_SERVER['DOCUMENT_ROOT'].'/'.trim($this->tempath,'/').'/'.$this->path;
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
			
			if(function_exists('exif_imagetype'))
			{
				if(exif_imagetype($_FILES[$this->field]['tmp_name'])<1)
				{						
					$arr['error']="not picture type ";
					return $arr;
				}
			}
			else
			{
				$ext=$this->getext($_FILES[$this->field]['name']);
				if(!in_array($ext,array(".gif", ".png", ".jpg", ".jpeg", ".bmp")))
				{
					$arr['error']="not picture type ";
					return $arr;
				}	
			}
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
		$filename=	$this->getfilename();
		$file=$this->tempath.'/'.$filename;
		if(! move_uploaded_file($_FILES[$this->field]['tmp_name'],$file))
		{
			$arr['status']=0;
			$arr['error']='can not move to tempath';
			return $arr;
		}
		$arr['status']=1;
		$arr['file']='/data/'.$this->path.'/'.$filename;
		return $arr;
	}
	
}