<?php
/**
 * @author Tissot.Cai
 * @copyright ���Ŵ�������Ϣ�Ƽ����޹�˾
 * @version 1.0
 */
include("phpmailer/class.phpmailer.php");
class Mail {
    public static $msg = '';
    /**
     * �����ʼ�
     * @param $subject ����
     * @param $body �ʼ�����
     * @param $from ��������
     * @param $from_name �����ǳ�
     * @param $to �ʼ������� array(
     *      array(mail_address, mail_name)
     * )
     * @return bool
     */
    public static function Send ($subject, $body, $to) {
        global $mysql, $_G;

        $mail = new PHPMailer();
        $body = eregi_replace("[\]",'',$body);
        
        $mail->CharSet = 'utf-8';
        $mail->IsSMTP();
        # ���SMTP�������Ƿ���Ҫ��֤��trueΪ��Ҫ��falseΪ����Ҫ
        $mail->SMTPAuth   = 1?true:false;
        # �������SMTP������
        $mail->Host       = 'smtp.qq.com';
        # �����ͨSMTP��������䣻����һ��163�������
        $mail->Username   = '353889718@qq.com';
        # ��� ���������Ӧ������
        $mail->Password   = "qqww1122";
        # ���������Email
        $mail->From       = '353889718@qq.com';
        # ����������ǳƻ�����
        $mail->FromName   = '��ȯ�����̳�';
        # ����ʼ����⣨���⣩
        $mail->Subject    = $subject;
        # ��ѡ�����ı��������û�����������
        $mail->AltBody    = "";
        # �Զ����е�����
        $mail->WordWrap   = 50;

        $mail->MsgHTML($body);

        # �ظ������ַ
        $mail->AddReplyTo($mail->From, $mail->FromName);

        # ��Ӹ���,ע��·��
        # $mail->AddAttachment("http://rescdn.qqmail.com/zh_CN/htmledition/images/spacer104474.gif");
        # $mail->AddAttachment("http://rescdn.qqmail.com/zh_CN/htmledition/images/spacer104474.gif", "new.jpg");

        # �ռ��˵�ַ������һ�������˵������ַ������Ӷ�������������ռ��˳ƺ�
       // foreach ($to as $list) {
         //   $mail->AddAddress($list[0], $list[1]);
       // }
		
		$mail->AddAddress($to);
		
        # �Ƿ���HTML��ʽ���ͣ�������ǣ���ɾ������
        $mail->IsHTML(true);

        if(!$mail->Send()) {
          self::$msg = $mail->ErrorInfo;
          return false;
        }

        return true;
    }
}
?>
