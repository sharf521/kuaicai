<?php
/**
 * @author Tissot.Cai
 * @copyright 厦门贷齐乐信息科技有限公司
 * @version 1.0
 */
include_once 'mail.php';

$subject='测试';
$body='asdfkasdjfklasdjf没试测';
$to='373455742@qq.com';
if (Mail::send($subject, $body,  $to)) 
{
    echo '发送成功！';
}
else{
    echo Mail::$msg;
}
?>
