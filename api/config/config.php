<?php
/**
 * @author haimerb
 * 
 * Valores de configuracion de bases de datos
 */
if (!defined('RAZON_SOCIAL_BASE')) define("RAZON_SOCIAL_BASE", "APEX TOOL GROUP S.A.S");
if (!defined('NIT_BASE')) define("NIT_BASE","890.311.366");
if (!defined('DIR_BASE')) define("DIR_BASE","AV CL 26 69 D 91 TO 1 OF 406 BOGOTA");
if (!defined('TEL_BASE')) define("TEL_BASE","(57-2) 3873000");
return
json_encode(
array(
    "host"=> "localhost",
    "db_name"=>"apex",
    "username" => "root",
    "password" => ""
    )
);
?>