<?php 
/**
 *  Main cache class that stores cached data
 */
class cache {
    /* Singleton pattern */
    private static $_instance = null;
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    static public function getInstance() {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /* thats it */
    
    
    public $json = false;
    
    private $cache = [];
   
    public function get($key) {
        if(!isset($this->cache[$key])) {
            $name = pathinfo($key)['filename'];
            $filename = BASE_PATH . $key;
            if(!file_exists($filename)) return [];
            if($this->json($key)) {
                $data = json_decode(file_get_contents($filename), true);                
            } else {
                include($filename);
                $data = $$name;
            }
            //if(isset($data['data'])) $data = $data['data'];
            $this->cache[$key] = $data;
        }
        return $this->cache[$key];
    }
    
    public function set($key, $data) {
        $this->cache[$key] = $data;
        $filename = BASE_PATH . $key;
        $name = pathinfo($key)['filename'];
        $dir = dirname($filename); 
        if(!file_exists($dir)) mkdir($dir, 0777,true);
        if($this->json($key)) {            
            file_put_contents($filename, json_encode($data));
        } else {
            file_put_contents($filename, '<?php $' . $name .' = ' . var_export($data, TRUE) . ";" ) ;
        }
    }
    
    public function json($key) {
        return (strpos($key, '.json') !== false);
    }
    
    public function del($key) {
        unset($this->cache[$key]);
        $filename = BASE_PATH . $key; 
        if(file_exists($filename)) {
            unlink($filename);
        }
    }
    
}



/** Cache getter\setter **/
function cache($name, $data = NULL, $json = false, $cachepath = 'data/cache/', $returndata = true) {  
	$key =  $cachepath . $name . ($json ? '.json' : '.php');
    $cache = cache::getInstance(); 
	if(NULL !== $data) { 
        $cache->set($key, $data);
	}
    $data = $cache->get($key);
    if(isset($data['data']) && $returndata) $data = $data['data'];
    return $data;
}


/** Clears cache **/
function cacherm($name, $json = false, $cachepath = 'data/cache/') {
    $key =  $cachepath . $name . ($json ? '.json' : '.php');
	cache::getInstance()->del($key);
}

