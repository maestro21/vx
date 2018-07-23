<?php
abstract class masterdb extends mastercache {
	
	/** default field for a field type in table **/
	const FIELDTYPE = 1;
	

	/**
	 *	Default method for class data listing
	 *	@return array|false;
	 */
  	public function items() {
		/* initialize local variables */  
		$p	= page();
		$pp = $this->perpage;		
		$where = [];

		/* Apply search **/
		if(search()){
			foreach($this->fields() as $k => $field){
				if($field['search']) {
					$where[] = qEq($k,search());
				}
			}		
		}
		/** Count pages */
		$this->pageCount = ceil(q($this->cl())->count($where)->run() / $pp);	
		
		/* Initialize query */
		$q = q($this->cl())
					->select()
					->where($where)
					->limit($p * $pp, $pp);

		/** Apply sort filter **/
		$filter = filter('sort_' . $this->cl());
		if($filter && isset($this->fields[key($filter)])) {
			$q->order($filter);
		}

		/** Execute query and return result */
		return $q->run();
  	}


    /**
     * New format for tables
     *
     *
     * @return array tables
     */

  	function tables()
    {
        return [
            $this->cl() => [
                'fields' => $this->fields(),
            ]
        ];
    }


    /** Opens form for adding new element **/
	public function add($data = NULL) {
		$this->tpl = 'addedit';  
		return array('data' => $data, 'fields' =>$this->fields());
	}	
	
	/** Retrieves data of a single element for edit **/
    public function edit($id = NULL) {
		$id = v($id, $this->id());
		return $this->view($id);
    }	
	
	/** Retrieves data of a single element for view **/
    public function view($id = NULL) {
		$id = v($id, $this->id());
        return q($this)->get($id)->run();
    }     

     
    /** Save element **/
    public function save($row = null) {  //die();
        $this->parse = P_JSON;
        $this->saveFiles();// print_r($this->post);
        if(!$row) $row = post('form');
		$ret = $this->saveDB($row);
		return $ret;
	}


	/**
	 * Save data to database
	 */
	function saveDB($data = null) { 
		/** Preprocess data */
		foreach($this->fields() as $k => $v) {
            if(!is_array($v)) continue;
            $dbtype = (isset($v['dbtype']) ? $v['dbtype'] : getWidgetDefaultDBType($v[0]));
            if($dbtype == DB_VOID) continue;

			$data[$k] = sqlFormat([
			    'dbtype'    => $dbtype,
                'widget'    => $v[0],
                'data'      => @$data[$k],
                'quote'     => @$v['quote'],
            ]);
		}

		/**
		 * Insert new element if ID is not present
		 */ 
		if($this->id() < 1) {			
			q($this)->insert($data)->run();
			$this->id(DBinsertId());

			return [
				'redirect' => BASE_URL . $this->cl . '/edit/' . $this->id(), 
				'id' => $this->id(), 
				'status' => 'ok'
			];
		}	

		/**
		 * Update if element exists
		 */
		q($this)->update($data, $this->id());
		return [
			'id' => $this->id(),
			'message' => T('saved'), 
			'status' => 'ok'
		];
	}
	

     
    /** Delete element **/
    public function del($id = NULL) {		
		$this->parse = P_JSON;		
		$id =  v($id, $this->id());
		q($this)->delete($id)->run();

		return [
			'redirect' => 'self', 
			'status' => 'ok', 
			'timeout' => 1
		];
    }
	
         
    /** Class installation method **/ 
    public function install() { install($this->tables()); }
	

	/** Class uninstallation method **/ 
    public function uninstall() { 
		uninstall(array_reverse($this->tables()));
		cacherm($this->cl());
	}	
   
}
