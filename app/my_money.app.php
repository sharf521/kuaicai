<?php

class My_moneyApp extends MemberbaseApp
{


    function My_moneyApp()
    {
        parent::__construct();
        $this->my_money_mod =& m('my_money');
        $this->my_moneylog_mod =& m('my_moneylog');
        $this->my_mibao_mod =& m('my_mibao');
        $this->order_mod =& m('order');
        $this->my_card_mod =& m('my_card');
        $this->my_jifen_mod =& m('my_jifen');
        $this->my_paysetup_mod =& m('my_paysetup');
        $this->canshu_mod =& m('canshu');
        $this->kaiguan_mod =& m('kaiguan');
        $this->accountlog_mod =& m('accountlog');
        $this->zongjine_mod =& m('zongjine');
        $this->member_mod =& m('member');
        $this->city_mod =& m('city');
        $this->moneylog_mod =& m('moneylog');

    }

    function exits()
    {
        //执行关闭页面
        echo "<script language='javascript'>window.opener=null;window.close();</script>";
    }

    function index()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('jiaoyichaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('jiaoyichaxun');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shangfutong'));
        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $this->assign('my_money', $my_money);
        $this->display('my_money.index.html');
    }

    function loglist()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('jiaoyichaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('jiaoyichaxun');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('jiaoyichaxun') . ' - ' . Lang::get('yuezhuanzhang'));
        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $kai_jifen = $this->kaiguan_mod->getRow("select jifen_zhuan from " . DB_PREFIX . "kaiguan");

        $this->assign('my_money', $my_money);
        $this->assign('kai_jifen', $kai_jifen);
        $this->display('my_jifen.loglist.html');
    }

//买入查询
    function buyer()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('mairuchaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('jiaoyichaxun');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('jiaoyichaxun') . ' - ' . Lang::get('mairuchaxun'));
        $page = $this->_get_page();
        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and s_and_z=2 and user_log_del=0 and leixing=20",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.buyer.html');
    }

//收入查询
    function seller()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('maichuchaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('jiaoyichaxun');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('jiaoyichaxun') . ' - ' . Lang::get('maichuchaxun'));
        $page = $this->_get_page();

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "seller_id='$user_id' and user_log_del=0 and type=10",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));
        /*$my_money=$this->moneylog_mod->find(array(
			'conditions' => "user_id='$user_id' and (type=17 or type=16) " ,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true,
        ));	*/


        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.seller.html');
    }

//帐户转出
    function outlog()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhuanchuchaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('jiaoyichaxun');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('jiaoyichaxun') . ' - ' . Lang::get('zhuanchuchaxun'));
        $page = $this->_get_page();
        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and s_and_z=2 and user_log_del=0 and leixing=21",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        foreach ($my_money as $mon => $mone) {
            $my_money[$mon]['duihuanjifen'] = abs($mone['duihuanjifen']);
        }
        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.outlog.html');
    }

//帐户转入
    function intolog()
    {
        $user_id = $this->visitor->get('user_id');
        $user_name = $this->visitor->get('user_name');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhuanruchaxun')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('jiaoyichaxun') . ' - ' . Lang::get('zhuanruchaxun'));
        $this->_curitem('jiaoyichaxun');
        $page = $this->_get_page();

        /*$my_money=$this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and s_and_z=1 and user_log_del=0 and leixing=11" ,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true,
        ));	兑换积分记录
*/

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_name='$user_name' and type=18",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.intolog.html');
    }

//充值查询
    function paylist()
    {

        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('chongzhichaxun')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('chongzhichaxun') . ' - ' . Lang::get('zaixianchongzhi'));
        $this->_curitem('chongzhichaxun');

        $my_money = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $canshu = $this->canshu_mod->getRow("select * from " . DB_PREFIX . "canshu");
        $kaiguan = $this->kaiguan_mod->getRow("select * from " . DB_PREFIX . "kaiguan");
        $city_row = $this->kaiguan_mod->get_cityrow();
        $city_row['recharge'] = $city_row['recharge'] * 100;
        $this->assign('city_row', $city_row);
        $this->assign('canshu', $canshu);
        $this->assign('kaiguan', $kaiguan);
        $this->assign('my_money', $my_money);
        $this->display('my_money.paylist.html');
    }

//积分兑换
    function jifen()
    {
        //$url=$_SERVER['HTTP_HOST'];//获得当前网址
        //$row_city=$this->city_mod->getrow("select city_id from ".DB_PREFIX."city where city_yuming = '$url'");
        //$city_id=$row_city['city_id'];

        $cityrow = $this->city_mod->get_cityrow();
        $city_id = $cityrow['city_id'];
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('jifenduihuan')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shangfutong') . ' - ' . Lang::get('jifenduihuan'));
        $this->_curitem('jifenduihuan');

        $page = $this->_get_page(2);
        $index = $this->my_jifen_mod->find(array(
            'conditions' => "yes_no=1 and user_id=0 and jf_city='$city_id'",//条件
            'limit' => $page['limit'],
            'order' => 'jifen desc',
            'count' => true));

        $page['item_count'] = $this->my_jifen_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('index', $index);
        $this->display('my_money.jifen.html');
    }

    function jifen_post()
    {
        $id = $_GET["id"];
        $user_id = $this->visitor->get('user_id');
        $row_member = $this->member_mod->getRow("select city from " . DB_PREFIX . "member where user_id = '$user_id' limit 1");
        if ($_POST) {
            $duihuanshu = trim($_POST['duihuanshu']);
            $my_jifen = $this->my_jifen_mod->getRow("select * from " . DB_PREFIX . "my_jifen where id=$id limit 1");
            $shengyushuliang = $my_jifen['shuliang'] - $my_jifen['yiduihuan'];//剩余可兑换数

            if (empty($duihuanshu)) {
                $this->show_warning('shuliangbugou');
                return;
            }
            if (preg_match("/[^0.-9]/", $duihuanshu)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }
            if ($duihuanshu > $shengyushuliang) {
                $this->show_warning('shuliangbugou');
                return;
            }
            $jifen = $my_jifen['jifen'] * $duihuanshu;
            $money_row = $this->my_money_mod->getRow("select jifen from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            if ($jifen > $money_row['jifen']) {
                $this->show_warning('jifenbuzu');//积分不足
                return;
            }
            //兑换成功，减少该用户的积分
            $xjifen = $money_row['jifen'] - $jifen;
            $user_jifen = array(
                'jifen' => $xjifen,
            );
            $this->my_money_mod->edit('user_id=' . $user_id, $user_jifen);
            //兑换成功，写入一条数据
            $add_array = array(
                'add_time' => time(),
                'jifen' => $jifen,
                'wupin_name' => $my_jifen['wupin_name'],
                'wupin_img' => $my_jifen['wupin_img'],
                'jiazhi' => $my_jifen['jiazhi'],
                'shuliang' => $duihuanshu,
                'user_id' => $this->visitor->get('user_id'),
                'user_name' => $this->visitor->get('user_name'),
                'my_name' => trim($_POST['my_name']),
                'my_add' => trim($_POST['my_add']),
                'my_tel' => trim($_POST['my_tel']),
                'my_mobile' => trim($_POST['my_mobile']),
                'log_text' => $my_jifen['log_text'],
                'riqi' => date('Y-m-d H:i:s'),
                'jf_city' => $row_member['city'],
            );
            $this->my_jifen_mod->add($add_array);
            //兑换成功，更新ID对应的数量及已兑换数量
            $edit_array = array(
                'yiduihuan' => $my_jifen['yiduihuan'] + $duihuanshu,
            );
            $this->my_jifen_mod->edit('id=' . $id, $edit_array);
            $this->show_message('duihuanchenggong', 'duihuanchenggong', 'index.php?app=my_money&act=duihuan_jilu');//兑换成功 index.php?app=my_money&act=duihuan_jilu
            return;
        } else {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
                LANG::get('jifenduihuan')
            );
            /* 当前用户中心菜单 */
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shangfutong') . ' - ' . Lang::get('jifenduihuan'));
            $this->_curitem('jifenduihuan');


            $index = $this->my_jifen_mod->find(array(
                'conditions' => "yes_no=1 and id='$id' and user_id=0",//条件
                'limit' => $page['limit'],
                'count' => true));


            $this->assign('index', $index);
            $this->display('my_money.jifen_post.html');
        }
    }

//已兑换记录
    function duihuan_jilu()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('jifenduihuan')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shangfutong') . ' - ' . Lang::get('jifenduihuan'));
        $this->_curitem('jifenduihuan');
        $page = $this->_get_page();

        $index = $this->my_jifen_mod->find(array(
            'conditions' => "yes_no=0 and user_id='$user_id'",//条件
            'limit' => $page['limit'],
            'order' => 'id desc',
            'count' => true,
        ));

        $page['item_count'] = $this->my_jifen_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('index', $index);
        $this->display('my_money.jifen_duihuan_jilu.html');
    }

//充值记录
    function paylog()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhuanruchaxun')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('chongzhichaxun') . ' - ' . Lang::get('chongzhijilu'));
        $this->_curitem('chongzhichaxun');
        $page = $this->_get_page();

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and s_and_z=1 and user_log_del=0 and leixing=30",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        foreach ($my_money as $mon => $mone) {
            $my_money[$mon]['money_feiyong'] = abs($mone['money_feiyong']);
        }

        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.paylog.html');
    }

    //推荐收入记录
    function shouru()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('shourulog')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shourulog') . ' - ' . Lang::get('shourulog'));
        $this->_curitem('shourulog');
        $page = $this->_get_page();

        $my_money = $this->moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and (type=101 or type=102 or type=103)",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('shouru.paylog.html');
    }


    //兑换积分记录

    function jifenduihuan()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('jifen_duihuan')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('xianjinjifen') . ' - ' . Lang::get('jifen_duihuan'));
        $this->_curitem('jifen_duihuan');
        $page = $this->_get_page();

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and caozuo=11 and leixing=11",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        foreach ($my_money as $mon => $mone) {
            $my_money[$mon]['money'] = abs($mone['money']);
        }
        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.jifenduihuan.html');

    }


    //兑换现金记录

    function xianjinduihuan()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('xianjinjilu')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('duihuanxianjijifen') . ' - ' . Lang::get('xianjinduihuan'));
        $this->_curitem('duihuanxianjijifen');
        $page = $this->_get_page();

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and caozuo=12 and leixing=12",
            'limit' => $page['limit'],
            'order' => "id desc",
            'count' => true,
        ));

        foreach ($my_money as $key => $var) {
            $my_money[$key]['duihuanjifen'] = abs($var['duihuanjifen']);
        }
        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.xianjinduihuan.html');

    }

//提现查询
    function txlist()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('tixianshenqing')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('tixianshenqing'));
        $this->_curitem('tixianshenqing');

        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $canshu = $this->canshu_mod->getRow("select * from " . DB_PREFIX . "canshu limit 1");
        $canshu['tixianfeilv'] = ($canshu['tixianfeilv'] * 100) . '%';
        $canshu['ks_txfeilv'] = ($canshu['ks_txfeilv'] * 100) . '%';
        $this->assign('canshu', $canshu);
        $this->assign('my_money', $my_money);
        $this->display('my_money.txlist.html');
    }

//提现记录
    function txlog()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('tixianjilu')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('tixianshenqing') . ' - ' . Lang::get('tixianjilu'));
        $this->_curitem('tixianshenqing');
        $page = $this->_get_page();

        $my_money = $this->my_moneylog_mod->find(array(
            'conditions' => "user_id='$user_id' and s_and_z=2 and user_log_del=0 and leixing=40",
            'limit' => $page['limit'],
            'count' => true,
            'order' => "id desc",

        ));

        foreach ($my_money as $mon => $mone) {
            $my_money[$mon]['money_feiyong'] = abs($mone['money_feiyong']);
            $my_money[$mon]['money'] = abs($mone['money']);
        }
        $page['item_count'] = $this->my_moneylog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('my_money', $my_money);
        $this->display('my_money.txlog.html');
    }

//用户设置
    function mylist()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('zhanghushezhi')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('zhanghushezhi');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('zhanghushezhi'));
        //读取帐户金额
        $my_money = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $mem = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
        $this->assign('my_money', $my_money);
        $this->assign('mem', $mem);
        $this->display('my_money.mylist.html');//对应风格文件
    }

//用户隐藏流水，但不会删除数据
    function user_log_del()
    {
        $user_id = $this->visitor->get('user_id');
        $id = trim($_GET['id']);
        if (empty($id)) {
            $this->show_warning('feifacanshu');
            return;
        } else {
            $ids = explode(',', $id);
            $user_log_del = array(
                'user_log_del' => 1,
            );
            $this->my_moneylog_mod->edit($ids, $user_log_del);
            $this->show_message('shanchuchenggong');
            return;
        }
    }

//用户显示流水，但不会删除数据，此功能暂时隐藏不使用
    function user_log_huifu()
    {
        $user_id = $this->visitor->get('user_id');
        $id = trim($_GET['id']);
        if (empty($id)) {
            $this->show_warning('feifacanshu');
            return;
        } else {
            $ids = explode(',', $id);
            $user_log_del = array(
                'user_log_del' => 0,
            );
            $this->my_moneylog_mod->edit($ids, $user_log_del);
            $this->show_message('ok');
            return;
        }
    }

//积分现金兑换
    function duihuanxianjinjifen()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('xianjinjifen')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('xianjinjifen'));
        $this->_curitem('xianjinjifen');

        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $canshu = $this->canshu_mod->getAll("select * from " . DB_PREFIX . "canshu");
        $kaiguan = $this->kaiguan_mod->getAll("select * from " . DB_PREFIX . "kaiguan");
        $this->assign('kaiguan', $kaiguan);
        $this->assign('canshu', $canshu);
        $this->assign('my_money', $my_money);
        $this->display('my_money.duihuanxianjinjifen.html');
    }

