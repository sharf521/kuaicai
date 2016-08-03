<?php

/* 配送方式 shipping */
class ShippingsModel extends BaseModel
{
    var $table  = 'shippings';
    var $prikey = 'shipping_id';
    var $_name  = 'shippings';
    var $_autov = array(
        'shipping_name' =>  array(
            'required'  => true,
            'filter'    => 'trim',
        ),
        'cod_regions'   =>  array(
            'filter'    => 'trim',
        ),
        'enabled'       =>  array(
            'filter'    => 'intval',
        ),
    );

    var $_relation  =   array(
        // 一个配送方式只能属于一个店铺
        'belongs_to_store' => array(
            'model'         => 'store',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'store_id',
            'reverse'       => 'has_shipping',
        ),
    );
}

?>