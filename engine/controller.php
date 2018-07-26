<?php
class controller {
    const PARSE_RAW = 1;
    const PARSE_JSON = 2;
    const PARSE_VIEW = 3;
    const PARSE_FULL = 4;

    protected $view = '';
    protected $cl;
    protected $module;
    protected $parseLvl = self::PARSE_RAW;
    protected $data;
    protected $id;

    public function __construct() {
      $this->cl = str_replace('controller', '', get_class($this));
    }

    protected $rights = [
      'add' => 'admin',
      'edit' => 'admin',
      'save' => 'admin',
      'del' => 'admin',
    ];


    public $methods = [
        'get' => ['list', 'view'],
        'post' => ['save'],
        'delete' => ['del']
    ];

    /*
     * To call api style
     */
    public function api() {
      $request = server('REQUEST_METHOD');
      $this->id = id();
      if(in_array(id(), $this->methods[$request]) {
        $method = id();
        $this->id = path(3);
      }

      $method = $this->methods[$request][0];
      $parse = null;
      switch($request) {
        case GET:
          $parse = self::PARSE_FULL;
          if(id()) $method = 'view';
          $data = get();
        break;
        case POST: $data = post();
      }

      $output = $this->call($method, $data, $parse);
      if(!$output) {
        redirect();
      }
      return $output;
    }


    /**
     * One and only legit way to call any method
     */
    public function call($method, $data = [], $parse = NULL) {
        if(!method_exists(get_class($this), $method)) {
          return false;
        }

        if(!$this->can($method)) {
          return false;
        }

        if($data) {
            $this->data = $data;
        }
        $this->id = $this->id ?? $this->data['id'] ?? data('id') ?? null;

        $data = $this->$method();
        $this->parse = $parse ?? $this->parse ?? self::PARSE_JSON;
        $this->data = null;

        return $this->parse($return, $this->parse);
    }

    protected function parse($data, $mode) {
      switch($mode) {
        case self::PARSE_JSON:
            $data = json_encode($data)
            break;

        case self::PARSE_VIEW:
            $data = view($this->cl . '/' . $this->view, $data);
            break;

        case self::PARSE_FULL:
            $data = view($this->cl . '/' . $this->view, $data);
            $data = tpl('main', ['content' => $data, 'class' => $this->cl ]);
            break;
      }
      return $data;
    }

    protected function add() {
        $this->view = 'form';
        $this->parse = self::PARSE_VIEW;
        return $this->model()->fields();
    }

    protected function edit() {
      $this->view = 'form';
      $this->parse = self::PARSE_VIEW;
      return  $this->model()->get($this->id);
    }

    protected function list() {
      $this->view = 'list';
      $this->parse = self::PARSE_VIEW;
      return  $this->model()->list();
    }

    protected function view() {
      $this->view = 'view';
      $this->parse = self::PARSE_VIEW;
      return  $this->model()->get($this->id);
    }

    protected function del() {
      $this->view = 'view';
      $this->parse = self::PARSE_JSON;
      $result = $this->model()->get($this->id);
      if($result) {
        return [
          'status' => 'ok',
          'msg' => T('success'),
          'redirect' => 'self'
        ];
      }
    }

    protected function save() {
      $this->parse = self::PARSE_JSON;
      if($this->id) {
        $this->model()->set();
      }
      $id = $this->model()->add();
    }


    protected model() {
      return model($this->module . '/' . $this->model);
    }

    /**
     *  Checks if user can call method;
     */
    public function can($method) {
      if(!isset($this->rights[$method])) {
        return true;
      }
      return can($this->cl . '_' . $method);
    }

}
