<?php
require_once(dirname(__FILE__) . '/common.php');

class ControllerApiSetting extends CommonController
{

    public function save(){
        $content = file_get_contents('php://input');
        $this->load->model('api/setting');

        $post_data = (array)json_decode($content, true); //解码为数组
        if (empty($post_data) || empty($post_data["configs"])){
            echo json_encode(["code"=>400,"message"=>"参数错误"]);
            return false;
        }

        foreach ($post_data['configs'] as $config){
            $store_id= isset( $config["store_id"])?$config["store_id"]:0;
            $this->model_api_setting->updateSiteConfig($config['key'],$config['value'],$config["code"],$store_id);

        }
        echo json_encode(["code"=>200,"message"=>"修改成功"]);
    }
}