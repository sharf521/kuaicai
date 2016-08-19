<? if (!defined('ROOT'))  die('no allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?=$_G['city']['city_title']?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Description" content="<?=$_G['city']['city_desc']?>" />
    <meta name="Keywords" content="<?=$_G['city']['city_keywords']?>" />
    <link rel="stylesheet" href="<?=$tpldir?>style.css"/>
    <script type="text/javascript" src="/plugin/js/jquery.js"></script>
</head>
<body>

<div class="top-box">
    <div class="top-nav">
        <ul class="leftbox">
            <li><img src="<?=$tpldir?>images/sian.jpg"/><a href="<?=$_G['domain_city']?>?app=message&act=newpm">站内消息</a></li>
            <li>|</li>
            <li><img src="<?=$tpldir?>images/help.jpg"/><a href="<?=$_G['domain_city']?>?app=article&code=$acc_help">帮助中心</a></li>
            <li>|</li>
            <li class="left-li"><img src="<?=$tpldir?>images/shi.jpg" /><a href="<?=$_G['domain_city']?>?app=article&cate_id=23">视频</a></li>
        </ul>
        <ul class="rightbox">
            <li>您好  <?=($_SESSION['user_info'])?iconv("gb2312","utf-8//IGNORE",$_SESSION['user_info']['user_name']):'游客'?>，欢迎来到<?=$_G['city']['city_name']?>！&nbsp;&nbsp;&nbsp;&nbsp;</li>
            <? if($_SESSION['user_info']){?>
                <li><a class="land" href="<?=$_G['domain_city']?>?app=member">用户中心</a></li>
                <li><a href="<?=$_G['domain_city']?>?app=member&act=logout">退出</a></li>
            <? }else{?>
                <li><a class="land" href="<?=$_G['domain_city']?>?app=member&act=login">登录</a></li>
                <li><a href="<?=$_G['domain_city']?>?app=member&act=register">注册</a></li>
            <? }?>
            <li>|</li>
            <li><img src="<?=$tpldir?>images/weixin.jpg" /><a href="<?=$_G['domain_city']?>?app=article&cate_id=27">微信公众账号</a></li>
            <li>|</li>
            <li><img src="<?=$tpldir?>images/phone.png" /><a href="<?=$_G['domain_city']?>?app=article&cate_id=24">手机商城</a></li>
        </ul>
    </div>
</div>
<div class="mid-box">
    <div class="mid-nav">
        <a href="<?=$_G['domain_city']?>"><img class="logo" src="<?=$_G['city']['city_logo']?>" alt="<?=$_G['city']['city_title']?>"/></a>
        <div class="g-w-s">
            <a href="<?=$_G['domain_city']?>?app=cart"><img src="<?=$tpldir?>images/che.jpg" />购物车</a>
            <a href="<?=$_G['domain_city']?>?app=buyer_order"><img src="<?=$tpldir?>images/dingdan.png"/>我的订单</a>
            <a href="<?=$_G['domain_city']?>?app=my_favorite"><img src="<?=$tpldir?>images/shoucang.png"/>收藏夹</a>
        </div>
    </div>
</div>
<div class="details-box">
    <div class="details">
        <div class="name">
            <a href="/">
                <? if($_G['shop']['store_logo']){?>
                    <img class="touxiang" src="<?=$_G['shop']['store_logo']?>"/>
                <? }else{?>
                    <img class="touxiang" src="/themes/images/store_logo.gif"/>
                <? }?>
            </a>
            <h2><?=$_G['shop']['store_name']?></h2>
            <a class="name-a shoucang" href="javascript:collect_store()">收藏</a>
            <? if($_G['shop']['im_qq']){?>
                <a class=" siliao" href="http://wpa.qq.com/msgrd?v=3&uin=<?=$_G['shop']['im_qq']?>&site=qq&menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=1:<?=$_G['shop']['im_qq']?>:4" alt="QQ"></a>
            <? }?>
            <? if($_G['shop']['im_ww']){?>
                <a class=" siliao" target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=<?=urlencode($_G['shop']['im_ww'])?>&site=cntaobao&s=2&charset=UTF-8" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?=urlencode($_G['shop']['im_ww'])?>&site=cntaobao&s=2&charset=UTF-8" alt="点击这里给我发消息" /></a>
            <? }?>
        </div>
        <div class="xinyong-box">
            <? $sgrade=array('1'=>'扶持版','2'=>'精英版');?>
            <ul class="xinyong">
                <li>信用度：<img src="/themes/images/<?=$_G['shop']['credit_value']?>" align="absmiddle"/></li>
                <li class="xinyong_you">总销量：<?=($_G['shop']['goods_countall'])?$_G['shop']['goods_countall']:'0'?></li><br />
                <li>店铺等级：<?=$sgrade[$_G['shop']['sgrade']]?></li>
                <li class="xinyong_you">商品数量：<?=($_G['shop']['goods_count'])?$_G['shop']['goods_count']:'0'?></li>
            </ul>
        </div>
        <div class="search">
            <form method="get" action="/category">
                <input class="input1" type="text" name="keyword" value="<?=$_GET['keyword']?>" />
                <input class="but" type="submit" value="搜本站" />
            </form>
        </div>
    </div>
</div>
<div class="clear"></div>
<script type="text/javascript">
    $(function () {
        if ($.browser.msie && $.browser.version.substr(0, 1) < 7) {
            $('li').has('ul').mouseover(function () {
                $(this).children('ul').show();
            }).mouseout(function () {
                $(this).children('ul').hide();
            })
        }
    });
</script>
<div class="nav-box">
    <ul class="nav1" id="menu">
        <li><a href="/">店铺首页</a></li>
        <li>
            <a class="fenlei" href="/category">商品分类</a>
            <ul>
                <? foreach($_G['cate'] as $key=>$value){?>
                <li><a href="/category/<?=$key?>"><?=$value['cate_name']?></a>
                    <? if($value['son']){?>
                        <ul>
                            <? foreach($value['son'] as $key_s=>$value_s){?>
                            <li><a href="/category/<?=$key_s?>"><?=$value_s['cate_name']?></a> </li>
                            <? }?>
                        </ul>
                    <? }?>
                </li>
                <? }?>
            </ul>
        </li>

        <? foreach($_G['navs'] as $value){?>
        <li><a href="/article/index/<?=$value['article_id']?>"><?=$value['title']?></a></li>
        <? }?>
        <li><a href="/groupbuy">团购活动</a></li>
        <li><a href="/about">店铺信息</a></li>
        <? if($_G['promise']){?>
        <li><a href="/promise">承诺奖励</a></li>
        <? }?>
    </ul>
</div>

<script>
    /* 收藏店铺 */
    var user_id='<?=$_SESSION['user_info']['user_id']?>';
    function collect_store()
    {
        if(user_id == '')
        {
            alert('收藏店铺须先登录！');
        }
        else
        {
            var ajax=$.ajax({url:"/index.php/ajax/add_collect_store/<?=$_G['shop']['store_id']?>",async:false});
            if(ajax.responseText =='ok')
            {
                alert('收藏店铺成功！');
            }
        }
    }
</script>