<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .modal-content-selecParticip{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
            .modal-content-relacParticip{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
            }
            .modalMsg-content-Escala{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 7% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
            }
            .quadrinho {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }
            .quadrinhoClick {
                font-size: 90%;
                min-width: 50px;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: pointer;
            }
            .quadrgrupo {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }

        </style>
        <script>
             function ajaxIni(){
                try{
                ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                catch(e){
                try{
                   ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                   catch(ex) {
                   try{
                       ajax = new XMLHttpRequest();}
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }
            $(document).ready(function(){


            });


            function foco(id){
                document.getElementById(id).focus();
            }

        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg

        //Provisório
        //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves (
            id SERIAL PRIMARY KEY, 
            chavenum integer NOT NULL DEFAULT 0,
            chavenumcompl VARCHAR(5),
            chavelocal VARCHAR(50),
            chavesala VARCHAR(50),
            chaveobs text, 
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");

        //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves_ctl (
            id SERIAL PRIMARY KEY, 
            datasaida timestamp without time zone DEFAULT '3000-12-31',
            datavolta timestamp without time zone DEFAULT '3000-12-31',
            funcentrega bigint NOT NULL DEFAULT 0,
            funcrecebe bigint NOT NULL DEFAULT 0,
            usuretira bigint NOT NULL DEFAULT 0,
            usudevolve bigint NOT NULL DEFAULT 0,
            cpfretira VARCHAR(20),
            cpfdevolve VARCHAR(20),
            telef VARCHAR(20),
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");
        
        
//

        ?>
 
    </body>
</html>