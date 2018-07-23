<?php class forms extends masterdb {
	
	var $description = 'Module for form creation and management';

	function fields() {
		return [					
			'name'	=> 	[ WIDGET_TEXT, 'search' => TRUE, 'required' => TRUE ],
			'split' => [ WIDGET_CHECKBOX ],
			'sendmail' => [ WIDGET_CHECKBOX  ],
			'mail_topic' => [ WIDGET_TEXT ],                    
			'fields' => [ DB_ARRAY, WIDGET_TABLE,
				'children' => [
					'name' => [ WIDGET_TEXT],
					'type' => [ WIDGET_SELECT, 'options' => dbtypes()],
					'widget' => [  WIDGET_SELECT, 'options' => widgets()],
					'options' => [ WIDGET_KEYVALUES],
					'required' => [ WIDGET_CHECKBOX],
				] 
			],					
		];
	}
    
    
	function tables() {
		return 
		[
			'forms' => [
				'fields' => $this->fields(),
			],
			'forms_messages' => [
				'fields' => [					
					'form_id'	=> 	[ WIDGET_HIDDEN, 'dbtype' => DB_INT, 'index' => TRUE ],
					'data' => [ WIDGET_ARRAY ],
					'sent' => [ WIDGET_DATE],
					
				],
				'fk' => [
					'form_id' => 'forms(id)'
				],
			],
		];		
	}
	
	function extend() {
		$this->buttons['admin']['messages'] = 'fa-commenting';
	}


	
	function field($key = null, $data = null) {
		$this->parse = P_TPL;		
		
		return [
			'key' => $key,
			'types' => $this->options['dbtypes'],
			'widgets' => $this->options['widgets'],
			//'validators' =>$this->options['validators'],
			'data' => $data
		];
	}


	function view($id = NULL) {
		$data = parent:: view($id);
		$_fields = unserialize($data['fields']);

		$fields = array();

		foreach($_fields as $field) {
			$fields[$field['name']] = [
				$field['type'],
				$field['widget'],
				'required' => (int)@$field['required'],
                'options' => @$field['options'],
			];
		}
		$data['fields'] = $fields;
		$data['id'] = $id;
		
		return $data;
	}	
	
	function post() { 
		$this->parse = P_JSON;
		$formid = (int)$this->post['id'];
        if($formid < -1) {
            echo json_encode(array('message' => T('wrong data'), 'status' => 'error'));	die();	
        }
		$form = q('forms')->qget($formid)->run();

		$fields = unserialize($form['fields']);
		$fdata = array(); 
		foreach($this->post['form'] as $k => $v) {
			$dbtype = getFieldDBType($k, $fields);
			$fdata[$k] = sqlFormat([
			    'widget' => getFieldType($k, $fields),
			    'dbtype' => getFieldDBType($k, $fields),
                $v]);
		}
		$fdata = moveToBottom($fdata, 'message');

		$data = [
			'form_id' => $formid,
			'data' =>  serialize($fdata),
			'sent' =>   now() ,
		];
		q('forms_messages')->qadd($data)->run();
		
		if($form['sendmail']) {
			$data = [
				'subject' => $form['mail_topic'],
				'body' => mtpl('mail', ['data' => $fdata]),
				'from' => $fdata['email'],
				'to'   => G('adm_mail'),
			];
			//print_r($data);
			sendMail($data);
		}
		
		return [
			'message' => T('form_submitted'), 
			'status' => 'ok'
		];	
	}
	
	function messages() {
		$qMsg = q('forms_messages')->qlist()->order('sent desc');
		/*if($this->id > 0) {
			$qMsg->where(qEq('form_id', $this->id));
		}
		return $qMsg->run();*/
		
		$qForm = q('forms')->qlist();
            
		if($this->id > 0) {
			$qMsg->where(qEq('form_id', $this->id));
			$qForm->where(qEq('id', $this->id));	
		}
        $name = '';
		$forms = array();
		$_forms = $qForm->run();
		foreach($_forms as $form) {
			$forms[$form['id']] = unserialize($form['fields']);
            if($this->id > 0) {
                $name = $form['name'];
            }
		}
		
		$messages = $qMsg->run(); 
		
		$data = [
			'forms' => $forms,
			'messages' => $messages,
            'name' => $name,
		];
		//print_r($data);
		return $data;
		
	}
    
    /** Delete message **/
    public function delmsg($id = NULL) {		
		$this->parse = P_JSON;
		$id = v($id, $this->id());
		
		q('forms_messages')->qdel($id)->run();

		return [ 
			'redirect' => 'self', 
			'status' => 'ok', 
			'timeout' => 1
		];
    }
		
}


function getFieldDBType($key, $fields) { //inspect($fields); 

	foreach($fields as $field) { 
		if($field['name'] == $key) {;
			return $field['type'];
		}
	}	
	return 'text';
}

function getFieldType($key, $fields) { //inspect($fields); 

	foreach($fields as $field) { 
		if($field['name'] == $key) {;
			return $field['widget'];
		}
	}	
	return 'text';
}
function getFieldOptions($key, $fields) { //inspect($fields); 

	foreach($fields as $field) { 
		if($field['name'] == $key) {;
			return keyvalues($field['options']);
		}
	}	
	return '';
}