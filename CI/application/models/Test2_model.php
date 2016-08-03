<?php
require_once 'TB_model.php';
class Test2_model extends TB_Model 
{
    public function __construct()
    {  
		$this->table='admin11';     
		$this->fields=array('id','createdate','deltime');
		parent::__construct();
    }
    
	
	public function tbs()
	{
		echo $this->table;	
	}
}
?>