<?php
if (!defined('ROOT'))  die('no allowed');
class ajax extends Control
{
	public function __construct()
    {
        parent::__construct();
    }

    function add_collect_store()
    {
        $store_id=$this->uri->get(2);
        $user_id=$_SESSION['user_info']['user_id'];
        $arr=array(
            'user_id'=>$user_id,
            'item_id'=>$store_id,
            'type'=>'store',
            'add_time'=>time(),
        );
        m('city/add_collect_store',$arr);
        echo 'ok';
    }
}