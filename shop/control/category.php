<?php
if (!defined('ROOT'))  die('no allowed');
class category extends Control
{
	public function __construct()
    {
        parent::__construct();
    }
    function error()
    {
        $category=(int)$this->uri->get(1);
        $order=(int)$this->uri->get(2);
        global $_G;
        $arr=array(
            'if_show'=>1,
            'closed'=>0,
            'store_id'=>$_G['shop']['store_id'],
            'category'=>$category,
            'keyword'=>$_GET['keyword'],
            'page'=>(int)$_REQUEST['page'],
            'epage'=>12
        );
        if($order==1)
        {
            $arr['order']='gs.views desc';
        }
        elseif($order==2)
        {
            $arr['order']='gs.sales desc';
        }
        $data=m('goods/getlist',$arr);
        $this->view('goods',$data);
    }
    //推荐商品列表
    function recommended()
    {
        $order=(int)$this->uri->get(2);
        global $_G;
        $arr=array(
            'if_show'=>1,
            'closed'=>0,
            'store_id'=>$_G['shop']['store_id'],
            'keyword'=>$_GET['keyword'],
            'recommended'=>1,
            'page'=>(int)$_REQUEST['page'],
            'epage'=>12
        );
        if($order==1)
        {
            $arr['order']='gs.views desc';
        }
        elseif($order==2)
        {
            $arr['order']='gs.sales desc';
        }
        $data=m('goods/getlist',$arr);
        $this->view('goods',$data);
    }
}
?>