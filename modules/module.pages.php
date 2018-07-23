<?php class pages extends masterdb  {

	function extend() {
		$this->description = 'Core module for creating website pages';
		$this->defmethod = 'view';
	}

	function fields() {

		$statusOptions = [
			0 => 'hidden',
			1 => 'visible',
			2 => 'in_menu',
		];
		$typeOptions = [
			1 => 'page',
			2 => 'redirect'
		];

		return [
            'pid'			=>	[ WIDGET_SELECT,  'default' => 0, 'null' => false, 'options' => $this->getPidOptions()],
            'name' 			=> 	[ 'string', 'text', 'search' => TRUE ],
            'url'			=>	[ 'string',	'text' ],
            'fullurl'		=>  [ 'string', 'info' ],
            'type'			=>	[ 'int', 'select', 'options' => $typeOptions ],
            'content' 		=> 	[ 'blob', 'html', 'search' => TRUE ],
            'status'		=>	[ 'int', 'select', 'options' => $statusOptions],
        ];
	}

	
	function install() { 
		parent :: install();		
		include('data/default.pages.php'); 
		foreach($pages as $page) {
			q($this)->insert($page)->run();
		}
	}

	
	
	function del($id = NULL) {
		$ret = parent::del($id = NULL);
		$this->cache();
		return $ret;
	}
	
	function save($row = null)  {
		$this->parse = FALSE;
		$form = post('form');
		$form['fullurl'] = $this->getFullUrl($form['pid'], $form['url']);		
		$ret = parent :: saveDB($form);
		$this->cache();
		return json_encode($ret);
	}
	
	function getPageTree($options = null) {	
		$q = q()	->select('id, pid, name, url, fullurl')
					->from($this->cl())
					->order('pid ASC, id ASC');
		if(@$options['id'] > 0) {
			$q->where('id != ' . $options['id']);	
		}
		if(@$options['status']) {
			$q->where('status >= ' . $options['status']);	
		}	
		$tree = $q->run();
		$T = new tree($tree);
		return $T;
	}
		
	function cache($data = array(), $cl = '') {
		
		$T = $this->getPageTree([ 'status' => 1]);
		cache($this->cl(), $T->treeList);
		
		$T = $this->getPageTree([ 'status' => 2]);
		cache('menu', $T->treeList);
		
		$T = $this->getPageTree();
		cache($this->cl() . 'options', $T->options);
	}	
	
	function getPidOptions() {
		if($this->method == 'edit') {
			$T = $this->getPageTree([ 'id' => (int)$this->id ]);
			return $T->options;		
		}
		return cache($this->cl() . 'options');
	}
	
	function getFullUrl($id, $url) {
		if($id > 0) {
			$T = $this->getPageTree();
			$ret = array_reverse($T->getFullUrl($id));
			$ret[] = $url;
			$ret = implode('/', $ret);
		} else {
			$ret = $url;
		}
		return $ret;
	}
	
	function admin() {
		$T = $this->getPageTree();
		$ret = $T->drawTree($this . '/adm');
		return $ret;
	}
	
				
	function menu($tpl = 'menu'){
		$this->parse = FALSE;
		$tree = cache('menu');
		foreach($tree as $lang => $topmenu) {
			if($topmenu['url'] == lang()) {
				$homeButton = [
					'fullurl' => $topmenu['url'],
					'name' => '',
					'class' => 'fa fa-home',
				];
				$leafs = array_merge([$homeButton], $topmenu['children']);
				$T = new tree();
				$ret = $T->drawTree($this . '/' . $tpl, $leafs);
				return $ret;
			}
		}
		return FALSE;
	}	
	
	function getSubMenu($id = 0) {
		$submenu = q($this)
						->select(['id', 'pid', 'name', 'url', 'fullurl'])
						->where([
							'pid' => $id,
							'status[>]' => 0
						])
					->run();
		return $submenu;			
	}
	
	function view($id = NULL) {
        $this->wrap = false;
		$url  = implode('/', path());
        if(empty($url)) $url = lang();
		$page = q($this)
					->select()
					->where(qEq('fullurl',$url))
					->run();
		
		// error page
		if(!isset($page[0])) {
			return $this->notFound();
		}

		$page = $page[0];
		$this->title = $page['name'];
			
		
		if($page['type'] == 2) redirect(strip_tags($page['content']), 0, true);
		return $page;	
	}
	
	function notFound() {
		return array(
			'name' => T('404 page not found'),
			'content' => '',
		);		
	}
	
}