<?php
if (!defined('ROOT'))  die('no allowed');
class about extends Control
{
	public function __construct()
    {
        parent::__construct();
    }
	function index()
	{
        global $_G;
        $photo=array(
            'link'		=>"http://".$_SERVER['HTTP_HOST'],
            'dir'		=>'./data/erweima/',
            'filename'	=>$_SERVER['HTTP_HOST'].".png"
        );
        $data['filename']=substr(qrcode($photo['link'],$photo['dir'],$photo['filename']),1);
        $certification=explode(',',$_G['shop']['certification']);
        foreach($certification as $value)
        {
            $data[$value]=$value;
        }
        $this->view('about',$data);
	}
}
?>