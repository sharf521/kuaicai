<?php
error_reporting (1);
header('Content-Type:text/plain; charset=utf-8');
if (!empty($_POST['UGC']) && !empty($_POST['UGL'])) {
	include_once(dirname(__FILE__) . '/geshi.php');
	if (get_magic_quotes_gpc()) $_POST['UGC'] = stripslashes($_POST['UGC']);
	$_POST['UGC'] = stripslashes($_POST['UGC']);
	$_POST['UGL'] = strtolower($_POST['UGL']);
	
	$GeSHi = new GeSHi($_POST['UGC'], $_POST['UGL']);
	//$GeSHi->enable_classes();
	$GeSHi->set_header_type(GESHI_HEADER_NONE);
	$GeSHi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
	$GeSHi->enable_keyword_links(false);
	$GeSHi->set_overall_style('');
	$GeSHi->set_tab_width(4);
	echo $GeSHi->parse_code();
}
?>