<?php

class Product implements ArrayAccess
{
    public $lang = 1;
    public $product_description = array();        //general 信息
    public $product_option = array();        //option 信息
    public $product_image = array();        //image 信息

    public $data = [

        //data 页面信息
        "model" => "",
        "sku" => "",
        "upc" => "",
        "ean" => "",
        "jan" => "",
        "isbn" => "",
        "mpn" => "",
        "location" => 12,
        "price" => 3.3,
        "tax_class_id" => 0,
        "quantity" => 1,
        "minimum" => 1,
        "subtract" => 1,
        "stock_status_id" => 6,
        "shipping" => 1,
        "date_available" => "",
        "length" => 0.00000000,
        "width" => 0.00000000,
        "height" => 0.00000000,
        "length_class_id" => 1,
        "weight" => 0,
        "weight_class_id" => 1,
        "status" => 1,
        "sort_order" => 1,

        //link 页面信息
        "manufacturer" => "",
        "manufacturer_id" => 0,
        "category" => "",
        "product_category" => [],
        "filter" => "",
        "product_store" => [0],
        "download" => "",
        "related" => "",
        "option" => "",

        "image" => "",
    ];

    public function __construct($name, $description, $meta_title, $model, $sku, $price, $quantity, $weight, $image = "", $product_category = [],
                                $meta_description = "", $meta_keyword = "", $tag = "", $product_store = [0], $minimum = 1, $lang = 1)
    {
        $this->data['model'] = $model;
        $this->data['sku'] = $sku;
        $this->data['price'] = $price;
        $this->data['quantity'] = $quantity;
        $this->data['minimum'] = $minimum;
        $this->data['weight'] = $weight;
        $this->data['product_category'] = $product_category;
        $this->data['product_store'] = $product_store;
        $this->data['image'] = $image;
        $this->lang = $lang;
        $this->product_description = [
            "name" => $name,
            "description" => $description,
            "meta_title" => $meta_title,
            "meta_description" => $meta_description,
            "meta_keyword" => $meta_keyword,
            "tag" => $tag,
        ];
    }

    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function offsetExists($offset)
    {
        if ($offset === "product_option") {
            return !empty($this->product_option);
        }
        if ($offset === "product_image") {
            return !empty($this->product_image);
        }
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($key)
    {
        switch ($key) {
            case $key === "product_description":
                return $this->getProductDescription();

            case $key === "product_option":
                return $this->product_option;

            case $key === "product_image":
                return $this->product_image;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    private function getProductDescription()
    {
        return [$this->lang => $this->product_description];

    }

    public function addSelectOption(ProductOptionSelectorStructure $product_option)
    {
        array_push($this->product_option, $product_option);
    }

    public function addImg($image, $sort_order = 0)
    {
        array_push($this->product_image, ["image" => $image, "sort_order" => $sort_order]);
    }
}


class ProductOptionSelectorStructure implements ArrayAccess
{
    private $product_option_value = array();
    private $data;

    public function __construct($name, $option_id, $required = 1)
    {
        $this->data = [
            "name" => $name,
            "option_id" => $option_id,
            "type" => "select",
            "required" => $required,
        ];
    }

    public function addOptionValue($option_value_id, $quantity , $subtract = 1, $points_prefix = "+", $points = 0,
                                   $price_prefix = "+", $price = 0, $weight_prefix = "+", $weight = 0)
    {
        $data = [
            "option_value_id" => $option_value_id,
            "quantity" => $quantity,
            "subtract" => $subtract,
            "price_prefix" => $price_prefix,
            "price" => $price,
            "points_prefix" => $points_prefix,
            "points" => $points,
            "weight_prefix" => $weight_prefix,
            "weight" => $weight,
        ];
        array_push($this->product_option_value, $data);

    }

    public function offsetSet($key, $value)
    {

        $this->data[$key] = $value;

    }

    public function offsetExists($offset)
    {
        if ($offset === "product_option_value") {
            return !empty($this->product_option_value);
        }
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($key)
    {
        if ($key === "product_option_value") {
            return $this->product_option_value;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

}


