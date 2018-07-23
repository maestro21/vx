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
        $this->id = $this->data['id'] ?? data('id') ?? null;

        $data = $this->$method();
        $this->data = null;

        return $this->parse($return, $parse);
    }

    protected function parse($data, $mode) {
      switch($mode) {
        case self::PARSE_JSON:
            $data = json_encode($data)
            break;

        case self::PARSE_VIEW:
            $data = view($this->cl . '/' . $this->view, $data);

        case self::PARSE_FULL:
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
