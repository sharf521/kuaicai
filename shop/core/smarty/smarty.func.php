<?php

//自定义一个函数
//模版调用形式{tag var='var' module="health" function="get_list"}
function smarty_tag($args,$smarty)
{
	$var=$args['var']?$args['var']:'var';
    $result=array();	
	if($args['module'] && $args['function'])
	{
		$data=$args;
		unset($data['var']);
		unset($data['module']);
		unset($data['function']);
		$result=m("{$args['module']}/{$args['function']}",$data);
	}	
	$smarty->assign($var,$result);
}
$smarty->registerPlugin('function','tag','smarty_tag');



