<?php
define('THUMB_PREFIX', 'thumb_');
define('FMPATH', 'data/uploads/');
define('BASEFMDIR', BASE_PATH . FMPATH);
define('BASEFMURL', BASE_URL . FMPATH);


class filemanager {





	function dloop($path = '') {
		$dirs = array();
		$_dirs = scandir(BASEFMDIR . $path);
		$_dirs = array_diff($_dirs, array('.', '..'));
		foreach($_dirs as $dir) {
			$_dir = $path . '/' . $dir;
			if(is_dir(BASEFMDIR . $_dir)) {
				$dirs[$_dir] = $this->dloop($_dir);
			}
		}
		return $dirs;
	}


	public function dscan($path) {
		if(!file_exists($path)) return FALSE;

		$files = scandir($path);
		if(!$files) return FALSE;

		$files = array_diff($files, array('.', '..'));
		$_dirs = array();
		$_files = array();
		foreach($files as $file) {
			$_fpath = $path . '/' . $file;
			$_finfo = $this->finfo($_fpath);
			if($_finfo['is_dir']) {
				$_dirs[] = $_finfo;
			} else {
				$_files[] = $_finfo;
			}
		}
		$result = array_merge($_dirs, $_files);

		return $result;
	}

	public function fmk($path, $data = null){
		if(file_exists($path)) return TRUE;
		$this->dmk(dirname($path));
		$ret = fopen($path, 'w');
		if($ret) {
			return fclose($ret);
		}
		return FALSE;
	}

	public function dmk($path, $mode = 0777, $recursive = true) {
		return @mkdir($path, $mode, $recursive);
	}

	public function dlist($dir) {
        return array_diff(scandir($dir), array('.','..'));
    }

	public function drm($dir) {
		if(!file_exists($dir)) return FALSE;

		$files = dlist($dir);
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->drm("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public function fdrm($path) {
		if(!file_exists($path)) return FALSE;
		if(is_dir($path)) $this->drm($path); else $this->fdel($path);
	}

	public function fdel($path) {
		if(!file_exists($path)) return FALSE;
		unlink($path);
		$thumb = dirname($path) . '/' . THUMB_PREFIX . basename($path);
		if(file_exists($thumb)) unlink($thumb);
	}

	public function fget($path) {
		if(!file_exists($path)) {
			return null;
		}
		return file_get_contents($path);
	}

	public function fsave($path, $contents){
		if($this->fmk($path)) {
			return file_put_contents($path, $contents);
		}
		return false;
	}

	public function finfo($path){
		if(!file_exists($path)) return FALSE;

		$_finfo = array();
		$_finfo['name'] = basename($path);
		$_finfo['filemtime'] = filemtime($path);
		$_finfo['filectime'] = filemtime($path);
		if(is_dir($path)) {
			$_finfo['type'] = 'dir';
			$fi = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
			$_finfo['files'] = iterator_count($fi);
			$_finfo['is_dir'] = true;
		} else {
			$_finfo['type'] = 'file';
			$_finfo['filetype'] = mime_content_type($path);
			$_finfo['size'] = filesize($path);
			$_finfo['is_dir'] = false;
		}

		return $_finfo;
	}

	public function frn($path, $path2) {
		return rename($path, $path2);
	}

	public function fcopy($path, $path2) {
		return copy($path, $path2);
	}



	public function fupload($file, $path, $thumbpath = FALSE, $imgsize = false, $thumbsize = false) {
		global $_FILES;
		if(empty($_FILES) || !file_exists($_FILES[$file]["tmp_name"])) return FALSE;
		$file = $_FILES[$file];
		$tmpname = $file["tmp_name"];
        @mkdir(dirname($path), 0755, true);
		move_uploaded_file($tmpname, $path);
		/* create thumb for image */
		$type = explode('/',$file['type']);
		if($type[0] == 'image') {
            if(is_array($imgsize)) {
                createthumb($path, $path, $imgsize[0], $imgsize[1], $file['type']);
            }
            if($thumbpath && is_array($thumbsize)) {
                createthumb($path, $thumbpath, $thumbsize[0], $thumbsize[1], $file['type']);
            }
		}
	}
}



function fm() {
	return new filemanager();
}