//购买fbb
    function goumaifbb()
    {
        $this->kaiguan_mod =& m('kaiguan');
        $row_kaiguan = $this->kaiguan_mod->getRow("select webservice from " . DB_PREFIX . "kaiguan");
        $webservice = $row_kaiguan['webservice'];


        $user_id = $this->visitor->get('user_id');
        $use_name = $this->visitor->get('user_name');
        $userrow = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $city = $userrow['city'];

        $riqi = date('Y-m-d H:i:s');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('fbb')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('fbb'));
        $this->_curitem('fbb');


        $canshu = $this->canshu_mod->getAll("select * from " . DB_PREFIX . "canshu");
        $kaiguan = $this->kaiguan_mod->getAll("select * from " . DB_PREFIX . "kaiguan");
        $this->assign('kaiguan', $kaiguan);
        $this->assign('canshu', $canshu);
        $this->assign('my_money', $my_money);
        if ($_POST) {
            //购买fbb对接开始
            $user_id = $this->visitor->get('user_id');
            $fbb = trim($_POST['fbb']);
            $this->member_mod =& m('member');
            $this->order_mod =& m('order');
            $user_row = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $web_id = $user_row['web_id'];

            $post_data = array(
                "ID" => $web_id,
                "Money" => $fbb,
            );
            //print_r($post_data);
            if ($webservice == "yes") {
                $web_id = webService('Fbb_Regist_Money', $post_data);
                webService('FBB_Cal');
            }
//购买fbb对接结束
            $mymoney_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $money = $mymoney_row['money'];
            $dongjie_money = $mymoney_row['money_dj'];
            $duihuanjifen = $mymoney_row['duihuanjifen'];
            $dongjiejifen = $mymoney_row['dongjiejifen'];
            $suoding_money = $mymoney_row['suoding_money'];
            $keyong_money = $money - $suoding_money;
            $newmoney = $money - $fbb;
            if ($fbb > $keyong_money) {
                $this->show_warning('nindeyuebuzu');
                return;
            } else {

                //更新总账户资金
                $this->canshu_mod =& m('canshu');
                $jinbi_row = $this->canshu_mod->getRow("select zong_money,zong_jifen from " . DB_PREFIX . "canshu");
                $zong_money = $jinbi_row['zong_money'];
                $zong_jifen = $jinbi_row['zong_jifen'];
                $can_id = 1;
                $new_zong_money = $zong_money + $fbb;//从总账户加上购买fbb的钱金额
                $edit_canshu = array(
                    'zong_money' => $new_zong_money,
                );
                $this->canshu_mod->edit('id=' . $can_id, $edit_canshu);

                //添加accountlog日志,购买fbb
                $beizhu = Lang::get('shouqu') . $this->visitor->get('user_name') . Lang::get('goumaifbb') . $fbb . Lang::get('yuan');
                $add_account = array(
                    'money' => '+' . $fbb,
                    'time' => $riqi,
                    'user_name' => $use_name,
                    'user_id' => $user_id,
                    'zcity' => $city,
                    'type' => 23,
                    's_and_z' => 1,
                    'beizhu' => $beizhu,
                    'dq_money' => $new_zong_money,
                    'dq_jifen' => $zong_jifen,
                );
                $this->accountlog_mod->add($add_account);


//添加my_moneylog日志
                $log_text = Lang::get('tixian_yonghud') . $this->visitor->get('user_name') . Lang::get('goumaifbbhuafei') . $fbb . Lang::get('yuan');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $use_name,
                    'leixing' => 51,  //购买fbb的类型
                    'money' => '-' . $fbb,//购买fbb的金额
                    'riqi' => $riqi,
                    's_and_z' => 1,
                    'type' => 6,
                    'log_text' => $log_text,
                    'city' => $city,
                    'dq_money' => $newmoney,
                    'dq_money_dj' => $dongjie_money,
                    'dq_jifen' => $duihuanjifen,
                    'dq_jifen_dj' => $dongjiejifen,

                );
                //写入日志
                $this->my_moneylog_mod->add($add_mymoneylog);
//添加moneylog日志
                $beizhu = Lang::get('tixian_yonghud') . $this->visitor->get('user_name') . Lang::get('goumaifbbhuafei') . $fbb . Lang::get('yuan');
                $add_moneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $use_name,
                    'money' => '-' . $fbb,//购买fbb的金额
                    'time' => $riqi,
                    's_and_z' => 2,
                    'type' => 11,
                    'beizhu' => $beizhu,
                    'zcity' => $city,
                    'dq_money' => $newmoney,
                    'dq_money_dj' => $dongjie_money,
                    'dq_jifen' => $duihuanjifen,
                    'dq_jifen_dj' => $dongjiejifen,

                );

                $this->moneylog_mod->add($add_moneylog);
                //定义资金数组
                $add_money = array('money' => $newmoney,);

                //更新该用户资金
                $this->my_money_mod->edit('user_id=' . $user_id, $add_money);//增加my_money表里的资金

                $fb = 1;
                $add_fbb = array('fbb' => $fb,);
                $this->member_mod->edit('user_id=' . $user_id, $add_fbb);//增加my_money表里的资金
            }

            $this->show_message('goumaifbbchenggong',
                'chakancicichongzhi', 'index.php?app=my_money&act=goumaifbb',
                'guanbiyemian', 'index.php?app=my_money&act=exits');
            return;
        } else {
            //查询fbb收益开始
            $user_row = $this->member_mod->getRow("select fbb,web_id from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $fbb = $user_row['fbb'];
            $web_id = $user_row['web_id'];
            if ($fbb == 1) {
                $post_data = array(
                    "ID" => $web_id

                );
                $web_money = webService('Fbb_Query', $post_data);
                $this->assign('web_money', $web_money);
                $this->display('fbb.html');
                //查询fbb收益结束
            } else {
                $this->display('goumaifbb.html');
            }
        }
    }

//购买大小卓
    function goumaidaxiaozhuo()
    {
        $this->kaiguan_mod =& m('kaiguan');
        $row_kaiguan = $this->kaiguan_mod->getRow("select webservice from " . DB_PREFIX . "kaiguan");
        $webservice = $row_kaiguan['webservice'];

        $user_id = $this->visitor->get('user_id');
        $use_name = $this->visitor->get('user_name');
        $userrow = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $city = $userrow['city'];


        $daxiaozhuo = trim($_POST['daxiaozhuo']);
        $month = trim($_POST['month']);
        $riqi = date('Y-m-d H:i:s');


        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('daxiaozhuo')
        );
        /* 当前用户中心菜单 */
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('daxiaozhuo'));
        $this->_curitem('daxiaozhuo');

        if ($_POST) {
            //对接购买大小卓开始
            $user_id = $this->visitor->get('user_id');
            $fbb = trim($_POST['fbb']);
            $this->member_mod =& m('member');
            $this->order_mod =& m('order');
            $user_row = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $web_id = $user_row['web_id'];

            $post_data = array(
                "ID" => $web_id,
                "Money" => $daxiaozhuo,
                "Month" => $month,
            );
            //print_r($post_data);
            if ($webservice == "yes") {
                $web_id = webService('Z_Static_Regist', $post_data);
                webService('Z_Dynamic_Cal');
            }
            //对接购买大小卓结束


            $mymoney_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $money = $mymoney_row['money'];
            $dongjie_money = $mymoney_row['money_dj'];
            $duihuanjifen = $mymoney_row['duihuanjifen'];
            $dongjiejifen = $mymoney_row['dongjiejifen'];
            $suoding_money = $mymoney_row['suoding_money'];
            $keyong_money = $money - $suoding_money;
            $newmoney = $money - $daxiaozhuo;
            if ($daxiaozhuo > $keyong_money) {
                $this->show_warning('nindeyuebuzu');
                return;
            } else {
                //更新总账户资金
                $this->canshu_mod =& m('canshu');
                $jinbi_row = $this->canshu_mod->getRow("select zong_money,zong_jifen from " . DB_PREFIX . "canshu");
                $zong_money = $jinbi_row['zong_money'];
                $zong_jifen = $jinbi_row['zong_jifen'];
                $can_id = 1;
                $new_zong_money = $zong_money + $daxiaozhuo;//从总账户加上购买fbb的钱金额
                $edit_canshu = array(
                    'zong_money' => $new_zong_money,
                );
                $this->canshu_mod->edit('id=' . $can_id, $edit_canshu);


                //添加accountlog日志,购买大小卓
                $beizhu = Lang::get('shouqu') . $this->visitor->get('user_name') . Lang::get('goumaidaxiaozhuo') . $daxiaozhuo . Lang::get('yuan');
                $add_account = array(
                    'money' => '+' . $daxiaozhuo,
                    'time' => $riqi,
                    'user_name' => $use_name,
                    'user_id' => $user_id,
                    'zcity' => $city,
                    'type' => 24,
                    's_and_z' => 1,
                    'beizhu' => $beizhu,
                    'dq_money' => $new_zong_money,
                    'dq_jifen' => $zong_jifen,
                );
                $this->accountlog_mod->add($add_account);


//添加my_moneylog日志
                $log_text = Lang::get('tixian_yonghud') . $this->visitor->get('user_name') . Lang::get('goumaidaxiaozhuohuafei') . $daxiaozhuo . Lang::get('yuan');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $use_name,
                    'leixing' => 52,  //购买大小卓的类型
                    'money' => '-' . $daxiaozhuo,//购买大小卓的金额
                    'riqi' => $riqi,
                    'type' => 7,
                    's_and_z' => 1,
                    'log_text' => $log_text,
                    'city' => $city,
                    'month' => $month,
                    'dq_money' => $newmoney,
                    'dq_money_dj' => $dongjie_money,
                    'dq_jifen' => $duihuanjifen,
                    'dq_jifen_dj' => $dongjiejifen,

                );
                //写入日志
                $this->my_moneylog_mod->add($add_mymoneylog);
//添加moneylog购买大小卓日志
//添加moneylog日志
                $beizhu = Lang::get('tixian_yonghud') . $this->visitor->get('user_name') . Lang::get('goumaidaxiaozhuohuafei') . $daxiaozhuo . Lang::get('yuan');
                $add_moneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $use_name,
                    'money' => '-' . $daxiaozhuo,
                    'time' => $riqi,
                    's_and_z' => 2,
                    'type' => 12,
                    'beizhu' => $beizhu,
                    'zcity' => $city,
                    'dq_money' => $newmoney,
                    'dq_money_dj' => $dongjie_money,
                    'dq_jifen' => $duihuanjifen,
                    'dq_jifen_dj' => $dongjiejifen,

                );

                $this->moneylog_mod->add($add_moneylog);


                //定义资金数组
                $add_money = array('money' => $newmoney,);

                //更新该用户资金
                $this->my_money_mod->edit('user_id=' . $user_id, $add_money);//增加my_money表里的资金

                $daxiao = 1;
                $add_daxiaozhuo = array('daxiaozhuo' => $daxiao,);
                $this->member_mod->edit('user_id=' . $user_id, $add_daxiaozhuo);//增加my_money表里的资金
            }

            $this->show_message('goumaidaxiaozhuochenggong',
                'chakancicichongzhi', 'index.php?app=my_money&act=goumaidaxiaozhuo',
                'guanbiyemian', 'index.php?app=my_money&act=exits');
            return;
        } else {
            //查询大小卓收益开始
            $user_row = $this->member_mod->getRow("select daxiaozhuo,web_id from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
            $daxiaozhuo = $user_row['daxiaozhuo'];
            $web_id = $user_row['web_id'];
            if ($daxiaozhuo == 1) {
                $post_data = array(
                    "ID" => $web_id

                );
                $web_jingtai = webService('Z_Static_Query', $post_data);
                $web_dongtai = webService('Z_Dynamic_Query', $post_data);
                $this->assign('web_jingtai', $web_jingtai);
                $this->assign('web_dongtai', $web_dongtai);
                $this->display('daxiaozhuo.html');
                //查询大小卓收益结束
            } else {

                $this->display('goumaidaxiaozhuo.html');
            }
        }

    }


//修改支付密码
    function password()
    {
        $this->message_mod =& m('message');
        $user_id = $this->visitor->get('user_id');
        if ($_POST)//检测是否提交
        {
            $y_pass = trim($_POST['y_pass']);
            $zf_pass = trim($_POST['zf_pass']);
            $zf_pass2 = trim($_POST['zf_pass2']);
            if (empty($zf_pass)) {
                $this->show_warning('cuowu_zhifumimabunengweikong');
                return;
            }
            if ($zf_pass != $zf_pass2) {
                $this->show_warning('cuowu_liangcishurumimabuyizhi');
                return;
            }
//读原始密码
            $money_row = $this->my_money_mod->getRow("select zf_pass,user_name from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
//转换32位 MD5
            $md5y_pass = md5($y_pass);
            $md5zf_pass = md5($zf_pass);

            /*$md5y_pass=$y_pass;
	$md5zf_pass=$zf_pass;*/

            if ($money_row['zf_pass'] != $md5y_pass) {
                $this->show_warning('cuowu_yuanzhifumimayanzhengshibai');
                return;
            } else {
                $newpass_array = array(
                    'zf_pass' => $md5zf_pass,
                );
                $this->my_money_mod->edit('user_id=' . $user_id, $newpass_array);

                $content = Lang::get('xiugaicheng');
                $content = str_replace('{1}', $money_row['user_name'], $content);
                $add_notice1 = array(
                    'from_id' => 0,
                    'to_id' => $user_id,
                    'content' => $content,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice1);

                $this->show_message('zhifumimaxiugaichenggong', '', 'index.php?app=my_money&act=password');
                return;
            }
        } else {
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
                LANG::get('zhifumimaxiugai')
            );
            $this->_curitem('zhanghushezhi');
            $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('zhanghushezhi') . ' - ' . Lang::get('zhifumimaxiugai'));
            $this->display('my_money.password.html');
            return;
        }
    }

//显示找回支付密码
    function find_password()
    {
        header("Location: index.php?app=find_password");
        return;
    }


//密保绑定页面
    function mibao()
    {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
            LANG::get('shangfutong'), 'index.php?app=my_money&act=index',
            LANG::get('mibaobangding')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('zhanghushezhi');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('zhanghushezhi') . ' - ' . Lang::get('mibaobangding'));
        //读取帐户金额
        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $this->assign('my_money', $my_money);
        $this->display('my_money.mibao.html');//对应风格文件
    }


