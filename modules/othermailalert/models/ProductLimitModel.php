<?php

/**
 * Created by PhpStorm.
 * User: mdepe
 * Date: 08/01/2016
 * Time: 09:40
 */
class ProductLimitModel extends ObjectModel
{

    public $id_otherMailProduct;
    public $id_product;
    public $threshold;
    public $email;


    public static  $definition = array(
        "table" => "otherMailProduct",
        "primary" => "id_otherMailProduct",
        "fields" => array(
            "id_product" => array("type" => self::TYPE_INT),
            "theshold" => array("type" => self::TYPE_INT),
            "email" => array("type" => self::TYPE_STRING),
        )
    );

}