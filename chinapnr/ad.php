<?
require './init.php';
$user_id=(int)$_GET['id'];
$host=$_SERVER['HTTP_HOST'];

function htmltojs($str)
{
  $re='';
  $str= split("\r\n",$str);
  foreach($str as $v)
  {
	  $re.="document.writeln('".trim($v)."');\r\n";
  }  
  return $re;
}

?>