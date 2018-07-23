<?php 
/**
 * array handler class
 */
class arr {
    
    var $ai = 0;

    var $data = [];

    function __construct($data = []) {
        return $this->put($data);
    }

    function put($data = []) {
        if(is_array($data)) $this->data = $data;
        return $this;
    }

    /**
     * $a->set(i)
     */
    function set($row, $key = null) {
        if(key == NULL) $key = $this->ai(1);
        $this->data[$key] = $row;
        return $this;
    }


    function get($key = null) {
        if($key == null) $key = $this->ai();
        return $this->data[$key];        
    }

    function ai($inc = 0) {
        if($inc != 0) $this->ai += $inc;
        return $this->ai;
    } 

    function size() {
        return count($this->data);
    }

}

function arr($data = []) {
    return new arr($data);
}