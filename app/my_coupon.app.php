<?php

class My_couponApp extends MemberbaseApp 
{
    var $_user_mod;
    var $_store_mod;
    var $_coupon_mod;
    
    function index()
    {
        $page = $this->_get_page(10);
        $this->_user_mod =& m('member');
        $this->_store_mod =& m('store');
        $this->_coupon_mod =& m('coupon');
		$kaiguan=$this->_coupon_mod->kg();
		$this->assign('kaiguan',$kaiguan);
        $msg = $this->_user_mod->findAll(array(
            'conditions' => 'user_id = ' . $this->visitor->get('user_id'),
            'count' => true,
            'limit' => $page['limit'],
            'include' => array('bind_couponsn' => array())));
        $page['item_count'] = $this->_user_mod->getCount();
        $coupon = array();
        $coupon_ids = array();
        $msg = current($msg);
       if (!empty($msg['coupon_sn']))
       {
           foreach ($msg['coupon_sn'] as $key=>$val)
           {
               $coupon_tmp = $this->_coupon_mod->get(array(
                'fields' => "this.*,store.store_name,store.store_id",
                'conditions' => 'coupon_id = ' . $val['coupon_id'],
                'join' => 'belong_to_store',
                ));
                $coupon_tmp['valid'] = 0;
                $time = gmtime();
                if (($val['remain_times'] > 0) && ($coupon_tmp['end_time'] == 0 || $coupon_tmp['end_time'] > $time))
                {
                    $coupon_tmp['valid'] = 1;
                }
               $coupon[$key] = array_merge($val, $coupon_tmp);
           }
       }
       $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                            LANG::get('my_coupon'), 'index.php?app=my_coupon',
                            LANG::get('coupon_list'));
        $this->_curitem('my_coupon');

