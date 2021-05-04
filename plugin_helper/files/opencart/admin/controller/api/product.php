<?php
require_once(dirname(__FILE__) . '/common.php');
require_once(DIR_APPLICATION . "model/api/structure/product_option.php");
require_once(DIR_APPLICATION . "model/api/structure/product.php");

class ControllerApiProduct extends CommonController
{

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('api/product');
        $this->load->model('api/category');
        $this->load->model('api/product_option');

    }


    public function add()
    {
        $content = file_get_contents('php://input');

        $post_data = (array)json_decode($content, true); //解码为数组

        $category_full_ids = $this->model_api_category->addCategoryByFullName($post_data["category_full_name"]);

        $goods_list = $post_data["goods_list"];
        $color_options = [];
        $color_set = [];
        $size_set = [];
        $size_options = [];

        foreach ($goods_list as $goods) {
            if ($goods["color"] && !in_array($goods["color"], $color_set)) {
                $color_set[] = $goods["color"];
                $color_options[] = ["value" => $goods["color"], "img" => $goods["goods_image"]];
            }
            if ($goods["size"] && !in_array($goods["size"], $size_set)) {
                $size_set[] = $goods["size"];
                $size_options[] = ["value" => $goods["size"], "img" => $goods["goods_image"],];
            }
        }

        $main_picture = $this->model_api_product->downloadImage($post_data["main_picture"], SHARE_IMAGES);

        $product = new Product($post_data["name"], $post_data["description"], $post_data["name"], $post_data["spu"], $post_data["spu"], $post_data["price"], $post_data["quantity"],
            $post_data["weight"], $main_picture, $category_full_ids);

        $down_images = [];

        if ($color_options) {
            $attribute_id = $this->model_api_product_option->addProductAttribute("color");
            $color = new ProductOptionSelectorStructure("color", $attribute_id);
            foreach ($color_options as $option) {
                if ($option["img"]) {
                    $image_info = $this->model_api_product->getImageLocalPath($option["img"], "catalog/share_images/");

                    $image = $image_info["database_path"];
                    $down_images[] = array($option["img"], $image_info["absolute_path"]);

                } else {
                    $image = "";
                }

                $attribute_value_id = $this->model_api_product_option->addProductAttributeValue($attribute_id, $option["value"], $image);
                $color->addOptionValue($attribute_value_id, 100);

            }
            $product->addSelectOption($color);

        }

        if ($size_options) {
            $attribute_id = $this->model_api_product_option->addProductAttribute("size");
            $size = new ProductOptionSelectorStructure("size", $attribute_id);

            foreach ($size_options as $option) {
                if (empty($color_options) && $option["img"]) {
                    $image_info = $this->model_api_product->getImageLocalPath($option["img"], "catalog/share_images/");

                    $image = $image_info["database_path"];
                    $down_images[] = array($option["img"], $image_info["absolute_path"]);
                } else {
                    $image = "";
                }

                $attribute_value_id = $this->model_api_product_option->addProductAttributeValue($attribute_id, $option["value"], $image);
                $size->addOptionValue($attribute_value_id, 100);

            }
            $product->addSelectOption($size);

        }

        foreach ($post_data["extra_images"] as $img) {
            $image_info = $this->model_api_product->getImageLocalPath($img, "catalog/share_images/");

            $image = $image_info["database_path"];
            $down_images[] = array($option["img"], $image_info["absolute_path"]);
            $product->addImg($image);

        }

        $this->model_api_product->batch_down_image($down_images,BATCH_DOWNLOAD_ERROR_LOG_PATH);

        $product_id = $this->model_api_product->addProductApi($product,$post_data["product_id"]);
        if ($product_id) {
            echo  json_encode(["code"=>200,"message"=>"上传商品成功","data"=>["product_id"=>$product_id]]);
        }else{
            echo json_encode(["code"=>400,"message"=>"上传商品失败"]);

        }
    }
}
