<?php
class i18n extends mastercache {

	function gettables() {}

	
	function extend() {
		$this->description = 'Core module for internationalization';		
		$this->data = cache('i18n');
	}

	
	/* fields */
	function fields() {
		$ret = [					
			'label'			=>	[ WIDGET_STRING, 'search' => TRUE,],	
			'type'			=>	[ WIDGET_SELECT, 'options' => [
				1 => WIDGET_TEXT,
				2 => WIDGET_TEXTAREA,
				3 => WIDGET_KEYVALUES
			],],						
		];

				
		$langs = langs();		
		foreach($langs as $lang) {
			$ret[$lang['abbr']] = [ WIDGET_STRING];
		}

		return $ret;
	}

	
	function items($data = NULL) {}
	function del($data = NULL) {}
	function add($data = NULL) {}
	function edit($data = NULL) {}
	function view($data = NULL) {}
	
	/**
		Admin method for class data listing
		@return array() or FALSE;
	**/
	public function admin() {
		if(R($this->rights['admin'])){
			return $this->data;
		}
		return FALSE;
	}	
	
	
	public function save($row = null) {
		$this->parse = P_JSON;
		$data = array();
		$langs = getLangs();
		foreach($this->post['form']['fields'] as $row) {
			if($row['type'] == 3) {
				$langs = getLangs();		
				foreach($langs as $lang) {
					$row[$lang['abbr']] = strToKeyValues($row[$lang['abbr']]);
				}	
			}
			$data[$row['label']] = $row;
		}	
		ksort($data, SORT_FLAG_CASE);
		$this->cache($data);
		
		return [
			'message' => T('saved'), 
			'status' => 'ok'
		];	
	}
	

	
	function addField($key = '{key}', $data = null) {
		$this->parse = P_TPL;		
		
		return tpl('i18n/field', array(
			'key' 		=> $key,
			'fields' 	=> $this->fields,
			'widgets' 	=> $this->options['type'],
			'data' 		=> $data)
		);
	}
	
}