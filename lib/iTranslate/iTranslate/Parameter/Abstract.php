<?php

abstract class iTranslate_Parameter_Abstract implements ArrayAccess {

    protected $_key;
    protected $_value;

    public function __construct($key, $value) {
        if (!empty($key) && !empty($value)) {
            $this->_key = $key;
            $this->_value = $value;
        } else
            throw new Exception("Could not create parameters since either key or value is empty");
    }

    public function offsetGet($offset) {
        if ($this->offsetExists($offset))
            return $this->{"_" . $offset};
        return null;
    }

    public function offsetSet($offset, $value) {
        if ($this->offsetExists($offset)) {
            if($offset == "timeout") {
                $maxExec = ini_get("max_execution_time")-3;
                $value = $maxExec !== 0 && $value >= $maxExec ? $maxExec : $value;
            }
            $this->{"_" . $offset} = $value;
        }
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset))
            unset($this->{"_" . $offset});
    }

    public function offsetExists($offset) {
        return property_exists($this, "_" . $offset);
    }

    public function __toString() {
        return (string) $this->_key . "=" . $this->_value;
    }

}