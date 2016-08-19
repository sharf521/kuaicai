<?php
class goodsClass extends Model
{	
	public function __construct()
    {  
		parent::__construct();
    }
    //首页商品列表
    function getindexlist($data)
    {
        $select="goods_id,goods_name,default_image,price,jifen_price,vip_price,store_id";
        $where=" where 1=1";
        if(isset($data['if_show']))
        {
            $where.=" and if_show={$data['if_show']}";
        }
        if(isset($data['closed']))
        {
            $where.=" and closed={$data['closed']}";
        }
        if(!empty($data['recommended']))
        {
            $where.=" and recommended={$data['recommended']}";
        }
        if(!empty($data['store_id']))
        {
            $where.=" and store_id={$data['store_id']}";
        }
        $sql = "select {$select} from {$this->dbfix}goods {$where} order by goods_id desc limit 8";
        $list = $this->mysql->get_all( $sql);
        global $_G;
        foreach($list as $key=>$value)
        {
            if(strpos(strtolower($value['default_image']),'http://') ===false)
            {
                $list[$key]['default_image']=$_G['domain_city'].'/'.$value['default_image'];
            }
        }
        return $list;
    }
    //获取商品列表
    function getlist($data)
    {
        global $pager;
        global $_G;
        $_select="g.goods_id,g.goods_name,g.default_image,g.price,g.jifen_price,g.vip_price,g.store_id";
        $where=" where 1=1";
        if(isset($data['if_show']))
        {
            $where.=" and g.if_show={$data['if_show']}";
        }
        if(isset($data['closed']))
        {
            $where.=" and g.closed={$data['closed']}";
        }
        if(!empty($data['recommended']))
        {
            $where.=" and g.recommended={$data['recommended']}";
        }
        if(!empty($data['category']))
        {
            $where.=" and (gc.cate_id={$data['category']} or gc.parent_id={$data['category']})";
        }
        if(!empty($data['keyword']))
        {
            $where.=" and g.goods_name like '%{$data['keyword']}%'";
        }
        if(!empty($data['store_id']))
        {
            $where.=" and g.store_id={$data['store_id']}";
        }
        $sql = "select SELECT from {$this->dbfix}goods g left join {$this->dbfix}category_goods cg on cg.goods_id=g.goods_id left join {$this->dbfix}gcategory gc on gc.cate_id=cg.cate_id left join {$this->dbfix}goods_statistics gs on gs.goods_id=g.goods_id {$where} ORDER LIMIT";
        $_order=isset($data['order'])?' order by '.$data['order']:'order by g.goods_id desc';
        //总条数
        $row=$this->mysql->get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];
        $epage = empty($data['epage'])?12:$data['epage'];
        $page=$data['page'];
        if(!empty($page))
        {
            $index = $epage * ($page - 1);
        }
        else
        {
            $index=0;$page=1;
        }
        if($index>$total){$index=0;$page=1;}
        $limit = " limit {$index}, {$epage}";
        $list = $this->mysql->get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        foreach($list as $key=>$value)
        {
            if(strpos(strtolower($value['default_image']),'http://') ===false)
            {
                $list[$key]['default_image']=$_G['domain_city'].'/'.$value['default_image'];
            }
        }
        global $pager;
        $pager->page=$page;
        $pager->epage=$epage;
        $pager->total=$total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }
    //获取团购列表
    function getgroupbuy()
    {
        global $pager;
        global $_G;
        $_select="gb.group_id,gb.group_name,gb.spec_price,gb.end_time,g.default_image";
        $where=" where 1=1";
        if(!empty($data['store_id']))
        {
            $where.=" and gb.store_id={$data['store_id']}";
        }
        $sql = "select SELECT from {$this->dbfix}groupbuy gb left join {$this->dbfix}goods g on gb.goods_id=g.goods_id {$where} ORDER LIMIT";
        $_order=isset($data['order'])?' order by '.$data['order']:'order by gb.start_time desc';
        //总条数
        $row=$this->mysql->get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];
        $epage = empty($data['epage'])?12:$data['epage'];
        $page=$data['page'];
        if(!empty($page))
        {
            $index = $epage * ($page - 1);
        }
        else
        {
            $index=0;$page=1;
        }
        if($index>$total){$index=0;$page=1;}
        $limit = " limit {$index}, {$epage}";
        $list = $this->mysql->get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        foreach($list as $key=>$value)
        {
            if(strpos(strtolower($value['default_image']),'http://') ===false)
            {
                $list[$key]['default_image']=$_G['domain_city'].'/'.$value['default_image'];
            }
        }
        global $pager;
        $pager->page=$page;
        $pager->epage=$epage;
        $pager->total=$total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }
}