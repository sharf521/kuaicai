<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class adminlog extends tb
{	
	function adminlog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{adminlog}';
		$this->fields=array('id','a_id','a_name','remark','createdate','status','deltime','type');
	}
	

	function edit($post)
	{			
		$post=$this->set($post);
	
		$user_id=intval($post['id']);
		
		$this->update($post,'id='.$id);
	}
	function delete($user_id)
	{
		//$this->db->query("delete from $this->table where user_id=$user_id limit 1");
	}
}
?>