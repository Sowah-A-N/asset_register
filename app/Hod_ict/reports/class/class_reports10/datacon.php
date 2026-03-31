<?php
$_d=__DIR__; while(!file_exists($_d.'/config/database.php')&&$_d!='/') $_d=dirname($_d);
require_once $_d.'/config/database.php'; unset($_d);
$conn=get_db_connection('dsu');
