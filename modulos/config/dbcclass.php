<?php
$url = $_SERVER["PHP_SELF"];
$urlIni = "/site.intranet/";
if(strcmp($url, $urlIni.'modulos/config/dbcclass.php') == 0){
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: ../../index.php"); // OK
}else{

    function conecPost(){
        if($_SERVER['HTTP_HOST'] == "localhost"){
            $con_string = "host = localhost port=5432 dbname=cesb user=postgres password=postgres";
        }else{
            $con_string = "host = 192.168.1.242 port=5432 dbname=cesb user=postgres password=scga2298";
        }
        if(function_exists("pg_pconnect")){ // para o caso de a extension=pgsql não estar habilitada no phpini
            if(@pg_connect($con_string)){
                $Con = @pg_connect($con_string) or die("Não foi possível conectar-se ao banco de dados.");
            }else{
                $Con = "sConec";
            }
        }else{
            $Con = "sFunc";
        }
        return $Con;
    }

    function conecPes(){
        if($_SERVER['HTTP_HOST'] == "localhost"){
            $con_stringpes = "host = localhost port=5432 dbname=pessoal user=postgres password=postgres";
        }else{
            $con_stringpes = "host= 192.168.1.242 port=5432 dbname=pessoal user=postgres password=scga2298";
        }
        if(function_exists("pg_pconnect")){ // para o caso de a extension=pgsql não estar habilitada no phpini
            if(@pg_connect($con_stringpes)){
                $Conpes = @pg_connect($con_stringpes) or die("Não foi possível conectar-se ao banco de dados.");
            }else{
                $Conpes = "sConec";
            }
        }else{
            $Conpes = "sFunc";
        }
        return $Conpes;
    }
}