<?php	 
function pageTop($title)
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',"\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml">',"\n";
	echo "<head>\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />'."\n";
	echo '<link href="style.css" type="text/css" rel="stylesheet" />'."\n";
	echo '<script language="javascript" src="./include/js/global.js"></script>';
	echo '<title>'.$title.'</title>'."\n";
	echo '</head>'."\n";
	echo '<body>'."\n";
}
function getHeadTitle($arr)
{
	$str='您的位置：';
	foreach ($arr as $i=>$v)
	{
		if(empty($v))
			$str.=$i;
		else
			$str.="<a href='$v'>$i</a> -> ";
	}
	return $str;
}
function echoTypeSel($array,$level)
{
	if(empty($array)) return false;
	echo "<select name='typeid'>";
	echo '<option value="" selected>下拉选择类型</option>';
	foreach($array as $k=>$v)
	{						
		$index=$k+1;
		$sel=$index==$level?'selected':'';								
		echo "<option value='$index' $sel>$v</option>";						
	}
	echo "</select>";
}
function echoTypeRadio($array,$level)
{
	if(empty($array)) return false;
	if(empty($level)) $level=1;
	foreach($array as $k=>$v)
	{						
		$index=$k+1;
		$sel=$index==$level?'checked':'';					
		echo "<input type='radio' value='$index' name='typeid' id='typeid$index' $sel><label for='typeid$index'>$v</label>";			
	}
}
function getChangeTr()
{
	echo 'bgcolor="#FFFFFF" onMouseMove="javascript:this.bgColor=\'#f1f4fb\'" onMouseOut="javascript:this.bgColor=\'#FFFFFF\'"';
}
function echoTh($array)
{
	echo '<tr bgcolor="#FFFFFF" onMouseMove="javascript:this.bgColor=\'#f1f4fb\'" onMouseOut="javascript:this.bgColor=\'#FFFFFF\'">';
	foreach($array as $v)
	{
		echo "<td class='th'>$v</td>";	
	}
	echo '</tr>';	
}
//获取内容的第一个图片的缩略图
function get_thumb($content,$width=100,$height=100)
{
	require_once ROOT.'/include/img.func.php';
	$img_array = array();
	preg_match_all("/src=[\"|'|\s]{0,}(([^>]*)\.(gif|jpg|png))/isU",stripslashes($content),$img_array);
	$img_array = array_unique($img_array[1]);
	foreach ($img_array as $v)
	{	
		if(strpos($v,'uploadfiles/'))
		{
			//取出图片路径前有引号-->去除..
			if(substr($v,0,1)=="'" || substr($v,0,1)=='"')
			{
				$v=substr($v,1,strlen($v)-1);
			}			
			$v=str_replace('/uploadfiles/','../uploadfiles/',$v);
			$name=file_basename($v);				
			$thumbname=$name."_thumb.".getextension($name);//生成缩略图文件名	
			$thumbpath='uploadfiles/'.date('Y').date('m');
			if(!(file_exists('../'.$thumbpath) && is_dir('../'.$thumbpath)))
			{
				mkdir('../'.$thumbpath,0777);
			}			
			resize($v,$width,$height,'../'.$thumbpath.'/',$thumbname);	//生成缩略图
			break;
		}
	}
	return '/'.$thumbpath.'/'.$thumbname;
}
function save_remote_img($content)
{
	$content = stripslashes($content);
	/*$pattern="/<img.*?src=.*?(http:\/\/.*?\.(jpg|png|gif)).*?[\/]?>/"; */
	$img_array = array();
	preg_match_all("/src=[\"|'|\s]{0,}(http:\/\/([^>]*)\.(gif|jpg|png))/isU",$content,$img_array);
	$img_array = array_unique($img_array[1]);
	$host=$_SERVER['HTTP_HOST'];
	foreach ($img_array as $v)
	{	
		if(!strpos($v,$host))
		{
			$img=file_get_contents($v);
			if(!empty($img))
			{					
				$name=file_basename($v);
				$pic_name=''.time().rand(1000,9999);
				$newname=$pic_name.".".getextension($name);//下载远程图片的文件名					
				$picpath='uploadfiles/'.date('Y').date('m');
				if(!(file_exists('../'.$picpath) && is_dir('../'.$picpath)))
				{
					mkdir('../'.$picpath,0777);
				}
				file_put_contents('../'.$picpath.'/'.$newname,$img);
				$name='/'.$picpath.'/'.$newname;
				$content=str_replace($v,$name,$content);					
			}
		}
	}
	$content = addslashes($content);
	return $content;	
}
function checkpurview()
{
	global $db,$a_purview;
	$file=$_REQUEST['act'];
/*	$link= "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$link=substr(strrchr($link, "?"), 0);
	$sql="select id from {menu} where file='$file' limit 1";
	$row=$db->get_one($sql);
	if($row)
	{
		$id=$row['id'];
		$purview=explode(',',$a_purview);
		if(!in_array($id,$purview))
		{
			showMsg('权限错误！');exit();		
		}
	}
	unset($row);*/
	//if($file!='login','modifypwd','manage'')
	if(!in_array($file,array('login','modifypwd','manage','user_buy')) && $_SESSION['admin_id']!=1)
	{
		if(!in_array($file,$_SESSION['purview']))
		{
			showMsg('权限错误！');exit();		
		}	
	}	
}
function adminlog($remark,$type=1)
{
	global $db,$a_id,$a_userid;
	$arr=array(
		'type'=>$type,
		'remark'=>$remark,
		'createdate'=>date('Y-m-d H:i:s'),
		'a_id'=>$a_id,
		'a_name'=>$a_userid,
		'status'=>1
	);
	$db->insert('{adminlog}',$arr);
}
?>