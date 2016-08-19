<?php require 'header.php';?>
    <div class="content-box">
        <div class="store-box">
            <h1>店铺信息</h1>
            <div class="store">
                <? $sgrade=array('1'=>'扶持版','2'=>'精英版');?>
                <ul style=" width:300px;">
                    <li>店主： <?=$_G['shop']['owner_name']?></li>
                    <li>店铺等级： <?=$sgrade[$_G['shop']['sgrade']]?></li>
                    <li>商品数量：<?=$_G['shop']['goods_count']?></li>
                    <li>所在地区： <?=$_G['shop']['region_name']?></li>
                    <li>创店时间：<?=date('Y-m-d',$_G['shop']['add_time'])?></li>
                    <li>详细地址：<?=$_G['shop']['address']?></li>
                    <li>联系电话： <?=$_G['shop']['tel']?></li>
                </ul>
                <ul class="renzheng-box">
                    <li>信用度：<img src="/themes/images/<?=$_G['shop']['credit_value']?>" align="absmiddle"/></li>
                    <li class="renzheng-li"><span>认证：</span>
                        <p>
                            <img src="<?=$tpldir?>images/shiming<?=($autonym)?1:0?>.png" align="absmiddle" />&nbsp;&nbsp;<?=($autonym)?'已':'未'?>实名认证<br />
                            <img src="<?=$tpldir?>images/shidian<?=($material)?1:0?>.png" align="absmiddle" />&nbsp;&nbsp;<?=($material)?'已':'未'?>实体店认证
                        </p>
                    </li>
                    <? if($_G['shop']['im_qq']){?>
                        <li>QQ客服：<?=$_G['shop']['im_qq']?></li>
                    <? }?>
                    <? if($_G['shop']['im_ww']){?>
                        <li>旺旺客服：<?=$_G['shop']['im_ww']?></li>
                    <? }?>
                    <? if($_G['shop']['im_msn']){?>
                        <li>MSN客服：<?=$_G['shop']['im_msn']?></li>
                    <? }?>
                </ul>
                <div>
                    <img src="<?=$filename?>" />
                    <p>扫一扫，进入店铺</p>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <? if($_G['shop']['description']!=''){?>
        <div class="store-box">
            <h1>店铺简介</h1>
            <div class="store">
                <?=$_G['shop']['description']?>
            </div>
        </div>
        <? }?>
    </div>
<?php require 'footer.php';?>