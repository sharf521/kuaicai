<?php
require_once ROOT.'/include/tb.class.php';
class systemClass extends tb
{	
	public function __construct()
    {  
		$this->table='system';
		$this->fields=array('id','name','code','value','typeid','style','showorder');
		parent::__construct();
    }
	function getlist()
	{
		$_system = $this->get_all();
		foreach ($_system as $key => $value){
			$system[$value['code']] = $value['value'];
			//$system_name[$value['code']] = $value['name'];
		}
		return $system;
		//$_G['system_name']=$system_name;
	}
	
	function edit($post,$id)
	{
		$this->update($post,"id=$id",1);
	}
}