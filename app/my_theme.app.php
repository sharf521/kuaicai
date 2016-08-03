<?php

/**
 *    主题设置控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class My_themeApp extends StoreadminbaseApp
{
	
	
    function index()
    {
        extract($this->_get_themes());

        if (empty($themes))
        {
            $this->show_warning('no_themes');

            return;
        }

        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                         LANG::get('theme_list'));

        /* 当前用户中心菜单 */
        $this->_curitem('my_theme');
        $this->_curmenu('theme_config');
        $this->assign('themes', $themes);
        $this->assign('curr_template_name', $curr_template_name);
        $this->assign('curr_style_name', $curr_style_name);
        $this->assign('manage_store', $this->visitor->get('manage_store'));
        $this->assign('id',$this->visitor->get('user_id'));
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
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('my_theme'));
        $this->display('my_theme.index.html');
    }
    function set()
    {
        $template_name = isset($_GET['template_name']) ? trim($_GET['template_name']) : null;
        $style_name = isset($_GET['style_name']) ? trim($_GET['style_name']) : null;
        if (!$template_name)
        {
            $this->json_error('no_such_template');

            return;
        }
        if (!$style_name)
        {
            $this->json_error('no_such_style');

            return;
        }
        extract($this->_get_themes());
        $theme = $template_name . '|' . $style_name;

        /* 检查是否可以选择此主题 */
        if (!isset($themes[$theme]))
        {
            $this->json_error('no_such_theme');

            return;
        }
        $model_store =& m('store');
        $model_store->edit($this->visitor->get('manage_store'), array('theme' => $theme));

        $this->json_result('', 'set_theme_successed');
    }

    function _get_themes()
    {
        /* 获取当前所使用的风格 */
        $model_store =& m('store');
        $store_info  = $model_store->get($this->visitor->get('manage_store'));
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($curr_template_name, $curr_style_name) = explode('|', $theme);

        /* 获取待选主题列表 */
        $model_grade =& m('sgrade');
        $grade_info  =  $model_grade->get($store_info['sgrade']);
        $skins = explode(',', $grade_info['skins']);
        $themes = array();
        foreach ($skins as $skin)
        {
            list($template_name, $style_name) = explode('|', $skin);
            $themes[$skin] = array('template_name' => $template_name, 'style_name' => $style_name);
        }

        return array(
            'curr_template_name' => $curr_template_name,
            'curr_style_name'    => $curr_style_name,
            'themes'             => $themes
        );
    }
	
	
	
	
	function gonghuo()
	{
	 //header("location:http://zhuzhan.cn/index.php?app=my_theme&act=gonghuo");
	 $this->member_mod =& m('member');
	 $this->gonghuo_mod =& m('gonghuo');
	 $user_id = $this->visitor->get('user_id');	 
	 $user_name = $this->visitor->get('user_name');	     
	$row_member=$this->member_mod->getRow("select status,city from ".DB_PREFIX."gonghuo_xinxi where user_id = '$user_id' limit 1");
	$city=$row_member['city'];
	$status=$row_member['status'];
	$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
   // $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
    
	if($_POST)
	{
		/* $beizhu = $_POST['beizhu'];
	  if($beizhu=="")
	  {
		   $this->show_warning('Hacking Attempt');
		   return;
	  }*/
     $goodsid = trim($_POST['goodsid']);
	 $goo=$this->member_mod->getRow("select default_image from ".DB_PREFIX."goods where goods_id = '$goodsid' limit 1"); 
	 $def=$goo['default_image'];
	 $goods_name = trim($_POST['goods_name']);
	 $goods_brand = trim($_POST['goods_brand']);
	 $tujing = trim($_POST['tujing']);
	 //$cankao_price = trim($_POST['cankao_price']);
	 $lingshou_price = trim($_POST['lingshou_price']);
	// $lingshou_jifen = trim($_POST['lingshou_jifen']);
	 //$pifa_price = trim($_POST['pifa_price']);
	 $zong_kucun = trim($_POST['zong_kucun']);
	 $source = trim($_POST['source']);
	 $status = trim($_POST['status']);
	 $beizhu = $_POST['beizhu'];
	 $jifen_price = trim($_POST['jifen_price']);

	  $data = array(
	            'user_id' => $user_id ,
				'user_name' => $user_name ,
				'goods_id' => $goodsid ,
                'goods_name'  =>  $goods_name,
                'goods_brand'  => $goods_brand,
				'tujing'  => $tujing,
                //'cankao_price'  => $cankao_price,
				'lingshou_price'  =>$lingshou_price,
				'jifen_price'  =>$jifen_price,
				'yu_kucun'  => $zong_kucun,
				'zong_kucun'  => $zong_kucun,
				'source'  => $source,
				'gh_city'  => $city,
				'status'  => $status,
				'chanpin'  => $def,
				'riqi'  => date('Y-m-d H:i:s'),
				'beizhu'  => $beizhu
				);
            if (!$gh_id = $this->gonghuo_mod->add($data))  //获取brand_id
            {
                $this->show_warning($this->gonghuo_mod->get_error());

                return;
            }
			
			if(empty($goodsid))
			{
            /* 处理上传的图片 */
            $logo       =   $this->_upload_logo($gh_id,'ziliao');
			$logo1       =   $this->_upload_logo($gh_id,'chanpin');
			
			
            if ($logo === false)
            {
                return;
            }
            
            $logo && $this->gonghuo_mod->edit($gh_id, array('ziliao' => $logo));
			$logo1 && $this->gonghuo_mod->edit($gh_id, array('chanpin' => $logo1));
			}
			  

            //$this->_clear_cache();
            $this->show_message('add_successed',
                'continue_add', 'index.php?app=my_theme&act=jibenxinxi'
            );
			}
			else
			{
			 $user_id = $this->visitor->get('user_id');	    
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuo'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
			 $canshu=$this->gonghuo_mod->can();
	         $this->assign('canshu', $canshu);
			 if(empty($status) || $status==1 || $status==3)
			 {
				 $mem=$this->member_mod->getRow("select * from ".DB_PREFIX."gonghuo_xinxi where user_id = '$user_id' limit 1");
				  $user = $this->visitor->get();
				  $this->assign('user', $user);
				  $this->assign('mem', $mem);
				  $this->display('gh_shenqing.html');
			 }
			 else
			 {
				 /* 检测支付方式、配送方式、商品数量等 */
        		if (!$this->_addible()) {
            	return;
        		}
			  	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
				$this->goods_mod=& m('goods');
				$good=$this->goods_mod->getRow("select g.*,gs.* from ".DB_PREFIX."goods g " .
				" left join " .DB_PREFIX. "goods_spec gs on gs.spec_id=g.default_spec ".
				" where g.goods_id = '$id'");
				$this->assign('good',$good);
	  		 	$this->display('gonghuo.index.html');
				//header("location:/index.php?app=my_goods&act=add&gh=2"); 
				//header("location:/index.php?app=my_theme&act=shangjiaxinxi");
			 }
			}
			
}
	
	
	function jibenxinxi()
	{
	
	 $user_id = $this->visitor->get('user_id');	    
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuoxinxi'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	 $this->gonghuo_mod =& m('gonghuo');
	$user_id = $this->visitor->get('user_id');	  
	$page = $this->_get_page();		
	 $jiben = $this->gonghuo_mod->find(
            array(
               
                'conditions' => 'user_id='.$user_id,
                'order' => 'gh_id DESC',
                'limit' => $page['limit'],  //获取当前页的数据
                'count' => true
            )
        );
		$page['item_count'] = $this->gonghuo_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('jiben', $jiben);
	 	$this->display('jibenxinxi.html');
	
	}
	function shangjiaxinxi()
	{
	
	 $user_id = $this->visitor->get('user_id');	    
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('shangjiaxinxi'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	 $this->gonghuo_xinxi_mod =& m('gonghuo_xinxi');
	$user_id = $this->visitor->get('user_id');	  
	$page = $this->_get_page();		
	 $jiben = $this->gonghuo_xinxi_mod->find(
            array(
               
                'conditions' => 'user_id='.$user_id,
                'order' => 'gh_id DESC',
                'limit' => $page['limit'],  //获取当前页的数据
                'count' => true
            )
        );
		
		 $page['item_count'] = $this->gonghuo_xinxi_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('jiben', $jiben);
	 $this->display('shangjiaxinxi.html');
	
	}
	
	
	function caigouxinxi()
	{
	
	 $user_id = $this->visitor->get('user_id');	    
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
							  LANG::get('caigouxinxi')
                             );

            /* 当前用户中心菜单 */
            $this->_curitem('caigouxinxi');
	 $this->caigou_mod =& m('caigou');
	$user_id = $this->visitor->get('user_id');	  
	$page = $this->_get_page();		
	 $xinxi = $this->caigou_mod->find(
            array(
               
                'conditions' => 'gong_id='.$user_id,
                'order' => 'id DESC',
                'limit' => $page['limit'],  //获取当前页的数据
                'count' => true
            )
        );
		
		 $page['item_count'] = $this->caigou_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('xinxi', $xinxi);
	 $this->display('caigouxinxi.html');
	
	}
	function ghshenhe()
	{
	 $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuo'));

            /* 当前用户中心菜单 */
			$this->message_mod =& m('message');
     $this->_curitem('gonghuo');
	 $this->caigou_mod =& m('caigou');
	 $this->my_money_mod =& m('my_money');
	 $this->moneylog_mod =& m('moneylog');
	  $this->my_moneylog_mod =& m('my_moneylog');
	 $this->accountlog_mod =& m('accountlog');
	 $this->canshu_mod =& m('canshu');
	 $this->gonghuo_mod=& m('gonghuo');
	 $id = empty($_GET['id']) ? null : trim($_GET['id']);
	 $num = empty($_GET['num']) ? null : trim($_GET['num']);
	 $find_data     = $this->caigou_mod->find($id);
	 $caigou  =   current($find_data);
	 $this->assign('caigou', $caigou);
	 $status=trim($_POST['status']);
	 $cai_id=trim($_POST['cai_id']);
	 $cai_name=trim($_POST['cai_name']);

	 $beizhu=trim($_POST['beizhu']);
	 $canshu_row=$this->canshu_mod->can();
     $zong_money=$canshu_row['zong_money'];
     $zong_jifen=$canshu_row['zong_jifen'];
     $jifenxianjin=$canshu_row['jifenxianjin'];//积分现金兑换比例
	 $daishou_bili=$canshu_row['daishou'];
	 $lv31=$canshu_row['lv31'];
	 $duihuanjifenfeilv=$canshu_row['duihuanjifenfeilv'];
	 
     $caigou_row=$this->caigou_mod->getRow("select * from ".DB_PREFIX."caigou where id='$id' limit 1");
	 $gh_id=$caigou_row['gh_id'];
	 $gong_id=$caigou_row['gong_id'];
	 $gong_name=$caigou_row['gong_name'];
	 $num=$caigou_row['num'];
	 $lingshou_price=$caigou_row['lingshou_price'];
	 $zong_e=$lingshou_price*$num;
	 $zhifufangshi=$caigou_row['zhifufangshi'];
	 //$jiage=$zong_e*$duihuanjifenfeilv;//采购的31%(金额)
	 $jiage=$zong_e*$lv31;//采购的31%(金额)
	 $jifen_jiage=$jiage*$jifenxianjin;//将采购的31%换算成积分
	 $jifen_jia=$zong_e*$jifenxianjin;//将采购的全部金额换算成积分
	 $cai_jifen=$jifen_jia+$jifen_jiage;//采购人的积分
	 $gong_jifen=$lingshou_price*$jifenxianjin;//供货人的积分
	 $gonghuo_row=$this->caigou_mod->getRow("select yu_kucun,zong_kucun from ".DB_PREFIX."gonghuo where gh_id='$gh_id' limit 1");
	 $zong_kucun=$gonghuo_row['zong_kucun'];
	 $yu_kucun=$gonghuo_row['yu_kucun'];
	 $money_row=$this->caigou_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$cai_id' limit 1");
	 $member_row=$this->caigou_mod->getRow("select * from ".DB_PREFIX."member where user_id='$cai_id' limit 1");
	 $money_dj=$money_row['money_dj'];//采购人的冻结金额
	 $money=$money_row['money'];//采购人的金额
	 $lev=$member_row['level'];
	 $duihuanjifen=$money_row['duihuanjifen'];
	 $dongjiejifen=$money_row['dongjiejifen'];
	 $city=$money_row['city'];
	 $new_money_dj=$money_dj-$jiage;
 	 $new_dongjiejifen=$dongjiejifen-$jifen_jiage;
     $new_zong_money=$zong_money+$jiage;
	 $new_zong_jifen=$zong_jifen+$jifen_jiage;
	if($yu_kucun<$num)
		{
		$this->show_message('nindekucunbuzong');
		return;
		}
	if($_POST)
	{
		if($status==1)
			{
				$yu_kucun=$yu_kucun-$num;
				$this->gonghuo_mod->edit('gh_id='.$gh_id,array('yu_kucun'=>$yu_kucun));
				$bb=explode(',',$lev);
				if(!in_array(1,$bb))//若是免费商家则解冻金额
				{
				  if($zhifufangshi=="xianjinzhifu")
				  {
						//更新用户money表
						$edit_money=array(
						'money_dj'=>$new_money_dj
						);
						//更新用户moneylog表
						$riqi=date('Y-m-d H:i:s');
						//$beizhu =Lang::get('jiedongyonghu').$cai_name.Lang::get('caigoudongjie').$jiage.Lang::get('yuan');
						$beizhu =Lang::get('gonghuoshang');
						$beizhu=str_replace('{1}',$gong_name,$beizhu);
						$beizhu=str_replace('{2}',$gh_id,$beizhu);
						
						$addlog=array(
						'money'=>'-'.$jiage,
						//'money_dj'=>'-'.$jiage,
						'time'=>$riqi,
						'user_name'=>$cai_name,
						'user_id'=>$cai_id,
						'zcity'=>$city,
						'type'=>10,
						's_and_z'=>2,
						'beizhu'=>$beizhu,
						'dq_money'=>$money,
						'dq_money_dj'=>$new_money_dj,
						'dq_jifen'=>$duihuanjifen,
						'dq_jifen_dj'=>$dongjiejifen
					);
						
				//更新总账户资金流水accountlog表
					$beizhu =Lang::get('gonghuoshang');
					$beizhu=str_replace('{1}',$gong_name,$beizhu);
					$beizhu=str_replace('{2}',$gh_id,$beizhu);
					$addaccount=array(
						'money'=>$jiage,
						'time'=>$riqi,
						'user_name'=>$cai_name,
						'user_id'=>$cai_id,
						'zcity'=>$city,
						'type'=>10,
						's_and_z'=>1,
						'beizhu'=>$beizhu,
						'dq_money'=>$new_zong_money,
						'dq_jifen'=>$zong_jifen,
					);
					$edit_canshu=array(
						'zong_money'=>$new_zong_money
						);
				  }
				  else
				  {
				//更新用户money表
						$edit_money=array(
						'dongjiejifen'=>$new_dongjiejifen
						);
						
						//更新用户moneylog表
						$riqi=date('Y-m-d H:i:s');
						$beizhu =Lang::get('gonghuoshang');
						$beizhu=str_replace('{1}',$gong_name,$beizhu);
						$beizhu=str_replace('{2}',$gh_id,$beizhu);
						$addlog=array(
						'jifen'=>'-'.$jifen_jiage,
						//'jifen_dj'=>'-'.$jifen_jiage,
						'time'=>$riqi,
						'user_name'=>$cai_name,
						'user_id'=>$cai_id,
						'zcity'=>$city,
						'type'=>10,
						's_and_z'=>2,
						'beizhu'=>$beizhu,
						'dq_money'=>$money,
						'dq_money_dj'=>$money_dj,
						'dq_jifen'=>$duihuanjifen,
						'dq_jifen_dj'=>$new_dongjiejifen
					);
						
				//更新总账户资金流水accountlog表
					$beizhu =Lang::get('gonghuoshang');
					$beizhu=str_replace('{1}',$gong_name,$beizhu);
					$beizhu=str_replace('{2}',$gh_id,$beizhu);
					$addaccount=array(
						'jifen'=>$jifen_jiage,
						'time'=>$riqi,
						'user_name'=>$cai_name,
						'user_id'=>$cai_id,
						'zcity'=>$city,
						'type'=>10,
						's_and_z'=>1,
						'beizhu'=>$beizhu,
						'dq_money'=>$zong_money,
						'dq_jifen'=>$new_zong_jifen,
					);
					$edit_canshu=array(
						'zong_jifen'=>$new_zong_jifen
						);
				  }
				     $this->my_money_mod->edit('user_id='.$cai_id,$edit_money);
					 $this->accountlog_mod->add($addaccount);
					 $this->moneylog_mod->add($addlog);
			//更新总账户
					    $can_id=1;
						
						$this->canshu_mod->edit('id='.$can_id,$edit_canshu);
						//对接webservice开始,将采购金额的31%传给webservice
						$this->kaiguan_mod =& m('kaiguan');
						$this->webservice_list_mod =& m('webservice_list');
						$row_kaiguan=$this->kaiguan_mod->getRow("select webservice from ".DB_PREFIX."kaiguan");
						$webservice=$row_kaiguan['webservice'];
						$this->member_mod =& m('member');
						$user_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$cai_id' limit 1");
						$userrow=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$gong_id' limit 1");		
						$wid=$user_row['web_id'];//采购人的wid
						$cai_city=$user_row['city'];//采购人的city
						$webid=$userrow['web_id'];//供货人的webid
						$gong_city=$userrow['city'];
						$riqi=date('Y-m-d H:i:s');
				
						if($webservice=="yes")
							{
								$post_data = array(//采购人
								"ID"=>$wid,
								"Money"=>$jifen_jia,
								"MoneyType"=>1,
								"Count"=>1
								); 
								$web_id= webService('C_Consume',$post_data);
								$daa=array(
								"gong_id"=>$cai_id,
								"type"=>1,
								"money"=>$jifen_jia,
								"consume_id"=>$web_id,
								"time"=>$riqi,
								"status"=>0,
								"city"=>$cai_city
								);
					$this->webservice_list_mod->add($daa);
								
								
									$post_da = array(//供货人
									"ID"=>$webid,
									"Money"=>$gong_jifen/2.52,
									"MoneyType"=>0,
									"Count"=>$num
									 ); 
				
								$webid= webService('C_Consume',$post_da);
								
								
								//print_r($webid);
								//print_r($post_da);
								$aa=explode(',',$webid);
								foreach($aa as $var){
									$add_web=array(
									'cai_id'=>$cai_id,
									'gong_id'=>$gong_id,
									'consume_id'=>$var,
									'status'=>0,
									'money'=>$gong_jifen/2.52,
									'type'=>0,
									'time'=>$riqi,
									'gh_id'=>$gh_id,
									'city'=>$cai_city
									);
								$this->webservice_list_mod->add($add_web);
							}
								$_S['canshu']=$this->member_mod->can();
									//c_cal();
									//exit;
								
				}
					
					
			
			$notice=Lang::get('caigouchenggong');	
			$notice=str_replace('{1}',$cai_name,$notice);	
			$notice=str_replace('{2}',$gong_name,$notice);
			$notice=str_replace('{3}',$num,$notice);		
			$add_notice=array(
				'from_id'=>0,
				'to_id'=>$cai_id,
				'content'=>$notice,  
				'add_time'=>gmtime(),
				'last_update'=>gmtime(),
				'new'=>1,
				'parent_id'=>0,
				'status'=>3,
				);
					
			$this->message_mod->add($add_notice);
			}
				
	}		
		
		if($status==2)//若审核不通过，则将冻结的金额返回给采购人
		{
		        $bb=explode(',',$lev);
				if(!in_array(1,$bb))//若是免费商家则解冻金额
				{
					$new_cai_money=$money+$jiage;
					$new_cai_money_dj=$money_dj-$jiage;
					$new_cai_jifen=$duihuanjifen+$jifen_jiage;
					$new_cai_jifen_dj=$dongjiejifen-$jifen_jiage;
					if($zhifufangshi=="xianjinzhifu")
					{
					 //添加my_moneylog日志
						$log_text =Lang::get('jiedongyh').$cai_name.Lang::get('dongjiejine').$jiage.Lang::get('yuan');
						$add_mymoneylog=array(
						'user_id'=>$cai_id,
						'user_name'=>$cai_name,
						'add_time'=>time(),
						'money_dj'=>'-'.$jiage,		
						'money'=>$jiage,
						'log_text'=>$log_text,
						//'feilv'=>$txfeilv,
						'status'=>0,
						'riqi'=>$riqi,	
						'type'=>34,	
						'city'=>$city,
						'dq_money'=>$new_cai_money,
						'dq_money_dj'=>$new_cai_money_dj,
						'dq_jifen'=>$duihuanjifen,
						'dq_jifen_dj'=>$dongjiejifen																			
						);
						
						//$beizhu =Lang::get('jiedongyh').$cai_name.Lang::get('dongjiejine').$jiage.Lang::get('yuan');
						$beizhu =Lang::get('gonghuoshang');
						$beizhu=str_replace('{1}',$gong_name,$beizhu);
						$beizhu=str_replace('{2}',$gh_id,$beizhu);
						$addlog=array(
							'money_dj'=>'-'.$jiage,
							'money'=>$jiage,
							'time'=>$riqi,
							'user_name'=>$user_name,
							'user_id'=>$user_id,
							'zcity'=>$mcity,
							'type'=>11,
							's_and_z'=>1,
							'beizhu'=>$beizhu,
							'dq_money'=>$new_cai_money,
							'dq_money_dj'=>$new_cai_money_dj,
							'dq_jifen'=>$duihuanjifen,
							'dq_jifen_dj'=>$dongjiejifen
						);
						
						$money_new=array(
						'money'=>$new_cai_money,
						'money_dj'=>$new_cai_money_dj
						);
						
				    }
					else
					{
						$log_text =Lang::get('jiedongyh').$cai_name.Lang::get('dongjiejifen').$jifen_jiage.Lang::get('jifen');
						$add_mymoneylog=array(
						'user_id'=>$cai_id,
						'user_name'=>$cai_name,
						'add_time'=>time(),
						'dongjiejifen'=>'-'.$jifen_jiage,		
						'duihuanjifen'=>$jifen_jiage,
						'log_text'=>$log_text,
						//'feilv'=>$txfeilv,
						'status'=>0,
						'riqi'=>$riqi,	
						'type'=>34,	
						'city'=>$city,
						'dq_money'=>$money,
						'dq_money_dj'=>$money_dj,
						'dq_jifen'=>$new_cai_jifen,
						'dq_jifen_dj'=>$new_cai_jifen_dj																			
						);
						
						//$beizhu =Lang::get('jiedongyh').$cai_name.Lang::get('dongjiejifen').$jifen_jiage.Lang::get('jifen');
						$beizhu =Lang::get('gonghuoshang');
						$beizhu=str_replace('{1}',$gong_name,$beizhu);
						$beizhu=str_replace('{2}',$gh_id,$beizhu);
						$addlog=array(
							'jifen_dj'=>'-'.$jifen_jiage,
							'jifen'=>$jifen_jiage,
							'time'=>$riqi,
							'user_name'=>$user_name,
							'user_id'=>$user_id,
							'zcity'=>$mcity,
							'type'=>11,
							's_and_z'=>1,
							'beizhu'=>$beizhu,
							'dq_money'=>$money,
							'dq_money_dj'=>$money_dj,
							'dq_jifen'=>$new_cai_jifen,
							'dq_jifen_dj'=>$new_cai_jifen_dj	
						);
						
				
						$money_new=array(
						'duihuanjifen'=>$new_cai_jifen,
						'dongjiejifen'=>$new_cai_jifen_dj
						);
						
					}
					$this->my_moneylog_mod->add($add_mymoneylog);
					//$this->moneylog_mod->add($addlog);
					$this->my_money_mod->edit('user_id='.$cai_id,$money_new);
				}
				
				
			
			$notice=Lang::get('caigoubchenggong');	
			$notice=str_replace('{1}',$cai_name,$notice);	
			
			$add_notice=array(
				'from_id'=>0,
				'to_id'=>$cai_id,
				'content'=>$notice,  
				'add_time'=>gmtime(),
				'last_update'=>gmtime(),
				'new'=>1,
				'parent_id'=>0,
				'status'=>3,
				);
					
			$this->message_mod->add($add_notice);
				
		}
		$new_cg=array(
				'status'=>$status,	
				'beizhu'=>$beizhu																	
		);
		
		$this->caigou_mod->edit('id='.$id,$new_cg);
		$this->show_message('shenhechenggong',
		'back_list',    'index.php?app=my_theme&act=caigouxinxi');
	}
    else
	{
		$this->display('cg_shenhe.html');
		return;
	}
}
	
	function my_caigou()
	{
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=my_caigou',
                             LANG::get('wodecaigou'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	 $this->caigou_mod =& m('caigou');
	$user_id = $this->visitor->get('user_id');	  
	$page = $this->_get_page();		
	 $cai = $this->caigou_mod->find(
            array(
               
                'conditions' => 'cai_id='.$user_id,
                'order' => 'id DESC',
                'limit' => $page['limit'],  //获取当前页的数据
                'count' => true
            )
        );
		
		 $page['item_count'] = $this->caigou_mod->getCount();
        $this->_format_page($page);
		$kaiguan=$this->caigou_mod->kg();
		$this->assign('kaiguan',$kaiguan);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('cai', $cai);
	 $this->display('my_caigou.html');
	}
	
	function my_caigou_k()
	{
        /* 当前位置 */
       $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('wodecaigou'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	 $this->caigou_mod =& m('caigou_k');
	$user_id = $this->visitor->get('user_id');	  
	$page = $this->_get_page();		
	 $cai = $this->caigou_mod->find(
            array(
               
                'conditions' => 'cai_id='.$user_id,
                'order' => 'id DESC',
                'limit' => $page['limit'],  //获取当前页的数据
                'count' => true
            )
        );
		
		 $page['item_count'] = $this->caigou_mod->getCount();
        $this->_format_page($page);
		$kaiguan=$this->caigou_mod->kg();
		$this->assign('kaiguan',$kaiguan);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		$this->assign('cai', $cai);
	 $this->display('my_caigou_k.html');
	}
	
	
	function fabu_k()
	{
		$this->member_mod=& m('member');
		$this->caigou_k_mod=& m('caigou_k');
		$this->spec_mod  =& m('goodsspec');
        $this->image_mod =& m('goodsimage');
        $this->uploadedfile_mod =& m('uploadedfile');
		$this->categorygoods_mod =& m('categorygoods');
		$this->_goods_mod =& m('goods');
		$this->goods_statistics_mod  =& m('goodsstatistics');
		$user_id = $this->visitor->get('user_id');
		//$this->goods_mod =& bm('goods', array('_store_id' => $user_id));
	 	/* 检测支付方式、配送方式、商品数量等 */
		if (!$this->_addible()) {  return;  }
		
		$member_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
		$mcity=$member_row['city'];	
		$fabuid = empty($_GET['fabuid']) ? null : trim($_GET['fabuid']);//发布id	
	
		$canshu=$this->member_mod->can();
		$jifenxianjin=$canshu['jifenxianjin'];
		$lv31=$canshu['lv31'];
		$lv21=$canshu['lv21'];
	
	$sql="select c.cai_id,c.num,g.*,gs.spec_1,gs.spec_2,gs.sku,gs.price_m as pricem from ".DB_PREFIX."caigou_k c left join ".DB_PREFIX."goods g on c.gh_id=g.goods_id left join ".DB_PREFIX."goods_spec gs on c.spec_id=gs.spec_id where c.cai_id='$user_id' and id='$fabuid'";
	
	$row=$this->caigou_k_mod->getRow($sql);
	
	if($row)
	{	
		$goodsid=$row['goods_id'];	
		$price=$row['pricem'];
		$jifen_price=$price*$jifenxianjin*(1+$lv31);
		$vip_price=$price*$jifenxianjin*(1+$lv21);
		
		//增加到商品goods表
		$add_goods=array(
			'store_id'=>$user_id,//商品店铺id
			'goods_name'=>$row['goods_name'],
			'brand'=>$row['brand'],
			'default_image'=>$row['default_image'],			
			'cityhao'=>$mcity,
			'description'=>addslashes($row['description']),
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),		
			'daishou'=>3,
			'gong_id'=>0,//供货id	
			'ger_id'=>0,
			'if_show'=>0,
			'price'=>$price,
			'jifen_price'=>$jifen_price,
			'vip_price'=>$vip_price,
			'price_m'=>$price,
			'spec_qty'=>$row['spec_qty'],
			'spec_name_1'=>$row['spec_name_1'],
			'spec_name_2'=>$row['spec_name_2'],
			'cate_id'=>$row['cate_id'],
			'cate_name'=>$row['cate_name'],
			'recommended'=>0,
			'cate_id_1'=>$row['cate_id_1'],
			'cate_id_2'=>$row['cate_id_2'],
			'cate_id_3'=>$row['cate_id_3'],
			'cate_id_4'=>$row['cate_id_4']
		);
    	$goods_id =$this->_goods_mod->add($add_goods); 
		//增加商品category_goods表
			$add_cate=array(
				'cate_id'=>0,
				'goods_id'=>$goods_id
			);
			$this->categorygoods_mod->add($add_cate);
		//增加商品goods_statistics表
		$add_statis=array(
		'goods_id'=>$goods_id,
		'views'=>0,
		'collects'=>0,
		'carts'=>0,
		'orders'=>0,
		'sales'=>0,
		'comments'=>0,
		);
	$this->goods_statistics_mod->add($add_statis);	

		
		//增加商品goods_spec表
		$add_spec=array(
			'goods_id'=>$goods_id,
			'price'=>$price,
			'jifen_price'=>$jifen_price,
			'vip_price'=>$vip_price,
			'stock'=>$row['num'],
			'price_m'=>$price,
			'spec_1'=>$row['spec_1'],
			'spec_2'=>$row['spec_2'],
			'sku'=>$row['sku']
		);
		$spec_id=$this->spec_mod->add($add_spec);
		
	$gim=$this->_goods_mod->getAll("select uf.*,gi.* from ".DB_PREFIX."uploaded_file uf " .
				" left join " .DB_PREFIX. "goods_image gi on uf.file_id=gi.file_id ".
				" where uf.item_id = '$goodsid'");	
		foreach ($gim as $tp)
		{
		//增加商品uploaded_file表
		
		$add_file=array(
		'store_id'=>$user_id,
		'file_type'=>'image/jpeg',
		'file_size'=>$tp['file_size'],
		'file_name'=>$tp['name'],
		'file_path'=>$tp['file_path'],
		'add_time'=>gmtime(),
		'belong'=>2,
		'item_id'=>$goods_id,
		);
		$file_id=$this->uploadedfile_mod->add($add_file);
		//增加goods_image表
		$add_image=array(
		'goods_id'=>$goods_id,
		'image_url'=>$tp['image_url'],
		'thumbnail'=>$tp['thumbnail'],
		'sort_order'=>255,
		'file_id'=>$file_id,
		);
		$this->image_mod->add($add_image);
		}
		//编辑goods表	
		$this->_goods_mod->edit($goods_id, array('default_spec'=>$spec_id));
	}
	$this->caigou_k_mod->edit('id='.$fabuid,array('fabu'=>'1'));
	$this->show_message('fabu','back_list','index.php?app=my_goods');
	}
	
	function fabu()
	{
		$this->member_mod=& m('member');
		$this->caigou_mod=& m('caigou');
		$this->gonghuo_mod=& m('gonghuo');
		$this->spec_mod  =& m('goodsspec');
        $this->image_mod =& m('goodsimage');
        $this->uploadedfile_mod =& m('uploadedfile');
		$this->categorygoods_mod =& m('categorygoods');
		$this->_goods_mod =& m('goods');
		$this->goods_statistics_mod  =& m('goodsstatistics');
		$user_id = $this->visitor->get('user_id');
		//$this->goods_mod =& bm('goods', array('_store_id' => $user_id));
	 /* 检测支付方式、配送方式、商品数量等 */
	if (!$this->_addible()) {
            return;
        }

	  $member_row=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id='$user_id' limit 1");
	  $mcity=$member_row['city'];	
	  $fabuid = empty($_GET['fabuid']) ? null : trim($_GET['fabuid']);//发布id
 
	$id = empty($_GET['gh_id']) ? null : trim($_GET['gh_id']);//供货id
	$num = empty($_GET['num']) ? null : trim($_GET['num']);//供货id  
	//echo $id;
	$row_huo=$this->gonghuo_mod->getrow("select * from ".DB_PREFIX."gonghuo where gh_id = '$id'");
	$goodid=$row_huo['goods_id'];
	$gong_id=$row_huo['user_id'];//供货人的用户id
	$canshu=$this->member_mod->can();
	
	$goo=$this->_goods_mod->getRow("select g.*,gs.spec_1,gs.spec_2,gs.stock,gs.sku from ".DB_PREFIX."goods g " .
				" left join " .DB_PREFIX. "goods_spec gs on gs.spec_id=g.default_spec ".
				" where g.goods_id = '$goodid'");
	
	
	//增加到商品goods表
	
	$add_goods=array(
	'store_id'=>$user_id,//商品店铺id
	'goods_name'=>$goo['goods_name'],
	'brand'=>$goo['brand'],
	'type'=>$goo['material'],
	'default_image'=>$goo['default_image'],
	'price'=>$goo['price'],
	'jifen_price'=>$goo['jifen_price'],
	'vip_price'=>$goo['vip_price'],
	'cityhao'=>$mcity,
	'description'=>addslashes($goo['description']),
	'cate_id'=>$goo['cate_id'],
	'cate_name'=>$goo['cate_name'],
	'spec_name_1'=>$goo['spec_name_1'],
	'spec_name_2'=>$goo['spec_name_2'],
	'if_show'=>0,
	'closed'=>0,
	'add_time'=>gmtime(),
	'last_update'=>gmtime(),
	'default_spec'=>$goo['default_spec'],
	'recommended'=>0,
	'cate_id_1'=>$goo['cate_id_1'],
	'cate_id_2'=>$goo['cate_id_2'],
	'cate_id_3'=>$goo['cate_id_3'],
	'cate_id_4'=>$goo['cate_id_4'],
	'daishou'=>1,
	'tags'=>$goo['tags'],
	'gong_id'=>$id,//供货id
	'ger_id'=>$gong_id,//供货人的id	
	);		
	$goods_id =$this->_goods_mod->add($add_goods); 

	//增加商品category_goods表
	$add_cate=array(
	'cate_id'=>0,
	'goods_id'=>$goods_id,
	);

	$this->categorygoods_mod->add($add_cate);
	
	//增加商品goods_statistics表
		$add_statis=array(
		'goods_id'=>$goods_id,
		'views'=>0,
		'collects'=>0,
		'carts'=>0,
		'orders'=>0,
		'sales'=>0,
		'comments'=>0,
		);
	$this->goods_statistics_mod->add($add_statis);	
	
	//增加商品goods_spec表
	$add_spec=array(
	'goods_id'=>$goods_id,
	'price'=>$goo['price'],
	'jifen_price'=>$goo['jifen_price'],
	'vip_price'=>$goo['vip_price'],
	'stock'=>$num,
	'sku'=>$goo['sku'],
	'spec_1'=>$goo['spec_1'],
	'spec_2'=>$goo['spec_2']
	);
	$spec_id=$this->spec_mod->add($add_spec);
	$gim=$this->_goods_mod->getAll("select uf.*,gi.* from ".DB_PREFIX."uploaded_file uf " .
				" left join " .DB_PREFIX. "goods_image gi on uf.file_id=gi.file_id ".
				" where uf.item_id = '$goodid'");	
	foreach($gim as $tp)
	{
	//增加商品uploaded_file表
		$add_file=array(
		'store_id'=>$user_id,
		'file_type'=>'image/jpeg',
		'file_size'=>$tp['file_size'],
		'file_name'=>$tp['name'],
		'file_path'=>$tp['file_path'],
		'add_time'=>gmtime(),
		'belong'=>2,
		'item_id'=>$goods_id,
		);
	$file_id=$this->uploadedfile_mod->add($add_file);
	//增加goods_image表
		$add_image=array(
		'goods_id'=>$goods_id,
		'image_url'=>$tp['image_url'],
		'thumbnail'=>$tp['thumbnail'],
		'sort_order'=>255,
		'file_id'=>$file_id,
		);
		
	$this->image_mod->add($add_image);
	}
	//编辑goods表
	$edit_good=array(
	'default_spec'=>$spec_id,
	);
	
	$this->_goods_mod->edit($goods_id, $edit_good);
 	$this->caigou_mod->edit('id='.$fabuid,array('fabu'=>'1'));
	$this->show_message('fabu',
    'back_list',    'index.php?app=my_goods');
	}
	
	function quxiaocaigou()
	{
		$this->caigou_mod=& m('caigou');
		$id = intval($_GET['id']);//供货id
		$sql="delete from ".DB_PREFIX."caigou where id = '$id'";
		$this->caigou_mod->db->query($sql);
		$this->show_message('delete','back_list','index.php?app=my_theme&act=my_caigou');
	}
	function gonghuo_delete()
	{
		$this->gonghuo_mod=& m('gonghuo');
		$gh_id = intval($_GET['gh_id']);//供货id
		$sql="delete from ".DB_PREFIX."gonghuo where gh_id = '$gh_id'";
		$this->gonghuo_mod->db->query($sql);
		$this->show_message('delete','back_list','index.php?app=my_theme&act=jibenxinxi');
	}
	function ghxx_delete()
	{ 
		$this->gonghuo_xinxi_mod=& m('gonghuo_xinxi');
		$gh_id = intval($_GET['gh_id']);//供货id
		$sql="delete from ".DB_PREFIX."gonghuo_xinxi where gh_id = '$gh_id'";
		$this->gonghuo_xinxi_mod->db->query($sql);
		$this->show_message('delete','back_list','index.php?app=my_theme&act=shangjiaxinxi');
	}
	
	function gh_edit()
	{
		$this->gonghuo_mod=& m('gonghuo');
	  $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
    $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
	
	 $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuo'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	
	$gh_id = empty($_GET['gh_id']) ? 0 : $_GET['gh_id'];

       /* if (!$city_id)
        {
            $this->show_warning('no_youhuiquan');
            return;
        }*/
         if (!IS_POST)
        {
            $find_data     = $this->gonghuo_mod->find($gh_id);
            if (empty($find_data))
            {
                $this->show_warning('no_gonghuo');

                return;
            }
            $gonghuo    =   current($find_data);
            if ($gonghuo['ziliao'])
            {
                $gonghuo['ziliao']  =   dirname(site_url()) . "/" . $gonghuo['ziliao'];
            }
			 if ($gonghuo['chanpin'])
            {
                $gonghuo['chanpin']  =   dirname(site_url()) . "/" . $gonghuo['chanpin'];
            }
			
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('gonghuo', $gonghuo);
            $this->display('gh_edit.html');
        }
        else
        {
		 
            $data = array();
			$data['goods_name']    =   $_POST['goods_name'];
			$data['goods_brand']    =   $_POST['goods_brand'];
			$data['tujing']    =   $_POST['tujing'];
			$data['cankao_price']    =   $_POST['cankao_price'];
			$data['lingshou_price']    =   $_POST['lingshou_price'];
			$data['pifa_price']    =   $_POST['pifa_price'];
			$data['yuji_price']    =   $_POST['yuji_price'];
			$data['source']    =   $_POST['source'];
			$data['beizhu']    =   $_POST['beizhu'];
            //$ziliao               =   $this->_upload_logo($gh_id);
			$logo       =   $this->_upload_logo($gh_id,'ziliao');
			$logo1       =   $this->_upload_logo($gh_id,'chanpin');
			
           
			 $logo && $this->gonghuo_mod->edit($gh_id, array('ziliao' => $logo));
			$logo1 && $this->gonghuo_mod->edit($gh_id, array('chanpin' => $logo1));
            $rows=$this->gonghuo_mod->edit($gh_id, $data);
            if ($this->gonghuo_mod->has_error())
            {
                $this->show_warning($this->gonghuo_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=my_theme&act=jibenxinxi');
        }
	
	
	}
	
	function ghxx_edit()
	{
		$this->gonghuo_xinxi_mod=& m('gonghuo_xinxi');
	  $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
   
	
	 $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuo'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
	
	$gh_id = empty($_GET['gh_id']) ? 0 : $_GET['gh_id'];

      
         if (!IS_POST)
        {
            $find_data     = $this->gonghuo_xinxi_mod->find($gh_id);
            if (empty($find_data))
            {
                $this->show_warning('no_gonghuo');

                return;
            }
            $gonghuo    =   current($find_data);
           
			
			
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('gonghuo', $gonghuo);
            $this->display('ghxx_edit.html');
        }
        else
        {
		
         $name = trim($_POST['name']);
		 $sex = trim($_POST['sex']);
		 $age = trim($_POST['age']);
		 $lxfs = trim($_POST['lxfs']);
		 $wangdian = trim($_POST['wangdian']);
		 $fenxiao = trim($_POST['fenxiao']);
		 $gongsi_name = trim($_POST['gongsi_name']);
		 $address = trim($_POST['address']);
		 $lianxiren = trim($_POST['lianxiren']);
		 $method = trim($_POST['method']);
		 $status = trim($_POST['status']);
		 $beizhu = $_POST['beizhu'];
		 $gh_id = trim($_POST['gh_id']);

	  $data = array(
	       
				'name'  => $name,
                'sex'  =>  $sex,
                'age'  => $age,
				'lxfs'  => $lxfs,
                'wangdian' => $wangdian ,
				'fenxiao'  => $fenxiao,
				'gongsi_name'  =>$gongsi_name,
				'address'  => $address,
				'lianxiren'  => $lianxiren,
				'method'  =>$method,
				'status'  => 1,
				'beizhu'  => $beizhu
				);
			
		  $this->gonghuo_xinxi_mod->edit('gh_id='.$gh_id, $data);
		  
			$logo       =   $this->_upload_logo($gh_id,'changjia');
			$logo1       =   $this->_upload_logo($gh_id,'zhizhao');
			$logo2       =   $this->_upload_logo($gh_id,'jigou');
			$logo3       =   $this->_upload_logo($gh_id,'shuiwu');
			$logo4       =   $this->_upload_logo($gh_id,'qita_1');
			$logo5       =   $this->_upload_logo($gh_id,'qita_2');
			$logo6       =   $this->_upload_logo($gh_id,'qita_3');
			
			
            $logo && $this->gonghuo_xinxi_mod->edit($gh_id, array('changjia' => $logo)); 
			$logo1 && $this->gonghuo_xinxi_mod->edit($gh_id, array('zhizhao' => $logo1)); 
			$logo2 && $this->gonghuo_xinxi_mod->edit($gh_id, array('jigou' => $logo2)); 
			$logo3 && $this->gonghuo_xinxi_mod->edit($gh_id, array('shuiwu' => $logo3));
			$logo4 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_1' => $logo4));
			$logo5 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_2' => $logo5));
			$logo6 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_3' => $logo6));

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=my_theme&act=shangjiaxinxi');
        }
	
	
	}
	
	
	
	
	
	function _addible()
    {

        $payment_mod =& m('payment');
	  $user_id= $this->visitor->get('user_id');
	
		$this->goods_mod =& bm('goods', array('_store_id' => $user_id));
        $payments = $payment_mod->get_enabled($user_id);
        if (empty($payments))
        {
            $this->show_message('please_install_payment', 'go_payment', 'index.php?app=my_payment');
                  return false;
        }

        $shipping_mod =& m('shippings');
        $shippings = $shipping_mod->find("store_id = '$user_id'");
        if (empty($shippings))
        {
                  $this->show_message('please_install_shipping', 'go_shipping', 'index.php?app=my_shipping');
                  return false;
        }

        /* 判断商品数是否已超过限制 */
        $store_mod =& m('store');

        $settings = $store_mod->get_settings($user_id);
	
        if ($settings['goods_limit'] > 0)
        {
                  $goods_count = $this->goods_mod->get_count();
                  if ($goods_count >= $settings['goods_limit'])
                  {
                         $this->show_warning('goods_limit_arrived');
                         return false;
                  }
        }
	
        return true;
    }
	
	function ghsq()
	{
	 $this->member_mod =& m('member');
	 $this->gonghuo_xinxi_mod =& m('gonghuo_xinxi');
	 $user_id = $this->visitor->get('user_id');	 
	 $user_name = $this->visitor->get('user_name');	 
	$row_mem=$this->member_mod->getRow("select * from ".DB_PREFIX."member where user_id = '$user_id' limit 1");
	$city=$row_mem['city'];
$xinxi=$this->gonghuo_xinxi_mod->getRow("select * from ".DB_PREFIX."gonghuo_xinxi where user_id = '$user_id' limit 1");
	if($_POST)
	{
	 $name = trim($_POST['name']);
	 $sex = trim($_POST['sex']);
	 $age = trim($_POST['age']);
	 $lxfs = trim($_POST['lxfs']);
	 $wangdian = trim($_POST['wangdian']);
	 $fenxiao = trim($_POST['fenxiao']);
	 $gongsi_name = trim($_POST['gongsi_name']);
	 $address = trim($_POST['address']);
	 $lianxiren = trim($_POST['lianxiren']);
	 $method = trim($_POST['method']);
	 $status = trim($_POST['status']);
	 $beizhu = $_POST['beizhu'];

	  $data = array(
	            'user_id' => $user_id ,
				'user_name' => $user_name ,
				'name'  => $name,
                'sex'  =>  $sex,
                'age'  => $age,
				'lxfs'  => $lxfs,
                'wangdian' => $wangdian ,
				'fenxiao'  => $fenxiao,
				'gongsi_name'  =>$gongsi_name,
				'address'  => $address,
				'lianxiren'  => $lianxiren,
				'method'  =>$method,
				'city'  => $city,
				'status'  => 1,
				'riqi'  => date('Y-m-d H:i:s'),
				'beizhu'  => $beizhu
				);
				
				
			if(empty($xinxi))
			{
				if (!$gh_id = $this->gonghuo_xinxi_mod->add($data))  //获取id
				{
					$this->show_warning($this->gonghuo_xinxi_mod->get_error());
					return;
				}
			}
			else
			{
				$gh_id=$xinxi['gh_id'];
				 $rows=$this->gonghuo_xinxi_mod->edit($gh_id, $data);
			}
            /* 处理上传的图片 */
			$logo       =   $this->_upload_logo($gh_id,'changjia');
			$logo1       =   $this->_upload_logo($gh_id,'zhizhao');
			$logo2       =   $this->_upload_logo($gh_id,'jigou');
			$logo3       =   $this->_upload_logo($gh_id,'shuiwu');
			$logo4       =   $this->_upload_logo($gh_id,'qita_1');
			$logo5       =   $this->_upload_logo($gh_id,'qita_2');
			$logo6       =   $this->_upload_logo($gh_id,'qita_3');
			
			 if ($logo === false)
            {
                return;
            }
          
			$logo && $this->gonghuo_xinxi_mod->edit($gh_id, array('changjia' => $logo)); 
			$logo1 && $this->gonghuo_xinxi_mod->edit($gh_id, array('zhizhao' => $logo1)); 
			$logo2 && $this->gonghuo_xinxi_mod->edit($gh_id, array('jigou' => $logo2)); 
			$logo3 && $this->gonghuo_xinxi_mod->edit($gh_id, array('shuiwu' => $logo3)); 
			$logo4 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_1' => $logo4));
			$logo5 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_2' => $logo5));
			$logo6 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_3' => $logo6)); 

          $this->show_message('tijiaochenggong','','index.php?app=my_theme&act=shangjiaxinxi');
	}
	else
	{
		$user_id = $this->visitor->get('user_id');	    
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuo'));

            /* 当前用户中心菜单 */
            $this->_curitem('gonghuo');
			$user = $this->visitor->get();
			$gonghuo=$this->gonghuo_xinxi_mod->getRow("select * from ".DB_PREFIX."gonghuo_xinxi where gh_id = '$gh_id' limit 1");
				  $this->assign('user', $user);
				  $this->assign('gonghuo', $gonghuo);
				  $this->show_message('shenqinggonghuo');
				  $this->display('gh_shenqing.html');
			 
			
			}
			
	}
	function _upload_logo($gh_id,$can)
    {
        $file = $_FILES[$can];
		$riqi=time().rand(100,999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo
		
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=my_theme&amp;act=gonghuo');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/store_' . $this->visitor->get('user_id') . '/goods_' . (time() % 200), $riqi.$gh_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }
	
	
	
	
	
	
}
/*三级菜单*/
    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name' => 'theme_config',
                'url'  => 'index.php?app=my_theme',
            ),
            );
        return $menus;
    }
?>