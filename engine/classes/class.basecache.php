<?php
define('P_JSON', 'json');
define('P_RAW', 'raw');
define('P_TPL', 'tpl');
define('P_FULL', 'full');


/*
 * The first very simple CRUD controller class that only loads and saves cache
 */


/**
 * Description of class
 *
 * @author MAECTPO
 */
abstract class basecache {

    public function __toString() {
        return $this->cl();
    }


    function install() {
        $this->uninstall();
        $this->cache($this->modules());
    }

    function uninstall()
    {
        $this->clear();
    }

    /**
     * ID getter/setter
     * @param null|$id
     * @return int|null
     */
    public $id = 0;
    function id($id = null) {
        if($id != null) $this->id = $id;
        return $this->id;
    }

    /**
     * Define if we want to cache data as json
     * @param null|$id
     * @return int|null
     */
    public $json = 0;
    function json($json = null) {
        if($json != null) $this->json = $json;
        return $this->json;
    }

    /**
     * Rendering mode:
     * P_FULL - will output template and site template
     * P_TPL  - will output template
     * P_JSON - will output json encoded data
     * P_RAW  - will output raw data
     */
    public $parse = P_FULL;

    /**
     * Set class name
     * @param null|$cl
     * @return string|null
     */
    public $cl;
    function cl($cl = null) {
        if($cl != null) $this->cl = $cl;
        if($this->cl == null) $this->cl = get_class($this);
        return $this->cl;
    }

    /**
     * Template
     */
    public $tpl;
    function tpl($tpl = null) {
        if($tpl != null) $this->tpl = $tpl;
        return $this->tpl;
    }

    /**
     * Setting properties that should be changed once in construct
     * and thus dont need getter\setter
     */

    /**
     * Method to be called
     */
    public $method;

    /**
     * Default method to be called
     */
    public $defmethod = 'items';



    /**
     * Defines if we want to wrap content or show it fullscreen
     */
    public $wrap = TRUE;


    /**
     * List of menu tabs
     */
    public $tabs = [];

    /**
     * Title
     */
    public $title;

    /**
     * Tab wrap
     */
    public $tabWrap = false;

    /**
     * List of buttons
     */
    public $buttons;
    function buttons() {
        return $this->buttons;
    }

    public $description;

    /**
     * Cache path
     */
    public $cachepath = 'data/cache/';

    /**
     * Data
     */
    public $data;
    function data($data = null) {
        if($data != null) {
            $this->data = $data;
        }
        return $this->data;
    }

    /**
     * mastercache constructor.
     */
    function __construct(){
        /** Class routing parameters **/
        if(empty($cl)) $cl = get_class($this);
        $this->tabs[] = $cl;
        $this->title = $this->cl = $cl;
        $this->method 	= (method_exists($this, fn()) ? fn() : $this->defmethod);
        $this->id 		= (post('id') ? post('id') : id());

        /** Class data parameters **/
        if(get('ajax') > 0) $this->parse = FALSE;
        $this->fileNamePolicy = array(
            '/^(.*)/' => [
                $this->cl() . '/{time}{uid}.{ext}',
                //$this->cl() . '/{uid}-{id}-{fkey}-{time}-{date}-{time}-{fname}.{ext}',
                'thumb' => $this->cl() . '/{id}/{uid}_thumb.{ext}',
                'imgsize' => [400,300],
                'thumbsize'  => [100,100],
            ]
        );

        /** Class template parameters **/
        $this->buttons = [
            'items' => [
                 'add' => 'fa-plus'
            ],
            //'view'  => array( 'items' => 'list', 'item/'.$this->id => 'edit' ),
            'table' => [
                'item/{id}' => 'edit',
                'view/{id}' => 'view'
            ],
        ];
        //$this->buttons->get('items')->set('add','val');

        /** Calls virtual method for class extension in child classes **/
        $this->extend();

    }

   var $fields = [];


   function fields() {
        return $this->fields;
   }


    /**
	 *	Default method for class data listing
	 *	@return array() or FALSE;
	 */
  	public function items() {
        $this->tabWrap = true;
        $this->tpl = 'form';
		    return ['fields' => $this->fields(), 'data' => $this->cache()];
  	}


    /** Save element **/
    public function save($data = null) {
        $this->parse = P_JSON;
        if($data == null) $data = post('form');

        $this->saveFiles();
        $this->cache($data);

        return [
            'message' => T('saved'),
            'status' => 'ok'
        ];
	}



    /** Renders class output **/
	public function render() {
        //if(isset($this->data['data'])) $this->data = $this->data['data'];
        switch($this->parse) {

            case P_RAW: return $this->data; break;

            case P_JSON: return json_encode($this->data); break;

            case P_TPL:
            case P_FULL:
                $params = [
                    'data' => $this->data,
                    'title' => $this->title,
                    'buttons' => $this->buttons,
                    'fields' => $this->fields(),
                    'id' => $this->id(),
                    'class' => $this->cl()
                ];
                $return = tpl( $this->cl() . '/' . $this->tpl, $params);
                if($this->tabWrap) {
                  $return = tabWrap($return, $this->tabs[0]);
                }
                return $return;
            break;
        }
        return NULL;
    }

    public function clear() {
        $this->data = [];
        cacherm($this->cl(), $this->json());
    }

    public function cache($data = null, $name = '') {
        if($name == '') $name = $this->cl();
        return cache($name, $data);
	}

	public function extend(){}

	public function saveFiles($pol = []) {
        $files = files();
        if(!empty($files)) {
            foreach($files as $fn => $file) {
                foreach($this->fileNamePolicy as $fnpkey => $fnp) {
                    //print_r($fn . ' ' . $fnpkey);
                    preg_match($fnpkey, $fn, $matches);
                    if(!empty($matches)) {
                        $filename = $fnp[0];
                        $type = explode('/',$file['type']);
                        $replace = [
                            '{uid}'     => uniqid(),
                            '{id}'      => $this->id,
                            '{fname}'   => slug($file['name']),
                            '{fkey}'    => $fn,
                            '{datetime}'=> date("YmdHis"),
                            '{date}'    => date("Ymd"),
                            '{time}'    => date("His"),
                            '{ext}'     => $type[1],
                        ];
                        if(!empty($pol)) $replace = array_merge($pol, $replace);
                        $filename =  strtr($filename, $replace);
                        $thumb = @$fnp['thumb'];
                        if($thumb) {
                            $thumb = strtr($thumb, $replace);
                        }
                        $fileparam = explode('-',$fn);
                        fm()->fupload($fn,BASEFMDIR . $filename, BASEFMDIR . $thumb, @$fnp['imgsize'], @$fnp['thumbsize']);
                        $imgsize = getimagesize(BASEFMDIR . $filename);
                        setArrayValue($this->post, $fileparam, [
                            'width' => $imgsize[0],
                            'height' => $imgsize[1],
                            'name' => $filename,
                            'thumb' => $thumb,
                            'type' => $type[0]
                        ]);
                        if(@$fnp['animate'] > 0) {
                            createCSSAnimation($fn . '_' . $this->id,[
                                'fname' => BASEFMURL . $filename,
                                'width' => $imgsize[0],
                                'height' => $imgsize[1],
                                'rows' => (isset($fnp['rows'])? $fnp['rows'] : 1 ),
                            ]);
                        }
                        continue;
                    }
                }
            }
        }
    }
}
