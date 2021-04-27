<?php

class ModelApiOrder extends Model
{

   public function getProcessingOrder(){
        $sql = sprintf("select *  from global_data.`orders`  where order_status_id=%d ",1);
        $result = $this->db->query($sql);

        return $result->rows;
    }

}
