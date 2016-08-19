<?php
class cityClass extends Model
{	
	public function __construct()
    {  
		parent::__construct();
    }
    //获取主站信息
    function getcity($data)
	{
		$select="*";
		$where="where 1=1";
		if(!empty($data['city_yuming']))
		{
			$where.=" and city_yuming like '%{$data['city_yuming']}%'";
		}
		$sql="select {$select} from {$this->dbfix}city {$where}";
        return $this->mysql->get_one($sql);
	}
    //获取店铺信息
    function getshop($data)
    {
        $select="*";
        $where="where 1=1";
        if(!empty($data['domain']))
        {
            $where.=" and domain='{$data['domain']}'";
        }
        if(!empty($data['store_id']))
        {
            $where.=" and store_id='{$data['store_id']}'";
        }
        $sql = "select {$select} from {$this->dbfix}store {$where}";
        $_data = $this->mysql->get_one($sql);

        //店铺等级换算
        if($_data)
        {
            $credit_value=$_data['credit_value'];
            if ($credit_value < 100)
            {
                $_data['credit_value'] = 'heart_' . (floor(($credit_value ) / 20) + 1) . '.gif';
            }
            elseif ($credit_value < 5000)
            {
                $_data['credit_value'] = 'diamond_' . (floor(($credit_value ) / 1000) + 1) . '.gif';
            }
            elseif ($credit_value < 10000)
            {
                $_data['credit_value'] = 'crown_1.gif';
            }
            elseif ($credit_value < 20000)
            {
                $_data['credit_value'] = 'crown_2.gif';
            }
            elseif ($credit_value < 50000)
            {
                $_data['credit_value'] = 'crown_3.gif';
            }
            elseif ($credit_value < 100000)
            {
                $_data['credit_value'] = 'crown_4.gif';
            }
            elseif ($credit_value < 100000)
            {
                $_data['credit_value'] = 'crown_5.gif';
            }
            else
            {
                $_data['credit_value'] = 'level_end.gif';
            }
        }
        return $_data;
    }
    //获取店主信息
    function getowner($data)
    {
        $select="*";
        $where="where 1=1";
        if(!empty($data['user_name']))
        {
            $where.=" and user_name='{$data['user_name']}'";
        }
        $sql = "select {$select} from {$this->dbfix}member {$where}";
        return $this->mysql->get_one($sql);
    }
    //收藏店铺
    function add_collect_store($data)
    {
        $_data = $this->mysql->one('collect',array('user_id'=>$data['user_id'],'item_id'=>$data['item_id']));
        if(!$_data)
        {
            $this->mysql->insert('collect',$data);
        }
    }
    //获取店铺商品数量
    function getcount($data)
    {
        $select="store_id, COUNT(goods_id) goods_count";
        $where="where if_show = 1 and closed = 0";
        if(!empty($data['store_id']))
        {
            $where.=" and store_id='{$data['store_id']}'";
        }
        $sql = "select {$select} from {$this->dbfix}goods {$where}";
        $_data=$this->mysql->get_one($sql);
        return  $_data['goods_count'];
    }
    //获取店铺商品总销量
    function getcountall($data)
    {
        $select="s.store_id, sum(og.quantity) goods_countall";
        $where="where 1=1 and o.status=40";
        if(!empty($data['store_id']))
        {
            $where.=" and s.store_id='{$data['store_id']}'";
        }
        $sql = "select {$select} from {$this->dbfix}store s left join {$this->dbfix}goods g on g.store_id=s.store_id left join {$this->dbfix}order_goods og on og.goods_id=g.goods_id left join {$this->dbfix}order o on o.order_id=og.order_id  {$where}";
        $_data=$this->mysql->get_one($sql);
        return  $_data['goods_countall'];
    }
    //获取店铺导航
    function getnavs($data)
    {
        $select="*";
        $where="where if_show = 1 and cate_id = -1";
        if(!empty($data['store_id']))
        {
            $where.=" and store_id='{$data['store_id']}'";
        }
        $sql = "select {$select} from {$this->dbfix}article {$where} order by sort_order asc";
        return $this->mysql->get_all($sql);
    }
    //获取店铺分类
    function getcate($data)
    {
        $where="where if_show=1";
        if(!empty($data['store_id']))
        {
            $where.=" and store_id={$data['store_id']}";
        }
        $sql="select * from {$this->dbfix}gcategory {$where} order by parent_id asc,sort_order asc";
        return $this->mysql->get_all($sql);
    }
    function getnavsone($data)
    {
        $where="where if_show = 1 and cate_id = -1";
        if(!empty($data['article_id']))
        {
            $where.=" and article_id='{$data['article_id']}'";
        }
        $sql = "select * from {$this->dbfix}article {$where} limit 1";
        return $this->mysql->get_one($sql);
    }

    //获取店铺承诺
    function getpromise($data)
    {
        $where="where 1=1";
        if(!empty($data['store_id']))
        {
            $where.=" and user_id='{$data['store_id']}'";
        }
        $sql = "select * from {$this->dbfix}article_user {$where} limit 1";
        return $this->mysql->get_one($sql);
    }

    //合作伙伴
    function getpriends($data)
    {
        $where="where 1=1";
        if(!empty($data['store_id']))
        {
            $where.=" and store_id={$data['store_id']}";
        }
        $sql="select * from {$this->dbfix}partner {$where} order by sort_order ASC";
        return $this->mysql->get_all($sql);
    }
}
?>