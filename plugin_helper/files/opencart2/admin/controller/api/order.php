<?php
require_once(dirname(__FILE__) . '/common.php');
require_once(DIR_APPLICATION . "custom_config.php");

class ControllerApiOrder extends Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('api/order');

    }

    public function processing(){
           $data= $this->model_api_order->getProcessingOrder();
           echo json_encode($data);

    }
}