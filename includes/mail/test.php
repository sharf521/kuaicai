<?php
/**
 * @author Tissot.Cai
 * @copyright ���Ŵ�������Ϣ�Ƽ����޹�˾
 * @version 1.0
 */
include_once 'mail.php';

$subject='����';
$body='asdfkasdjfklasdjfû�Բ�';
$to='373455742@qq.com';
if (Mail::send($subject, $body,  $to)) 
{
    echo '���ͳɹ���';
}
else{
    echo Mail::$msg;
}
?>
