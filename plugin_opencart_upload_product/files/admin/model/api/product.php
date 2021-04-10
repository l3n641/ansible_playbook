<?php
include_once(DIR_APPLICATION . "model/catalog/product.php");

class ModelApiProduct extends ModelCatalogProduct
{

    public function addProductApi($product,$remote_product_id)
    {
        //查询要上传的产品id 是否存在再上传日志里
        $this->createTableUploadProductLog();
        $local_product_id=$this->getUploadProductLog($remote_product_id);
        if ($local_product_id){
            return $local_product_id;
        }

        $product_id = parent::addProduct($product);

        if ($product_id) {
            $this->createTableCategoryAmount();
            $sub_category = $product["product_category"][count($product["product_category"]) - 1];
            $this->updateCategoryAmount($sub_category);
            $this->addUploadProductLog($remote_product_id,$product_id);
        }
        return $product_id;
    }

    public static function downloadImage($url, $path)
    {
        $dir_name = strtolower(substr(md5($url), 0, 4));
        $abs_path = DIR_IMAGE . $path . $dir_name;

        if (!file_exists($abs_path)) {
            mkdir($abs_path, 0777, true);
        }
        $file_name = pathinfo(parse_url($url)["path"])["basename"];
        $abs_path = $abs_path . '/' . $file_name;
        if (!file_exists($abs_path)) {
            $img = self::download($url);
            $downloaded_file = fopen($abs_path, 'w');
            fwrite($downloaded_file, $img);
            fclose($downloaded_file);
        }

        return $path . "$dir_name/" . $file_name;
    }

    private static function download($url)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_HEADER, false);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.1; SV1; InfoPath.2)");
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5);

        $img = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $img;
    }

    private function createTableCategoryAmount()
    {

        $sql = sprintf("SELECT table_name FROM information_schema.TABLES WHERE table_name ='%scategory_amount' and TABLE_SCHEMA='%s'", DB_PREFIX, DB_DATABASE);

        $result = $this->db->query($sql);
        if (!$result->rows) {
            $sql = sprintf("CREATE TABLE IF NOT EXISTS `%scategory_amount`(
            `category_id` INT UNSIGNED ,
            `amount` INT NOT NULL ,
            PRIMARY KEY ( `category_id` )
            )ENGINE=MyISAM DEFAULT CHARSET=utf8;", DB_PREFIX);
            $this->db->query($sql);
        }
    }

    private function createTableUploadProductLog()
    {

        $sql = sprintf("SELECT table_name FROM information_schema.TABLES WHERE table_name ='%supload_product_log' and TABLE_SCHEMA='%s'", DB_PREFIX, DB_DATABASE);
        $result = $this->db->query($sql);
        if (!$result->rows) {
            $sql = sprintf("CREATE TABLE IF NOT EXISTS `%supload_product_log`(
            `remote_product_id` INT UNSIGNED ,
            `local_product_id` INT UNSIGNED ,
            `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
            PRIMARY KEY ( `local_product_id` )
            )ENGINE=MyISAM DEFAULT CHARSET=utf8;", DB_PREFIX);
            $this->db->query($sql);
        }
    }

    private function updateCategoryAmount($category_id)
    {
        $lock_table = sprintf("lock table %scategory_amount write", DB_PREFIX);
        $this->db->query($lock_table);
        $sql = sprintf("select *  from %scategory_amount  where category_id=%d ", DB_PREFIX, $category_id);
        $result = $this->db->query($sql);
        if ($result->rows) {
            $amount = $result->rows[0]["amount"] + 1;
            $update_sql = sprintf("update  %scategory_amount set amount=%d where category_id=%d ", DB_PREFIX, $amount, $category_id);
            $this->db->query($update_sql);
        } else {
            $insert_sql = sprintf("insert  into  %scategory_amount (category_id, amount) VALUES (%d,%d)", DB_PREFIX, $category_id, 1);
            $this->db->query($insert_sql);

        }
        $this->db->query("unlock tables");

    }

    private function getUploadProductLog($remote_product_id){
        $sql = sprintf("select *  from %supload_product_log  where remote_product_id=%d ", DB_PREFIX, $remote_product_id);
        $result = $this->db->query($sql);
        if ($result->rows) {
            $data=$result->row;
            return $data["local_product_id"];
        }
        return false;
    }

    private function addUploadProductLog($remote_product_id,$local_product_id)
    {
        $lock_table = sprintf("lock table %supload_product_log write", DB_PREFIX);
        $this->db->query($lock_table);
        $insert_sql = sprintf("insert  into  %supload_product_log (remote_product_id, local_product_id) VALUES (%d,%d)", DB_PREFIX, $remote_product_id, $local_product_id);
        $this->db->query($insert_sql);
        $this->db->query("unlock tables");
    }

    public function add_to_mongodb($product_id, $product_data, $host, $user, $password, $db = DB_DATABASE, $port = 27017, $auth_db = "admin")
    {
        $product_data["product_id"] = $product_id;
        $uri = sprintf("mongodb://%s:%s@%s:%d/%s", $user, $password, $host, $port, $auth_db);
        $manager = new MongoDB\Driver\Manager($uri);
        $bulkWrite = new\MongoDB\Driver\BulkWrite(['ordered' => true]);
        $dbCollectionName = "$db.product";
        $bulkWrite->insert($product_data);
        $manager->executeBulkWrite($dbCollectionName, $bulkWrite);
    }


}