//提现申请
    function txsq()
    {
        if ($_POST) {
            $canshu_row = $this->canshu_mod->getRow("select * from " . DB_PREFIX . "canshu");
            $tx_min = $canshu_row['tx_min'];
            $tx_max = $canshu_row['tx_max'];
            $txfeilv = $canshu_row['tixianfeilv'];
            $txfeimin = $canshu_row['tixianfeimin'];
            $txfeimax = $canshu_row['tixianfeimax'];
            $ks_txfeilv = $canshu_row['ks_txfeilv'];
            $ks_fei = $canshu_row['ks_fei'];
            $tixian = trim($_POST['tx_edit']);

            $user_id = $this->visitor->get('user_id');
            $usename = $this->visitor->get('user_name');
            $tx_money = trim($_POST['tx_money']);
            if ($tx_money != ((int)($tx_money * 100)) / 100) {
                $this->show_warning('xiaoshu');
                return;
            }

            $status = trim($_POST['status']);
            $type = trim($_POST['type']);
            $riqi = date('Y-m-d H:i:s');
            /*$lev=$this->my_money_mod->getrow("select level from ".DB_PREFIX."member where user_id='$user_id'");

$bb=explode(',',$lev['level']);*/
            /*if(in_array(1,$bb))
{
$txfeiyong=0;
}
else
{*/

            //$txfeiyong = $tx_money * $txfeilv;//提现费用
            $txfeiyong=bcmul($tx_money,$txfeilv,3);
            //$txfeiyong = ceil($txfeiyong * 100) / 100;
            $txfeiyong=format_price($txfeiyong,2,2);
            if ($txfeiyong < $txfeimin) {
                $txfeiyong = $txfeimin;
            }
            if ($txfeiyong > $txfeimax) {
                $txfeiyong = $txfeimax;
            }

            if ($tixian == 1)//快速提现
            {
                //$txfeiyong = $txfeiyong + $tx_money * $ks_txfeilv + $ks_fei;
                $txfeiyong=bcadd($txfeiyong,$ks_fei,2);
                $__t=bcmul($tx_money,$ks_txfeilv,3);
                $txfeiyong=bcadd($txfeiyong,$__t,3);
                //$txfeiyong = ceil($txfeiyong * 100) / 100;
                $txfeiyong=format_price($txfeiyong,2,2);
            } else {
                $tixian = 0;
            }

            /*}*/

            $tx_shijimoney = $tx_money - $txfeiyong;//实际提现的金额
            $post_zf_pass = trim($_POST['post_zf_pass']);
            $user_zimuz1 = trim($_POST['user_zimuz1']);
            $user_zimuz2 = trim($_POST['user_zimuz2']);
            $user_zimuz3 = trim($_POST['user_zimuz3']);
            $md5zf_pass = md5($post_zf_pass);
            $user_shuzi1 = trim($_POST['user_shuzi1']);
            $user_shuzi2 = trim($_POST['user_shuzi2']);
            $user_shuzi3 = trim($_POST['user_shuzi3']);
            $money_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $city = $money_row['city'];
            $duihuanjifen = $money_row['duihuanjifen'];
            $dongjiejifen = $money_row['dongjiejifen'];
            $zhanghu_money = $money_row['money'];
            $suoding = $money_row['suoding_money'];//锁定金额
            $keyong_money = $zhanghu_money - $suoding;//可用金额


            if ($duihuanjifen < 0) {
                $this->show_warning('jifenweifu');
                return;
            }

//检测用户的银行信息
            if (empty($money_row['bank_sn']) or empty($money_row['bank_name']) or empty($money_row['bank_username'])) {
                $this->show_warning('cuowu_nihaimeiyoushezhiyinhangxinxi');


                return;
            }
            if ($money_row['money'] == 0.0 && $money_row['duihuanjifen'] == 0.0) {
                $this->show_warning('duibuqi_keyongyueweiling');
                return;
            }
            if ($money_row['money'] < $tx_min && $money_row['duihuanjifen'] != 0.0) {
                $this->show_warning('duibuqi_yuebuzuyongjifenduihuan');
                return;
            }
            if (empty($tx_money)) {
                $this->show_warning('cuowu_tixianjinebunengweikong');
                return;
            }
            if (preg_match("/[^0.-9]/", $tx_money)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }
            if ($keyong_money < $tx_money) {
                $this->show_warning('duibuqi_zhanghuyuebuzu');
                return;
            }
            if ($tx_money < $tx_min) {
                $this->show_warning('cuowu_tixianjinebunengxiaoyuwushiyuan');
                return;
            }
            if ($tx_money > $tx_max) {
                $this->show_warning('cuowu_tixianjinebunengdayuyuyiqianyuan');
                return;
            }
//检测是密保用户就执行
            if ($money_row['mibao_id'] > 0) {
                if (empty($user_shuzi1) or empty($user_shuzi2) or empty($user_shuzi3)) {
                    $this->show_warning('cuowu_dongtaimimabunengweikong');
                    return;
                }
                $mibao_row = $this->my_mibao_mod->getRow("select * from " . DB_PREFIX . "my_mibao where user_id='$user_id' limit 1");
//检测数字错，就提示并停止
                if ($mibao_row[$user_zimuz1] != $user_shuzi1 or $mibao_row[$user_zimuz2] != $user_shuzi2 or $mibao_row[$user_zimuz2] != $user_shuzi2) {
                    echo Lang::get('money_banben');
                    $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                    return;
                }
            } else {
//否则检测 支付密码
                if (empty($post_zf_pass)) {
                    $this->show_warning('cuowu_zhifumimabunengweikong');
                    return;
                }
                if ($money_row['zf_pass'] != $md5zf_pass)    /*if($money_row['zf_pass'] != $post_zf_pass)*/ {
                    $this->show_warning('cuowu_zhifumimayanzhengshibai');
                    return;
                }
            }
//通过验证 开始操作数据
            $newmoney = $money_row['money'] - $tx_money;
            $newmoney_dj = $money_row['money_dj'] + $tx_money;
//添加my_moneylog日志
            $log_text = $this->visitor->get('user_name') . Lang::get('tixianshenqingjine') . $tx_money . Lang::get('yuan');
            $add_mymoneylog = array(
                'user_id' => $user_id,
                'user_name' => $this->visitor->get('user_name'),
                'order_id ' => Lang::get('tixian_dengdaiguanliyuangongbu'),
                'add_time' => time(),
                'leixing' => 40,
                's_and_z' => 2,
                //'money_zs'=>$tx_shijimoney,
                'money_dj' => $tx_money,
                'money' => '-' . $tx_shijimoney,
                'log_text' => $log_text,
                'caozuo' => 60,
                'money_feiyong' => '-' . $txfeiyong,
                //'feilv'=>$txfeilv,
                'status1' => 1,
                'riqi' => $riqi,
                'type' => 3,
                'tx_type' => $tixian,
                'city' => $city,
                'dq_money' => $newmoney,//扣除提现的金额
                'dq_money_dj' => $newmoney_dj,
                'dq_jifen' => $duihuanjifen,
                'dq_jifen_dj' => $dongjiejifen,
            );
            $this->my_moneylog_mod->add($add_mymoneylog);
            //添加moneylog日志
            //添加moneylog提现金额日志

//$beizhu =$this->visitor->get('user_name').Lang::get('tixianshenqingjine').$tx_money.Lang::get('yuan');
            $addlog = array(
                'money_dj' => $tx_money,//负数
                'money' => '-' . $tx_money,
                'time' => $riqi,
                'user_name' => $this->visitor->get('user_name'),
                'user_id' => $user_id,
                'zcity' => $city,
                'type' => 5,
                's_and_z' => 2,
                'beizhu' => $beizhu,
                'dq_money' => $newmoney,//扣除提现的金额
                'dq_money_dj' => $newmoney_dj,
                'dq_jifen' => $duihuanjifen,
                'dq_jifen_dj' => $dongjiejifen,
            );
            //$this->moneylog_mod->add($addlog);

            $edit_mymoney = array(
                'money_dj' => $newmoney_dj,
                'money' => $newmoney,
            );
            $this->my_money_mod->edit('user_id=' . $user_id, $edit_mymoney);
            $this->show_message('tixian_chenggong', '', 'index.php?app=my_money&act=txlog');
            return;

        } else {
            $this->show_warning('feifacanshu');
            return;
        }
    }


//银行信息设置
    function bank_set()
    {
        if ($_POST) {
            //检测两次银行号码
            if (trim($_POST['yes_bank_sn']) != trim($_POST['yes_bank_sn_queren'])) {
                $this->show_warning('liangxitixianzhenghaobuyizhi');
                return;
            }

            if (!trim($_POST['yes_bank_username'])) {
                $this->show_message('zhanghubuneng', 'go_back', 'index.php?app=member&act=profile');
                return;
            }
            $kahao = trim($_POST['yes_bank_sn']);
            if (strlen($kahao) < 16) {
                $this->show_warning('zhengquekahao');
                return;
            }


            $user_id = $this->visitor->get('user_id');
            $bank_edit = trim($_POST['bank_edit']);
            if ($bank_edit == "YES") {
                $zf_pass = trim($_POST['zf_pass']);
                $user_zimuz1 = trim($_POST['user_zimuz1']);
                $user_zimuz2 = trim($_POST['user_zimuz2']);
                $user_zimuz3 = trim($_POST['user_zimuz3']);
                $user_shuzi1 = trim($_POST['user_shuzi1']);
                $user_shuzi2 = trim($_POST['user_shuzi2']);
                $user_shuzi3 = trim($_POST['user_shuzi3']);

//读取密保卡资料
                $money_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
                if ($money_row['mibao_id'] > 0) {
                    $mibao_row = $this->my_mibao_mod->getrow("select * from " . DB_PREFIX . "my_mibao where user_id='$user_id'");
//检测数字错，就提示并停止
                    if ($mibao_row[$user_zimuz1] != $user_shuzi1 or $mibao_row[$user_zimuz2] != $user_shuzi2 or $mibao_row[$user_zimuz2] != $user_shuzi2) {
                        echo Lang::get('money_banben');
                        $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                        return;
                    }
                } else {
//检测密码回答
                    if (empty($zf_pass)) {
                        $this->show_warning('cuowu_zhifumimabunengweikong');
                        return;
                    }
                    $md5zf_pass = md5($zf_pass);
                    if ($money_row['zf_pass'] != $md5zf_pass) {
                        $this->show_warning('cuowu_zhifumimayanzhengshibai');
                        return;
                    }

                }//mibao>0
//验证都通过了开始修改数据
                $bank_array = array(
                    'bank_name' => trim($_POST['yes_bank_name']),
                    'bank_sn' => trim($_POST['yes_bank_sn']),
                    'bank_username' => trim($_POST['yes_bank_username']),
                    'bank_add' => trim($_POST['yes_bank_add']),
                );
//执行SQL操作
                $this->my_money_mod->edit('user_id=' . $user_id, $bank_array);
                $this->show_message('baocuntixianxinxichenggong');
                return;
            }//YES
        }//post
        else {
            $this->show_warning('feifacanshu');
            return;
        }
    }


//绑定密保卡
    function add_mibao()
    {
        if ($_POST) {
            $user_id = $this->visitor->get('user_id');
            $zf_pass = trim($_POST['zf_pass']);
            $post_mb_sn = trim($_POST['post_mb_sn']);
            $user_zimuz1 = trim($_POST['user_zimuz1']);
            $user_zimuz2 = trim($_POST['user_zimuz2']);
            $user_zimuz3 = trim($_POST['user_zimuz3']);
            $user_shuzi1 = trim($_POST['user_shuzi1']);
            $user_shuzi2 = trim($_POST['user_shuzi2']);
            $user_shuzi3 = trim($_POST['user_shuzi3']);
            if (empty($zf_pass)) {
                $this->show_warning('cuowu_zhifumimabunengweikong');
                return;
            }
            if (empty($post_mb_sn)) {
                $this->show_warning('mibaosnbunengweikong');
                return;
            }
            $money_row = $this->my_money_mod->getRow("select zf_pass from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");

            if ($money_row['mibao_id'] > 0) {
                $this->show_warning('cuowu_gaiyonghuyijingbangdingmibaole');
                return;
            }
            $md5zf_pass = md5($zf_pass);
            if ($money_row['zf_pass'] != $md5zf_pass) {
                $this->show_warning('cuowu_zhifumimayanzhengshibai');
                return;
            }
            $mibao_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_mibao where mibao_sn='$post_mb_sn'");
            $mibao_id = $mibao_row['id'];
            $mibao_sn = $mibao_row['mibao_sn'];
            $mibao_shuzi1 = $mibao_row[$user_zimuz1];
            $mibao_shuzi2 = $mibao_row[$user_zimuz2];
            $mibao_shuzi3 = $mibao_row[$user_zimuz3];
            if (empty($mibao_id) or empty($mibao_sn)) {
                $this->show_warning('cuowu_mibaokasncuowu');
                return;
            }
            if ($mibao_row['user_id'] > 0) {
                $this->show_warning('cuowu_gaimibaokazhengzaishiyongzhong');
                return;
            }
            if ($user_shuzi1 != $mibao_shuzi1 or $user_shuzi2 != $mibao_shuzi2 or $user_shuzi3 != $mibao_shuzi3) {
                echo Lang::get('money_banben');
                $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                return;
            } else {
                //检测绑定时间
                if (empty($mibao_row['bd_time'])) {
                    $mibao_array = array(
                        'user_id' => $this->visitor->get('user_id'),
                        'user_name' => $this->visitor->get('user_name'),
                        'bd_time' => time(),
                        'dq_time' => time() + 31536000,
                        'ztai' => 1,
                    );
                } else//绑时间 否则
                {
                    $mibao_array = array(
                        'user_id' => $this->visitor->get('user_id'),
                        'user_name' => $this->visitor->get('user_name'),
                    );
                }

                $money_edit = array(
                    'mibao_id' => $mibao_id,
                    'mibao_sn' => $mibao_sn,
                );

                $this->my_money_mod->edit('user_id=' . $user_id, $money_edit);
                $this->my_mibao_mod->edit('id=' . $mibao_id, $mibao_array);
                $this->show_message('bangding_chenggong');
            }
        } else {
            $this->show_warning('feifacanshu');
            return;
        }
    }


