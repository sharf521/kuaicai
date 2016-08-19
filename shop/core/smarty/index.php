<?php
require 'Smarty.class.php';

$smarty = new Smarty;
$smarty->setCompileDir("../../data/templates_c/default");
$smarty->setConfigDir("configs");//../../Smarty/configs;
$smarty->setTemplateDir("../../home/themes/default");

//$smarty->force_compile = true;
$smarty->debugging = true;
$smarty->caching = false;
$smarty->cache_lifetime = 120;
//$smarty->setAllow_php_tag(true)   #设置开启识别php的标签

require 'smarty.func.php';


$smarty->display('youhuihuodong.html');