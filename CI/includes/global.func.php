<?php

function getuserno($user_id)
{
	$len=strlen($user_id);
	if($len<8)
	{	$str='';
		for($i=0;$i<8-$len;$i++)
		{
			$str.='0'; 	
		}
		return $str.$user_id;
	}
	else
	{
		return $user_id;
	}
}