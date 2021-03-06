<?php
require_once(dirname(__FILE__) . '/../catalog/category.php');

class ModelApiCategory extends ModelCatalogCategory
{

    public function getCategoriesByParentId($parent_id = 0)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
    }

    public function getCategoriesByName($name, $parent_id = 0)
    {
        $name = addslashes($name);
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND cd.name = '$name' ORDER BY c.sort_order, LCASE(cd.name)");
        return $query->rows;
    }

    public function addCategoryByFullName($categories)
    {
        $parent_id = 0;
        $category_full_ids = [];
        foreach ($categories as $name) {
            $category = $this->getCategoriesByName($name, $parent_id);
            if (!$category) {
                $category_description = [
                    "name" => $name,
                    "description" => $name,
                    "meta_title" => $name,
                    "meta_description" => $name,
                    "meta_keyword" => $name,
                ];
                $category_descriptions = [1 => $category_description];
                $category_id = $this->addCategoryApi($parent_id, 0, 1, 1, $category_descriptions);
                $parent_id = $category_id;
            } else {
                $parent_id = $category[0]["category_id"];
            }
            $category_full_ids[] = $parent_id;
        }
        return $category_full_ids;
    }


    public function getCategoryRecursion($parent_id = 0)
    {
        $parent_category = $this->getCategoriesByParentId($parent_id);
        foreach ($parent_category as &$category) {
            $category['child_category'] = $this->getCategoryRecursion($category["category_id"]);
        }
        return $parent_category;
    }


    /**
     * @param $parent_id integer  ?????????id
     * @param $sort_order integer ??????
     * @param $status integer ???????????? 1-?????? 0-??????
     * @param $language_id integer ??????id
     * @param array $category_description array ????????????  [
     * "$language_id"=>[
     * "name" => "????????????",
     * "description" => "??????description",
     * "description" => "??????description",
     * "meta_title" => "??????meta_title",
     * "meta_description" => "??????meta_description",
     * "meta_keyword" => "??????meta_keyword",
     * ]
     * ]
     * @param $column integer Number of columns to use for the bottom 3 categories. Only works for the top parent categories. ?????????1
     * @param array $category_store array ?????????id,?????????[0]
     * @param array $category_seo_url array seo ?????? [["language_id"=>"keyword"],] ????????????
     * @param array $category_layout ?????????????????? ?????????[],
     * @param string $image string ???????????? ????????????
     * @param int $top integer Display in the top menu bar. Only works for the top parent categories. 1-??????,0-???
     * @param array $category_filter array ?????????????????? ,????????????
     * @return mixed
     */
    public function addCategoryApi($parent_id, $sort_order, $status, $language_id, array $category_description, $column = 1, array $category_store = [0],
                                   array $category_seo_url = [], array $category_layout = [], $image = "", $top = 1, array $category_filter = [])
    {
        $data = [
            "parent_id" => $parent_id,
            "column" => $column,
            "sort_order" => $sort_order,
            "status" => $status,
            "language_id" => $language_id,
            "category_description" => $category_description,
            "category_filter" => $category_filter,
            "category_store" => $category_store,
            "category_seo_url" => $category_seo_url,
            "category_layout" => $category_layout,
            "image" => $image,
            "top" => $top,
        ];
        return parent::addCategory($data);
    }


}