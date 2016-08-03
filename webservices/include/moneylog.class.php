<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class moneylog extends tb
{	
	function moneylog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{moneylog}';
		$this->fields=array('id','user_id','user_name','type','s_and_z','money','money_dj','jifen','jifen_dj','time','zcity','dq_money','dq_jifen','dq_money_dj','dq_jifen_dj','beizhu');
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