<?php
$url = $_SERVER["PHP_SELF"];
if(strtolower($url) == "/cesb/modulos/config/dbiclass.php"){
    header("Location: /cesb/index.php");
}else{
    return [
        'servidor'  => 'localhost',
        'usuario'   => 'root',
        'senha'     => '',
        'banco'     => 'cesb'
    ];  
}