//解除密保卡
    function del_mibao()
    {
        if ($_POST) {
            $user_id = $this->visitor->get('user_id');
            $post_mb_sn = trim($_POST['post_mb_sn']);
            $user_zimuz1 = trim($_POST['user_zimuz1']);
            $user_zimuz2 = trim($_POST['user_zimuz2']);
            $user_zimuz3 = trim($_POST['user_zimuz3']);
            $user_shuzi1 = trim($_POST['user_shuzi1']);
            $user_shuzi2 = trim($_POST['user_shuzi2']);
            $user_shuzi3 = trim($_POST['user_shuzi3']);
            if (empty($post_mb_sn)) {
                $this->show_warning('mibaosnbunengweikong');
                return;
            }

            $mibao_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_mibao where mibao_sn='$post_mb_sn'");

            $mibao_id = $mibao_row['id'];
            $mibao_sn = $mibao_row['mibao_sn'];

            $mibao_shuzi1 = $mibao_row[$user_zimuz1];
            $mibao_shuzi2 = $mibao_row[$user_zimuz2];
            $mibao_shuzi3 = $mibao_row[$user_zimuz3];
            if (empty($mibao_id) or empty($mibao_sn)) {
                $this->show_warning('cuowu_mibaokasncuowu');
                return;
            }
            if ($user_shuzi1 != $mibao_shuzi1 or $user_shuzi2 != $mibao_shuzi2 or $user_shuzi3 != $mibao_shuzi3) {
                echo Lang::get('money_banben');
                $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                return;
            } else {
                $mibao_array = array(
                    'user_id' => 0,
                    'user_name' => "",
                );

                $money_array = array(
                    'mibao_id' => 0,
                    'mibao_sn' => "",
                );
            }
            $this->my_mibao_mod->edit('id=' . $mibao_id, $mibao_array);
            $this->my_money_mod->edit('user_id=' . $user_id, $money_array);
            $this->show_message('jiechu_chenggong');
        }//POST
        else {//POST
            $this->show_warning('feifacanshu');
            return;
        }//POST
    }


//支付定单
    function payment()
    {
        $this->message_mod =& m('message');
        $this->_city_mod =& m('city');
        $cityrow = $this->_city_mod->get_cityrow();
        $city_id = $cityrow['city_id'];
        $user_id = $this->visitor->get('user_id');
        $zf_pass = trim($_POST['zf_pass']);
        $zhifufangshi = trim($_POST['zhifufangshi']);
        $post_money = trim($_POST['post_money']);//提交过来的 金钱
        $post_jifen = trim($_POST['post_jifen']);//提交过来的的订单总 积分
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;//提交过来的 定单号码
        if (empty($order_id)) {
            $this->show_warning('feifacanshu');
            return;
        }

        if ($_POST)//检测是否提交
        {

            $is_zhe = $_POST['is_zhe'];//是否打折
            //读取moneylog 为了检测提交不重复
            $moneylog_row = $this->my_moneylog_mod->getRow("select order_id from " . DB_PREFIX . "my_moneylog where user_id='$user_id' and order_id='$order_id' and caozuo='10'");
            if ($moneylog_row['order_id'] == $order_id) {
                $this->show_warning('cuowu_gaidingdanyijingzhufule');
                return;//定单已经支付
            }
            //读取买家SQL
            $buyer_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $buyer_name = $buyer_row['user_name'];//买家用户名
            $buyer_zf_pass = $buyer_row['zf_pass'];//支付密码
            $buyer_money = $buyer_row['money'];//当前用户的原始金钱
            $buyer_duihuanjifen = $buyer_row['duihuanjifen'];//当前用户的原始积分
            $buyer_money_dj = $buyer_row['money_dj'];//当前用户的原始冻结金额
            $buyer_dongjiejifen = $buyer_row['dongjiejifen'];//当前用户的原始冻结积分
            $buyer_city = $buyer_row['city'];//当前用户所属分站
            $suoding_money = $buyer_row['suoding_money'];//买家用户的锁定金额
            $suoding_jifen = $buyer_row['suoding_jifen'];//买家用户的锁定积分
            $keyong_money = $buyer_money - $suoding_money;
            $keyong_jifen = $buyer_duihuanjifen - $suoding_jifen;

            //检测是否使用支付密码 开始
            $new_zf_pass = md5($zf_pass);

            if ($new_zf_pass != $buyer_zf_pass) { //支付密码 错误 开始
                $this->show_warning('cuowu_zhifumimayanzhengshibai');
                return;
            }
            //支付密码 错误 结束

            $canshu_row = $this->canshu_mod->can();
            //从定单中 读取卖家信息
            $order_row = $this->order_mod->getRow("select * from " . DB_PREFIX . "order where order_id='$order_id' limit 1");
            $order_order_sn = $order_row['order_sn'];//定单号
            $order_seller_id = $order_row['seller_id'];//定单里的 卖家ID
            $order_money = $order_row['order_amount_m'];//定单里的 最后定单总价格


            if ($is_zhe == 1) {
                $jifen_order = $order_row['order_jifen'] * $canshu_row['zhe_jifen'];
            } else {
                $jifen_order = $order_row['order_jifen'];//定单里的 最后定单总积分
            }


            $coupon_sn = $order_row['coupon_sn'];//使用优惠券的编号
            $coupon_id = $order_row['coupon_id'];//优惠券的id
            $youhui_id = $order_row['youhui_id'];//使用付费优惠券的id
            $youhui_name = $order_row['youhui_name'];//付费优惠券的名称
            $youhuidiscount = $order_row['youhuidiscount'];//使用付费优惠券的id
            $discount = $order_row['discount'];//使用付费优惠券的id
            $bianhao = $order_row['bianhao'];//使用付费优惠券的编号
            $riqi = date('Y-m-d H:i:s');

            //读取卖家SQL
            $seller_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$order_seller_id' limit 1");
            $seller_id = $seller_row['user_id'];//卖家ID
            $seller_name = $seller_row['user_name'];//卖家用户名
            $seller_money_dj = $seller_row['money_dj'];//卖家的原始冻结金钱
            $seller_dongjiejifen = $seller_row['dongjiejifen'];//卖家的原始冻结积分
            $seller_money = $seller_row['money'];//卖家的原始金钱
            $seller_duihuanjifen = $seller_row['duihuanjifen'];//卖家的原始积分
            $seller_city = $seller_row['city'];//卖家所属分站

            $canshu_row = $this->canshu_mod->can();
            $jifenxianjin_bili = $canshu_row['jifenxianjin'];
            $daishou_bili = $canshu_row['daishou'];
            $zong_money = $canshu_row['zong_money'];
            $zong_jifen = $canshu_row['zong_jifen'];
            //$order_jifen=$order_money*$jifenxianjin_bili;//返还价

            //检查是否是采购的商品
            $go_row = $this->order_mod->getRow("select g.daishou,g.gong_id from " . DB_PREFIX . "order_goods og left join " . DB_PREFIX . "goods g on og.goods_id=g.goods_id  where og.order_id='$order_id'   limit 1");

            $daishou = $go_row['daishou'];
            $gong_id = $go_row['gong_id'];


            if ($daishou == 1) {
                $this->order_mod->edit('order_id=' . $order_id, array('is_gh' => 1, 'gh_id' => $gong_id));

                $go_row = $this->order_mod->getRow("select user_id,user_name from " . DB_PREFIX . "gonghuo where gh_id='$gong_id' limit 1");
                $gong_userid = $go_row['user_id'];//供货商
                $gong_username = $go_row['user_name'];

                $content = Lang::get('caishou');
                $content = str_replace('{1}', $gong_username, $content);
                $content = str_replace('{2}', $seller_name, $content);
                $content = str_replace('{3}', $buyer_name, $content);
                $add_notice1 = array(
                    'from_id' => 0,
                    'to_id' => $gong_userid,
                    'content' => $content,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice1);
            } else {
                $content = Lang::get('pushou');
                $content = str_replace('{1}', $seller_name, $content);
                $content = str_replace('{2}', $buyer_name, $content);
                $add_notice1 = array(
                    'from_id' => 0,
                    'to_id' => $seller_id,
                    'content' => $content,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice1);
            }

            //检测余额是否足够
            if ($zhifufangshi == "jifenzhifu") {
                if ($keyong_jifen < $jifen_order) {   //检测余额是否足够 开始
                    $this->show_warning('cuowu_zhanghujifenbuzu',
                        'lijichongzhi', 'index.php?app=my_money&act=duihuanxianjinjifen'
                    );
                    return;
                }
            } else {
                if ($keyong_money < $order_money) {   //检测余额是否足够 开始
                    $this->show_warning('cuowu_zhanghuyuebuzu',
                        'lijichongzhi', 'index.php?app=my_money&act=duihuanxianjinjifen'
                    );
                    return;
                }
            }
            //金额是否相同
            if ($post_money != $order_money) {
                $this->show_warning('fashengcuowu_jineshujukeyi');
                return;
            }


            //检测SESSION 是否存为空
            if ($_SESSION['session_order_sn'] != $order_order_sn) {//检测SESSION 开始
                if ($zhifufangshi == "jifenzhifu") {
                    //更新扣除买家的积分
                    $buyer_array = array('duihuanjifen' => $buyer_duihuanjifen - $jifen_order);

                } else {
                    //更新扣除买家的金钱
                    $buyer_array = array(
                        'money' => $buyer_money - $order_money
                    );
                    //更新卖家的冻结金额
                }
                $this->my_money_mod->edit('user_id=' . $user_id, $buyer_array);

                //买家使用优惠券开始
                if ($coupon_id == 0 && $youhui_id == 0) {
                    $buyer_log_text = Lang::get('jiaoyidingdan') . $order_order_sn;
                    $typee = 10;
                } else {
                    if ($coupon_id != 0) {
                        $buyer_log_text = Lang::get('jiaoyidan');
                        $buyer_log_text = str_replace('{1}', $order_order_sn, $buyer_log_text);
                        $buyer_log_text = str_replace('{2}', $coupon_sn, $buyer_log_text);

                        $typee = 24;
                    }
                    if ($youhui_id != 0) {
                        $buyer_log_text = Lang::get('jiaoyiding');
                        $buyer_log_text = str_replace('{1}', $order_order_sn, $buyer_log_text);
                        $buyer_log_text = str_replace('{2}', $bianhao, $buyer_log_text);
                        $typee = 25;
                    }
                }
                //买家使用优惠券结束
                if ($zhifufangshi == "jifenzhifu") {
                    $new_buy_jifen = $buyer_duihuanjifen - $jifen_order;
                    $buyer_add_array = array(
                        'user_id' => $user_id,
                        'user_name' => $buyer_name,
                        'order_id ' => $order_id,
                        'order_sn ' => $order_order_sn,
                        'seller_id' => $seller_id,
                        'seller_name' => $seller_name,
                        'buyer_id' => $user_id,
                        'buyer_name' => $buyer_name,
                        'add_time' => time(),
                        'leixing' => 20,
                        //'money_dj'=>$order_money,
                        'duihuanjifen' => "-" . $jifen_order,
                        'dongjiejifen' => $jifen_order,
                        'log_text' => $buyer_log_text,
                        'caozuo' => 10,
                        's_and_z' => 2,
                        'riqi' => $riqi,
                        'type' => $typee,
                        'city' => $buyer_city,
                        'dq_money' => $buyer_money,
                        'dq_money_dj' => $buyer_money_dj,
                        'dq_jifen' => $new_buy_jifen,
                        'dq_jifen_dj' => $buyer_dongjiejifen,
                        'coupon_id' => $coupon_id,
                        'coupon_sn' => $coupon_sn,
                        'coupon_amount' => $discount,
                        'bianhao' => $bianhao,
                        'youhui_id' => $youhui_id,
                        'youhui_name' => $youhui_name,
                        'youhui_jine' => $youhuidiscount,
                    );

                    $buyer = array(
                        'jifen' => '-' . $jifen_order,
                        'time' => $riqi,
                        'user_name' => $buyer_name,
                        'user_id' => $user_id,
                        'zcity' => $buyer_city,
                        'type' => 15,
                        's_and_z' => 2,
                        'beizhu' => $buyer_log_text,
                        'dq_money' => $buyer_money,
                        'dq_money_dj' => $buyer_money_dj,
                        'dq_jifen' => $new_buy_jifen,
                        'dq_jifen_dj' => $buyer_dongjiejifen
                    );
                    // $beizhu =Lang::get('sq').$buyer_name.Lang::get('jydd').$jifen_order.Lang::get('jifen').Lang::get('zzh');

                    $beizhu = Lang::get('jiaoyidingdan') . $order_order_sn;

                    $addaccount = array(
                        'jifen' => $jifen_order,
                        'time' => $riqi,
                        'user_name' => $buyer_name,
                        'user_id' => $user_id,
                        'zcity' => $buyer_city,
                        'type' => 15,
                        's_and_z' => 1,
                        'beizhu' => $beizhu,
                        'dq_money' => $zong_money,
                        'dq_jifen' => $zong_jifen + $jifen_order,
                        'xiaofei' => $buyer_name,
                        'shangjia' => $seller_name,
                        'gonghuoshang' => $gong_username
                    );
                    $zong_array = array('zong_jifen' => $zong_jifen + $jifen_order);
                } else {
                    $new_buy_money = $buyer_money - $order_money;
                    $buyer_add_array = array(
                        'user_id' => $user_id,
                        'user_name' => $buyer_name,
                        'order_id ' => $order_id,
                        'order_sn ' => $order_order_sn,
                        'seller_id' => $seller_id,
                        'seller_name' => $seller_name,
                        'buyer_id' => $user_id,
                        'buyer_name' => $buyer_name,
                        'add_time' => time(),
                        'leixing' => 20,
                        'money_dj' => $order_money,
                        'money' => '-' . $order_money,
                        'log_text' => $buyer_log_text,
                        'caozuo' => 10,
                        'type' => $typee,
                        's_and_z' => 2,
                        'city' => $buyer_city,
                        'dq_money' => $new_buy_money,
                        'dq_money_dj' => $buyer_money_dj,
                        'dq_jifen' => $buyer_duihuanjifen,
                        'dq_jifen_dj' => $buyer_dongjiejifen,
                        'coupon_id' => $coupon_id,
                        'coupon_sn' => $coupon_sn,
                        'coupon_amount' => $discount,
                        'bianhao' => $bianhao,
                        'youhui_id' => $youhui_id,
                        'youhui_name' => $youhui_name,
                        'youhui_jine' => $youhuidiscount,
                    );
                    $buyer = array(
                        'money' => '-' . $order_money,
                        'time' => $riqi,
                        'user_name' => $buyer_name,
                        'user_id' => $user_id,
                        'zcity' => $buyer_city,
                        'type' => 15,
                        's_and_z' => 2,
                        'beizhu' => $buyer_log_text,
                        'dq_money' => $new_buy_money,
                        'dq_money_dj' => $buyer_money_dj,
                        'dq_jifen' => $buyer_duihuanjifen,
                        'dq_jifen_dj' => $buyer_dongjiejifen
                    );
                    $beizhu = Lang::get('jiaoyidingdan') . $order_order_sn;
                    $addaccount = array(
                        'money' => $order_money,
                        'time' => $riqi,
                        'user_name' => $buyer_name,
                        'user_id' => $user_id,
                        'zcity' => $buyer_city,
                        'type' => 15,
                        's_and_z' => 1,
                        'beizhu' => $beizhu,
                        'dq_money' => $zong_money + $order_money,
                        'dq_jifen' => $zong_jifen,
                        'xiaofei' => $buyer_name,
                        'shangjia' => $seller_name,
                        'gonghuoshang' => $gong_username
                    );
                    $zong_array = array('zong_money' => $zong_money + $order_money);
                }
                $this->canshu_mod->edit('id=1', $zong_array);
                $this->my_moneylog_mod->add($buyer_add_array);
                $this->moneylog_mod->add($buyer);
                $this->accountlog_mod =& m('accountlog');
                $this->accountlog_mod->add($addaccount);


                //改变定单为 已支付等待卖家确认  status10改为20
                $payment_code = "sft";
                //更新定单状态

                $order_edit_array = array(
                    'payment_name' => Lang::get('shangfutong'),
                    'payment_code' => $payment_code,
                    'pay_time' => time(),
                    'out_trade_sn' => $order_sn,
                    'status' => 20,//20就是 待发货了
                    'zhifufangshi' => $zhifufangshi,
                    /*'city'  =>$city_id,*/
                );


                if ($is_zhe == 1) {
                    $order_edit_array['zhe_jifen'] = $canshu_row['zhe_jifen'];
                }

                $this->order_mod->edit($order_id, $order_edit_array);
                //$edit_data['status']    =   ORDER_ACCEPTED;//定义 为 20 待发货
                //$order_model->edit($order_id, $edit_data);//直接更改为 20 待发货
                //支付成功
                $this->show_message('zhifu_chenggong',
                    'sanmiaohouzidongtiaozhuandaodingdanliebiao', 'index.php?app=buyer_order',
                    'chankandingdan', 'index.php?app=buyer_order',
                    'guanbiyemian', 'index.php?app=my_money&act=exits'
                );
//定义SESSION值
                $_SESSION['session_order_sn'] = $order_order_sn;
            }//检测SESSION为空 执行完毕
            else//检测SESSION为空 否则
            {//检测SESSION为空 否则 开始
                $this->show_warning('jinggao_qingbuyaochongfushuaxinyemian');
                return;
            }//检测SESSION为空 否则 结束
        } else {
            $this->show_warning('feifacanshu');
            return;
        }
    }


