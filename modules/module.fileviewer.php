<?php 

class fileviewer extends mastercache {

	private $fpath;
	
	function tables() {}

	function fields() {}

	function items() {
		if(is_file($this->fpath)) {
			return $this->item();
		}
		$result = fm()->dscan($this->fpath);
		//print_r($result);
		return $result;
	}
	
	function item() {		
		$this->tpl = 'item';
		$result = $this->fpath;
		
		return fm()->finfo($this->fpath);
		
	}
	
	
	function del($id = null) { 
		$path = $this->fpath . $this->post['path'];
		foreach($this->post['files'] as $file) {
			$_file = $path . '/' . $file;
			fm()->fdrm($_file);	
		}
		msg('ok', 'files deleted', 'self');
	}
	
	function save($row = null)  {
		$path = BASEFMDIR . getPost('path');
		if(fm()->fsave($path, getPost('content'))) {
			msg('ok', 'file saved created', 'self');	
		} else {
			msg('error', 'can`t save file');
		}
	}
	
	function extend() {
		$this->parse=true;
		/** getting file path **/
		$this->fpath = path();
		unset($this->fpath[0]);	
		if(isset($this->fpath[1]) && method_exists(__CLASS__, $this->fpath[1])) unset($this->fpath[1]);
		$this->fpath = BASEFMDIR . trimslashes(@implode('/', $this->fpath));
		
		if(@$this->path[1] == 'admin') redirect(BASE_URL . $this->cl());

        $this->description = 'File viewer';
	}
	
	
	function dialog() {		
		Hset('dropzone');
		return $this->post;
	}
	
	
	function upload() {
		$path = $this->getPath() . $this->files['file']['name'];
		$thumbpath = BASEFMDIR . $this->getPath() . THUMB_PREFIX . $this->files['file']['name'];
		if($this->files['file']) {
			fm()->fupload('file', $path, $thumbpath);
		}
	}
   
   
	function mkdir() {
		$path = $this->getPath() . getPost('dirname');
		if(@mkdir($path)) {
			msg('ok', 'directory created', 'self');	
		} else {
			msg('error', 'can`t create directory');
		}
	}
	
	
	function mkfile() {
		$path = $this->getPath() . getPost('filename'); //echo $path;
		if(fm()->fmk($path)) {
			msg('ok', 'file created', 'self');	
		} else {
			msg('error', 'can`t create file');
		}
	}
	
	
	function getPath() {
		$path = BASEFMDIR . getPost('path');
		if($path != '') $path .= '/';
		return $path;
	}
	

	function rename() {
		$path = $this->getPath() . getPost('files');
		$newpath = $this->getPath() . getPost('filename');
		if(fm()->frn($path, $newpath)) {
			msg('ok', 'file renamed', 'self');	
		} else {
			msg('error', 'can`t rename file');
		}
	}
		
		
	function copy() {
		$path = $this->getPath() . getPost('files');
		$newpath = $this->getPath() . getPost('filename');
		if(fm()->fcopy($path, $newpath)) {
			msg('ok', 'file copied', 'self');	
		} else {
			msg('error', 'can`t copy file');
		}
	}	
	
	function movetodir() {
		$files = explode(',',getPost('files'));
		$path =  $this->getPath();
		$newpath = tslash(BASEFMDIR . getPost('dir'));
		
		foreach($files as $file) {
			$oldname = $path . $file;
			$newname = $newpath . $file;			
			if(!fm()->frn($oldname, $newname)) {
				msg('error', 'can`t moved file');
			}
		}		
		msg('ok', 'files moved', 'self');
	}	
	
	
	function copytodir() {
		$files = explode(',',getPost('files'));
		$path =  $this->getPath();
		$newpath = tslash(BASEFMDIR . getPost('dir'));
		
		foreach($files as $file) {
			$oldname = $path . $file;
			$newname = $newpath . $file;			
			if(!fm()->fcopy($oldname, $newname)) {
				msg('error', 'can`t copy file');
			}
		}		
		msg('ok', 'files copied', 'self');
	}	
	

}