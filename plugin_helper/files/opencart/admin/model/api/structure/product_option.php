<?php

class SelectStructure implements ArrayAccess
{
    public $type = 1;
    public $data = array();
    public $options = array();

    public function __construct($name, $sort_order, $lang = 1)
    {
        $this->name = $name;
        $this->sort_order = $sort_order;
        $this->lang = $lang;

        $this->data = [
            "type" => $this->type,
            "sort_order" => $sort_order,
            "option_description" => [$lang => ["name" => $name]],
            "option_value" => $this->options,

        ];
    }


    public function offsetSet($key, $value)
    {

        $this->data[$key] = $value;

    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($key)
    {
        if ($key === "option_value") {
            return $this->options;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function addOption($option)
    {
        array_push($this->options, $option);
    }
}

class OptionValueStructure implements ArrayAccess
{
    public $data, $lang;

    public function __construct($name, $image = "", $sort_order = 1, $option_value_id = null, $lang = 1)
    {
        $this->data = [
            "name" => $name,
            "image" => $image,
            "sort_order" => $sort_order,
            "option_value_id" => $option_value_id,
        ];
        $this->lang = $lang;

    }

    public function offsetSet($key, $value)
    {

        $this->data[$key] = $value;

    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($key)
    {
        if ($key === "option_value_description") {
            return [$this->lang => ["name" => $this->data["name"]]];
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

}