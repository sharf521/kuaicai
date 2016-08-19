<?php
if (!defined('ROOT'))  die('no allowed');
class groupbuy extends Control
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
            'store_id'=>$_G['shop']['store_id'],
            'page'=>(int)$_REQUEST['page'],
            'epage'=>12
        );
        $data=m('goods/getgroupbuy',$arr);
        foreach($data['list'] as $key=>$value)
        {
            //设置积分价   和  VIP 积分价
            $price=unserialize($value['spec_price']);
            $_price=reset($price);
            $data['list'][$key]['price']=number_format($_price['price']*2.52*1.31,5,'.','');
            $data['list'][$key]['vip_price']=number_format($_price['price']*2.52*1.21,5,'.','');

            //设置团购倒计时
            $time=$value['end_time']-time();
            if($time>=86400)
            {
                $day=floor($time/86400).'天';
                $time=($time%86400);
            }
            if($time>=3600)
            {
                $hours=floor($time/3600).'小时';
            }
            else
            {
                $hours='0小时';
            }
            $data['list'][$key]['time']=$day.$hours;
        }
        //print_r($data);
        $this->view('goods',$data);
	}
}
?>