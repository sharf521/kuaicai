<?php
if (!defined('ROOT'))  die('no allowed');
class article extends Control
{
	public function __construct()
    {
        parent::__construct();
    }
	function index()
	{
		$id=(int)$this->uri->get(2);
        $data=m('city/getnavsone',array('article_id'=>$id));
        $this->view('article',$data);
	}
}
?>