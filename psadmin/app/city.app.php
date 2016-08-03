<?php

/* 管理员控制器 */
class CityApp extends BackendApp
{
    var $_city_mod;
    

    function __construct()
    {
        $this->CityApp();
    }

    function CityApp()
    {
        parent::__construct();
        $this->_city_mod = & m('city');
        
    }
	
	
   
}

?>
