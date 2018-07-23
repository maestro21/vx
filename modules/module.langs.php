<?php

class langs extends basecache {



	function fields() {
		return [
			'langs' => [
				'langs' => [
					WIDGET_TABLE, 'children' => [
							'abbr' => [ WIDGET_TEXT, 'search' => TRUE],
							'name' => [	WIDGET_TEXT, 'null' => TRUE ]	,
							'website' => [WIDGET_URL,  'null' => TRUE ],
							'active' => [ WIDGET_CHECKBOX, 'null' => TRUE],
					],
					'table' => false,
				],
			],
			'i18n' => [
				'i18n' => [ WIDGET_TABLE,
						'children' => [
							'abbr' => [ WIDGET_TEXT ],
							'label' => [	WIDGET_TEXT, 'multilang' => TRUE ]
					]
				],
			],
			'lang_widget' => [
				'fullname' => [ WIDGET_CHECKBOX ],
				'show_flag' => [ WIDGET_CHECKBOX ],
				'dropdown' => [ WIDGET_CHECKBOX ]
			],
		];
	}


	public function items() {
			$this->tabs = array_keys($this->fields());
			return parent::items();
	}

    /** Save element **/
    public function save($data = null) {
    	$data = post('form');
    	if(isset($data['langs'])) $data['langs'] = saveByKey($data['langs'],'abbr');
    	if(isset($data['i18n']))  $data['i18n']  = saveByKey($data['i18n'],'abbr');

        return parent::save($data);
	}

	public function install() {
		$this->cache(
			[ 1=> [
				'abbr' => 'en',
				'name' => 'English',
				'active' => 1,
				'id' => 1,
			]]);
	}
}
