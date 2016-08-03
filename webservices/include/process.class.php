<?
if (!defined('ROOT'))  die('Access Denied');//·ÀÖ¹Ö±½Ó·ÃÎÊ
require_once ROOT.'/include/tb.class.php';
class process extends tb
{	
	function process()
	{
		global $db;
		$this->db=$db;	
		$this->table='{process}';
		$this->fields=array('ProcessID','UserID','FromUserID','PlateNum','Mony','IncomeTime','Aside1','Aside2','Aside3','Aside4','Aside5','Aside6','Aside7','Aside8','Aside9','Aside10','status','upTime');
	}	
}
?>