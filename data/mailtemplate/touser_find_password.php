<?php 
return array (
  'version' => '1.0',
  'subject' => '{$site_name}����:{$user.user_name}�޸���������',
  'content' => '<p>�װ���{$user.user_name}:</p>
<p style="padding-left: 30px;">����, ���ղ��� {$site_name} �������������룬������������ӽ������ã�</p>
<p style="padding-left: 30px;">��¼�����޸�������<a href="{$site_url}/index.php?app=find_password&amp;act=set_password&amp;id={$user.user_id}&amp;activation={$word}">{$site_url}/index.php?app=find_password&amp;act=set_password&amp;id={$user.user_id}&amp;activation={$word}</a></p>
<p>&nbsp;</p>
<p style="padding-left: 30px;">֧�������޸�������<a href="{$site_url}/index.php?app=find_password&amp;act=set_zf_password&amp;id={$user.user_id}&amp;activation={$word}">{$site_url}/index.php?app=find_password&amp;act=set_zf_password&amp;id={$user.user_id}&amp;activation={$word}</a></p>
<p>&nbsp;</p>
<p style="padding-left: 30px;">������ֻ��ʹ��һ��, ���ʧЧ����������. ������������޷�������뽫�������������(����IE)�ĵ�ַ���С�</p>
<p style="text-align: right;">{$site_name}</p>
<p style="text-align: right;">{$mail_send_time}</p>',
);
?>