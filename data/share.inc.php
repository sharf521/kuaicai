<?php 
return array (
  1 => 
  array (
    'title' => '百度收藏',
    'link' => 'http://cang.baidu.com/do/add?it={$title}++++++&iu={$link}&fr=ien#nw=1',
    'type' => 'collect',
    'sort_order' => 255,
    'logo' => 'data/system/baidushoucang.gif',
  ),
  2 => 
  array (
    'title' => '人人网',
    'link' => 'http://share.renren.com/share/buttonshare.do?link={$link}&title={$title}',
    'type' => 'share',
    'sort_order' => 255,
    'logo' => 'data/system/renren.gif',
  ),
  3 => 
  array (
    'title' => 'QQ书签',
    'link' => 'http://shuqian.qq.com/post?from=3&title={$title}++++++&uri={$link}&jumpback=2&noui=1',
    'type' => 'collect',
    'sort_order' => 255,
    'logo' => 'data/system/qqshuqian.gif',
  ),
  4 => 
  array (
    'title' => '开心网',
    'link' => 'http://www.kaixin001.com/repaste/share.php?rtitle={$title}&rurl={$link}',
    'type' => 'share',
    'sort_order' => 255,
    'logo' => 'data/system/kaixin001.gif',
  ),
  5 => 
  array (
    'title' => '新浪微博',
    'link' => 'http://service.weibo.com/share/share.php?url={$link}&title={$title}',
    'type' => 'share',
    'sort_order' => 255,
    'logo' => 'data/files/mall/share/5.png',
  ),
  6 => 
  array (
    'title' => 'QQ空间',
    'link' => 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={$link}&title={$title}',
    'type' => 'share',
    'sort_order' => 255,
    'logo' => 'data/files/mall/share/6.png',
  ),
);
?>