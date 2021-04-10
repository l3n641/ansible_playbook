<?php
include_once(DIR_APPLICATION . "model/catalog/option.php");

class ModelApiProductOption extends ModelCatalogOption
{
    public function addOptionSelector($selector)
    {

        return parent::addOption($selector);
    }

    public function addProductAttribute($name, $sort_order = 1, $language_id = 1)
    {
        $type = "select";
        $this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = '" . $this->db->escape($type) . "', sort_order = '" . (int)$sort_order . "'");

        $option_id = $this->db->getLastId();

        $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($name) . "'");
        return $option_id;
    }

    public function addProductAttributeValue($attribute_id, $name, $image = "", $sort_order = 0, $language_id = 1)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$attribute_id . "', image = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$sort_order . "'");

        $option_value_id = $this->db->getLastId();

        $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '" . (int)$language_id . "', option_id = '" . (int)$attribute_id . "', name = '" . $this->db->escape($name) . "'");

        return $option_value_id;
    }

}

