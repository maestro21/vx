<?php //if(!isset($index)) die();

$settings = [
    'folder_path' => '',
    'db_host'    => 'localhost',
    'db_name'    => 'root',
    'db_pass'    => '',
    'db_db'      => 'maestro',
    'db_type'    => 'mysql',
    'mysql_dump_path'   =>  'C:/xampp/mysql/bin/mysqldump.exe',
    'mysql_path'        =>  'C:/xampp/mysql/bin/mysql.exe',
];

if(!empty($_POST)) { print_r($_POST);
    file_put_contents('settings.php', '<?php $settings =' . var_export($_POST,true) . ';');
    $installed = 2;

} else { ?>
<h2>Installation</h2>
<form method="post"><table>
<?php
foreach ($settings  as $k => $v) { ?>
    <tr><td><?=$k;?>:</td><td> <input name="<?=$k;?>" value="<?=$v;?>"></td>
<?php } ?>
        <tr><td colspan="2"><input type="submit"></td></tr>
</form>
</table>
<?php } ?>