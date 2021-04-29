<?php
include_once(DIR_APPLICATION . "model/sale/order.php");

class ModelApiOrder extends ModelSaleOrder
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


    public function getOrderBysn($order_sn){
        $sql = sprintf("select *  from global_data.`orders`  where order_sn='%s' limit 1 ",$order_sn);
		$result = $this->db->query($sql);

        return $result->rows;
    }
	public function updateGlobalOrderStatus($order_sn,$order_status_id){
        $sql = sprintf("UPDATE global_data.`orders` SET order_status_id = %d , date_modified = NOW()  where order_sn='%s' limit 1 ",$order_status_id,$order_sn);
		$this->db->query($sql);
    }

    public function updateOrderStatus($order_id,$order_sn, $order_status_id, $comment = '', $notify = false, $override = false) {
		$order_id=intval($order_id);
		$order_info = $this->getOrder($order_id);
        $global_order=$this->getOrderBysn($order_sn);
		if ($order_info && $global_order && $global_order[0]["order_id"]==$order_id&& $global_order[0]["store_url"]==$order_info["store_url"]) {


				// Add commission if sale is linked to affiliate referral.
		if ($order_info['affiliate_id'] && $this->config->get('config_affiliate_auto')) {
					$this->load->model('account/customer');
			if (!$this->model_account_customer->getTotalTransactionsByOrderId($order_id)) {
						$this->model_account_customer->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
					}
				}


			// Update the DB with the new statuses
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

			$this->updateGlobalOrderStatus($order_sn,$order_status_id);

			// If old order status is the processing or complete status but new status is not then commence restock, and remove coupon, voucher and reward history
			if (in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && !in_array($order_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
				// Restock
				$order_products = $this->getOrderProducts($order_id);

				foreach($order_products as $order_product) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

					$order_options = $this->getOrderOptions($order_id, $order_product['order_product_id']);

					foreach ($order_options as $order_option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}

				// Remove coupon, vouchers and reward points history
				$order_totals = $this->getOrderTotals($order_id);

				foreach ($order_totals as $order_total) {
					$this->load->model('extension/total/' . $order_total['code']);

					if (property_exists($this->{'model_extension_total_' . $order_total['code']}, 'unconfirm')) {
						$this->{'model_extension_total_' . $order_total['code']}->unconfirm($order_id);
					}
				}

				// Remove commission if sale is linked to affiliate referral.
				if ($order_info['affiliate_id']) {
					$this->load->model('account/customer');

					$this->model_account_customer->deleteTransactionByOrderId($order_id);
				}
			}

			return true;


		}
	}

}