//筛选充值方式
    function czfs()
    {
        if ($_POST) {
            $user_id = $this->visitor->get('user_id');
            $user_name = $this->visitor->get('user_name');
            $cz_money = trim($_POST['cz_money']);
            $czfs = trim($_POST['czfs']);


            $pay_row = $this->my_paysetup_mod->getRow("select * from " . DB_PREFIX . "my_paysetup");

            $v_mid = $pay_row['chinabank_mid'];
            $v_url = $pay_row['chinabank_url'];
            $key = $pay_row['chinabank_key'];


            if ($czfs == 'chinabank') {
                $v_oid = date('Ymd-His', time()) . "-" . $user_id . "-" . $cz_money;      //网银定单号,不加商号了
                $v_moneytype = "CNY";                                            //币种
                $text = $cz_money . $v_moneytype . $v_oid . $v_mid . $v_url . $key;        //md5加密拼凑串,注意顺序不能变
                //充值金额+CMY+定单号+URL地址+KEY密匙
                $v_md5info = strtoupper(md5($text));                             //md5函数加密并转化成大写字母
                ?>
                <body onLoad="javascript:document.E_FORM.submit()">
                <form method="post" name="E_FORM" action="https://pay3.chinabank.com.cn/PayGate">
                    <input type="hidden" name="v_mid" value="<?php echo $v_mid; ?>">
                    <input type="hidden" name="v_oid" value="<?php echo $v_oid; ?>">
                    <input type="hidden" name="v_amount" value="<?php echo $cz_money; ?>">
                    <input type="hidden" name="v_moneytype" value="<?php echo $v_moneytype; ?>">
                    <input type="hidden" name="v_url" value="<?php echo $v_url; ?>">
                    <input type="hidden" name="v_md5info" value="<?php echo $v_md5info; ?>">
                    <input type="hidden" name="remark1" value="<?php echo $remark1; ?>">
                    <input type="hidden" name="remark2" value="<?php echo $remark2; ?>">
                </form>
                </body>
                <?php
                return;//网银充值转向结束
            } else if ($czfs == 'yeepay')//易宝支付
            {
                $p1_MerId = $pay_row['yeepay_mid'];

                $p2_Order = date('Ymd-His', time()) . "-" . $user_id . "-" . $cz_money;//给易宝的定单号
                $p3_Amt = trim($_POST['cz_money']);//给易宝的提交金额
                $p8_Url = $pay_row['yeepay_url'];//给易宝的返回URL
                //pr_NeedResponse是返回机制0不需要  1需要
                ?>
                <body onLoad="document.yeepay.submit();">
                <form name='yeepay' action='yeepay/req.php' method='post'>
                    <input type='hidden' name='p1_MerId' value='<?php echo $p1_MerId; ?>'>
                    <input type='hidden' name='p2_Order' value='<?php echo $p2_Order; ?>'>
                    <input type='hidden' name='p3_Amt' value='<?php echo $p3_Amt; ?>'>
                    <input type='hidden' name='p5_Pid' value=''>
                    <input type='hidden' name='p6_Pcat' value=''>
                    <input type='hidden' name='p7_Pdesc' value=''>
                    <input type='hidden' name='p8_Url' value='<?php echo $p8_Url; ?>'>
                    <input type='hidden' name='p9_SAF' value='0'>
                    <input type='hidden' name='pa_MP' value='<?php echo $user_name; ?>'>
                    <input type='hidden' name='pd_FrpId' value=''>
                    <input type='hidden' name='pr_NeedResponse' value='1'>
                </form>
                </body>
                <?php
                return;
            } else if ($czfs == 'alipay') {
                $alipay_id = $pay_row['alipay_id'];
                $alipay_key = $pay_row['alipay_key'];
                $alipay_jiekou = $pay_row['alipay_jiekou'];
                $alipay_qubiema = $pay_row['alipay_qubiema'];
                ?>
                <body onLoad="javascript:document.alipay.submit()">
                <!--https://tradeexprod.alipay.com/cooperate/createTradeByBuyer.htm?partner=2088701920622911&out_trade_no=1224940715-->
                <form method="post" name="alipay" action="/zhifubao/alipayto.php">
                    <input type="hidden" name="subject" value="<?php echo $subject; ?>">
                    <input type="hidden" name="total_fee " value="<?php echo $cz_money; ?>">
                    <input type="hidden" name="alibody" value="<?php echo $alibody; ?>">

                </form>
                </body>
                <?php
                return;//支付宝充值转向结束
            }


            /*else if($czfs !='chinabank')
	{
	   $this->show_warning('kaifazhong');
       return;
	}
*/
        } else {
            //不是提交的，直接跳到充值页，重新提交
            header("Location: index.php?app=my_money&act=paylist");
            return;
        }
    }


//易宝支付返回数据 进行站内冲值
    function yee_pay()
    {
        include('yeepay/yeepayCommon.php');
#	只有支付成功时易宝支付才会通知商户.
##支付成功回调有两次，都会通知到在线支付请求参数中的p8_Url上：浏览器重定向;服务器点对点通讯.
#	解析返回参数.
        $return = getCallBackValue($r0_Cmd, $r1_Code, $r2_TrxId, $r3_Amt, $r4_Cur, $r5_Pid, $r6_Order, $r7_Uid, $r8_MP, $r9_BType, $hmac);
#	判断返回签名是否正确（True/False）
        $bRet = CheckHmac($r0_Cmd, $r1_Code, $r2_TrxId, $r3_Amt, $r4_Cur, $r5_Pid, $r6_Order, $r7_Uid, $r8_MP, $r9_BType, $hmac);
#	以上代码和变量不需要修改.
#	校验码正确.
        if ($bRet) {
            if ($r1_Code == "1") {
                #	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
                #	并且需要对返回的处理进行事务控制，进行记录的排它性处理，防止对同一条交易重复发货的情况发生.
                if ($r9_BType == "1") {
                    $user_id = $this->visitor->get('user_id');
                    //读取汇率
                    $paysetup = $this->my_paysetup_mod->getRow("select * from " . DB_PREFIX . "my_paysetup where id='1'");
                    $rb_BankId = $_GET["rb_BankId"];//读取易宝返回的银行编码，判定什么接口
//判断使用银行的  计算汇率
                    if ($rb_BankId == "ICBC-NET" or $rb_BankId == "ICBC-WAP" or $rb_BankId == "CMBCHINA-NET" or $rb_BankId == "CMBCHINA-WAP" or $rb_BankId == "ABC-NET" or $rb_BankId == "CCB-NET" or $rb_BankId == "CCB-PHONE" or $rb_BankId == "BCCB-NET" or $rb_BankId == "BOCO-NET" or $rb_BankId == "CIB-NET" or $rb_BankId == "NJCB-NET" or $rb_BankId == "CMBC-NET" or $rb_BankId == "CEB-NET" or $rb_BankId == "BOC-NET" or $rb_BankId == "PINGANBANK-NET" or $rb_BankId == "CBHB-NET" or $rb_BankId == "HKBEA-NET" or $rb_BankId == "ECITIC-NET" or $rb_BankId == "SDB-NET" or $rb_BankId == "SPDB-NET" or $rb_BankId == "POST-NET" or $rb_BankId == "1000000-NET") {
//银行 一般99%
//$r3_Amt=$r3_Amt / 100 * $paysetup['yeepay_bank'];
//sprintf("%0.2f",值) 是取0.00格式
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_bank']);
                    } //骏网一卡通
                    else if ($rb_BankId == "JUNNET-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_junnet']);
                    } //盛大卡
                    else if ($rb_BankId == "SNDACARD-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_sndacard']);
                    } //神州行
                    else if ($rb_BankId == "SZX-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_szx']);
                    } //征途卡
                    else if ($rb_BankId == "ZHENGTU-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_zhengtu']);
                    } //Q币卡
                    else if ($rb_BankId == "QQCARD-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_qqcard']);
                    } //联通卡
                    else if ($rb_BankId == "UNICOM-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_unicon']);
                    } //久游卡
                    else if ($rb_BankId == "JIUYOU-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_jiuyou']);
                    } //易宝一卡通
                    else if ($rb_BankId == "YPCARD-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_ypcard']);
                    } //联华OK卡
                    else if ($rb_BankId == "LIANHUAOKCARD-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_lianhuaokcard']);
                    } //网易卡
                    else if ($rb_BankId == "NETEASE-NET") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_netease']);
                    } //完美卡
                    else if ($rb_BankId == "WANMEI") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_wanmei']);
                    } //搜狐卡
                    else if ($rb_BankId == "SOHU") {
                        $r3_Amt = sprintf("%0.2f", $r3_Amt / 100 * $paysetup['yeepay_sohu']);
                    } //充值成功，出现错误，请联系管理员
                    else {
                        $this->show_warning('yeepaychenggongdanchuxiancuowuqinglianxiadmin');
                        return;
                    }

//检测定单是否重复提交
                    $order_row = $this->my_moneylog_mod->getRow("select order_sn from " . DB_PREFIX . "my_moneylog where user_id='$user_id' and order_sn='$r6_Order'");

                    if ($r6_Order != $order_row['order_sn']) {
                        //支付成功，可进行逻辑处理！
                        //商户系统的逻辑处理（例如判断金额，判断支付状态，更新订单状态等等）......
                        $user_row = $this->my_money_mod->getRow("select money from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
                        $user_money = $user_row['money'];

                        $new_money = $user_money + $r3_Amt;
                        $edit_mymoney = array(
                            'money' => $new_money,
                        );
                        $this->my_money_mod->edit('user_id=' . $this->visitor->get('user_id'), $edit_mymoney);
                        //添加日志
                        $log_text = $this->visitor->get('user_name') . Lang::get('tongguoyeepaychongzhi') . $r3_Amt . Lang::get('yuan');

                        $add_mymoneylog = array(
                            'user_id' => $user_id,
                            'user_name' => $this->visitor->get('user_name'),
                            'buyer_name' => Lang::get('yeepay'),
                            'seller_id' => $user_id,
                            'seller_name' => $this->visitor->get('user_name'),
                            'order_sn ' => $r2_TrxId,
                            'add_time' => time(),
                            'leixing' => 30,
                            'money_zs' => $r3_Amt,
                            'money' => $r3_Amt,
                            'log_text' => $log_text,
                            'caozuo' => 50,
                            's_and_z' => 1,
                        );
                        $this->my_moneylog_mod->add($add_mymoneylog);
                    } else {
                        $this->show_warning('jinggao_qingbuyaochongfushuaxinyemian',
                            'guanbiyemian', 'index.php?app=my_money&act=exits'
                        );
                        return;
                    }

                } elseif ($r9_BType == "2") {
                    #如果需要应答机制则必须回写流,以success开头,大小写不敏感.
                    $this->show_warning('success');
                    return;

                }
            }


            $this->show_message('chongzhi_chenggong_jineyiruzhang',
                'chakancicichongzhi', 'index.php?app=my_money&act=paylog',
                'guanbiyemian', 'index.php?app=my_money&act=exits'
            );


        } else {
            $this->show_warning('feifacanshu');
            return;
        }

    }

