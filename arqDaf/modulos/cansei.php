<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start();
//require_once(dirname(__FILE__)."/config/abrealas.php");
//pg_query($Conec, "UPDATE ".$xProj.".usulog SET datalogout = NOW() WHERE id = ".$_SESSION["CodUsuLog"].""); 
//$_SESSION = array();
session_destroy();
header("Location: ../index.php");