<?php
if (!defined('ROOT'))  die('no allowed');
class index extends Control
{
	public function __construct()
    {
        parent::__construct();
    }
	function index()
	{
        global $_G;
        //取得推荐商品
        $arr=array(
			'if_show'=>1,
            'closed'=>0,
            'recommended'=>1,
            'store_id'=>$_G['shop']['store_id'],
		);
		$data['tuijian']=m('goods/getindexlist',$arr);

        //取得最新商品
        unset($arr['recommended']);
        $data['zuixin']=m('goods/getindexlist',$arr);

        //合作伙伴
        $data['friends']=m('city/getpriends',array('store_id'=>$_G['shop']['store_id']));

		$this->view('index',$data);	
	}
}
?>