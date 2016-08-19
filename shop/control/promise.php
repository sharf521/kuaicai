<?php
if (!defined('ROOT'))  die('no allowed');
class promise extends Control
{
    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        global $_G;
        $data=$_G['promise'];
        $this->view('article',$data);
    }
}