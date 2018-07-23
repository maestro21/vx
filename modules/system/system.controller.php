<?php class system extends basecache  {

	function extend() {
		$this->description = 'Core module for setting up global settings';
	}

	public $fields = null;

	function fields() {
		if($this->fields == null) $this->fields = cache('settings');
		return $this->fields;
    }

	
	function install() {
		cache('settings', [ 'settings' => [ WIDGET_TABS, 'table' => false, 'children' => [], ], ]);
		$this->settings('general',[
			'sitename' => [ WIDGET_TEXT, 'default' => 'Maestro Engine v8'],
			'description' => [ WIDGET_TEXTAREA, 'default' => 'Website powered by Maestro Engine v8'],
			'theme' => [ WIDGET_TEXT, 'default' => 'maestro'], //TODO : select
			'defmodule' => [ WIDGET_TEXT, 'default' => 'pages'],
			'deflang' => [ WIDGET_TEXT, 'default' => 'en'],
		]);
		/*
		$this->settings('db_backup', [
			'db_last_backup' => [ WIDGET_TIME, 'default' => NULL],
			'db_backup_frequency' => [ WIDGET_TEXT, 'default' => '+1 day'],
		]); */
	}

	function settings($key = null, $data = null) {		
		if($this->fields == null) {
			$this->fields = cache('settings');
		}
		if($data != null) {
			$this->fields['settings']['children'][$key] =  $data;
			cache('settings', $this->fields);
		}
		return $this->fields;
	}

	function save($row = null) { 
		$this->parse = P_JSON;
		$data = post('form'); print_r($data);
		$this->cache($data, "settings_data");
		return [
			'message' => T('saved'), 
			'status' => 'ok'
		];	
	}


	function login() { 
		if(superAdmin()) redirect(BASE_URL);
	
		if($this->post) { 
			$this->parse = true;
			if(md5($_POST['pass']) == ADM_PASS){;	
				session('user', true);				
				echo json_encode(array('message' => T('success'), 'status' => 'ok', 'redirect' => BASE_URL));  die();
			}
			echo json_encode(array('message' => T('wrong pass'), 'status' => 'error', 'redirect' => BASE_URL));  die();
		}
	}

	function logout() {
		session('user',null);
		redirect(BASE_URL);		
	}

	/**
	 * return globals
	 */
	function globals() {
		return $this->cache();
	}	

}