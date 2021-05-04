<?php
require_once(DIR_APPLICATION . "custom_config.php");

class CommonController extends Controller
{
public function __construct($registry)
{
    parent::__construct($registry);

    if (empty($_SERVER["HTTP_TIMESTAMP"]) || empty($_SERVER["HTTP_SECRET"])){
        $response= ["code"=>401,"message"=>"没有授权信息" ];
        echo    json_encode($response);
        exit();
    }


    if ($_SERVER["HTTP_TIMESTAMP"]<time()-3600*12 ){
        $response= ["code"=>401,"message"=>"授权过期" ];
        echo   json_encode($response);
        exit();

    }

    $hash=md5(SECRET_KEY.$_SERVER["HTTP_TIMESTAMP"]);
    if ($hash !== $_SERVER["HTTP_SECRET"]){
        $response=  ["code"=>401,"message"=>"密钥错误" ];
        echo  json_encode($response);
        exit();

        }





}
}