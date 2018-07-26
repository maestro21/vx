<?php



/**
 * Model
 */
function model($path) {
  $path = explode('/', $path);
  $module = $path[0];
  $model = $path[1] ?? $module;
  $filepath = BASE_PATH . 'modules/' . $module . '/' . $module . ' .model.' . $model . '.php';
  if(file_exists($filepath)) {
    require_once($filepath);
    return new {$model . 'Model'}();
  }
  return null;
}

/**
 * View
 */
function view($path, $data = array()){
  $path = explode('/', $path);
  $module = $path[0];
  $view = $path[1] ?? $module;
  $filepath = BASE_PATH . 'modules/' . $module . '/' . $module . ' .view.' . $view . '.php';
  if(!file_exists($filepath)) {
    $filepath = BASE_PATH . 'engine/view/view.' . $view . '.php';
    if(!file_exists($filepath)) {
      return null;
    }
  }

  /**
   * Parsing view variables and returning parsed template
   */
  if($_url){
      foreach ($data as $k =>$v) {
        $$k=$v;
      }
      ob_start();
      include($_url);
      $content = ob_get_contents();
      ob_end_clean();
  }
  return $content;
}


/**
 * Controller
 */
function controller($path) {
  $path = explode('/', $path);
  $module = $path[0];
  $controller = $path[1] ?? $module;
  $filepath = BASE_PATH . 'modules/' . $module . '/' . $module . ' .model.' . $model . '.php';
  if(file_exists($filepath)) {
    require_once($filepath);
    return new {$controller . 'Controller'}();
  }
  return null;
}

function c($path) {
  return controller($path);
}
