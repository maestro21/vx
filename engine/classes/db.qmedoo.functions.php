<?php 

/**
 * QMEDOO DATABASE FUNCTIONS
 */

/** 
 * Simple request function. Examples:
 *      q($tbl)->select($columns)->limit([40,20])->run(); - select list of items
 *      q($tbl)->get($id)->run(); - select single item
 *      q($tbl)->insert($data)->run(); - insert
 *      q($tbl)->delete($id)->run();  - delete
 *      q($tbl)->update($data,$id)->run(); - update
 *      q()->query($sql) - execute raw query
 * @param $tbl name
 * @return QMedoo
 */
function q($tbl = NULL) {
	if(is_object($tbl)) $tbl = $tbl->cl();
	$obj = QMedoo::getInstance();
	if(!empty($tbl)) {
		$obj->clear()->tbl($tbl);
	}
    return $obj;
}


/**
 * Old query functions support
 */

/**
 * Wrapper for raw query request
 */
function DBquery($sql) {
	return q()->query($sql);
}

/**
 * Fetch single cell
 */
function DBcell($sql) {
	return DBquery($sql)->fetchColumn();		
}

/**
 * Fetch row
 */
function DBrow($sql) {
	return DBquery($sql)->fetch();		
}

/**
 * Fetch column
 */
function DBcol($sql) {
	$arr = array();
	$res = DBquery($sql)->fetchAll();
	foreach($res as $row){
		$arr[] = $row[0];
	}
	return $arr;
}

/**
 * Fetch all
 */
function DBall($sql, $echo = false) {
	return DBquery($sql, $echo)->fetchAll();	
}

/**
 * Fetch insert id
 */
function DBinsertId(){
	return q()->id();
}

/**
 * quote for db
 */
function dbquote($val) {
	return q()->quote($val);
}



/**
 * 
 * Conditional functions for WHERE clause.
 * Medoo approach:
 * [
 * "AND" => [
 *		"OR" => [
 *			"user_name" => "foo",
 *			"email" => "foo@bar.com"
 *		],
 *		"password" => "12345"
 *	]
 * ]
 * MedooQ approach:
 * 	qAnd([
 * 		qOr([
 * 			qEq("user_name", "foo"),
 * 			qEq("email", "foo@bar.com")
 * 		]),
 * 		qEq("password", "12345")
 * ])	
 * 
 * You are free to use any approach or combine them:
 * qAnd([
 * 		qOr([
 *			"user_name" => "foo",
 *			"email" => "foo@bar.com"
 *		]),
 * 		"password" => "12345"
 * ])	
 *  
 */
/**
 * Equals to key = value
 */
function qEq($key, $value) {
	return [$key => $value];
}
/**
 * Equals to [ 'AND' => $data ]
 */
function qAnd($data) {
    return ['AND' => $data];
}
/**
 * Equals to [ 'OR' => $data ]
 */
function qOr($data) {
    return ['OR' => $data];
}


/** DB schema functions **/
function uninstall($tables) {
	foreach($tables as $table_name => $table) {
		/** droping first; it's new install, so old table means to be dropped if exists **/
		$sql = "DROP TABLE IF EXISTS `$table_name`"; DBquery($sql);
	}
}


