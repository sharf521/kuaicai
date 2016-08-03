<?php

class YouhuilistModel extends BaseModel
{
    var $table  = 'youhuilist';
    var $prikey = 'id';
    var $_name  = 'youhuilist';
    var $_relation  = array(
        // 一种优惠券有多个优惠券编号
        'has_couponsn' => array(
            'model'         => 'couponsn',
            'type'          => HAS_MANY,
            'foreign_key'   => 'coupon_id',
            'dependent'     => true
        ),
        // 一种优惠券只能属于一个店铺
        'belong_to_store' => array(
            'model'         => 'store',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'store_id',
            'reverse'       => 'has_coupon',    
        ),
    );
}

?>
