<?php
require_once(BASE_PATH . 'external/Medoo.php');
/**
 * Medoo query wrapper. Documentation  https://medoo.in/api/select
 */
class QMedoo
{

    /** SINGLETON */

    private static $instance = null;
    /**
     * @return Singleton
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __clone() {}
    private function __construct() {}

    /** EOF SINGLETON **/    

    public $db = null; 
    public $tbl ='';
    public $calls = 0;
    public $method = '';
   
    public function tbl($tbl = '') {
        $this->tbl = $tbl;
        return $this;
    }

    public function db($tbl = '')
    {

        if (!$this->db) {
            /**
             * 'db_host' => 'localhost',
             * 'db_name' => 'root',
             * 'db_pass' => '',
             * 'db_db' => 'maestro',
             * 'db_type' => 'mysql',
             */
            $this->db = new Medoo\Medoo([
                'database_type' => DB_TYPE,
                'database_name' => HOST_DB,
                'server' => HOST_SERVER,
                'username' => HOST_NAME,
                'password' => HOST_PASS,
                'charset' => 'utf8',
            ]);
        }

        return $this->db;

    }

    /**
     * Type of query to be executed
     */
    private $queryType = 'select';
    /**
     * $queryType getter|setter
     */
    public function queryType($queryType = NULL) {
        if($queryType != NULL) $this->queryType = $queryType;
        return $this->queryType;
    }


    /**
     * REQUEST METHODS - just fo beautifying query
     * Was:
     * $db->select('account', [list of joins], [list of columns], [where])
     * Now:
     * db('tbl')
     *      ->select([list of columns])
     *      ->join([list of joins])
     *      ->where([where])
     * ->run();
     */

    /**
     * @var string|array - list of columns. Can be string ('*') or array of columns
     * For documentation see https://medoo.in/api/select
     */
    public $cols = '*';
    public function cols($cols = NULL) {
        if($cols != NULL ) $this->cols = $cols;
        return $this;
    }

    /**
     * @var array - set the list of variables to be added\altered in database
     */
    public $data = [];
    public function data($data = null) {
        if($data != NULL && is_array($data)) $this->data = $data;
        return $this;
    }

    /**
     * ID to be used for row select
     */
    public $id = 0;
    public function id($id = NULL) {
        if($id != NULL ) $this->id = $id;
        return $this;
    }
    /**
     * Set variable
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    /**
     * Unset variable
     */
    public function unsetvar($key) {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * @var array - set where condintion
     * For documentation see https://medoo.in/api/where
     */
    public $where = [];
    public function where($where = '') {
        if($where != NULL) $this->where = $where;
        return $this;
    }

    /**
     * @var array - list of tables to join
     * For documentation see https://medoo.in/api/select
     */
    public $join = [];
    public function join($k, $v) {
        $this->join[$k] = $v;
        return $this;
    }
    public function unjoin($k) {
        unset($this->join[$k]);
        return $this;
    }

    /**
     * @var string|array LIMIT clause
     */
    public $limit = null;
    public function limit($limit) {
        $this->limit = $limit; 
        return $this;
    }

    /**
     * @var string|array GROUP BY clause
     */
    public $group = null;
    public function group($group) {
        $this->group = $group; 
        return $this;
    }

    /**
     * @var string|array HAVING clause
     */
    public $having = null;
    public function having($having) {
        $this->having = $having; 
        return $this;
    }

    /**
     * @var string|array MATCH clause
     */
    public $match = null;
    public function match($match) {
        $this->match = $match; 
        return $this;
    }

    function clear() {
        $this->cols = '*';
        $this->join = [];
        $this->where = [];
        $this->limit = NULL;
        $this->group = NULL;
        $this->having = NULL;
        $this->limit = NULL;
        $this->data = [];
        return $this;
    }



    /** 
     * Handler wrapper of all requests to Medoo
     *   select ($columns)
     *   get / delete 
     *   insert ($data)
     *   update / replace ($data,$id)
     *   max / min / avg/ sum ($cols, $where)
     *   count / has ($where)
     */
    function __call($method, $args) {
        if(!method_exists($this->db(), $method)) return $this;
        $this->method = $method;
        switch($method) {
            case 'select':
                $this->cols = v([$args,0],'*');
                break;
            
            case 'insert':
                $this->data = v([$args,0],[]);
                break;

            case 'update':
            case 'replace':
                $this->data = v([$args,0],[]);
                $this->id = v([$args,1],0);
            
            case 'delete':
            case 'get':
                $this->id = v([$args,1],0);
                break;
            
            case 'max':
            case 'min':
            case 'avg':
            case 'sum':
                $this->cols = v([$args,0],'*');
                $this->where = v([$args,1],[]);
                break;
            
            case 'count':
            case 'has':
                $this->where = v([$args,0],[]);
                break;
        }
        return $this;
    }


    private $methodArgs = [
        'select' => [ 'table', 'join', 'columns', 'where'],
        'insert' => [ 'table', 'data' ],
        'update' => [ 'table', 'data', 'where'],
        'delete' => [ 'table', 'where' ],
        'has' => [ 'table' , 'where' ],
        'count' => [ 'table' , 'where' ],        
        'replace' => [ 'table',  'columns', 'where'],
        'get' => [ 'table', 'columns', 'where'],
        'max' => [ 'table', 'columns', 'where'],
        'min' => [ 'table', 'columns', 'where'],
        'avg' => [ 'table', 'columns', 'where'],
        'sum' => [ 'table', 'columns', 'where'],
    ];

    public function args($method) {
        if(isset($this->methodArgs[$method])) {
            $args = $this->methodArgs[$method];
            $return = [];
            foreach($args as $arg) {
                switch($arg) {
                    case 'where':
                        $return[] =  $this->buildWhere();
                    break;   
                
                    case 'table': 
                        $return[] = $this->tbl;
                        break;

                    case 'columns':
                        $return[] = $this->cols;
                        break;

                    default:    
                        if(isset($this->$arg) && !empty($this->arg)) {
                            $return[] = $this->$arg;
                        }
                    break;
                }       
            }
            return $return;
        }
        return FALSE;
    }

    public function buildWhere() {
        $where = $this->where;
        $cols = [ 'id', 'limit', 'group', 'having', 'match'];
        foreach($cols as $col) { 
            $val = $this->$col; 
            if($val != null) {
                if($col != 'id') $col = strtoupper($col);
                $where[$col] = $val;
            }
        }
        return $where;
    }

    /**
     * Executes raw query;
     */
    public function query($sql) {
        return $this->db()->query($sql);
    }

    /**
     * Executes request
     */
    public function run($debug = false)
    {
        $this->calls++;
        if ($this->calls > 100) {
            debug_print_backtrace();
            die();
        }
        $db = self::db(); 
        if($debug) $db = $db->debug();
        $method = $this->method;
        $args = $this->args($method);  
        if(!$args) return FALSE;
        if(!method_exists($db, $method)) return FALSE;
        return call_user_func_array([$db, $method], $args);

    } # end method

}
