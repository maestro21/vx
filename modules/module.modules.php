<?php class modules extends mastercache  {
 
	private $systemModules = array('module', 'system', 'pages');


    function fields() {
		return
		[
            'name' 			=> [ WIDGET_TEXT, 'search' => TRUE ],
            'description' 	=> [ WIDGET_TEXTAREA, 'null' => TRUE  ],
            'status' 		=> [ NULL, 'dbtype' => DB_INT, 'null' => TRUE  ], // 0 - not installed, 1 - installed, 2 - active
		];	
	}

    function __construct()
    {
        return parent::__construct();
    }

    function extend() {
		$this->buttons = [
			'items' => [
				'reinstall'	=> 'reinstall'
			]
		];	
		$this->description = 'Core module for managing other modules';	
	}

    /**
     * Reinstall modules
     */
	function reinstall() {		
		$this->install();
		$modules = $this->modules();
		foreach($modules as $module) {
            if($module['status'] > 0 && $module != $this->cl()) M($module)->install();
		}
	}


    function install() {
        return;
    }


    function items() {
        return $this->modules();
    }
	
	function modules() {		
        $this->cache();
		$modules = scandir(CLASS_FOLDER);
		unset($modules[0]);
		unset($modules[1]);
		foreach($modules as $k => $module) {
			$module =  str_replace('module.','',str_replace('.php','', $module));
            $row = [
                'name' 			=> $module,
                'description' 	=> M($module)->description,
                'status' 		=> $this->status($module),
            ];
            $modules[$k] = $row;
		}
		
        $this->cache($modules);
		return $modules;
	}


    /**
     * Status getter/setter
     * @param null $module
     * @param null $status
     * @return int
     */
	function status($module = null, $status = null)    {
        $row = $this->find('name',$module);
        if($row){ 
           if ($status !== null) {
               $row['status'] = $status; 
               $this->set($row['id'], $row);
           }
           if(isset($row['status'])) return (int)$row['status'];
        }
        return 0;
    }

    /**
     * Get request handler to change status
     * @return int
     */
    function changestatus() { 
        $this->cache();
        $this->parse = FALSE;
        $status = path(3);
        $module = id(); 
        $res = $this->status($module,$status); 
        if($res == 1) {
            M($module)->install();
        }
        return $res;
    }

}