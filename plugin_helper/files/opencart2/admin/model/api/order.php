<?php

class ModelApiOrder extends Model
{

   public function getProcessingOrder(){
        $sql = sprintf("select *  from global_data.`orders`  where order_status_id=%d ",1);
        $result = $this->db->query($sql);
        $ordes=[];
        if ( $result->rows){
           foreach($result->rows as $order){
           $sql = sprintf("select *  from global_data.`order_products`  where order_id=%d ", $order["id"]);
           $products_query = $this->db->query($sql);
           $products=[];
           foreach($products_query->rows as $product) {

            $products[]=[
                "product_id"=> $product["product_id"],
                "name"=> $product["name"],
                "model"=> $product["model"],
                "quantity"=> $product["quantity"],
                "color"=> $product["color"],
                "size"=> $product["size"],
                "price"=> $product["price"],
                "total"=> $product["total"],
                "tax"=> $product["tax"],
                "reward"=> $product["reward"],
            ];

           }
            $order["products"]=$products;
            $ordes[]=$order;
           }

        }
        return $ordes;
    }

}
