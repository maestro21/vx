<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class
 *
 * @author MAECTPO
 */
abstract class mastercache extends basecache{

 
    /**
     * Key properties that are changed a lot and thus require getter\setter
     * ai, id, json, parse
     */


    /*
     * Autoincrement
     * @var int
     */
    public $ai = 0;
    function ai($ai = null) {
        if($ai != null) $this->ai = $ai;
        return $this->ai;
    }


    /**
     * Defines how many items should be shown per page
     * @var int
     */
    public $perpage = 20;


    /**
     * Data
     */
    public $data;
    function data($data = null) {
        if($data != null) {
            $this->data = $data;
            $this->ai = count($data);
        }
        return $this->data;
    }

      

    /**
	 *	Default method for class data listing
	 *	@return array() or FALSE;
	 */
  	public function items() {
		return $this->cache();
  	}


  	public function reset() {
        $this->data = [];
        $this->ai = 0;
    }

    /**
     * set and save element
     */
    public function set($key, $row) {
        if(empty($this->data)) {
            $this->cache();
        }
        $this->data[$key] = $row; 
        $this->cache($this->data);
    }

    public function get($key) {
        $this->cache();
        if($this->data[$key]) return $this->data[$key];
        return NULL;
    }

    public function find($k,$v) {
        if(is_array($this->data())) {
            foreach ($this->data() as $id => $row) {
                if (isset($row[$k]) && $row[$k] == $v) {
                    $row['id'] = $id;
                    return $row;
                }
            }
        }
        return NULL;
    }

    /**
     * Add data to array
     */
    public function push($row) {
        $data = $this->cache();
        $this->ai++;
        $this->id($this->ai);
        $row['id'] = $this->id();
        $data[$this->id()] = $row;
        $this->cache($data);
    }


    /** Save element **/
    public function save($row = null) {
		$this->parse = P_JSON;
        $data = $this->cache();
        if($row == null) $row = post('form');
        if($this->id() < 1) {
            $this->ai++;
            $this->id($this->ai);
        }
        $this->saveFiles();
        $row['id'] = $this->id();
        $data[$this->id()] = $row;
        $this->cache($data);

        return [
            'redirect' => BASE_URL . $this->cl() . '/edit/' . $this->id(),
            'id' => $this->id(),
            'status' => 'ok',
            'message' => 'saved'
        ];
	}

   /** Retrieves data of a single element for edit **/
    public function edit($id = NULL) {
        $id = V($id, $this->id());
		return $this->add($this->view($id));
    }

	/** Retrieves data of a single element for view **/
    public function view($id = NULL) {
        $id = V($id, $this->id());
		$data = $this->cache();
        return $data[$id];
    }

    /* Opens form for adding new element **/
	public function add($data = NULL) {
		$this->tpl = 'addedit';
		return array('data' => $data, 'fields' =>$this->fields());
	}

    /** Delete element **/
    public function del($id = NULL) {
		$this->parse = P_JSON;
        $id = v($id, $this->id());
		$data = $this->cache();
        unset($data[$id]);
        $this->cache($data);
		return json_encode(array('redirect' => 'self', 'status' => 'ok', 'timeout' => 1));
    }

   

    public function clear() {
        $this->data = [];
        $this->ai = 0;
        cacherm($this->cl(), $this->json());
    }
  

    public function cache($data = null, $name = '') {
        if(empty($name)) $name = $this->cl();
        // save
		if(!empty($data)) {
            $data = [
                'ai' => $this->ai,
                'data' => $data,
            ]; 
            cache($name, $data, $this->json, $this->cachepath);
        }
        // load
        $data =  cache($name, null, $this->json, $this->cachepath);
        if(!isset($data['ai'])) {
            $data = [
                'ai' => @max(array_keys($data)),
                'data' => $data,
            ];
            cache($name, $data, $this->json, $this->cachepath);
        }
        $this->data = $data['data'];
        $this->ai = (int)@$data['ai'];
        return $this->data();
	}
	
}