       $this->_curmenu('coupon_list');
       $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
       $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('coupon_list'));
       $this->_format_page($page);
       $this->assign('coupons', $coupon);
       $this->display('my_coupon.index.html');
    }
    
    function bind()
    {
        if (!IS_POST)
        {
            header("Content-Type:text/html;charset=" . CHARSET);
            $this->display('my_coupon.form.html');
        }
        else 
        {
            $coupon_sn = isset($_POST['coupon_sn']) ? trim($_POST['coupon_sn']) : '';
            if (empty($coupon_sn))
            {
                $this->pop_warning('coupon_sn_not_empty');
                exit;
            }
            $coupon_sn_mod =&m ('couponsn');
            $coupon = $coupon_sn_mod->get_info($coupon_sn);
            if (empty($coupon))
            {
                $this->pop_warning('involid_data');
                exit;
            }
            $coupon_sn_mod->createRelation('bind_user', $coupon_sn, $this->visitor->get('user_id'));
            $this->pop_warning('ok', 'my_coupon_bind');
            exit;
        }
    }
    
    function drop()
    {
        if (!isset($_GET['id']) && empty($_GET['id']))
        {
            $this->show_warning("involid_data");
            exit;
        }
        $ids = explode(',', trim($_GET['id']));
        $couponsn_mod =& m('couponsn');
        $couponsn_mod->unlinkRelation('bind_user', db_create_in($ids, 'coupon_sn'));
        if ($couponsn_mod->has_error())
        {
            $this->show_warning($couponsn_mod->get_error());
            exit;
        }
        $this->show_message('drop_ok',
            'back_list', 'index.php?app=my_coupon');
    }
    
    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'coupon_list',
                'url'   => 'index.php?app=my_coupon',
            ),
        );
        return $menus;
    }
	
	function goumaiyouhuiquan()
    {
	$this->youhuiquan_mod =& m('youhuiquan');
	$this->my_money_mod =& m('my_money');
	$this->moneylog_mod =& m('my_moneylog');
	$this->member_mod =& m('member');
	$this->canshu_mod =& m('canshu');
	$this->accountlog_mod =& m('accountlog');
	$this->mlog_mod =& m('moneylog');
	$this->youhuilist_mod =& m('youhuilist');
	$this->message_mod =& m('message');
	   $user_id = $this->visitor->get('user_id');	
	   $user_name = $this->visitor->get('user_name');
	    $this->_curlocal(array(array('text' => Lang::get('goumaiyouhuiquan'))));
	    
	   $my_money_row=$this->my_money_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$user_id' limit 1");
	   $zhifu_password= $my_money_row['zf_pass'];
	   $money=$my_money_row['money'];
	   $youhui_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	   $you_hui=$this->youhuiquan_mod->getRow("select * from ".DB_PREFIX."youhuiquan where youhui_id='$youhui_id' limit 1");

	if($_POST)
	   {
	   $money=$my_money_row['money'];
	   $dongjie_money=$my_money_row['money_dj'];
	   $duihuanjifen=$my_money_row['duihuanjifen'];
	   $dongjiejifen=$my_money_row['dongjiejifen'];
	   $suoding_money=$my_money_row['suoding_money'];
	   $suoding_jifen=$my_money_row['suoding_jifen'];
	   $keyong_money=$money-$suoding_money;
	   $keyong_jifen=$duihuanjifen-$suoding_jifen;
	   $user_id = $this->visitor->get('user_id');	
	   $user_name = $this->visitor->get('user_name');
	  $kaiguan=$this->my_money_mod->kg();
	  $this->assign('kaiguan',$kaiguan);
	 
	    $member_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
	   $mcity=$member_row['city'];
	   $goumai = isset($_GET['goumai']) ? intval($_GET['goumai']) : 0;//传递过来的购买钱
	   $youhui_jine = isset($_GET['youhui_jine']) ? intval($_GET['youhui_jine']) : 0;
	   $youhui_name = isset($_GET['youhui_name']) ? intval($_GET['youhui_name']) : 0;
	   $youhui_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	
	   $password= trim($_POST['password']);
	   $goumai= trim($_POST['goumai']);
	   $goumai_jifen= trim($_POST['goumai_jifen']);
	   $youhui_id= trim($_POST['youhui_id']);
	   $youhui_name= trim($_POST['youhui_name']);
	   $youhui_jine= trim($_POST['youhui_jine']);
	   $youhui_jifen= trim($_POST['youhui_jifen']);
	   $youhui_image= trim($_POST['youhui_image']);
	   $zhifufangshi= trim($_POST['zhifufangshi']);
	   //echo $zhifufangshi;
	   $riqi=date('Y-m-d H:i:s');
	  
	  // echo $goumai;
	   $password=md5($password);
	  // echo $password;
	  if($zhifufangshi=="xianjinzhifu")
	  {
		  if($keyong_money<$goumai)
		   {
			   $this->show_warning('zhanghuyuebuzu');
			   return false;
		   }
	  }
	  else
	  {
	  		if($keyong_jifen<$goumai_jifen)
		   {
			   $this->show_warning('nindejifenbuzu');
			   return false;
		   }
	  }
	  
	   if($password!=$zhifu_password)
	   {
	   $this->show_warning('zhifu');//有待改进
	   return false;
	   }
	$this->canshu_mod=& m('canshu');
	$jinbi_row=$this->canshu_mod->getRow("select yu_jinbi,zong_money,zong_jifen from ".DB_PREFIX."canshu limit 1");
	$yu_jinbi=$jinbi_row['yu_jinbi'];
	$zong_money=$jinbi_row['zong_money'];
	$zong_jifen=$jinbi_row['zong_jifen'];
	if($zhifufangshi=="xianjinzhifu")
	{
	    //增加mymoneylog日志
		$newmoney=$money-$goumai;
		$log_text=Lang::get('goumai').$goumai.Lang::get('yuan').Lang::get('youhuiquan');
		$add_mylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'money'=>'-'.$goumai,
		'log_text'=>$log_text,
		'leixing'=>33,	//购买优惠券类型
		'riqi'=>$riqi,
		'youhui_id'=>$youhui_id,	
		'youhui_name'=>$youhui_name,	
		'youhui_jine'=>$youhui_jine,
		'city'=>$mcity,	
		'type'=>13,	
		'dq_money'=>$newmoney,
		'dq_money_dj'=>$dongjie_money,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen,																			
		);
		
	   //更新my_money表
		$money_da=array(
		'money'=>$newmoney,
		);

		$date=time();
		$bianhao=$youhui_id.$date;
//echo $bianhao;
 		$log_text=$user_name.Lang::get('goumai').$goumai.Lang::get('yuan').Lang::get('youhuiquan');
		$add_youhui=array(
		
		'user_name'=>$user_name,
		'beizhu'=>$log_text,
		'youhui_id'=>$youhui_id,	
		'youhui_name'=>$youhui_name,	
		'youhui_jine'=>$youhui_jine,
		'youhui_jifen'=>$youhui_jifen,
		'ycity'=>$mcity,
		'riqi'=>$riqi,		
		'status'=>yes,	
		'bianhao'=>$bianhao,	
		'youhui_image'=>$youhui_image,	
		'zhifufangshi'=>$zhifufangshi,																			
		);
	//更新参数表
		$new_zong_money=$zong_money+$goumai;//将购买金额增加到总账户资金
		$edit_canshu=array(
		'zong_money'=>$new_zong_money,
		);
	//增加accountlog日志	
	$beizhu=Lang::get('bianhao').$bianhao;
	$addaccount=array(
		'money'=>'+'.$goumai,
		'time'=>$riqi,
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$mcity,
		'type'=>8,
		's_and_z'=>1,
		'beizhu'=>$beizhu,
		'dq_money'=>$new_zong_money,
		'dq_jifen'=>$zong_jifen,
	);
	//添加moneylog日志
	 //$beizhu=$user_name.Lang::get('goumai').$goumai.Lang::get('yuan').Lang::get('youhuiquan');
	 $beizhu=Lang::get('bianhao').$bianhao;
	   $add_moneylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'money'=>'-'.$goumai,
		'time'=>$riqi,	
		's_and_z'=>2,
		'type'=>8,	
		'beizhu'=>$beizhu,	
		'zcity'=>$mcity,	
		'dq_money'=>$newmoney,
		'dq_money_dj'=>$dongjie_money,
		'dq_jifen'=>$duihuanjifen,
		'dq_jifen_dj'=>$dongjiejifen,	
												
		);
}	
else
{
	//增加mymoneylog日志
		$new_jifen=$duihuanjifen-$goumai_jifen;
		$log_text=Lang::get('goumai').$goumai_jifen.Lang::get('jifen').Lang::get('youhuiquan');
		$add_mylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'duihuanjifen'=>'-'.$goumai_jifen,
		'log_text'=>$log_text,
		'leixing'=>33,	//购买优惠券类型
		'riqi'=>$riqi,
		'youhui_id'=>$youhui_id,	
		'youhui_name'=>$youhui_name,	
		'youhui_jine'=>$youhui_jine,
		'city'=>$mcity,	
		'type'=>13,	
		'dq_money'=>$money,
		'dq_money_dj'=>$dongjie_money,
		'dq_jifen'=>$new_jifen,
		'dq_jifen_dj'=>$dongjiejifen,																			
		);
		
	   //更新my_money表
		$money_da=array(
		'duihuanjifen'=>$new_jifen,
		);

		$date=time();
		$bianhao=$youhui_id.$date;