//网银支付返回数据 进行站内冲值
    function chinabank_pay()
    {
        $user_id = $this->visitor->get('user_id');
        if ($_POST) {
            $pay_row = $this->my_paysetup_mod->getRow("select * from " . DB_PREFIX . "my_paysetup where id='1'");
            $key = $pay_row['chinabank_key'];

            $v_oid = trim($_POST['v_oid']);       // 商户发送的v_oid定单编号
            $v_pmode = trim($_POST['v_pmode']);    // 支付方式（字符串）
            $v_pstatus = trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
            $v_pstring = trim($_POST['v_pstring']);   //提示中文"支付成功"字符串

            $v_amount = trim($_POST['v_amount']);     // 订单实际支付金额
            $v_moneytype = trim($_POST['v_moneytype']); //订单实际支付币种
            $remark1 = trim($_POST['remark1']);      //备注字段1
            $remark2 = trim($_POST['remark2']);     //备注字段2
            $v_md5str = trim($_POST['v_md5str']);   //拼凑后的MD5校验值

//重新计算md5的值
            $md5string = strtoupper(md5($v_oid . $v_pstatus . $v_amount . $v_moneytype . $key));
            if ($v_md5str == $md5string)//校验MD5 开始
            {//校验MD5 IF括号
                if ($v_pstatus == "20") {
//检测定单是否重复提交
                    $order_row = $this->my_moneylog_mod->getRow("select order_sn from " . DB_PREFIX . "my_moneylog where user_id='$user_id' and order_sn='$v_oid'");

                    if ($v_oid != $order_row['order_sn']) {
                        //支付成功，可进行逻辑处理！
                        //商户系统的逻辑处理（例如判断金额，判断支付状态，更新订单状态等等）......
                        $user_row = $this->my_money_mod->getRow("select money from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
                        $user_money = $user_row['money'];
                        $user_city = $user_row['city'];

                        $new_money = $user_money + $v_amount;
                        $edit_mymoney = array(
                            'money' => $new_money,
                        );
                        $this->my_money_mod->edit('user_id=' . $user_id, $edit_mymoney);
                        //添加日志
                        $log_text = $this->visitor->get('user_name') . Lang::get('tongguowangyinjishichongzhi') . $v_amount . Lang::get('yuan');

                        $add_mymoneylog = array(
                            'user_id' => $user_id,
                            'user_name' => $this->visitor->get('user_name'),
                            'buyer_name' => Lang::get('chinabankzhifu') . $v_pmode,
                            'seller_id' => $user_id,
                            'seller_name' => $this->visitor->get('user_name'),
                            'order_sn ' => $v_oid,
                            'add_time' => time(),
                            'leixing' => 30,
                            'money_zs' => $v_amount,
                            'money' => $v_amount,
                            'log_text' => $log_text,
                            'caozuo' => 50,
                            's_and_z' => 1,
                            'city' => $user_city,

                        );
                        $this->my_moneylog_mod->add($add_mymoneylog);
                        $this->show_message('chongzhi_chenggong_jineyiruzhang',
                            'chakancicichongzhi', 'index.php?app=my_money&act=paylog',
                            'guanbiyemian', 'index.php?app=my_money&act=exits'
                        );
                    } else {
                        $this->show_warning('jinggao_qingbuyaochongfushuaxinyemian',
                            'guanbiyemian', 'index.php?app=my_money&act=exits'
                        );
                        return;
                    }

                } else {
                    $this->show_warning('chongzhi_shibai_qingchongxintijiao',
                        'guanbiyemian', 'index.php?app=my_money&act=exits'
                    );
                    return;
                }
            } else { //否则 校验MD5
                $this->show_warning('wangyinshujuxiaoyanshibai_shujukeyi',
                    'guanbiyemian', 'index.php?app=my_money&act=exits'
                );
                return;
            }//否则 校验MD5  结束

        } else {
            $this->show_warning('feifacanshu',
                'guanbiyemian', 'index.php?app=my_money&act=exits'
            );
            return;
        }
    }


    function alipay()
    {
        require_once("alipay.config.php");
        require_once("lib/alipay_service.class.php");

        /**************************请求参数**************************/

//必填参数//

        $out_trade_no = $_POST['order_no'];        //请与贵网站订单系统中的唯一订单号匹配
        $subject = "订单号：" . $_POST['order_no'];    //订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
        $body = "";    //订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
        $price = $_POST['order_total'];    //订单总金额，显示在支付宝收银台里的“应付总额”里

        $logistics_fee = "0.00";                //物流费用，即运费。
        $logistics_type = "EXPRESS";            //物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        $logistics_payment = "SELLER_PAY";            //物流支付方式，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）

        $quantity = "1";                    //商品数量，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品。

//选填参数//

//买家收货信息（推荐作为必填）
//该功能作用在于买家已经在商户网站的下单流程中填过一次收货信息，而不需要买家在支付宝的付款流程中再次填写收货信息。
//若要使用该功能，请至少保证receive_name、receive_address有值
//收货信息格式请严格按照姓名、地址、邮编、电话、手机的格式填写
        $receive_name = $_POST['receive_name'];            //收货人姓名，如：张三
        $receive_address = $_POST['receive_address'];            //收货人地址，如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        $receive_zip = $_POST['receive_zip'];                //收货人邮编，如：123456
        $receive_phone = $_POST['receive_phone'];        //收货人电话号码，如：0571-81234567
        $receive_mobile = $_POST['receive_mobile'];        //收货人手机号码，如：13312341234

//网站商品的展示地址，不允许加?id=123这类自定义参数
        $show_url = "http://www.xxx.com/myorder.php";

        /************************************************************/

//构造要请求的参数数组
        $parameter = array(
            "service" => "create_partner_trade_by_buyer",
            "payment_type" => "1",

            "partner" => trim($aliapy_config['partner']),
            "_input_charset" => trim(strtolower($aliapy_config['input_charset'])),
            "seller_email" => trim($aliapy_config['seller_email']),
            "return_url" => trim($aliapy_config['return_url']),
            "notify_url" => trim($aliapy_config['notify_url']),

            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "body" => $body,
            "price" => $price,
            "quantity" => $quantity,

            "logistics_fee" => $logistics_fee,
            "logistics_type" => $logistics_type,
            "logistics_payment" => $logistics_payment,

            "receive_name" => $receive_name,
            "receive_address" => $receive_address,
            "receive_zip" => $receive_zip,
            "receive_phone" => $receive_phone,
            "receive_mobile" => $receive_mobile,

            "show_url" => $show_url
        );

//构造担保交易接口
        $alipayService = new AlipayService($aliapy_config);
        $html_text = $alipayService->create_partner_trade_by_buyer($parameter);
        echo $html_text;

    }


//冲值卡
    function card_cz()
    {  //充值开始
        $user_name = $this->visitor->get('user_name');
        $userid = $this->visitor->get('user_id');
        //$user_name = trim($_POST['user_name1']);
        $card_sn = trim($_POST['card_sn']);//充值金额
        $bank_username = trim($_POST['bank_username']);
        $bank_name = trim($_POST['bank_name']);

        /*	$riqi = trim($_POST['riqi']);*/
        $type = trim($_POST['type']);
        $status = trim($_POST['status']);
        $danhao = trim($_POST['danhao']);
        $czfeilv = trim($_POST['czfeilv']);
        $beizhu = trim($_POST['beizhu']);

        $czkg = trim($_POST['czkg']);
        //充值结束
        $riqi = date('Y-m-d H:i:s');
        $this->canshu_mod =& m('canshu');
        $jinbi_row = $this->canshu_mod->getRow("select yu_jinbi,zong_money,chongzhifeilv,chongzhifeimin,chongzhifeimax from " . DB_PREFIX . "canshu limit 1");
        $yu_jinbi = $jinbi_row['yu_jinbi'];
        $chongzhifeilv = $jinbi_row['chongzhifeilv'];
        $chongzhifeimin = $jinbi_row['chongzhifeimin'];
        $chongzhifeimax = $jinbi_row['chongzhifeimax'];
        $zong_money = $jinbi_row['zong_money'];
        $new_yu_jinbi = $yu_jinbi - $card_sn;
        $cz_min = $jinbi_row['cz_min'];
        $cz_max = $jinbi_row['cz_max'];
        $zmoney = $card_sn - $card_sn * $chongzhifeilv;//实际充值
        $czfeiyong = $card_sn * $chongzhifeilv;//充值费用

        if ($czfeiyong < $chongzhifeimin) {
            $czfeiyong = $chongzhifeimin;
        }
        if ($czfeiyong > $chongzhifeimax) {
            $czfeiyong = $chongzhifeimax;
        }


        //$new_zong_money=$zong_money+$card_sn;
        $new_zong_money = $zong_money + $czfeiyong;
        /*if($card_sn < $cz_min)
	{
		$czmin=Lang::get('czbunengxiaoyu');
		$czmin=str_replace('{1}',$cz_min,$czmin);
		$this->show_warning($czmin);
		return;
	}
	if($card_sn > $cz_max)
	{
		$czmax=Lang::get('czbunengdayu');
		$czmax=str_replace('{1}',$cz_max,$czmax);
		$this->show_warning($czmax);
		return;
	}*/
        if ($yu_jinbi < $card_sn) {
            //$this->show_warning('zijinbuzu');
            $this->show_message('zijinbuzu',
                'guanbiyemian', 'index.php?app=my_money&act=paylist');
            return;
        }

        if ($_POST)//检测有提交
        {//检测有提交
            /*if (preg_match("/[^0.-9]/",$cz_jine))*/
            if (preg_match("/[^0.-9]/", $card_sn)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }
//    //充值对象不能为空
//	if(empty($user_name))
//    {
//	$this->show_warning('cuowu_mubiaoyonghubucunzai');
//    return;
//	}


            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$userid' limit 1");
            $user_money = $user_row['money'];
            $new_user_money = $user_money + $card_sn;
            $user_money_dj = $user_row['money_dj'];
            $duihuanjifen = $user_row['duihuanjifen'];
            $dongjiejifen = $user_row['dongjiejifen'];
            $user_id = $user_row['user_id'];
            $city = $user_row['city'];
            if (empty($user_id)) {
                $this->show_warning('cuowu_mubiaoyonghubucunzai');
                return;
            }
//$card_row=$this->my_card_mod->getrow("select * from ".DB_PREFIX."my_card where card_pass='$card_pass'");
//$card_id=$card_row['id'];
//    //读取空 提示卡号、密码错误
            //if(empty($card_row))
//    {
//	$this->show_warning('cuowu_card_pass');
//    return;
//	}
//	//检测过期时间小于现在时间，则提示已经过期
//	if($card_row['guoqi_time'] < time())
//    {
//	$this->show_warning('cuowu_cardyijingguoqi');
//    return;
//	}
            if ($card_row['user_id'] != 0) {
                $this->show_warning('cuowu_cardyijingshiyongguole');
                return;
            } else {

                //添加my_moneylog日志
                $log_text = $user_name . Lang::get('chongzhile') . $card_sn . Lang::get('yuan') . Lang::get('kouchuchongzhifeiyong') . $czfeiyong . Lang::get('yuan');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'buyer_id' => $this->visitor->get('user_id'),
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_id' => $user_id,
                    'seller_name' => $user_name,
                    'order_sn ' => $cz_book,
                    'add_time' => time(),
                    'leixing' => 30,
                    'log_text' => $log_text,
                    'caozuo' => 50,
                    's_and_z' => 1,
                    'money' => '+' . $card_sn,
                    //'money_zs'=>$card_sn,
                    'bank_username' => $bank_username,
                    'bank_name' => $bank_name,
                    'riqi' => $riqi,
                    'type' => 1,
                    'status' => $status,
                    'danhao' => $danhao,
                    //'feilv'=>$czfeilv,
                    'money_feiyong' => '-' . $czfeiyong,
                    'beizhu' => $beizhu,
                    'city' => $city,
                    'dq_money' => $user_money,//没有加充值的金额
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $duihuanjifen,
                    'dq_jifen_dj' => $dongjiejifen,

                );

                //写入日志
                $this->my_moneylog_mod->add($add_mymoneylog);


                //定义新资金
                $yuanmoney = $zmoney;
                $new_user_money = $user_money + $yuanmoney;

                /*$new_user_money = $user_money+$card_row['money'];*/
                //定义资金数组
                $add_money = array('money' => $new_user_money);
                if ($czkg == 'no') {
                    $log_id = 1;
                    $edit_canshu = array(
                        'yu_jinbi' => $new_yu_jinbi,
                        'zong_money' => $new_zong_money,
                    );
                    $this->canshu_mod->edit('id=' . $log_id, $edit_canshu);
                    //更新该用户资金
                    $this->my_money_mod->edit('user_id=' . $user_id, $add_money);//增加my_money表里的资金
                }
                //改变充值卡信息 已使用
                $add_cardlog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'cz_time' => time(),
                );
//	$this->my_card_mod->edit('id='.$card_id,$add_cardlog);
//    //提示语言
                $this->show_message('chongzhi_chenggong_jineyiruzhang',
                    'chakancicichongzhi', 'index.php?app=my_money&act=paylog',
                    'guanbiyemian', 'index.php?app=my_money&act=exits');
                return;
            }
        } else//检测提交 否则
        {//检测提交 开始
            header("Location: index.php?app=my_money");
            return;
        }//检测提交 结束
    }

