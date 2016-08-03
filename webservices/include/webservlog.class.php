<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class webservlog extends tb
{	
	function webservlog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{webservlog}';
		$this->fields=array('id','user_id','user_name','date','jifen','zengjin','yujifen','createdate','status');
	}

	

	function edit($post)
	{			
		$post=$this->set($post);
	
		$user_id=intval($post['id']);
		
		$this->update($post,'id='.$id);
	}
	function delete($id)
	{
		$this->db->query("delete from $this->table where id=$id limit 1");
	}
}
?>