//echo $bianhao;
 		$log_text=$user_name.Lang::get('goumai').$goumai_jifen.Lang::get('jifen').Lang::get('youhuiquan');
		$add_youhui=array(
		'user_name'=>$user_name,
		'beizhu'=>$log_text,
		'youhui_id'=>$youhui_id,	
		'youhui_name'=>$youhui_name,	
		'youhui_jine'=>$youhui_jine,
		'youhui_jifen'=>$youhui_jifen,
		'ycity'=>$mcity,
		'riqi'=>$riqi,		
		'status'=>yes,	
		'bianhao'=>$bianhao,	
		'youhui_image'=>$youhui_image,	
		'zhifufangshi'=>$zhifufangshi,																			
		);
	//更新参数表
		$new_zong_jifen=$zong_jifen+$goumai_jifen;//将购买金额增加到总账户资金
		$edit_canshu=array(
		'zong_jifen'=>$new_zong_jifen
		);
	//增加accountlog日志	
	// $beizhu=$user_name.Lang::get('goumai').$goumai_jifen.Lang::get('jifen').Lang::get('youhuiquan');
	$beizhu=Lang::get('bianhao').$bianhao;
	$addaccount=array(
		'jifen'=>'+'.$goumai_jifen,
		'time'=>$riqi,
		'user_name'=>$user_name,
		'user_id'=>$user_id,
		'zcity'=>$mcity,
		'type'=>8,
		's_and_z'=>1,
		'beizhu'=>$beizhu,
		'dq_money'=>$zong_money,
		'dq_jifen'=>$new_zong_jifen,
	);
	//添加moneylog日志
	 //$beizhu=$user_name.Lang::get('goumai').$goumai_jifen.Lang::get('jifen').Lang::get('youhuiquan');
	 $beizhu=Lang::get('bianhao').$bianhao;
	   $add_moneylog=array(
		'user_id'=>$user_id,
		'user_name'=>$user_name,
		'jifen'=>'-'.$goumai_jifen,
		'time'=>$riqi,	
		's_and_z'=>2,
		'type'=>8,	
		'beizhu'=>$beizhu,	
		'zcity'=>$mcity,	
		'dq_money'=>$money,
		'dq_money_dj'=>$dongjie_money,
		'dq_jifen'=>$new_jifen,
		'dq_jifen_dj'=>$dongjiejifen,	
												
		);
}
 $this->accountlog_mod->add($addaccount);
 $this->my_money_mod->edit('user_id='.$user_id,$money_da);
 $this->mlog_mod->add($add_moneylog);
 $this->moneylog_mod->add($add_mylog);
 $this->youhuilist_mod->add($add_youhui);  
 $can_id=1;
 $this->canshu_mod->edit('id='.$can_id,$edit_canshu);
 
 
 $content=Lang::get('goumaiyou');
	$content=str_replace('{1}',$user_name,$content);		
	$add_notice1=array(
	'from_id'=>0,
	'to_id'=>$user_id,
	'content'=>$content,  
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'new'=>1,
	'parent_id'=>0,
	'status'=>3,
	);
	$this->message_mod->add($add_notice1);

 

 $this->show_message('zhifuchenggong',
            'back_list', 'index.php?app=my_coupon&act=goumailiebiao');
}
else
{
$this->assign('you_hui',$you_hui);
$this->assign('youhui',$youhui);
       $this->assign('guanggaowei', $this->guanggaowei(3,9));
       $this->assign('_last_goumai_youhui', $this->_last_goumai_youhui(6));
        $this->display('goumailist.html');
    }
	}
	
