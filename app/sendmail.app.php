<?php

/**
 *    Sendmail发送邮件
 *
 *    @author    Garbin
 *    @usage    none
 */
class SendmailApp extends FrontendApp
{
    function index()
    {
        $send_result     = $this->_sendmail(true);

        echo 'var send_result=' . ecm_json_encode($send_result) . ';';
    }
	
	//消息提醒
	function message()
	{
		//$message =& m('message');
//$message->tuijian(60,658,657,1233038534);
//exit();
		$user_id=(int)$this->visitor->get('user_id');

		if($user_id!=0)
		{
			$location=$_REQUEST['location'];
			$first=(int)$_REQUEST['first'];
			$message =& m('message');
			
			if(strrpos($location,'message')===false)
			{
				if($first==1)
				{
					$row=$message->getRow("select count(*) count from ".DB_PREFIX."message where to_id='$user_id' and new=1");
					$count=$row['count'];
					
					if($count>0)
					{
						echo "您有{$count}条新消息，<a href='/index.php?app=message&act=newpm' target='_blank'>点击查看</a><br>";	
					}
				}
				else
				{
					$riqi=time()-3600;
					$result=$message->getAll("select msg_id,content from ".DB_PREFIX."message where to_id='$user_id' and new=1 and last_update>$riqi");
				
					foreach($result as $row)
					{
						$id=$row['msg_id'];
						echo '&nbsp;　&nbsp;'.iconv('gb2312','utf-8',$row['content'])." <a href='/index.php?app=message&act=view&msg_id=$id' target='_blank'>点击查看</a><br>";
					}
					$result=null;
				}
			}
			
			
			
			
		}

	}
	
	
}

?>