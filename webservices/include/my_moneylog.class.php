<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class my_moneylog extends tb
{	
	function my_moneylog()
	{
		global $db;
		$this->db=$db;	
		$this->table='{my_moneylog}';
		$this->fields=array('id','user_id','user_name','s_and_z','money','money_dj','duihuanjifen','dongjiejifen','suoding_money','suoding_jifen','riqi','add_time','type','status','log_text','city','dq_money','dq_jifen','dq_money_dj','dq_jifen_dj','money_feiyong','jifen_feiyong');
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