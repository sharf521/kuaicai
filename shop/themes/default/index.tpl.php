<?php require 'header.php';?>
<div class="content-box">
    <div style="margin-top: 1px;">
        <? if($_G['shop']['store_banner']){?>
            <img src="<?=$_G['shop']['store_banner']?>" style="width: 1000px;"/>
        <? }else{?>
            <img src="/themes/images/store_banner.jpg" style="width: 1000px;"/>
        <? }?>
    </div>

    <div>
        <div class="title">
            <img src="<?=$tpldir?>images/tui.png" />
            <a href="/category/recommended">more</a>
        </div>
        <ul class="goods-box">
            <? foreach($tuijian as $key=>$value){?>
            <li <?if($key%4==3)echo 'class="cur-li"';?>>
                <a href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><img class="good-img" src="<?=$value['default_image']?>" /></a>
                <a class="good-title" href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><?=$value['goods_name']?></a>
                <p>积分价：<span><?=$value['jifen_price']?>积分</span><br />VIP价：<span><?=$value['vip_price']?>积分</span></p>
            </li>
            <?if($key%4==3){?><div class="clear"></div><? }?>
            <? }?>
        </ul>
    </div>
    <div>
        <div class="title">
            <img src="<?=$tpldir?>images/xin.png" />
            <a href="/category">more</a>
        </div>
        <ul class="goods-box">
            <? foreach($zuixin as $key=>$value){?>
                <li <?if($key%4==3)echo 'class="cur-li"';?>>
                    <a href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><img class="good-img" src="<?=$value['default_image']?>" /></a>
                    <a class="good-title" href="<?=$_G['domain_city']?>/goods/<?=$value['goods_id']?>" target="_blank"><?=$value['goods_name']?></a>
                    <p>积分价：<span><?=$value['jifen_price']?>积分</span><br />VIP价：<span><?=$value['vip_price']?>积分</span></p>
                </li>
                <?if($key%4==3){?><div class="clear"></div><? }?>
            <? }?>
        </ul>
    </div>
    <div class="clear"></div>
    <? if($friends){?>
    <div class="partner-box">
		<span>合作伙伴 : </span>  
        <div class="par-a">
            <? foreach($friends as $value){?>
            <a href="<?=$value['link']?>" target="_blank"><?=$value['title']?></a>
            <? }?>
        </div>
    </div>
    <? }?>
</div>
<?php require 'footer.php';?>