//兑换积分
    function duihuan_jifen()
    {
        $canshu_row = $this->canshu_mod->can();
        $jifen_xianjin = $canshu_row['jifenxianjin'];
        $bili = $canshu_row['duihuanjifenfeilv'];//兑换积分费率

//$user_name=$this->visitor->get('user_name');
        $user_name = trim($_POST['user_name1']);
        $duihuanjine = trim($_POST['duihuanjine']);//兑换金额
        $zhesuan_duihuanjifen = $duihuanjine * $jifen_xianjin;//折算过之后的积分
        /*$riqi = trim($_POST['riqi']);*/
        $type = trim($_POST['type']);
        $status = trim($_POST['status']);
        $beizhu = trim($_POST['beizhu']);
        //$bili = trim($_POST['bili']);
        $duihuanjifen_feiyong = $zhesuan_duihuanjifen * $bili;//兑换积分费用(积分)
        $duihuanjifen = $zhesuan_duihuanjifen - $duihuanjifen_feiyong;//实际兑换的积分
        $dhjf_feiyong = $duihuanjine * $bili;//兑换积分费用（现金）


        $log_text = trim($_POST['log_text']);
        $riqi = date('Y-m-d H:i:s');
        $dhjf = trim($_POST['dhjf']);
        if ($_POST)//检测有提交
        {
            //检测有提交
            if ($duihuanjine <= 0) {
                $this->show_warning('duihuanjinebunengxiaoyuling');
                return;
            }

            /*if (preg_match("/[^0.-9]/",$cz_jine))*/
            if (preg_match("/[^0.-9]/", $duihuanjine)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }

            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_name='$user_name'");
            $user_money = $user_row['money'];
            //echo "select * from ".DB_PREFIX."my_money where user_name='$user_name'";
            $user_id = $user_row['user_id'];
            $user_moneydj = $user_row['money_dj'];
            $user_duihuanjifen = $user_row['duihuanjifen'];
            $user_dongjiejifen = $user_row['dongjiejifen'];
            $suoding = $user_row['suoding_money'];
            $keyong_money = $user_money - $suoding;
            $city = $user_row['city'];

            if ($keyong_money < $duihuanjine) {
                $this->show_message('cuowu_nideyuebuzu',
                    'guanbiyemian', 'index.php?app=my_money&act=duihuanxianjinjifen');
                return;
            }
            if (empty($user_id)) {
                $this->show_warning('cuowu_mubiaoyonghubucunzai');
                return;
            }

            if ($card_row['user_id'] != 0) {
                $this->show_warning('cuowu_cardyijingshiyongguole');
                return;
            } else {
                //对接webservice开始
                $this->kaiguan_mod =& m('kaiguan');
                $row_kaiguan = $this->kaiguan_mod->getRow("select webservice from " . DB_PREFIX . "kaiguan");
                $webservice = $row_kaiguan['webservice'];
                $user_id = $this->visitor->get('user_id');
                $this->member_mod =& m('member');
                $user_row = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
                $web_id = $user_row['web_id'];
                /*$post_data = array(
	"ID"=>$web_id,
	"Money"=>$duihuanjifen_feiyong,
	"MoneyType"=>2,
	"Count"=>1
	); */
                //print_r($post_data);

                //对接webservice结束

                //定义新资金
                $new_user_money = $user_money - $duihuanjine;
                $new_moneydj = $user_moneydj + $duihuanjine;
                $new_duihuanjifen = $user_duihuanjifen + $duihuanjifen;

                //添加mymoneylog日志
                $log_text = $user_name . Lang::get('duihuanle') . $duihuanjine . Lang::get('yuan') . Lang::get('huodejifen') . $zhesuan_duihuanjifen . Lang::get('jifen') . Lang::get('kouchujifenfeiyong') . $duihuanjifen_feiyong . Lang::get('jifen');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'buyer_id' => $this->visitor->get('user_id'),
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_id' => $user_id,
                    'seller_name' => $user_name,
                    'order_sn ' => $cz_book,
                    'add_time' => time(),
                    'leixing' => 11,
//	'money_zs'=>$card_row['money'],
//	'money'=>$card_row['money'],
                    'log_text' => $log_text,
                    'caozuo' => 11,
                    's_and_z' => 1,
                    'riqi' => $riqi,
                    'type' => $type,
                    'status' => $status,
                    'duihuanjifen' => $zhesuan_duihuanjifen,
                    'money' => '-' . $duihuanjine,
                    'jifen_feiyong' => '-' . $duihuanjifen_feiyong,
                    'city' => $city,
                    'dq_money' => $new_user_money,
                    'dq_money_dj' => $new_moneydj,
                    'dq_jifen' => $new_duihuanjifen,
                    'dq_jifen_dj' => $user_dongjiejifen,
                );
                //写入日志
                $this->my_moneylog_mod->add($add_mymoneylog);

                //定义资金数组
                $add_money = array(
                    'money' => $new_user_money,
                    'money_dj' => $new_moneydj,
                );
                $add_duihuanjifen = array(
                    'money' => $new_user_money,
                    'duihuanjifen' => $new_duihuanjifen,
                );
                //更新该用户资金

                if ($dhjf == 'yes') {
                    $this->my_money_mod->edit('user_id=' . $user_id, $add_money);//增加my_money表里的资金
                } else {
                    $this->my_money_mod->edit('user_id=' . $user_id, $add_duihuanjifen);

                    $canshu = $this->my_money_mod->can();
                    //添加moneylog兑换积分的兑换金额日志
//$beizhu =$user_name.Lang::get('duihuanle').$duihuanjine.Lang::get('yuan');
                    $addmlog = array(
                        'money' => '-' . $duihuanjine,
                        'time' => $riqi,
                        'user_name' => $user_name,
                        'user_id' => $user_id,
                        'zcity' => $city,
                        'type' => 3,
                        's_and_z' => 2,
                        'beizhu' => $beizhu,
                        'dq_money' => $new_user_money,
                        'dq_money_dj' => $user_moneydj,
                        'dq_jifen' => $new_duihuanjifen,
                        'dq_jifen_dj' => $user_dongjiejifen,
                    );
                    //$this->moneylog_mod->add($addmlog);

                    //添加moneylog兑换积分的兑换了积分日志
//$beizhu =$user_name.Lang::get('duihuanle').$duihuanjine.Lang::get('yuan').Lang::get('huodejifen').$duihuanjifen.Lang::get('jifen').Lang::get('kouchujifenfeiyong').$duihuanjifen_feiyong.Lang::get('jifen');
                    $addmoneylog = array(
                        'jifen' => '+' . $duihuanjifen,
                        'money' => '-' . $duihuanjine,
                        'time' => $riqi,
                        'user_name' => $user_name,
                        'user_id' => $user_id,
                        'zcity' => $city,
                        'type' => 3,
                        's_and_z' => 1,
                        'beizhu' => $beizhu,
                        'dq_money' => $new_user_money,
                        'dq_money_dj' => $user_moneydj,
                        'dq_jifen' => $new_duihuanjifen,
                        'dq_jifen_dj' => $user_dongjiejifen,
                    );
                    $this->moneylog_mod->add($addmoneylog);

                    $add_account = array(
                        'money' => $duihuanjine,
                        'jifen' => '-' . $duihuanjifen,
                        'time' => $riqi,
                        'user_name' => $user_name,
                        'user_id' => $user_id,
                        'zcity' => $city,
                        'type' => 3,
                        's_and_z' => 1,
                        'beizhu' => $beizhu,
                        'dq_money' => $canshu['zong_money'] + $duihuanjine,
                        'dq_jifen' => $canshu['zong_jifen'] - $duihuanjifen
                    );
                    $this->accountlog_mod->add($add_account);

                    $this->canshu_mod->edit('id=1', array('zong_money' => $canshu['zong_money'] + $duihuanjine, 'zong_jifen' => $canshu['zong_jifen'] - $duihuanjifen));


                }


                //改变充值卡信息 已使用
                $add_cardlog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'cz_time' => time(),
                );
//	$this->my_card_mod->edit('id='.$card_id,$add_cardlog);
//    //提示语言
                if ($dhjf == 'yes') {
                    $this->show_message('duihuan_chenggong',
                        'chakancicichongzhi', 'index.php?app=my_money&act=jifenduihuan',
                        'guanbiyemian', 'index.php?app=my_money&act=exits');
                    return;
                } else {
                    $this->show_message('duihuangong',
                        'chakancicichongzhi', 'index.php?app=my_money&act=jifenduihuan',
                        'guanbiyemian', 'index.php?app=my_money&act=exits');
                    return;
                }


            }
        } else//检测提交 否则
        {//检测提交 开始
            header("Location: index.php?app=my_money");
            return;
        }//检测提交 结束
    }


//兑换现金
    function duihuan_xianjin()
    {
        $canshu_row = $this->canshu_mod->can();
        $jifen_xianjin = $canshu_row['jifenxianjin'];
        $lv31 = $canshu_row['lv31'];

        $user_name = trim($_POST['user_name1']);
        $duihuanjifen = trim($_POST['duihuanjifen']);//兑换积分的
        /*$riqi = trim($_POST['riqi']);*/
        $type = trim($_POST['type']);
        $status = trim($_POST['status']);
        $beizhu = trim($_POST['beizhu']);
        $bili = trim($_POST['bili']);
        $bili = 0;
        $yu_jifen = $duihuanjifen * (1 - $lv31);
        $yu_money1 = $yu_jifen / $jifen_xianjin;//扣除31%剩余兑换的金额

        $yu_money = ((int)($yu_money1 * 100)) / 100;
        $zhesuan_jifen = $duihuanjifen * 1 / $jifen_xianjin;//将积分折算成现金
        $money_feiyong = $zhesuan_jifen * $bili;//兑换费用（现金）
        $money_jifen_feiyong = $duihuanjifen * $bili;//兑换费用（积分）将该值传递给webservice
        $money_zs = $zhesuan_jifen - $money_feiyong;//实际兑换的现金
        $jifen_kou = $duihuanjifen - $money_jifen_feiyong;//扣除积分费用的积分

        $log_text = trim($_POST['log_text']);
        $riqi = date('Y-m-d H:i:s');

        if ($_POST)//检测有提交
        {//检测有提交
            /*if (preg_match("/[^0.-9]/",$cz_jine))*/
            if (preg_match("/[^0.-9]/", $duihuanjifen)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }
            if ($duihuanjifen < 10) {
                $this->show_warning('diyu');
                return;
            }
            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_name='$user_name' limit 1");
            $user_money = $user_row['money'];
            $user_money_dj = $user_row['money_dj'];
            $user_id = $user_row['user_id'];
            $user_duihuanjifen = $user_row['duihuanjifen'];
            $user_dongjiejifen = $user_row['dongjiejifen'];
            $city = $user_row['city'];
            $suoding_jifen = $user_row['suoding_jifen'];
            $keyong_jifen = $user_duihuanjifen - $suoding_jifen;
            $new_user_money = $user_money + $money_zs;
            if ($keyong_jifen < $duihuanjifen) {
                $this->show_warning('cuowu_nidejifenbuzu');
                return;
            }
            if (empty($user_id)) {
                $this->show_warning('cuowu_mubiaoyonghubucunzai');
                return;
            }

            if ($card_row['user_id'] != 0) {
                $this->show_warning('cuowu_cardyijingshiyongguole');
                return;
            } else {


                //定义新资金
                $new_duihuanjifen = $user_duihuanjifen - $duihuanjifen;
                $new_dongjiejifen = $user_dongjiejifen + $duihuanjifen;

                //添加日志

                $log_text = $user_name . Lang::get('duihuanle') . $duihuanjifen . Lang::get('jifen') . Lang::get('huodexianjin') . $yu_money . Lang::get('yuan');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'buyer_id' => $this->visitor->get('user_id'),
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_id' => $user_id,
                    'seller_name' => $user_name,
                    'order_sn ' => $cz_book,
                    'add_time' => time(),
                    'leixing' => 12,
                    'log_text' => $log_text,
                    'caozuo' => 12,
                    's_and_z' => 1,
                    'riqi' => $riqi,
                    'type' => 5,
                    'status' => $status,
                    'duihuanjifen' => '-' . $jifen_kou,
                    'money' => $yu_money,
                    'jifen_feiyong' => '-' . $money_jifen_feiyong,
                    'city' => $city,
                    'dq_money' => $new_user_money,//加充值的金额
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $new_duihuanjifen,
                    'dq_jifen_dj' => $new_dongjiejifen,
                );
                //写入日志
                $this->my_moneylog_mod->add($add_mymoneylog);
                //添加moneylog兑换现金的积分
                $addmlog = array(
                    'jifen' => '-' . $duihuanjifen,
                    'time' => $riqi,
                    'user_name' => $user_name,
                    'user_id' => $user_id,
                    'zcity' => $city,
                    'type' => 4,
                    's_and_z' => 2,
                    'beizhu' => $beizhu,
                    'dq_money' => $user_money,//加充值的金额
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $new_duihuanjifen,
                    'dq_jifen_dj' => $new_dongjiejifen,
                );
                //$this->moneylog_mod->add($addmlog);


                //定义资金数组
                $add_jifen = array(
                    'duihuanjifen' => $new_duihuanjifen,
                    'dongjiejifen' => $new_dongjiejifen,
                );
                //更新该用户资金
                $this->my_money_mod->edit('user_id=' . $user_id, $add_jifen);//增加my_money表里的资金
                //改变充值卡信息 已使用
                $add_cardlog = array(
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'cz_time' => time(),
                );
//	$this->my_card_mod->edit('id='.$card_id,$add_cardlog);
//    //提示语言
                $this->show_message('duihuan_chenggong',
                    'chakancicichongzhi', 'index.php?app=my_money&act=txlist',
                    'guanbiyemian', 'index.php?app=my_money&act=exits');
                return;
            }
        } else//检测提交 否则
        {//检测提交 开始
            header("Location: index.php?app=my_money");
            return;
        }//检测提交 结束
    }


