<?php
require_once 'TB_model.php';
class Test_model extends TB_Model 
{
    public function __construct()
    {  
		$this->table='admin';     
		$this->fields=array('id','typeid','userid','password','username','status','times','purview','createdate','deltime');
		parent::__construct();
    }
    
    public function test()
    {
        $a=$this->getone("id=1");
		print_r($a);
		
		$b=$this->get_all("select * from {admin}");
		print_r($b);
		$post=array(
			''
		);
    }
	
	public function tbs()
	{
		echo $this->table;	
	}
}
?>