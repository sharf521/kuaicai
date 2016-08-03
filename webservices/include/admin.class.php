<?
if (!defined('ROOT'))  die('Access Denied');//防止直接访问
require_once ROOT.'/include/tb.class.php';
class admin extends tb
{	
	var $db;
	var $fields=array();
	var $errmsg;
	var $table;
	var $id=0;	
	function admin()
	{
		global $db;
		$this->db=$db;
		$this->table='{admin}';
		$this->fields=array('id','typeid','userid','password','username','status','times','purview','createdate','deltime');
	}
	function existUserId($userid,$where='')
	{
		if(!empty($where))	$where=" and $where";
		$row=$this->db->get_one("select id from {$this->table} where userid='$userid' $where limit 1");
		if($row)
			return 1;
		else
			return 0;
	}
	function login($userid,$password)
	{
		$sql="select id,userid,password,username,status,typeid,purview from {$this->table} where status>0 and userid='$userid' and password='".md5($password."art")."' limit 0,1";
		$row=$this->db->get_one($sql);
		if($row)
		{		
			$_SESSION["admin_id"]=$row["id"];
			$_SESSION["admin_userid"]=$row["userid"];
			$_SESSION["admin_username"]=$row["username"];
			$_SESSION["admin_typeid"]=$row['typeid'];
			$_SESSION["admin_purview"]=$row['purview'];
			$_SESSION['purview']=array();
			$result1=$this->db->get_all("select file from {menu} where id in(".$row['purview']."0)");
			foreach($result1 as $row1)
			{
				array_push($_SESSION['purview'],$row1['file']);
			}
			return $row['id'];
		}
		else
		{
			return -1;
		}	
	}
	function logout()
	{
		$_SESSION["admin_id"]="";
		$_SESSION["admin_userid"]="";
		$_SESSION["admin_username"]='';	
		$_SESSION["admin_typeid"]='';	
		$_SESSION["admin_purview"]='';	
		unset($_SESSION['purview']);
	}
	function pass($post) 
	{
		if(!is_array($post)) return false;		
		$userid=checkPost(strip_tags($post["userid"]));		
		if(strlen($userid)<3 || strlen($userid)>18)		return $this->_('用户名长度请控制在3到18位！');
		$password=checkPost(strip_tags($post["password"]));
		$password1=checkPost(strip_tags($post["password1"]));
		if($post['func']=='add')
		{
			if($this->existUserId($userid))	return $this->_('用户名己存在！');	
			if($password!=$password1)						return $this->_('输入的两次密码不一致！');
			if(strlen($password)<6 || strlen($password)>18)	return $this->_('密码长度请控制在6到18位！');
		}
		else
		{
			$id=intval($post['id']);
			if($this->existUserId($userid,"id<>$id"))	return $this->_('用户名己存在！');	
			if($password!='' || $password1!='')
			{
				if($password!=$password1)						return $this->_('输入的两次密码不一致！');
				if(strlen($password)<6 || strlen($password)>18)	return $this->_('密码长度请控制在6到18位！');
			}
		}	
		return true;
	}
	function set($post)
	{
		$post['purview']=implode(',',$post['purview']).',';		
		$post['userid']=checkPost(strip_tags($post["userid"]));
		$post['username']=checkPost(strip_tags($post["username"]));	
		return $post;	
	}	
	function add($post)
	{
		$post=$this->set($post);	

		$post['password']=checkPost(strip_tags($post["password"]));		
		$post['password']=md5($post['password'].'art');
		
		$post['createdate']=date("Y-m-d H:i:s");
		$post['times']=1;
		$post['status']=1;
		$this->insert($post);
		return $this->db->get_insert_id();
	}
	function edit($post)
	{	
		$post=$this->set($post);	
		$this->id=intval($post['id']);

		if(empty($post['password']))
		{
			unset($post['password']);	
		}
		else
		{
			$post['password']=checkPost(strip_tags($post["password"]));
			$post['password']=md5($post['password'].'art');		
		}

		$this->update($post,'id='.$this->id);
	}	
}
?>