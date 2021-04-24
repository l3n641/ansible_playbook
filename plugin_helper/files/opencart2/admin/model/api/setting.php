<?php
include_once(DIR_APPLICATION . "model/setting/setting.php");

class ModelApiSetting extends ModelSettingSetting
{
    /**
     * 更新网站配置
     */
    public function updateSiteConfig( $key,$value,$code="config", $store_id=0)
    {
        $store_id=intval($store_id);
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" .$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");

    }

}
