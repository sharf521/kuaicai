<?
defined('SiteName') or exit('Access Denied');
require_once ROOT.'/include/tb.class.php';
class module extends tb
{	
	var $typeid;
	function module()
	{
		global $db;
		$this->db=$db;		
		$this->table='{menu}';
		$this->fields=array('id','name','code','file','remark','parentid','path','fullpath','level','template','purview','link','showorder','deltime','status');
	}
	function getSubCategory($id)
	{
		$field=implode(',',$this->fields);
		$strw="status=1 and parentid=$id";
		if($this->typeid!=0)	$strw.=" and typeid=$this->typeid";
	//	echo "select $field from $this->table where $strw order by showorder";
		return $this->db->get_all("select $field from $this->table where $strw order by showorder");	
	}
	function getMenu($id,$purview)
	{
		$purview.='0';
		$field=implode(',',$this->fields);
		$strw="status=1 and parentid=$id and id in($purview)";
		if($this->typeid!=0)	$strw.=" and typeid=$this->typeid";
		//echo "select $field from $this->table where $strw order by showorder";
		return $this->db->get_all("select $field from $this->table where $strw order by showorder");	
	}
	function getParentId($id)
	{
		$row=$this->db->get_one("select parentid from $this->table where status=1 and id=$id limit 1");	
		return $row['parentid'];
	}
	function getIdByPath($path)
	{
		$row=$this->db->get_one("select id from $this->table where path='$path' limit 1");	
		return $row['id'];
	}
	function pass($post)
	{
		$name=checkPost(strip_tags($post['name']));
		if(empty($name)) return $this->_('名称不能为空！');
		return true;
	}
	function set($post)
	{
		$post['name']=checkPost(strip_tags($post['name']));
		$post['code']=checkPost(strip_tags($post['code']));
;
		$post['remark']=checkPost(strip_tags($post['remark']));
		$post['template']=checkPost(strip_tags($post['template']));

		$post['link']=checkPost(strip_tags($post['link']));
		return $post;	
	}
	function add($post)
	{
		$post=$this->set($post);
		if(empty($post['parentfullpath']))
			$post['fullpath']=$post['name'];//顶级
		else
			$post['fullpath']=$post['parentfullpath'].'->'.$post['name'];		
		$post['parentid']=intval($post['parentid']);
		$post['level']=intval($post['level']);
		$post['path']='';
		$insertid=$this->insert($post);
		$path=$post['parentpath'].$insertid.',';
		$this->db->query("update $this->table set path='$path',showorder=$insertid where id=$insertid limit 1");			
	}
	function edit($post)
	{
		$post=$this->set($post);
		$this->id=intval($post['id']);
		$this->update($post);
		if($post['name']!=$post['oldname'])//更新fullpath
		{
			$sql="update $this->table set fullpath=replace(fullpath,'$post[oldname]','$post[name]') where fullpath like '$post[oldname]%'";
			$this->db->query($sql);
		}
	}
	function getListArray()
	{
		$array=array();
		$this->fields=array('id','fullpath');
		$result=$this->getall(0,1000);
		foreach($result as $row)
		{
			$array[$row['id']]=$row['fullpath'];
		}
		return $array;
	}
	function echoOption($pid,$id=0,$path='')
	{
		$this->fields=array('id','path','name','level');
		$result=$this->getSubCategory($pid);
		$count=count($result);
		$num=1;
		foreach($result as $row)
		{
			$str='';
			for($i=1;$i<$row['level'];$i++)
			{
				$str.= '&nbsp;&nbsp;';	
			}		
			if($row['level']==1)
				$name=$row['name'];
			else
			{
				if($num==$count)
					$name=$str.'└'.$row['name'];
				else
					$name=$str.'├'.$row['name'];
			}	
			$sel=$row['id']	 ==$id	?'selected':'';
			if($sel!='selected')
				$sel=$row['path']==$path?'selected':'';
			echo "<option value=".$row['path']." $sel>$name</option>\r\n";	
			$this->echoOption($row['id'],$id,$path);
			$num++;
		}
		$result=null;
	}
	
	/*
	function echoSubList($id)
	{
		$result=$this->getSubCategory($id);
		foreach($result as $row)
		{
			$id=$row['id'];
			echo "&nbsp;&nbsp;<a href='articlelist.php?id=$id'>".$row['name']."</a>";	
		}	
	}*/
	function getSubLi($id)
	{
		$str='';
		$result=$this->getSubCategory($id);
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$link=$row['link'];
				if(empty($link)) $link='/articlelist/'.$row['id'].'.html';
				if($id==$row['id'])
					$str.="<li class='this'><a href='$link'>$row[name]</a></li>";
				else
					$str.="<li><a href='$link'>".$row['name']."</a></li>";	
			}	
		}
		else
		{
			$parentid=$this->getParentId($id);
			$result=$this->getSubCategory($parentid);
			foreach($result as $row)
			{
				$link=$row['link'];
				if(empty($link)) $link='/articlelist/'.$row['id'].'.html';
				if($id==$row['id'])
					$str.="<li class='this'><a href='$link'>$row[name]</a></li>";
				else
					$str.="<li><a href='$link'>".$row['name']."</a></li>";	
			}
		}
		return $str;
	}
	function getHeaderTitle($categorypath)
	{
		$arr_ids=explode(",",$categorypath);
		array_shift($arr_ids);//除移第一个元素0
		array_pop($arr_ids);
		$headtitle='';
		foreach($arr_ids as $id)
		{
			$row=$this->getone($id);
			$link=$row['link'];
			if(empty($link)) $link='/barlist/'.$row['id'].'.html';
			//if(empty($link)) $link='/'.$row['htmlpath'];
			if($headtitle=='')		
				$headtitle.='<a href="'.$link.'">'.$row['name'].'</a>';
			else 
				$headtitle.=' > <a href="'.$link.'">'.$row['name'].'</a>';
			$row=null;
		}
		return $headtitle;	
	}
	
	
	function getSubA($id)
	{
		$str='';
		$result=$this->getSubCategory($id);

		foreach($result as $row)
		{
			$link=$row['link'];
			if(empty($link)) $link='/articlelist/'.$row['id'].'.html';
		
			$str.="<a href='$link'>".$row['name']."</a>";	
		}	
		
		return $str;
	}
	function delete()
	{
		$this->db->query("update $this->table set status=-1,deltime=now()  where id=$this->id  limit 1");	
	}
}
?>