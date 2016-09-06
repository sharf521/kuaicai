<?php

/**
 *    买家的订单管理控制器
 *
 * @author    Garbin
 * @usage    none
 */
class Buyer_orderApp extends MemberbaseApp
{
    function index()
    {
        /* 获取订单列表 */
        $this->_get_orders();

        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('my_order'), 'index.php?app=buyer_order',
            LANG::get('order_list'));

        /* 当前用户中心菜单 */
        $this->_curitem('my_order');
        $this->_curmenu('order_list');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));
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
            'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));


        /* 显示订单列表 */
        $this->display('buyer_order.index.html');
    }

    /**
     *    查看订单详情
     *
     * @author    Garbin
     * @return    void
     */
    function view()
    {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        $model_order =& m('order');
        $kaiguan = $model_order->kg();
        $kaiguan = $this->assign('kaiguan', $kaiguan);
        $canshu = $model_order->can();
        $bili = $canshu['jifenxianjin'];
        //$order_info  = $model_order->get("order_id={$order_id} AND buyer_id=" . $this->visitor->get('user_id'));
        $order_info = $model_order->get(array(
            'fields' => " order_alias.*,s.store_name,s.tel,s.im_qq,s.im_ww,s.im_msn,s.address,s.region_name ",
            'conditions' => "order_id={$order_id} AND buyer_id=" . $this->visitor->get('user_id'),
            'join' => 'belongs_to_store',
        ));


        if (!$order_info) {
            $this->show_warning('no_such_order');

            return;
        }

        /* 团购信息 */
        if ($order_info['extension'] == 'groupbuy') {
            $groupbuy_mod = &m('groupbuy');
            $group = $groupbuy_mod->get(array(
                'join' => 'be_join',
                'conditions' => 'order_id=' . $order_id,
                'fields' => 'gb.group_id',
            ));
            $this->assign('group_id', $group['group_id']);
        }

        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('my_order'), 'index.php?app=buyer_order',
            LANG::get('view_order'));

        /* 当前用户中心菜单 */
        $this->_curitem('my_order');

        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('order_detail'));

        /* 调用相应的订单类型，获取整个订单详情数据 */
        $order_type =& ot($order_info['extension']);
        $order_detail = $order_type->get_order_detail($order_id, $order_info);

        foreach ($order_detail['data']['goods_list'] as $key => $goods) {
            empty($goods['goods_image']) && $order_detail['data']['goods_list'][$key]['goods_image'] = Conf::get('default_goods_image');
        }

        $buyer_id = $order_info['buyer_id'];
        $result = $model_order->getRow("select * from " . DB_PREFIX . "member where user_id='$buyer_id' limit 1");
        $vip = $result['vip'];
        /*if($vip==1)
        {		
            $order_info['shipping_jifen']=$order_detail['data']['order_extm']['shipping_fee']*$bili*(1+$canshu['lv21']); 
        }
        else
        {
            $order_info['shipping_jifen']=$order_detail['data']['order_extm']['shipping_fee']*$bili*(1+$canshu['lv31']); 
        }*/

        if ($order_info['discount_jifen'] == 0.00) {
            $order_info['jifen'] = $order_info['youhuidiscount_jifen'];
        } else {
            $order_info['jifen'] = $order_info['discount_jifen'];
        }
        if ($order_info['pay_time'] != "") {
            $order_info['pay_time'] = date('Y-m-d H:i:s', $order_info['pay_time']);
        }

        if (!empty($order_info['zhe_jifen']))//是否参与打折
        {
            $order_info['dazhe'] = $order_info['zhe_jifen'] * $order_info['order_jifen'];
            $order_info['zhe_jifen'] = $order_info['zhe_jifen'] * 10;
        }


        $this->assign('order', $order_info);
        $this->assign($order_detail['data']);
        $this->display('buyer_order.view.html');
    }

    /**
     *    取消订单
     *
     * @author    Garbin
     * @return    void
     */
    function cancel_order()
    {

        $this->message_mod =& m('message');
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if (!$order_id) {
            echo Lang::get('no_such_order');
            return;
        }
        $model_order =& m('order');
        $user_id = $_POST['user_id'];
        if ($user_id == "") {
            $user_id = $this->visitor->get('user_id');
        }

        /* 只有待付款的订单可以取消 */
        $order_info = $model_order->get("order_id={$order_id} AND buyer_id=" . $user_id . " AND status " . db_create_in(array(ORDER_PENDING, ORDER_SUBMITTED)));
        if (empty($order_info)) {
            echo Lang::get('no_such_order');
            return;
        }
        if (!IS_POST) {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('buyer_order.cancel.html');
        } else {
            $model_order->edit($order_id, array('status' => ORDER_CANCELED));
            if ($model_order->has_error()) {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }

            /* 加回商品库存 */
            $model_order->change_stock('+', $order_id);
            $cancel_reason = (!empty($_POST['remark'])) ? $_POST['remark'] : $_POST['cancel_reason'];
            if ($cancel_reason == "") {
                $cancel_reason = Lang::get('maijiaweizhifu');
            }

            $usename = iconv('utf-8', 'gbk', $_POST['user_name']);
            if ($usename == "") {
                $usename = $this->visitor->get('user_name');
            }
            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id' => $order_id,
                'operator' => addslashes($usename),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_CANCELED),
                'remark' => $cancel_reason,
                'log_time' => gmtime(),
            ));

            /* 发送给卖家订单取消通知 */
            $model_member =& m('member');
            $seller_info = $model_member->get($order_info['seller_id']);
            $mail = get_mail('toseller_cancel_order_notify', array('order' => $order_info, 'reason' => $_POST['remark']));
            //$this->_mailto($seller_info['email'], addslashes($mail['subject']), addslashes($mail['message']));

            //sendmail(addslashes($mail['subject']),addslashes($mail['message']),$seller_info['email']);
            $new_data = array(
                'status' => Lang::get('order_canceled'),
                'actions' => array(), //取消订单后就不能做任何操作了
            );
            $seller_id = $order_info['seller_id'];
            $sell = $model_member->getRow("select user_name from " . DB_PREFIX . "member where user_id='$seller_id' limit 1");
            $sell_name = $sell['user_name'];
            $notice = Lang::get('quxiaodan');
            $notice = str_replace('{1}', $sell_name, $notice);
            $notice = str_replace('{2}', $order_info['buyer_name'], $notice);
            $notice = str_replace('{3}', $order_info['order_sn'], $notice);
            $add_notice = array(
                'from_id' => 0,
                'to_id' => $order_info['seller_id'],
                'content' => $notice,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice);
            $this->pop_warning('ok');
        }

    }

    /**
     *    确认订单
     *
     * @author    Garbin
     * @return    void
     */
    function confirm_order()
    {
        $this->kaiguan_mod =& m('kaiguan');
        $this->message_mod =& m('message');
        $row_kaiguan = $this->kaiguan_mod->getRow("select webservice from " . DB_PREFIX . "kaiguan limit 1");
        $webservice = $row_kaiguan['webservice'];
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if (!$order_id) {
            echo Lang::get('no_such_order');
            return;
        }
        $model_order =& m('order');
        $user_id = $_POST['user_id'];
        if ($user_id == "") {
            $user_id = $this->visitor->get('user_id');
        }
        /* 只有已发货的订单可以确认 */
        $order_info = $model_order->get("order_id={$order_id} AND buyer_id=" . $user_id . " AND status=" . ORDER_SHIPPED);
        if (empty($order_info)) {
            echo Lang::get('no_such_order');
            return;
        }
        if (!IS_POST) {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('buyer_order.confirm.html');
        } else {

            $this->member_mod =& m('member');
            $this->order_mod =& m('order');
            $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
            $user_row = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $buy_web_id = $user_row['web_id'];//买家
            $vip = $user_row['vip'];//买家

            $model_order->edit($order_id, array('status' => ORDER_FINISHED, 'finished_time' => gmtime()));
            if ($model_order->has_error()) {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));
                return;
            }

            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $riqi = date('Y-m-d H:i:s');
            $usename = iconv('utf-8', 'gbk', $_POST['user_name']);
            if ($usename == "") {
                $usename = $this->visitor->get('user_name');
            }

            $order_log->add(array(
                'order_id' => $order_id,
                'operator' => addslashes($usename),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_FINISHED),
                'remark' => Lang::get('buyer_confirm'),
                'log_time' => gmtime(),
                'time' => $riqi,
            ));

            /* by:xiaohei QQ:77491010 商付通 更新商付通定单状态 开始******************************************************/
            $this->my_money_mod =& m('my_money');
            $this->moneylog_mod =& m('moneylog');
            $this->my_moneylog_mod =& m('my_moneylog');
            $this->order_mod =& m('order');
            $this->message_mod =& m('message');
            $this->canshu_mod =& m('canshu');
            $this->webservice_list_mod =& m('webservice_list');
            $this->accountlog_mod =& m('accountlog');
            $this->store_mod =& m('store');
            $canshu = $this->member_mod->can();
            $jifenxianjin = $canshu['jifenxianjin'];
            $lv21 = $canshu['lv21'];
            $lv31 = $canshu['lv31'];
            $daishou_bili = $canshu['daishou'];
            $account_zongmoney = $canshu['zong_money'];
            $account_zongjifen = $canshu['zong_jifen'];
            $order_row = $this->order_mod->getRow("select o.*,ex.shipping_fee from " . DB_PREFIX . "order o left join " . DB_PREFIX . "order_extm ex on ex.order_id=o.order_id where o.order_id='$order_id' limit 1");

            $goods_amount = $order_row['goods_amount'];//定单价格
            $order_amount = $order_row['order_amount'];//定单价格
            $order_amount_m = $order_row['order_amount_m'];//定单价格(抬高的)
            $order_jifen = $order_row['order_jifen'];//定单积分
            $sell_jifen = $order_row['fanhuan_jia'];//定单积分
            $sell_fanhuan = $order_row['fanhuan_jia'];//定单积分
            $order_sn = $order_row['order_sn'];//定单号
            $daishou = $order_row['daishou'];
            $shippingfee = $order_row['shipping_fee'];
            $sell_back = ($order_amount_m - $order_amount) * 100 / 21;//卖家实际返还金额
            //$sell_back=($order_amount_m-$shippingfee)/(1+$lv21);//卖家实际返还金额
            //$sell_back_money=$sell_back+$shippingfee;//卖家实际得到的金额
            //$sell_back_jifen=(int)(($sell_back+$shippingfee)*$jifenxianjin*100000)/100000;//卖家实际得到的积分
            $sell_back_money = $order_amount;//卖家实际得到的金额
            $sell_back_jifen = $sell_back_money * $jifenxianjin;
            $jifen_zhesuan = $order_amount * $jifenxianjin;//若是正常商品,定单积分返还价

            $my_moneylog_row = $this->my_moneylog_mod->getRow("select * from " . DB_PREFIX . "my_moneylog where order_id='$order_id' and s_and_z=2 and caozuo=20 limit 1");
            //$money=$my_moneylog_row['money'];//定单价格
            //$money=$my_moneylog_row['money_dj'];//定单价格
            //$dongjiejifen=$my_moneylog_row['dongjiejifen'];//定单积分
            //若是正常商品

            $sell_user_id = $my_moneylog_row['seller_id'];//卖家ID
            $buyer_user_id = $my_moneylog_row['buyer_id'];//买家ID
            $buyer_user_name = $my_moneylog_row['buyer_name'];//买家用户名
            $sell_user_name = $my_moneylog_row['seller_name'];//卖家用户名
            $riqi = date('Y-m-d H:i:s');
            $city = $my_moneylog_row['city'];

            $zhifufangshi = $order_row['zhifufangshi'];
            $is_gh = $order_row['is_gh'];
            $gh_id = $order_row['gh_id'];
            $lev = $this->order_mod->getRow("select level,web_id from " . DB_PREFIX . "member where user_id='$sell_user_id' limit 1");//卖家
            $bb = explode(',', $lev['level']);
            $sell_web_id = $lev['web_id'];
            //推荐奖励
            if ($is_gh == 1) {
                $go_row = $this->order_mod->getRow("select user_id,user_name from " . DB_PREFIX . "gonghuo where gh_id='$gh_id' limit 1");

                $gong_userid = $go_row['user_id'];//供货人的用户id
                $gong_username = $go_row['user_name'];//供货人的用户名	
                $go_row = $this->my_money_mod->getRow("select money,money_dj,duihuanjifen,dongjiejifen,city from " . DB_PREFIX . "my_money where user_id='$gong_userid' limit 1");
                $gong_u_money = $go_row['money'];
                $gong_u_moeny_dj = $go_row['money_dj'];
                $gong_u_jf_duihuan = $go_row['duihuanjifen'];
                $gong_u_jf_dongjie = $go_row['dongjiejifen'];
                $gong_city = $go_row['city'];

                $row = $this->my_money_mod->getRow("select gettype from " . DB_PREFIX . "store where store_id='$gong_userid' limit 1");
                $go_gettype = $row['gettype'];//供货人收款方式
                $row = null;
            }

            if ($my_moneylog_row['order_id'] == $order_id) {

                $sell_money_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id' limit 1");
                $sell_money = $sell_money_row['money'];//卖家的资金
                $sell_money_dj = $sell_money_row['money_dj'];//卖家的冻结资金
                $sell_username = $sell_money_row['user_name'];
                $sell_city = $sell_money_row['city'];
                $sell_duihuanjifen = $sell_money_row['duihuanjifen'];//卖家的积分
                $sell_dongjiejifen = $sell_money_row['dongjiejifen'];//卖家的冻结积分
                $sell_yingde_jifen = $sell_money_row['yingde_jifen'];//卖家的累计应得积分
                $buy_money_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$buyer_user_id' limit 1");
                $buy_yingde_jifen = $buy_money_row['yingde_jifen'];//买家的累计应得积分
                $buy_city = $buy_money_row['city'];

                $row = $this->my_money_mod->getRow("select gettype from " . DB_PREFIX . "store where store_id='$sell_user_id' limit 1");
                $sell_gettype = $row['gettype'];//卖家收款方式
                $row = null;

//买家对接开始

//****		
//更新数据		
                if ($is_gh == 1)//更新供货人的金钱或积分
                {
                    //买家对接开始
                    if ($webservice == "yes") {
                        $tem = format_price($sell_jifen / $jifenxianjin);
                        $post_data = array(
                            "ID" => $buy_web_id,
                            "Money" => $tem,
                            "MoneyType" => 1,
                            "Count" => 1
                        );
                        if ($sell_jifen >= 1) {
                            $web_id = webService('C_Consume', $post_data);
                            $daa = array(
                                "gong_id" => $buyer_user_id,
                                "type" => 1,
                                "money" => $tem,
                                "time" => date('Y-m-d H:i:s'),
                                "consume_id" => $web_id,
                                "status" => 0,
                                "city" => $buy_city
                            );
                            $this->webservice_list_mod->add($daa);
                        }
                    }


                    if (in_array(1, $bb))//若是付费商家
                    {
                        if ($go_gettype == 1) {
                            $gonghuo_jifen = $sell_jifen * (1 - $daishou_bili);
                            $gonghuo_money = 0;
                        } else {
                            $gonghuo_money = $order_amount * (1 - $daishou_bili);
                            $gonghuo_jifen = 0;
                        }

                        $new_gong_money_array = array(
                            'duihuanjifen' => $gong_u_jf_duihuan + $gonghuo_jifen,
                            'money' => $gong_u_money + $gonghuo_money
                        );
                        //添加供货人的moneylog
                        $beizhu = Lang::get('dingdanhao') . $order_sn;
                        $gong_money = array(
                            'money' => $gonghuo_money,
                            'jifen' => $gonghuo_jifen,
                            'time' => $riqi,
                            'user_name' => $gong_username,
                            'user_id' => $gong_userid,
                            'zcity' => $gong_city,
                            'type' => 17,
                            's_and_z' => 1,
                            'beizhu' => $beizhu,
                            'orderid' => $order_id,
                            'dq_money' => $gong_u_money + $gonghuo_money,
                            'dq_money_dj' => $gong_u_moeny_dj,
                            'dq_jifen' => $gong_u_jf_duihuan + $gonghuo_jifen,
                            'dq_jifen_dj' => $gong_u_jf_dongjie
                        );

                        $beizhu1 = Lang::get('dingdanhao') . $order_sn;
                        $addaccount = array(
                            'jifen' => '-' . $gonghuo_jifen,
                            'money' => '-' . $gonghuo_money,
                            'time' => $riqi,
                            'user_name' => $gong_username,
                            'user_id' => $gong_userid,
                            'zcity' => $gong_city,
                            'type' => 17,
                            's_and_z' => 2,
                            'beizhu' => $beizhu1,
                            'dq_money' => $account_zongmoney - $gonghuo_money,
                            'dq_jifen' => $account_zongjifen - $gonghuo_jifen,
                            'xiaofei' => $buyer_user_name,
                            'shangjia' => $sell_username,
                            'gonghuoshang' => $gong_username
                        );
                        $edit_account = array(
                            'zong_jifen' => $account_zongjifen - $gonghuo_jifen,
                            'zong_money' => $account_zongmoney - $gonghuo_money
                        );

                        $this->accountlog_mod->add($addaccount);
                        $this->my_money_mod->edit('user_id=' . $gong_userid, $new_gong_money_array);
                        $this->moneylog_mod->add($gong_money);
                        $this->canshu_mod->edit('id=1', $edit_account);

                        $notice = Lang::get('zunjing');
                        $notice = str_replace('{1}', $sell_user_name, $notice);
                        $notice = str_replace('{2}', $sell_jifen, $notice);

                        $add_notice = array(
                            'from_id' => 0,
                            'to_id' => $sell_user_id,
                            'content' => $notice,
                            'add_time' => gmtime(),
                            'last_update' => gmtime(),
                            'new' => 1,
                            'parent_id' => 0,
                            'status' => 3,
                        );

                        $this->message_mod->add($add_notice);

                        $this->my_money_mod->edit('user_id=' . $sell_user_id, array('yingde_jifen' => $sell_yingde_jifen + $sell_jifen));//添加卖家的累计应得积分
                        //$this->order_mod->edit('order_id='.$order_id,array('yingde_jifen'=>$jifen_zhesuan));//添加卖家的应得积分
                        $this->order_mod->edit('order_id=' . $order_id, array('yingde_jifen' => $sell_jifen));//添加卖家的应得积分
                        $ri = date('Y-m-d H:i:s');

                        if ($webservice == "yes")//卖家对接webservice开始
                        {
                            $tem = format_price($sell_jifen / $jifenxianjin);
                            $post_data = array(
                                "ID" => $sell_web_id,
                                "Money" => $tem,//订单积分
                                "MoneyType" => 1,
                                "Count" => 1
                            );
                            if ($sell_jifen >= 1) {
                                $web_id = webService('C_Consume', $post_data);
                                $da = array(
                                    "gong_id" => $sell_user_id,
                                    "type" => 1,
                                    "money" => $tem,
                                    "time" => $ri,
                                    "consume_id" => $web_id,
                                    "status" => 0,
                                    "city" => $sell_city
                                );
                                $this->webservice_list_mod->add($da);
                            }
                        }
                        //卖家对接webservices结束				
                    } else//若是免费商家
                    {
                        $ord_row = $this->my_money_mod->getAll("select gh_id,quantity from " . DB_PREFIX . "order_goods where order_id='$order_id'");
                        $shouyi_jifen_count = 0;
                        foreach ($ord_row as $val) {
                            $ghid = $val['gh_id'];
                            $quantity = $val['quantity'];
                            $result = $this->my_money_mod->getAll("select id,consume_id from " . DB_PREFIX . "webservice_list where cai_id='$sell_user_id' and gong_id='$gong_userid' and gh_id=$ghid and status=0 limit 0,$quantity");
                            foreach ($result as $row) {
                                $con_id = $row['id'];
                                $consume_id = $row['consume_id'];//队列id
                                $shouyi_jifen = 0;
                                if ($webservice == "yes")//卖家对接webservice
                                {
                                    $shouyi_jifen = webService('C_Query', array("ID" => $consume_id));
                                    if ($shouyi_jifen < 0) {
                                        $shouyi_jifen = 0;
                                    }
                                    webService('C_Consume_Close', array("ID" => $consume_id));
                                }
                                $this->webservice_list_mod->edit('id=' . $con_id, array("jifen" => $shouyi_jifen, "status" => 1));//更新webservice_list表
                                $shouyi_jifen_count += $shouyi_jifen;
                            }
                            $result = null;
                        }
                        $ord_row = null;


                        //$shouyi_money=$shouyi_jifen_count/$jifenxianjin;				
                        $this->accountlog_mod =& m('accountlog');

                        if ($go_gettype == 1) {
                            $gong_shouyi = $sell_jifen - $shouyi_jifen_count;//供货人的收益积分
                            $gong_shouyi_money = 0;
                        } else {
                            $gong_shouyi_money = $order_amount;//供货人的收益钱
                            $gong_shouyi = '-' . $shouyi_jifen_count;
                        }

                        $log_text = Lang::get('dingdanhao') . $order_sn;
                        $addaccount = array(
                            'jifen' => '-' . $gong_shouyi,
                            'money' => '-' . $gong_shouyi_money,
                            'time' => $riqi,
                            'user_name' => $gong_username,
                            'user_id' => $gong_userid,
                            'zcity' => $gong_city,
                            'type' => 17,
                            's_and_z' => 2,
                            'beizhu' => $log_text,
                            'dq_money' => $account_zongmoney - $gong_shouyi_money,
                            'dq_jifen' => $account_zongjifen - $gong_shouyi,
                            'xiaofei' => $buyer_user_name,
                            'shangjia' => $sell_username,
                            'gonghuoshang' => $gong_username
                        );

                        //添加供货人的moneylog
                        $beizhu = Lang::get('dingdanhao') . $order_sn;
                        $gong_shouyi_array_log = array(
                            'jifen' => $gong_shouyi,
                            'money' => $gong_shouyi_money,
                            'time' => $riqi,
                            'user_name' => $gong_username,
                            'user_id' => $gong_userid,
                            'zcity' => $gong_city,
                            'type' => 17,
                            's_and_z' => 1,
                            'beizhu' => $beizhu,
                            'orderid' => $order_id,
                            'dq_money' => $gong_u_money + $gong_shouyi_money,
                            'dq_money_dj' => $gong_u_moeny_dj,
                            'dq_jifen' => $gong_u_jf_duihuan + $gong_shouyi,
                            'dq_jifen_dj' => $gong_u_jf_dongjie
                        );
                        $new_account_zongjifen = $account_zongjifen - $gong_shouyi;
                        $new_account_zongmoney = $account_zongmoney - $gong_shouyi_money;
                        $gong_shouyi_array = array(
                            'duihuanjifen' => $gong_u_jf_duihuan + $gong_shouyi,
                            'money' => $gong_u_money + $gong_shouyi_money
                        );

                        $this->accountlog_mod->add($addaccount);
                        $this->canshu_mod->edit('id=1', array("zong_jifen" => $new_account_zongjifen, "zong_money" => $new_account_zongmoney));
                        $this->my_money_mod->edit('user_id=' . $gong_userid, $gong_shouyi_array);
                        $this->moneylog_mod->add($gong_shouyi_array_log);
                    }

                    $this->order_mod->tuijian($sell_fanhuan, $buyer_user_id, $sell_user_id, $order_sn, $order_jifen);
                } else//添加普通商品的卖家操作日志
                {
                    $beizhu = Lang::get('dingdanhao') . $order_sn;

                    if ($sell_gettype == 1) {
                        $new_money_array = array('duihuanjifen' => $sell_duihuanjifen + $sell_back_jifen);
                        $sell_back_money = 0;
                    } else {
                        $new_money_array = array('money' => $sell_money + $sell_back_money);
                        $sell_back_jifen = 0;
                    }
                    $new_money = $sell_money + $sell_back_money;
                    $new_duihuanjifen = $sell_duihuanjifen + $sell_back_jifen;
                    $sell_money = array(
                        'jifen' => $sell_back_jifen,
                        'money' => $sell_back_money,
                        'time' => $riqi,
                        'user_name' => $sell_username,
                        'user_id' => $sell_user_id,
                        'zcity' => $sell_city,
                        'type' => 16,
                        's_and_z' => 1,
                        'beizhu' => $beizhu,
                        'orderid' => $order_id,
                        'dq_money' => $new_money,
                        'dq_money_dj' => $sell_money_dj,
                        'dq_jifen' => $new_duihuanjifen,
                        'dq_jifen_dj' => $sell_dongjiejifen
                    );
                    $log_text = Lang::get('dingdanhao') . $order_sn;
                    $addaccount = array(
                        'jifen' => '-' . $sell_back_jifen,
                        'money' => '-' . $sell_back_money,
                        'time' => $riqi,
                        'user_name' => $sell_username,
                        'user_id' => $sell_user_id,
                        'zcity' => $sell_city,
                        'type' => 16,
                        's_and_z' => 2,
                        'beizhu' => $log_text,
                        'dq_money' => $account_zongmoney - $sell_back_money,
                        'dq_jifen' => $account_zongjifen - $sell_back_jifen,
                        'xiaofei' => $buyer_user_name,
                        'shangjia' => $sell_username,
                        'gonghuoshang' => $gong_username
                    );

                    $edit_account = array(
                        'zong_jifen' => $account_zongjifen - $sell_back_jifen,
                        'zong_money' => $account_zongmoney - $sell_back_money
                    );
                    $this->my_money_mod->edit('user_id=' . $sell_user_id, $new_money_array);
                    $this->moneylog_mod->add($sell_money);
                    $this->accountlog_mod->add($addaccount);
                    $this->canshu_mod->edit('id=1', $edit_account);
                    if ($daishou != 3)//若商品不是采购过来的再销售
                    {
                        if ($webservice == "yes")//卖家对接webservice
                        {
                            $post_data = array(
                                "ID" => $sell_web_id,
                                "Money" => $sell_back,//订单积分
                                "MoneyType" => 1,
                                "Count" => 1
                            );

                            if ($daishou == 2)//商品是采购商品
                            {
                                $post_data['ID'] = $buy_web_id;
                            }

                            if ($sell_back * $jifenxianjin >= 1 && empty($order_row['zhe_jifen']))//没有使用快速通道，参加积分返还
                            {
                                $web_id = webService('C_Consume', $post_data);
                                $da = array(
                                    "gong_id" => $sell_user_id,
                                    "type" => 1,
                                    "money" => $sell_back,
                                    "time" => date('Y-m-d H:i:s'),
                                    "consume_id" => $web_id,
                                    "status" => 0,
                                    "city" => $sell_city
                                );
                            }
                            if ($daishou == 2 && empty($order_row['zhe_jifen']))//若是采购商品,且没有参加快速通道
                            {
                                $da['gong_id'] = $buyer_user_id;
                                $da['city'] = $buy_city;
                            }
                            $this->webservice_list_mod->add($da);
                        }
                    }
                    if ($daishou != 3)//若商品不是采购过来的再销售，则参加推荐奖励
                    {
                        $this->order_mod->tuijian($sell_back * $jifenxianjin, $buyer_user_id, $sell_user_id, $order_sn, $sell_back * $jifenxianjin);
                    }
                    //快速采购，采购商品
                    if ($daishou == 2) {
                        $this->caigou_k_mod =& m('caigou_k');
                        $result = $this->order_mod->getAll("select * from " . DB_PREFIX . "order_goods where order_id='$order_id'");
                        foreach ($result as $key => $var) {
                            $add_caigou = array(
                                //'gong_id'=>$gong_id,//供货人的用户id
                                //'gong_name'=>$gong_name,//供货人的用户名
                                'cai_id' => $buyer_user_id,//采购人的用户id
                                'cai_name' => $buyer_user_name,//采购人的用户名
                                'gh_id' => $var['goods_id'],// 供货id
                                'num' => $var['quantity'],
                                'goods_name' => $var['goods_name'],
                                'spec_id' => $var['spec_id'],
                                'lingshou_price' => $var['price_m'],
                                //'jifen_price'=>$jifen_price,
                                'city' => $buy_city,
                                'riqi' => date('Y-m-d H:i:s'),
                                'fabu' => 0,
                                'chanpin' => $var['goods_image'],
                                //'zhifufangshi'=>$zhifufangshi																		
                            );
                            $this->caigou_k_mod->add($add_caigou);

                        }
                    }
                }


                //$this->my_money_mod->edit('user_id='.$buyer_user_id,array('yingde_jifen'=>$buy_yingde_jifen+$jifen_zhesuan));//添加买家的累计应得积
                //$this->my_money_mod->edit('user_id='.$buyer_user_id,array('yingde_jifen'=>$buy_yingde_jifen+$sell_jifen));//添加买家的累计应得积
                //买家对接结束


                /*if($vip!=1 && $zhifufangshi=='jifenzhifu')
                {	
                    $this->member_mod->edit('user_id='.$buyer_user_id,array("vip"=>1));
                }*/
                $stor = $this->store_mod->getRow("select is_cai from " . DB_PREFIX . "store where store_id='$buyer_user_id' limit 1");
                if (!empty($stor))//判断是否符合采购条件
                {
                    if ($stor['is_cai'] != 1 && $order_amount_m >= 500) {
                        $this->store_mod->edit('store_id=' . $buyer_user_id, array("is_cai" => 1));
                    }
                }
//更新商付通log为 定单已完成


                $this->my_moneylog_mod->edit('order_id=' . $order_id, array('caozuo' => 40));

//增加积分
                $buyer_jifen_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$buyer_user_id' limit 1");
                $jifen = $buyer_jifen_row['jifen'];
                $jifen_z = $buyer_jifen_row['jifen_z'];
                if ($zhifufangshi == "jifenzhifu") {
                    $new_jifen = $jifen + $order_jifen;
                    $new_jifen_z = $jifen_z + $order_jifen;
                } else {
                    $jjfen = $order_amount * $jifenxianjin;
                    $new_jifen = $jifen + $jjfen;
                    $new_jifen_z = $jifen_z + $jjfen;
                }

                $new_jifen_array = array(
                    'jifen' => $new_jifen,
                    'jifen_z' => $new_jifen_z,
                );
                $this->my_money_mod->edit('user_id=' . $buyer_user_id, $new_jifen_array);

                /*//将消费积分通过比例换算增加到对应商品店铺
                $this->store_mod =& m('store');
                $credit_store=$this->store_mod->getrow("select store_jifen,credit_value from ".DB_PREFIX."store where store_id='$sell_user_id'");
                $ji=$credit_store['store_jifen'];
                $value=$credit_store['credit_value'];
                
                $this->canshu_mod =& m('canshu');
                $jifenbili_row=$this->canshu_mod->getrow("select jifenbili from ".DB_PREFIX."canshu");
                 
                $jifenbili=$jifenbili_row['jifenbili'];
                
                $storejifen=$money/$jifenbili;  //将消费积分按相应比例转换
                $new_storejifen=$ji+$storejifen;
                $new_sjifen=$value+$storejifen;
                $new_storejifen_array=array(
                'store_jifen'=>$new_storejifen,
                'credit_value'=>$new_sjifen,
                );
                $this->store_mod->edit('store_id='.$sell_user_id,$new_storejifen_array);*/

                /*$fp = fopen('ceshishuchu.txt', 'w');
                fwrite($fp, $storejifen);;
                fclose($fp);*/

            }

            /* by:xiaohei QQ:77491010 商付通 更新商付通定单状态 结束******************************************************/

            /* 发送给卖家买家确认收货邮件，交易完成 */
            $model_member =& m('member');
            $seller_info = $model_member->get($order_info['seller_id']);
            $mail = get_mail('toseller_finish_notify', array('order' => $order_info));
            //$this->_mailto($seller_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
            //sendmail(addslashes($mail['subject']),addslashes($mail['message']),$seller_info['email']);
            $new_data = array(
                'status' => Lang::get('order_finished'),
                'actions' => array('evaluate'),
            );

            /* 更新累计销售件数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $model_ordergoods =& m('ordergoods');
            $order_goods = $model_ordergoods->find("order_id={$order_id}");
            foreach ($order_goods as $goods) {
                $model_goodsstatistics->edit($goods['goods_id'], "sales=sales+{$goods['quantity']}");
            }

            $this->pop_warning('ok', '', 'index.php?app=buyer_order&act=evaluate&order_id=' . $order_id);
            //$this->show_message('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);
        }

    }

    /**
     *    给卖家评价
     *
     * @author    Garbin
     * @param    none
     * @return    void
     */
    function evaluate()
    {
        $this->_city_mod =& m('city');
        $this->message_mod =& m('message');
        //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
//	$city_id=$row_city['city_id'];
        $kaiguan = $this->_city_mod->kg();
        $cityrow = $this->_city_mod->get_cityrow();
        $city_id = $cityrow['city_id'];
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if (!$order_id) {
            $this->show_warning('no_such_order');

            return;
        }

        /* 验证订单有效性 */
        $model_order =& m('order');
        $order_info = $model_order->get("order_id={$order_id} AND buyer_id=" . $this->visitor->get('user_id'));
        $seller_id = $order_info['seller_id'];
        $result1 = $model_order->getRow("select user_name from " . DB_PREFIX . "member where user_id='$seller_id' limit 1");

        $seller_name = $result1['user_name'];


        if (!$order_info) {
            $this->show_warning('no_such_order');

            return;
        }
        if ($order_info['status'] != ORDER_FINISHED) {
            /* 不是已完成的订单，无法评价 */
            $this->show_warning('cant_evaluate');

            return;
        }
        if ($order_info['evaluation_status'] != 0) {
            /* 已评价的订单 */
            $this->show_warning('already_evaluate');

            return;
        }
        $model_ordergoods =& m('ordergoods');

        if (!IS_POST) {
            /* 显示评价表单 */
            /* 获取订单商品 */
            $goods_list = $model_ordergoods->find("order_id={$order_id}");

            foreach ($goods_list as $key => $goods) {
                empty($goods['goods_image']) && $goods_list[$key]['goods_image'] = Conf::get('default_goods_image');

            }
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('my_order'), 'index.php?app=buyer_order',
                LANG::get('evaluate'));
            $this->assign('goods_list', $goods_list);
            $this->assign('order', $order_info);
            $this->assign('kaiguan', $kaiguan);
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('credit_evaluate'));
            $this->display('buyer_order.evaluate.html');
        } else {
            $evaluations = array();
            /* 写入评价 */
            foreach ($_POST['evaluations'] as $rec_id => $evaluation) {
                if ($evaluation['evaluation'] <= 0 || $evaluation['evaluation'] > 3) {
                    $this->show_warning('evaluation_error');

                    return;
                }
                switch ($evaluation['evaluation']) {
                    case 3:
                        $credit_value = 1;
                        break;
                    case 1:
                        $credit_value = -1;
                        break;
                    default:
                        $credit_value = 0;
                        break;
                }
                $evaluations[intval($rec_id)] = array(
                    'evaluation' => $evaluation['evaluation'],
                    'comment' => $evaluation['comment'],
                    'credit_value' => $credit_value,
                    /*'ordercity'  => $city_id*/
                );
            }
            $goods_list = $model_ordergoods->find("order_id={$order_id}");
            foreach ($evaluations as $rec_id => $evaluation) {
                $model_ordergoods->edit("rec_id={$rec_id} AND order_id={$order_id}", $evaluation);
                $goods_url = SITE_URL . '/' . url('app=goods&id=' . $goods_list[$rec_id]['goods_id']);
                $goods_name = $goods_list[$rec_id]['goods_name'];
                $this->send_feed('goods_evaluated', array(
                    'user_id' => $this->visitor->get('user_id'),
                    'user_name' => $this->visitor->get('user_name'),
                    'goods_url' => $goods_url,
                    'goods_name' => $goods_name,
                    'evaluation' => Lang::get('order_eval.' . $evaluation['evaluation']),
                    'comment' => $evaluation['comment'],
                    'images' => array(
                        array(
                            'url' => SITE_URL . '/' . $goods_list[$rec_id]['goods_image'],
                            'link' => $goods_url,
                        ),
                    ),
                ));
            }

            /* 更新订单评价状态 */
            $model_order->edit($order_id, array(
                'evaluation_status' => 1,
                'evaluation_time' => gmtime()
            ));

            /* 更新卖家信用度及好评率 */
            $model_store =& m('store');
            $model_store->edit($order_info['seller_id'], array(
                'credit_value' => $model_store->recount_credit_value($order_info['seller_id']),
                'praise_rate' => $model_store->recount_praise_rate($order_info['seller_id'])
            ));


            /* 更新商品评价数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $goods_ids = array();
            foreach ($goods_list as $goods) {
                $goods_ids[] = $goods['goods_id'];
            }
            $model_goodsstatistics->edit($goods_ids, 'comments=comments+1');


            $notice = Lang::get('pingjiashengxiao');
            $notice = str_replace('{1}', $seller_name, $notice);
            $notice = str_replace('{2}', $order_info['buyer_name'], $notice);
            $add_notice = array(
                'from_id' => 0,
                'to_id' => $order_info['seller_id'],
                'content' => $notice,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice);


            $this->show_message('evaluate_successed',
                'back_list', 'index.php?app=buyer_order');
        }
    }

    /**
     *    获取订单列表
     *
     * @author    Garbin
     * @return    void
     */
    function _get_orders()
    {
        $page = $this->_get_page(10);
        $model_order =& m('order');
        $kaiguan = $model_order->kg();
        $this->assign('kaiguan', $kaiguan);
        !$_GET['type'] && $_GET['type'] = 'all_orders';
        $con = array(
            array(      //按订单状态搜索
                'field' => 'status',
                'name' => 'type',
                'handler' => 'order_status_translator',
            ),
            array(      //按店铺名称搜索
                'field' => 'seller_name',
                'equal' => 'LIKE',
            ),
            array(      //按下单时间搜索,起始时间
                'field' => 'add_time',
                'name' => 'add_time_from',
                'equal' => '>=',
                'handler' => 'gmstr2time',
            ),
            array(      //按下单时间搜索,结束时间
                'field' => 'add_time',
                'name' => 'add_time_to',
                'equal' => '<=',
                'handler' => 'gmstr2time_end',
            ),
            array(      //按订单号
                'field' => 'order_sn',
            ),
        );
        $conditions = $this->_get_query_conditions($con);
        /* 查找订单 */
        $orders = $model_order->findAll(array(
            'conditions' => "buyer_id=" . $this->visitor->get('user_id') . "{$conditions}",
            'fields' => 'this.*',
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time DESC',
            'include' => array(
                'has_ordergoods',       //取出商品
            ),
        ));

        foreach ($orders as $key1 => $order) {
            foreach ($order['order_goods'] as $key2 => $goods) {
                empty($goods['goods_image']) && $orders[$key1]['order_goods'][$key2]['goods_image'] = Conf::get('default_goods_image');
                // $orders[$key1]['order_goods'][$key2]['jifen']=round($goods['jifen'],2); 
            }

            $orders[$key1]['or'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $order['pay_time']) . '30day'));

            //$orders[$key1]['order_jifen']=round($order['order_jifen'],2); 

            if (!empty($order['zhe_jifen']))//打折过后的积分
            {
                $orders[$key1]['dazhe'] = $order['zhe_jifen'] * $order['order_jifen'];
            }


        }
        $riqi = date('Y-m-d H:i:s');

        $page['item_count'] = $model_order->getCount();
        $this->assign('types', array('all' => Lang::get('all_orders'),
            'pending' => Lang::get('pending_orders'),
            'submitted' => Lang::get('submitted_orders'),
            'accepted' => Lang::get('accepted_orders'),
            'shipped' => Lang::get('shipped_orders'),
            'finished' => Lang::get('finished_orders'),
            'canceled' => Lang::get('canceled_orders')));
        $this->assign('type', $_GET['type']);
        $this->assign('orders', $orders);
        $this->assign('or', $or);
        $this->assign('riqi', $riqi);
        $this->_format_page($page);
        $this->assign('page_info', $page);
    }

    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name' => 'order_list',
                'url' => 'index.php?app=buyer_order',
            ),
        );
        return $menus;
    }

    function tousu()
    {
        $this->complain_mod =& m('complain');
        $this->message_mod =& m('message');
        $this->order_mod =& m('order');
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        $ts = $this->order_mod->getRow("select * from " . DB_PREFIX . "order  where order_id='$order_id' limit 1");
        $tsu = $this->order_mod->getAll("select * from " . DB_PREFIX . "order_goods  where order_id='$order_id'");
        $riqi = date('Y-m-d H:i:s');
//$comp=$this->order_mod->getrow("select * from ". DB_PREFIX ."complain  where order_id='$order_id'");


        if ($_POST) {

            $str1 = $_POST[fu];
            if ($str1 == "") {
                $this->show_warning('xuanzeshangpin');
                return;
            }

            $str = implode(', ', $_POST[fu]);

            $complainant = trim($_POST['complainant']);
            $complainant_id = trim($_POST['complainant_id']);
            //$respondent = trim($_POST['respondent']);
            $respondent_id = trim($_POST['respondent_id']);
            $order_id = trim($_POST['order_id']);
            $order_sn = trim($_POST['order_sn']);
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status1 = trim($_POST['status1']);
            $city = trim($_POST['city']);
            $beizhu = trim($_POST['beizhu']);
            $time = $riqi;
            $rec_id = $str;
            $id = trim($_POST['id']);

            $mm = $this->order_mod->getRow("select * from " . DB_PREFIX . "member  where user_id='$respondent_id' limit 1");

            $respondent = $mm['user_name'];
            $da = array(
                'complainant' => $complainant,
                'complainant_id' => $complainant_id,
                'respondent' => $respondent,
                'respondent_id' => $respondent_id,
                'order_id' => $order_id,
                'order_sn' => $order_sn,
                'title' => $title,
                'content' => $content,
                'status1' => $status1,
                'city' => $city,
                'time' => $time,
                'rec_id' => $rec_id,
                'type' => 1,
                'beizhu' => $beizhu

            );
            if (!empty($id)) {
                $this->complain_mod->edit('id=' . $id, $da);
            } else {
                if (!$id = $this->complain_mod->add($da)) {


                    $content = Lang::get('dianputousu');
                    $content = str_replace('{1}', $respondent, $content);
                    $add_notice1 = array(
                        'from_id' => 0,
                        'to_id' => $respondent_id,
                        'content' => $content,
                        'add_time' => gmtime(),
                        'last_update' => gmtime(),
                        'new' => 1,
                        'parent_id' => 0,
                        'status' => 3,
                    );
                    $this->message_mod->add($add_notice1);

                    $this->show_warning($this->complain_mod->get_error());
                    return;
                }
            }
            /* 处理上传的图片 */
            $logo = $this->_upload_logo($id, 'imag_1');
            $logo1 = $this->_upload_logo($id, 'imag_2');
            $logo2 = $this->_upload_logo($id, 'imag_3');

            if ($logo === false) {
                return;
            }

            $logo && $this->complain_mod->edit($id, array('imag_1' => $logo));
            $logo1 && $this->complain_mod->edit($id, array('imag_2' => $logo1));
            $logo2 && $this->complain_mod->edit($id, array('imag_3' => $logo2));

            //$this->_clear_cache();
            $this->show_message('tousuchenggong',
                'continue_add', 'index.php?app=buyer_order&act=my_tousu'
            );
        } else {
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('complain_manage'), 'index.php?app=buy_order&act=tousu',
                LANG::get('tousuchuli'));

            /* 当前用户中心菜单 */
            $this->_curitem('tousuchuli');
            $comp = $this->order_mod->getRow("select * from " . DB_PREFIX . "complain  where order_id='$order_id' limit 1");


            $str = '';
            foreach ($tsu as $key => $val) {

                if (strpos($comp['rec_id'], $val['rec_id']) === false) {
                    $tsu[$key]['check'] = '';
                } else {
                    $tsu[$key]['check'] = 'checked';
                }
                $str .= '<input type="checkbox" name="fu[]" value="' . $val['rec_id'] . '" ' . $tsu[$key]['check'] . '/>';
            }

            $this->assign('ts', $ts);
            $this->assign('comp', $comp);
            $this->assign('tsu', $tsu);
            $this->display('complain.index.html');
        }

    }


    function my_tousu()
    {

        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_tousu',
            LANG::get('mytousu'));

        /* 当前用户中心菜单 */
        $this->_curitem('mytousu');
        $user_id = $this->visitor->get('user_id');
        $this->complain_mod =& m('complain');
        $tou = $this->complain_mod->getAll("select * from " . DB_PREFIX . "complain  where complainant_id='$user_id' and ts_id=0 order by id desc");
        $this->assign('tou', $tou);

        $this->display('my_tousu.html');
    }

    function bei_tousu()
    {

        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_tousu',
            LANG::get('beitousu'));

        /* 当前用户中心菜单 */
        $this->_curitem('beitousu');
        $user_id = $this->visitor->get('user_id');
        $this->complain_mod =& m('complain');
        $btou = $this->complain_mod->getAll("select * from " . DB_PREFIX . "complain  where respondent_id='$user_id' and ts_id=0 order by time desc");
        $this->assign('btou', $btou);

        $this->display('bei_tousu.html');
    }

    function ts_xiangqing()
    {
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_tousu',
            LANG::get('mytousu'));

        /* 当前用户中心菜单 */
        $this->_curitem('mytousu');
        $this->complain_mod =& m('complain');
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
        $order_id = empty($_GET['order_id']) ? 0 : $_GET['order_id'];
        $com = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where id = '$id' limit 1");
        $recid = $com['rec_id'];
        $or = $this->complain_mod->getAll("select * from " . DB_PREFIX . "order_goods where order_id = '$order_id' and rec_id in ($recid) ");


        /* 显示新增表单 */
        $yes_or_no = array(
            1 => Lang::get('yes'),
            0 => Lang::get('no'),
        );
        $this->import_resource(array(
            'script' => 'jquery.plugins/jquery.validate.js'
        ));
        $this->assign('yes_or_no', $yes_or_no);
        $this->assign('com', $com);
        $this->assign('or', $or);
        $this->display('ts_xiangqing.html');


    }

    function ts_drop()
    {
        $this->complain_mod =& m('complain');
        $id = intval($_GET['id']);//供货id
        $sql = "delete from " . DB_PREFIX . "complain where id = '$id'";
        $this->complain_mod->db->query($sql);
        $this->show_message('delete', 'fanhui', 'index.php?app=buyer_order&act=my_tousu');
    }

    function shensu()
    {
        $this->complain_mod =& m('complain');
        $this->message_mod =& m('message');
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ts = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain  where id='$id' limit 1");
        $riqi = date('Y-m-d H:i:s');
//$comp=$this->order_mod->getrow("select * from ". DB_PREFIX ."complain  where order_id='$order_id'");
        if ($_POST) {
            $complainant = trim($_POST['complainant']);//申诉人
            $complainant_id = trim($_POST['complainant_id']);//申诉人id
            $respondent = trim($_POST['respondent']);//被申诉人
            $respondent_id = trim($_POST['respondent_id']);//被申诉id
            $content = trim($_POST['content']);
            $status1 = trim($_POST['status1']);
            $city = trim($_POST['city']);
            $ts_id = trim($_POST['ts_id']);//投诉id
            $beizhu = trim($_POST['beizhu']);
            $time = $riqi;
            $id = trim($_POST['id']);//申诉id
            $da = array(
                'complainant' => $complainant,
                'complainant_id' => $complainant_id,
                'respondent' => $respondent,
                'respondent_id' => $respondent_id,
                'content' => $content,
                'status1' => $status1,
                'city' => $city,
                'time' => $time,
                'ts_id' => $ts_id,
                'type' => 2,
                'beizhu' => $beizhu
            );
            if (!empty($id)) {
                $this->complain_mod->edit('id=' . $id, $da);
            } else {
                if (!$id = $this->complain_mod->add($da)) {
                    $this->show_warning($this->complain_mod->get_error());

                    return;
                }

                $content = Lang::get('dianpushensu');
                $content = str_replace('{1}', $respondent, $content);
                $add_notice1 = array(
                    'from_id' => 0,
                    'to_id' => $respondent_id,
                    'content' => $content,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice1);
            }
            /* 处理上传的图片 */
            $logo = $this->_upload_logo($id, 'imag_1');
            $logo1 = $this->_upload_logo($id, 'imag_2');
            $logo2 = $this->_upload_logo($id, 'imag_3');

            if ($logo === false) {
                return;
            }

            $logo && $this->complain_mod->edit($id, array('imag_1' => $logo));
            $logo1 && $this->complain_mod->edit($id, array('imag_2' => $logo1));
            $logo2 && $this->complain_mod->edit($id, array('imag_3' => $logo2));

            //$this->_clear_cache();
            $this->show_message('shensuchenggong',
                'continue_add', 'index.php?app=buyer_order&act=my_shensu'
            );
        } else {
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_shensu',
                LANG::get('my_shensu'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_shensu');
            $shs = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain  where ts_id='$id' limit 1");


            $this->assign('ts', $ts);
            $this->assign('shs', $shs);
            $this->display('shensu.index.html');
        }

    }

    function my_shensu()
    {

        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_shensu',
            LANG::get('my_shensu'));

        /* 当前用户中心菜单 */
        $this->_curitem('my_shensu');
        $user_id = $this->visitor->get('user_id');
        $this->complain_mod =& m('complain');
        $tou = $this->complain_mod->getAll("select * from " . DB_PREFIX . "complain  where complainant_id='$user_id' and ts_id!=0 order by id desc");
        $this->assign('tou', $tou);

        $this->display('my_shensu.html');
    }

    function beishensu()
    {

        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_shensu',
            LANG::get('beishensu'));

        /* 当前用户中心菜单 */
        $this->_curitem('beishensu');
        $user_id = $this->visitor->get('user_id');
        $this->complain_mod =& m('complain');
        $btou = $this->complain_mod->getAll("select * from " . DB_PREFIX . "complain  where respondent_id='$user_id' and ts_id!=0 order by time desc");
        $this->assign('btou', $btou);

        $this->display('bei_shensu.html');
    }

    function shensu_xiangqing()
    {
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('complain_manage'), 'index.php?app=buy_order&act=my_shensu',
            LANG::get('beishensu'));

        /* 当前用户中心菜单 */
        $this->_curitem('beishensu');
        $this->complain_mod =& m('complain');
        $id = empty($_GET['shensu_id']) ? 0 : $_GET['shensu_id'];
        $com = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where id = '$id' limit 1");
        $ts_id = $com['ts_id'];
        $tous = $this->complain_mod->getRow("select * from " . DB_PREFIX . "complain where id = '$ts_id' limit 1");
        $order_id = $tous['order_id'];
        $recid = $tous['rec_id'];
        $or = $this->complain_mod->getAll("select * from " . DB_PREFIX . "order_goods where order_id = '$order_id' and rec_id in ($recid) ");


        /* 显示新增表单 */
        $yes_or_no = array(
            1 => Lang::get('yes'),
            0 => Lang::get('no'),
        );
        $this->import_resource(array(
            'script' => 'jquery.plugins/jquery.validate.js'
        ));


        foreach ($or as $i=>$v){
            if(strtolower(substr($v['goods_image'],0,4))!='http'){
                $or[$i]['goods_image']='/'.$v;
            }
        }

        if($com['imag_1']!='' && strtolower(substr($com['imag_1'],0,4))!='http'){
            $com['imag_1']='/'.$com['imag_1'];
        }
        if($com['imag_2']!='' && strtolower(substr($com['imag_2'],0,4))!='http'){
            $com['imag_2']='/'.$com['imag_2'];
        }
        if($com['imag_3']!='' && strtolower(substr($com['imag_3'],0,4))!='http'){
            $com['imag_3']='/'.$com['imag_3'];
        }

        $this->assign('yes_or_no', $yes_or_no);
        $this->assign('com', $com);
        $this->assign('or', $or);
        $this->display('shensu_xiangqing.html');


    }

    function _upload_logo($id, $can)
    {
        $file = $_FILES[$can];
        $riqi = time() . rand(100, 999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo

        if (!$uploader->file_info()) {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=buyer_order&amp;act=tousu');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/tousu_' . $this->visitor->get('user_id'), $riqi . $id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        } else {
            return false;
        }
    }


}

?>