function install($tables) {
	/** running through all tables **/ 
	foreach($tables as $table_name => $table) {
		/** droping first; it's new install, so old table means to be dropped if exists **/
		$sql = "DROP TABLE IF EXISTS `$table_name`"; DBquery($sql);
		$sql =	"CREATE TABLE `$table_name`(";
		
		$fieldsql = array();
		foreach ($table['fields'] as  $field_name => $field){
			/** adding fields **/
			$fsql = '';
			$type = getWidgetDefaultDBType($field[0]);
			switch($type){
				case DB_STRING: $type = ' VARCHAR(255)'; break;
				case DB_BLOB: $type = ' BLOB'; break;
				case DB_TEXT: $type = ' TEXT'; break;
				case DB_INT : $type = ' INT'; break;
				case DB_DATE : $type = ' DATETIME'; break;
				case DB_FLOAT : $type = ' FLOAT'; break;
				case DB_BOOL : $type = ' TINYINT(1)'; break;
                case DB_CHAR: $type = ' CHAR(1)'; break;
                case DB_VOID:
				default : continue 2;break;
			}
			if($type != '' && $field_name!='') $fsql .= "`$field_name` $type";
			
			/** adding field options **/
			if(isset($field[2])) {
				$options = $field[2];
				if(isset($options['null'])) {
					if(!$options['null']) $fsql .= ' NOT';  $fsql .= ' NULL';
				}
				if(isset($options['ai'])) {
					$fsql .= ' AUTO_INCREMENT';
				}
				if(isset($options['default'])) {
					$fsql .= ' DEFAULT "' . $options['default'] . '"';
				}	
			}
			
			/* composing query */
			if($fsql != '')	$fieldsql[] = $fsql;
		}
		$sql .= implode(',', $fieldsql);
		
		/** adding primary key **/
		if(isset($table['pk'])) {
			if(NULL != $table['pk']) {
				$sql .=	sprintf(", PRIMARY KEY(%s)", $table['pk']);	
			}	
		} else {
			$sql .=	",\r\n id INT NOT NULL AUTO_INCREMENT PRIMARY KEY";	
		}
		
		/** adding foreing keys **/
		if(isset($table['fk'])) {
			foreach($table['fk'] as $key => $target) {
				$sql .=	sprintf(", FOREIGN KEY(%s) REFERENCES %s ON DELETE CASCADE ON UPDATE CASCADE", $key, $target);	
			}	
		}
		
		/** adding indexes **/
		if(isset($table['idx'])) {
			foreach($table['idx'] as $idx_name => $idx) {				
				$sql .= "," . (isset($idx[1]) ? 'UNIQUE' : 'INDEX') . " `$idx_name`(" . $idx[0] . ")";
			}	
		}
		
		$sql.=	");";
		/* executing query **/
		DBquery($sql);
	}
}

function update($update) {
	foreach ($update as $table_name => $fields) {
		$parts = array();
		foreach ($fields as  $field => $val){
			if($field!='') {				
				$action = $val['do'];
				$type	= $val['type'];				
				if($action == 'DROP') {
					if($type == ('index' || 'unique')) {
						"DROP INDEX($field),";
					} else {
						$parts[] = "DROP `$field`,";
					}
				} else {
					$newname = '';
					if($action == 'CHANGE') {
						$newname = "`". $val[1] . "`";
					}
					switch($type){
						case 'string': 	$type = ' VARCHAR(255)'; break;
						case 'blob': 	$type = ' BLOB'; break;
						case 'text': 	$type = ' TEXT'; break;
						case 'int' : 	$type = ' INT DEFAULT 0'; break;
						case 'date' :
						case 'time' : 	$type = ' DATETIME DEFAULT CURRENT_TIMESTAMP;'; break;
						case 'float' : 	$type = ' FLOAT DEFAULT 0'; break;	
						case 'unique':  $type = 'UNIQUE'; break;
						case 'index':	$type = 'INDEX'; break;
						default : $type = '';	
					}
					if($type!='') {
						if($action == 'CHANGE') {
							$parts[] = "CHANGE `$field` $newname $type";
						} else {
							if($type == 'UNIQUE' || $type == 'INDEX') { 
								$parts[] = "ADD $type($field)";
							} else {
								$parts[] = "ADD COLUMN `$field` $type";
							}
						}
					}		
				}
			}
		}
		$sql =	"ALTER TABLE `$table_name` " . implode(',', $parts) . ";";
	} //echo $sql;
	if($sql) {
		DBquery($sql) or die(mysql_error());
	}
}


function checkTables($tables) {
	$arr = DBcol(sprintf("SHOW TABLES FROM '%s'", HOST_DB));
	return in_array($tables, $arr);
}


/** checking if our engine is installed; `globals` and `modules` are the only crucial modules, both cached, so if no cache exists, engine is not installed **/
function dbinstall() {
    dbempty();
    $modules = array('modules', 'system', 'pages');
    $dbrestored = false;
    foreach($modules as $module) {
        //if(NULL == cache($module)) {
        if(!$dbrestored) $dbrestored = dbrestore();
        if(!$dbrestored) call($module, 'install');

        //call($module, 'cache');
        //M($module)->call('install');
        //M($module)->call('cache');


        //q()->update('modules')->set('status',2)->where(qEq('name', $module))->run();
        //}
    }
    /* update module info */
    //call('modules', 'cache');
}