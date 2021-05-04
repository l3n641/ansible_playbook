<?php
require_once(dirname(__FILE__) . '/common.php');

class ControllerApiOrder extends CommonController
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('api/order');
        $this->response->addHeader('Content-Type: application/json');

    }

    public function processing(){
           $data= $this->model_api_order->getProcessingOrder();
           echo json_encode($data);

    }

    public function status(){

        $content = file_get_contents('php://input');

        $post_data = (array)json_decode($content, true); //解码为数组


        $order_info= $this->model_api_order->updateOrderStatus($post_data["order_id"],$post_data["order_sn"],$post_data["order_status_id"] );

        $json=[];
        if ($order_info) {
            $json= ["code"=>200,"message"=>"修改状态成功"];
        } else {
            $json= ["code"=>400,"message"=>"修改状态失败"];
        }


        $this->response->setOutput(json_encode($json));


    }
}