function _last_goumai_youhui($_num)
    {
        $this->youhuilist_mod =& m('youhuilist');
        $data = $this->youhuilist_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
            'conditions' => 'youhui_id > 0',
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));

        return $data;
    }
	
	
	
	
	function goumailiebiao()
	{
	$this->youhuiquan_mod =& m('youhuiquan');
	$this->youhuilist_mod =& m('youhuilist');
	$kaiguan=$this->youhuilist_mod->kg();
	$this->assign('kaiguan',$kaiguan);
	$user_name = $this->visitor->get('user_name');	    
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('shangfutong'),         'index.php?app=my_money&act=index',
                         LANG::get('coupon_list')
                         );
        /* 当前用户中心菜单 */
	$this->assign('page_title',Lang::get('member_center'). ' - ' .Lang::get('coupon_list').' - '.Lang::get('pingtai_youhuiquan'));
        $this->_curitem('pingtai_youhuiquan');	
	    $page = $this->_get_page();		
		
		 $youhui_list=$this->youhuilist_mod->getAll("SELECT * " .
                    " FROM " . DB_PREFIX . "youhuilist AS yl " .
                    "   LEFT JOIN " . DB_PREFIX . "youhuiquan AS yq ON yl.youhui_id = yq.youhui_id " .
                    "WHERE yl.user_name='$user_name'"  . 
                    "ORDER BY yl.id desc " .
					"LIMIT {$page['limit']}"
					);	
		
		$page['item_count'] =count($youhui_list);
        $this->_format_page($page);
	    $this->assign('page_info', $page);
		$this->assign('youhui_list', $youhui_list);
        $this->display('youhui.index.html');
	
	
	
	}
	
function guanggaowei($_num,$type)
    {
	//$url=$_SERVER['HTTP_HOST'];
	$this->_city_mod =& m('city');
	//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
	$city_id=$cityrow['city_id'];
	$time=date('Y-m-d H:i:s');
	 $this->adv_mod =& m('adv');
	$advs=$this->adv_mod->getAll("select * from ".DB_PREFIX."adv where type = '$type'");
	
        $data = $this->adv_mod->find(array(
            //'join' => 'be_join,belong_goods',
            'fields' => '*',
            'conditions' => "adv_city='$city_id' and type='$type' and start_time<='$time' and end_time>='$time'",
            'order' => 'riqi DESC',
            'limit' => $_num,
        ));
   
        return $data;
    }
	/*function applyible()
	{
	    $this =& m('store');
        $stores = $this->get_enabled($this->_store_id);
        if (empty($stores))
        {
            $this->show_message('please_install_payment', 'go_payment', 'index.php?app=my_payment');
                  return false;
        }	
    }*/
}
?>