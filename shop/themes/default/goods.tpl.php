<?php require 'header.php';?>
<? if($this->control=='category'){?>
    <div class="content-box">
        <ul class="sift-box">
            <? $_str='';if($_SERVER['QUERY_STRING']!=''){$_str='?'.$_SERVER['QUERY_STRING'];} $_href='/'.$this->control.'/'.$this->func.'/';?>
            <li><a <?=((int)$this->uri->get(2)==0)?'class="sift-a"':''?> href="<?=$_href?>0<?=$_str?>">最新</a></li>
            <li>｜</li>
            <li><a <?=((int)$this->uri->get(2)==1)?'class="sift-a"':''?> href="<?=$_href?>1<?=$_str?>">热门</a></li>
            <li>｜</li>
            <li><a <?=((int)$this->uri->get(2)==2)?'class="sift-a"':''?> href="<?=$_href?>2<?=$_str?>">销量</a></li>
        </ul>
        <ul class="goods-box">
            <? foreach($list as $key=>$value){?>
                <li <?if($key%4==3)echo 'class="cur-li"';?>>
                    <a href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><img class="good-img" src="<?=$value['default_image']?>" /></a>
                    <a class="good-title" href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><?=$value['goods_name']?></a>
                    <p>积分价：<span><?=$value['jifen_price']?>积分</span><br />VIP价：<span><?=$value['vip_price']?>积分</span></p>
                </li>
            <? }?>
        </ul>
        <?=$page?>
        <span style="line-height: 50px;">
        <? if($total<=0)echo '未找到相应商品！';?>
        </span>
    </div>
<? }elseif($this->control=='groupbuy'){?>
    <div class="content-box">
        <ul class="goods-box">
            <? foreach($list as $key=>$value){?>
                <li <?if($key%4==3)echo 'class="cur-li"';?>>
                    <a href="<?=$_G['domain_city']?>/groupbuy/<?=$value['group_id']?>" target="_blank"><img class="good-img" src="<?=$value['default_image']?>" /></a>
                    <a class="good-title" href="<?=$_G['domain_city']?>/groupbuy/<?=$value['group_id']?>" target="_blank"><?=$value['group_name']?></a>
                    <p>积分价：<span><?=$value['price']?>积分</span><br />VIP价：<span><?=$value['vip_price']?>积分</span><br />剩余：<span><?=$value['time']?></span></p>
                </li>
            <? }?>
        </ul>
        <?=$page?>
        <span style="line-height: 50px;">
        <? if($total<=0)echo '该店铺暂无团购商品！';?>
        </span>
    </div>
<? }?>
<?php require 'footer.php';?>