//余额转帐
    function to_user()
    {
        $to_user = trim($_POST['to_user']);
        $to_money = trim($_POST['to_money']);
        $user_id = $this->visitor->get('user_id');
        $riqi = date('Y-m-d H:i:s');
        if ($_POST)//检测有提交
        {//检测有提交
            if (preg_match("/[^0.-9]/", $to_money)) {
                $this->show_warning('cuowu_nishurudebushishuzilei');
                return;
            }


            $to_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_name='$to_user'");
            $to_user_id = $to_row['user_id'];
            $to_city_id = $to_row['city'];
            $to_user_name = $to_row['user_name'];
            $to_user_money = $to_row['money'];//转给的用户
            $to_user_money_dj = $to_row['money_dj'];//转给的用户
            $to_duihuanjifen = $to_row['duihuanjifen'];//转给的用户
            $to_dongjiejifen = $to_row['dongjiejifen'];//转给的用户


            if ($to_user_id == $user_id) {
                $this->show_warning('cuowu_bunenggeizijizhuanzhang');
                return;
            }

            if (empty($to_user_id)) {
                $this->show_warning('cuowu_mubiaoyonghubucunzai');
                return;
            }
            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $user_money = $user_row['money'];
            $user_city = $user_row['city'];
            $user_money_dj = $user_row['money_dj'];
            $user_duihuanjifen = $user_row['duihuanjifen'];
            $user_dongjiejifen = $user_row['dongjiejifen'];
            $suoding_money = $user_row['suoding_money'];
            $keyong_money = $user_money - $suoding_money;
            $user_zf_pass = $user_row['zf_pass'];
            $user_mibao_id = $user_row['mibao_id'];
            if (empty($user_mibao_id)) {
                $zf_pass = md5(trim($_POST['zf_pass']));
                if ($user_zf_pass != $zf_pass) {
                    $this->show_warning('cuowu_zhifumimayanzhengshibai');
                    return;
                }
            } else {
//读取密保卡资料
                $user_zimuz1 = trim($_POST['user_zimuz1']);
                $user_zimuz2 = trim($_POST['user_zimuz2']);
                $user_zimuz3 = trim($_POST['user_zimuz3']);
                $user_shuzi1 = trim($_POST['user_shuzi1']);
                $user_shuzi2 = trim($_POST['user_shuzi2']);
                $user_shuzi3 = trim($_POST['user_shuzi3']);
                if (empty($user_shuzi1) or empty($user_shuzi2) or empty($user_shuzi3)) {
                    $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                    return;
                }
                $mibao_row = $this->my_mibao_mod->getRow("select * from " . DB_PREFIX . "my_mibao where user_id='$user_id'");
                $mibao_shuzi1 = $mibao_row[$user_zimuz1];
                $mibao_shuzi2 = $mibao_row[$user_zimuz2];
                $mibao_shuzi3 = $mibao_row[$user_zimuz3];

                if ($user_shuzi1 != $mibao_shuzi1 or $user_shuzi2 != $mibao_shuzi2 or $user_shuzi3 != $mibao_shuzi3) { //检测密保相符 开始
                    echo Lang::get('money_banben');
                    $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                    return;
                } //检测密保 否则 结束
            }


            $order_id = date('Ymd-His', time()) . '-' . $to_money;
            if ($keyong_money < $to_money) {
                $this->show_warning('cuowu_zhanghuyuebuzu');
                return;
            } else {

                $new_user_money = $user_money - $to_money;
                $new_to_user_money = $to_user_money + $to_money;
                //添加日志,转出金额
                $log_text = $this->visitor->get('user_name') . Lang::get('gei') . $to_user . Lang::get('zhuanchujine') . $to_money . Lang::get('yuan');

                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $this->visitor->get('user_name'),
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_name' => $to_user_name,
                    'order_sn ' => $order_id,
                    'add_time' => date(),
                    'leixing' => 21,
                    //'money_zs'=>$to_money,
                    'money' => '-' . $to_money,
                    'log_text' => $log_text,
                    'caozuo' => 50,
                    's_and_z' => 2,
                    'type' => 17,
                    'riqi' => $riqi,
                    'city' => $user_city,
                    'dq_money' => $new_user_money,
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $user_duihuanjifen,
                    'dq_jifen_dj' => $user_dongjiejifen,

                );
                $this->my_moneylog_mod->add($add_mymoneylog);
                //添加moneylog日志
                $beizhu = $this->visitor->get('user_name') . Lang::get('gei') . $to_user . Lang::get('zhuanchujine') . $to_money . Lang::get('yuan');
                $addmlog = array(
                    'money' => '-' . $to_money,
                    'time' => $riqi,
                    'user_name' => $this->visitor->get('user_name'),
                    'user_id' => $this->visitor->get('user_id'),
                    'zcity' => $user_city,
                    'type' => 20,
                    's_and_z' => 2,
                    'beizhu' => $beizhu,
                    'dq_money' => $new_user_money,
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $user_duihuanjifen,
                    'dq_jifen_dj' => $user_dongjiejifen,
                );
                $this->moneylog_mod->add($addmlog);


                //转入金额
                $log_text_to = $this->visitor->get('user_name') . Lang::get('gei') . $to_user_name . Lang::get('zhuanrujine') . $to_money . Lang::get('yuan');
                $add_mymoneylog_to = array(
                    'user_id' => $to_user_id,
                    'user_name' => $to_user_name,
                    'order_sn ' => $order_id,
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_name' => $to_user_name,
                    'add_time' => time(),
                    'leixing' => 11,
                    //'money_zs'=>$to_money,
                    'money' => $to_money,
                    'log_text' => $log_text_to,
                    'caozuo' => 50,
                    's_and_z' => 1,
                    'type' => 19,
                    'riqi' => $riqi,
                    'city' => $to_city_id,
                    'dq_money' => $new_to_user_money,
                    'dq_money_dj' => $to_user_money_dj,
                    'dq_jifen' => $to_duihuanjifen,
                    'dq_jifen_dj' => $to_dongjiejifen,

                );
                $this->my_moneylog_mod->add($add_mymoneylog_to);

                //添加moneylog日志
                $beizhu = $this->visitor->get('user_name') . Lang::get('gei') . $to_user_name . Lang::get('zhuanrujine') . $to_money . Lang::get('yuan');
                $addmlog1 = array(
                    'money' => $to_money,
                    'time' => $riqi,
                    'user_name' => to_user_name,
                    'user_id' => to_user_id,
                    'zcity' => $to_city_id,
                    'type' => 21,
                    's_and_z' => 1,
                    'beizhu' => $beizhu,
                    'dq_money' => $new_to_user_money,
                    'dq_money_dj' => $to_user_money_dj,
                    'dq_jifen' => $to_duihuanjifen,
                    'dq_jifen_dj' => $to_dongjiejifen,
                );
                $this->moneylog_mod->add($addmlog1);


                $add_jia = array(
                    'money' => $new_to_user_money,
                );
                $this->my_money_mod->edit('user_id=' . $to_user_id, $add_jia);
                $add_jian = array(
                    'money' => $new_user_money,
                );
                $this->my_money_mod->edit('user_id=' . $user_id, $add_jian);

                $this->show_message('zhuanzhangchenggong');
                return;
            }
        } else//检测提交 否则
        {//检测提交 开始
            header("Location: index.php?app=my_money");
            return;
        }//检测提交 结束
    }

//转账积分

    function jifento_user()
    {

//$url=$_SERVER['HTTP_HOST'];//获得当前网址
        /*echo $url;*/
        $this->_city_mod =& m('city');
//$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
        //$zjf=$row_city['zjf'];
        $cityrow = $this->_city_mod->get_cityrow();
        $zjf = $cityrow['zjf'];


        $to_user = trim($_POST['to_user']);
        $to_jifen = (float)trim($_POST['to_jifen']);
        $user_id = $this->visitor->get('user_id');
        $riqi = date('Y-m-d H:i:s');
        if ($_POST)//检测有提交
        {//检测有提交
//            if (preg_match("/[^0.-9]/", $to_jifen)) {
//                $this->show_warning('cuowu_nishurudebushishuzilei');
//                return;
//            }

            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $user_city = $user_row['city'];//当前用户的city
            $to_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_name='$to_user'");
            $to_user_id = $to_row['user_id'];
            $to_city_id = $to_row['city'];//转给用户的city
            $to_user_name = $to_row['user_name'];//转给的用户
            $to_user_jifen = $to_row['duihuanjifen'];
            $to_user_dongjiejifen = $to_row['dongjiejifen'];
            $to_user_money = $to_row['money'];
            $to_user_money_dj = $to_row['money_dj'];


            if ($zjf == 'no') {
                if ($user_city != $to_city_id) {
                    $this->show_warning('cuowu_mubiaoyonghubushibenzhanhuiyuan');
                    return;
                }
            }
            if ($to_user_id == $user_id) {
                $this->show_warning('cuowu_bunenggeizijizhuanzhang');
                return;
            }

            if (empty($to_user_id)) {
                $this->show_warning('cuowu_mubiaoyonghubucunzai');
                return;
            }


            $user_row = $this->my_money_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            $user_jifen = $user_row['duihuanjifen'];//当前用户
            $user_dongjiejifen = $user_row['dongjiejifen'];//当前用户
            $user_money = $user_row['money'];//当前用户
            $user_money_dj = $user_row['money_dj'];//当前用户
            $suoding_jifen = $user_row['suoding_jifen'];
            $keyong_jifen = $user_jifen - $suoding_jifen;
            $user_zf_pass = $user_row['zf_pass'];
            $user_mibao_id = $user_row['mibao_id'];
            if ($to_jifen <= 0) {
                $this->show_warning('zhuanzhangjifenbunengxiaoyuling');
                return;
            }

            if ($keyong_jifen < $to_jifen) {
                $this->show_warning('cuowu_nidejifenbuzu');
                return;
            }

            if (empty($user_mibao_id)) {
                $zf_pass = md5(trim($_POST['zf_pass']));
                if ($user_zf_pass != $zf_pass) {
                    $this->show_warning('cuowu_zhifumimayanzhengshibai');
                    return;
                }
            } else {
//读取密保卡资料
                $user_zimuz1 = trim($_POST['user_zimuz1']);
                $user_zimuz2 = trim($_POST['user_zimuz2']);
                $user_zimuz3 = trim($_POST['user_zimuz3']);
                $user_shuzi1 = trim($_POST['user_shuzi1']);
                $user_shuzi2 = trim($_POST['user_shuzi2']);
                $user_shuzi3 = trim($_POST['user_shuzi3']);
                if (empty($user_shuzi1) or empty($user_shuzi2) or empty($user_shuzi3)) {
                    $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                    return;
                }
                $mibao_row = $this->my_mibao_mod->getRow("select * from " . DB_PREFIX . "my_mibao where user_id='$user_id'");
                $mibao_shuzi1 = $mibao_row[$user_zimuz1];
                $mibao_shuzi2 = $mibao_row[$user_zimuz2];
                $mibao_shuzi3 = $mibao_row[$user_zimuz3];

                if ($user_shuzi1 != $mibao_shuzi1 or $user_shuzi2 != $mibao_shuzi2 or $user_shuzi3 != $mibao_shuzi3) { //检测密保相符 开始
                    echo Lang::get('money_banben');
                    $this->show_warning('cuowu_dongtaimimayanzhengshibai');
                    return;
                } //检测密保 否则 结束
            }


            $order_id = date('Ymd-His', time()) . '-' . $to_jifen;
            if ($user_jifen < $to_jifen) {
                $this->show_warning('cuowu_zhanghuyuebuzu');
                return;
            } else {

                $new_user_jifen = $user_jifen - $to_jifen;
                $new_to_user_jifen = $to_user_jifen + $to_jifen;
                //添加日志
                //转出记录
                $log_text = $this->visitor->get('user_name') . Lang::get('gei') . $to_user . Lang::get('zhuanchujifen') . $to_jifen . Lang::get('jifen');
                $add_mymoneylog = array(
                    'user_id' => $user_id,
                    'user_name' => $this->visitor->get('user_name'),
                    'buyer_name' => $this->visitor->get('user_name'),
                    'seller_name' => $to_user_name,
                    'order_sn ' => $order_id,
                    'add_time' => time(),
                    'leixing' => 21,
                    'duihuanjifen' => '-' . $to_jifen,
                    //'dongjiejifen'=>'-'.$to_jifen,
                    'log_text' => $log_text,
                    'caozuo' => 50,
                    's_and_z' => 2,
                    'type' => 16,
                    'riqi' => $riqi,
                    'city' => $user_city,
                    'dq_money' => $user_money,
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $new_user_jifen,
                    'dq_jifen_dj' => $user_dongjiejifen,
                );
                $this->my_moneylog_mod->add($add_mymoneylog);

                //添加moneylog日志
//$beizhu =$this->visitor->get('user_name').Lang::get('gei').$to_user.Lang::get('zhuanchujifen').$to_jifen.Lang::get('jifen');
                $beizhu = Lang::get('zhuanruren') . $to_user;
                $addmlog1 = array(
                    'jifen' => '-' . $to_jifen,
                    'time' => $riqi,
                    'user_name' => $this->visitor->get('user_name'),
                    'user_id' => $this->visitor->get('user_id'),
                    'zcity' => $user_city,
                    'type' => 22,
                    's_and_z' => 2,
                    'beizhu' => $beizhu,
                    'dq_money' => $user_money,
                    'dq_money_dj' => $user_money_dj,
                    'dq_jifen' => $new_user_jifen,
                    'dq_jifen_dj' => $user_dongjiejifen,
                );
                $this->moneylog_mod->add($addmlog1);


                //转入记录
                $log_text_to = $this->visitor->get('user_name') . Lang::get('gei') . $to_user_name . Lang::get('zhuanrujifen') . $to_jifen . Lang::get('jifen');
                $add_mymoneylog_to = array(
                    'user_id' => $to_user_id,
                    'user_name' => $to_user_name,
                    'order_sn ' => $order_id,
                    'buyer_name' => $this->visitor->get('to_user_name'),
                    'seller_name' => $this->visitor->get('user_name'),
                    'add_time' => time(),
                    'leixing' => 11,
                    'duihuanjifen' => '+' . $to_jifen,
                    //'dongjiejifen'=>'-'.$to_jifen,
                    'log_text' => $log_text_to,
                    'caozuo' => 50,
                    's_and_z' => 1,
                    'riqi' => $riqi,
                    'type' => 18,
                    'city' => $to_city_id,
                    'dq_money' => $to_user_money,
                    'dq_money_dj' => $to_user_money_dj,
                    'dq_jifen' => $new_to_user_jifen,
                    'dq_jifen_dj' => $to_user_dongjiejifen,
                );
                $this->my_moneylog_mod->add($add_mymoneylog_to);
                //添加moneylog日志
                //$beizhu =$this->visitor->get('user_name').Lang::get('gei').$to_user_name.Lang::get('zhuanrujifen').$to_jifen.Lang::get('jifen');
                $beizhu = Lang::get('zhuanchuren') . $this->visitor->get('user_name');
                $addmlog = array(
                    'jifen' => $to_jifen,
                    'time' => $riqi,
                    'user_name' => $to_user_name,
                    'user_id' => $to_user_id,
                    'zcity' => $to_city_id,
                    'type' => 23,
                    's_and_z' => 1,
                    'beizhu' => $beizhu,
                    'dq_money' => $to_user_money,
                    'dq_money_dj' => $to_user_money_dj,
                    'dq_jifen' => $new_to_user_jifen,
                    'dq_jifen_dj' => $to_user_dongjiejifen,
                );
                $this->moneylog_mod->add($addmlog);


                $add_jia = array(
                    'duihuanjifen' => $new_to_user_jifen,
                );
                $this->my_money_mod->edit('user_id=' . $to_user_id, $add_jia);
                $add_jian = array(
                    'duihuanjifen' => $new_user_jifen,
                );
                $this->my_money_mod->edit('user_id=' . $user_id, $add_jian);

                $this->show_message('zhuanzhangchenggong');
                return;
            }
        } else//检测提交 否则
        {//检测提交 开始
            header("Location: index.php?app=my_money");
            return;
        }//检测提交 结束
    }

    function duihuan_delete()
    {

        $this->my_jifen_mod =& m('my_jifen');
        $id = intval($_GET['id']);//供货id
        $sql = "delete from " . DB_PREFIX . "my_jifen where id = '$id'";
        $this->my_jifen_mod->db->query($sql);
        $this->show_message('delete', 'back_list', 'index.php?app=my_money&act=duihuan_jilu');
    }

}

?>
