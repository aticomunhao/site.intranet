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
            td.other-month {
                opacity: .5;
                text-align: center;
            }
            .quadrinho {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
            }
            .quadroEscolha {
                position: relative; float: left; 
                border: 1px solid; border-radius: 5px; 
                text-align: center; 
                padding: 5px; width: 40px;
            }
            
            .modalEngr{
            display: none; /* Hidden por default */
            position: fixed;
            z-index: 200;
            left: 0;
            top: 0;
            width: 100%; /* largura total */
            height: 100%; /* altura total */
            overflow: auto; /* autoriza scroll se necessário */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
         }
         .modalEngr-content{
            background-color: transparent;
            margin: 20% auto; /* 15% do topo e centrado */
            text-align: center;
            width: 10%; /* acertar de acordo com a tela */
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
                document.getElementById("selecMesAno").value = document.getElementById("guardamesano").value;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=carregaOpr&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                document.getElementById("corOpr01").innerHTML = Resp.trigr1;
                                document.getElementById("corOpr01Prev").innerHTML = Resp.trigr1;
                                document.getElementById("corOpr02").innerHTML = Resp.trigr2;
                                document.getElementById("corOpr02Prev").innerHTML = Resp.trigr2;
                                document.getElementById("corOpr03").innerHTML = Resp.trigr3;
                                document.getElementById("corOpr03Prev").innerHTML = Resp.trigr3;
                                document.getElementById("corOpr04").innerHTML = Resp.trigr4;
                                document.getElementById("corOpr04Prev").innerHTML = Resp.trigr4;
                                document.getElementById("corOpr05").innerHTML = Resp.trigr5;
                                document.getElementById("corOpr05Prev").innerHTML = Resp.trigr5;
                                document.getElementById("corOpr06").innerHTML = Resp.trigr6;
                                document.getElementById("corOpr06Prev").innerHTML = Resp.trigr6;
                                document.getElementById("corOpr07").innerHTML = Resp.trigr7;
                                document.getElementById("corOpr07Prev").innerHTML = Resp.trigr7;
                                document.getElementById("corOpr08").innerHTML = Resp.trigr8;
                                document.getElementById("corOpr08Prev").innerHTML = Resp.trigr8;
                                document.getElementById("corOpr09").innerHTML = Resp.trigr9;
                                document.getElementById("corOpr09Prev").innerHTML = Resp.trigr9;
                                document.getElementById("corOpr10").innerHTML = Resp.trigr10;
                                document.getElementById("corOpr10Prev").innerHTML = Resp.trigr10;
                                document.getElementById("selecOpr1").value = Resp.codOpr1;
                                document.getElementById("selecOpr2").value = Resp.codOpr2;
                                document.getElementById("selecOpr3").value = Resp.codOpr3;
                                document.getElementById("selecOpr4").value = Resp.codOpr4;
                                document.getElementById("selecOpr5").value = Resp.codOpr5;
                                document.getElementById("selecOpr6").value = Resp.codOpr6;
                                document.getElementById("selecOpr7").value = Resp.codOpr7;
                                document.getElementById("selecOpr8").value = Resp.codOpr8;
                                document.getElementById("selecOpr9").value = Resp.codOpr9;
                                document.getElementById("selecOpr10").value = Resp.codOpr10;
                            }
                        }
                    };
                    ajax.send(null);
                }
                $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));

                

                modalMostra = document.getElementById('relacMostraEscala'); //span[0]
                spanMostra = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalMostra){
                        modalMostra.style.display = "none";
                    }
                };

                $("#selecMesAno").change(function(){
                    if(document.getElementById("selecMesAno").value != ""){
                        document.getElementById("engrenagem").style.display = "block";
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=carregaOpr&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr01").innerHTML = Resp.trigr1;
                                        document.getElementById("corOpr01Prev").innerHTML = Resp.trigr1;
                                        document.getElementById("corOpr02").innerHTML = Resp.trigr2;
                                        document.getElementById("corOpr02Prev").innerHTML = Resp.trigr2;
                                        document.getElementById("corOpr03").innerHTML = Resp.trigr3;
                                        document.getElementById("corOpr03Prev").innerHTML = Resp.trigr3;
                                        document.getElementById("corOpr04").innerHTML = Resp.trigr4;
                                        document.getElementById("corOpr04Prev").innerHTML = Resp.trigr4;
                                        document.getElementById("corOpr05").innerHTML = Resp.trigr5;
                                        document.getElementById("corOpr05Prev").innerHTML = Resp.trigr5;
                                        document.getElementById("corOpr06").innerHTML = Resp.trigr6;
                                        document.getElementById("corOpr06Prev").innerHTML = Resp.trigr6;
                                        document.getElementById("corOpr07").innerHTML = Resp.trigr7;
                                        document.getElementById("corOpr07Prev").innerHTML = Resp.trigr7;
                                        document.getElementById("corOpr08").innerHTML = Resp.trigr8;
                                        document.getElementById("corOpr08Prev").innerHTML = Resp.trigr8;
                                        document.getElementById("corOpr09").innerHTML = Resp.trigr9;
                                        document.getElementById("corOpr09Prev").innerHTML = Resp.trigr9;
                                        document.getElementById("corOpr10").innerHTML = Resp.trigr10;
                                        document.getElementById("corOpr10Prev").innerHTML = Resp.trigr10;
                                        document.getElementById("selecOpr1").value = Resp.codOpr1;
                                        document.getElementById("selecOpr2").value = Resp.codOpr2;
                                        document.getElementById("selecOpr3").value = Resp.codOpr3;
                                        document.getElementById("selecOpr4").value = Resp.codOpr4;
                                        document.getElementById("selecOpr5").value = Resp.codOpr5;
                                        document.getElementById("selecOpr6").value = Resp.codOpr6;
                                        document.getElementById("selecOpr7").value = Resp.codOpr7;
                                        document.getElementById("selecOpr8").value = Resp.codOpr8;
                                        document.getElementById("selecOpr9").value = Resp.codOpr9;
                                        document.getElementById("selecOpr10").value = Resp.codOpr10;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                        $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                        document.getElementById("engrenagem").style.display = "none";
                    }
                });

                $("#selecOpr1").change(function(){
                    if(document.getElementById("selecOpr1").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=1&codigo="+document.getElementById("selecOpr1").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr01").innerHTML = Resp.trigrama;

                                        document.getElementById("corOtrigramaoprpr01").value = Resp.trigrama;

                                        document.getElementById("corOpr01Prev").innerHTML = Resp.trigrama;

                                        
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });

                $("#selecOpr2").change(function(){
                    if(document.getElementById("selecOpr2").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=2&codigo="+document.getElementById("selecOpr2").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr02").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr02Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr3").change(function(){
                    if(document.getElementById("selecOpr3").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=3&codigo="+document.getElementById("selecOpr3").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr03").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr03Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr4").change(function(){
                    if(document.getElementById("selecOpr4").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=4&codigo="+document.getElementById("selecOpr4").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr04").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr04Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr5").change(function(){
                    if(document.getElementById("selecOpr5").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=5&codigo="+document.getElementById("selecOpr5").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr05").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr05Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr6").change(function(){
                    if(document.getElementById("selecOpr6").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=6&codigo="+document.getElementById("selecOpr6").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr06").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr06Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr7").change(function(){
                    if(document.getElementById("selecOpr7").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=7&codigo="+document.getElementById("selecOpr7").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr07").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr07Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr8").change(function(){
                    if(document.getElementById("selecOpr8").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=8&codigo="+document.getElementById("selecOpr8").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr08").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr08Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr9").change(function(){
                    if(document.getElementById("selecOpr9").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=9&codigo="+document.getElementById("selecOpr9").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr09").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr09Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });
                $("#selecOpr10").change(function(){
                    if(document.getElementById("selecOpr10").value != ""){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=buscaOpr&opr=10&codigo="+document.getElementById("selecOpr10").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText); 
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("corOpr10").innerHTML = Resp.trigrama;
                                        document.getElementById("corOpr10Prev").innerHTML = Resp.trigrama;
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });


            }); // fim do ready

 
            function validaData (valor) { // tks ao Arthur Ronconi  - https://devarthur.com/blog/funcao-para-validar-data-em-javascript
                // Verifica se a entrada é uma string
                if (typeof valor !== 'string') {
                    return false;
                }
                // Verifica formado da data
                if (!/^\d{2}\/\d{2}\/\d{4}$/.test(valor)) {
                    return false;
                }
                // Divide a data para o objeto "data"
                const partesData = valor.split('/')
                const data = { 
                    dia: partesData[0], 
                    mes: partesData[1], 
                    ano: partesData[2] 
                }
                // Converte strings em número
                const dia = parseInt(data.dia);
                const mes = parseInt(data.mes);
                const ano = parseInt(data.ano);
                // Dias de cada mês, incluindo ajuste para ano bissexto
                const diasNoMes = [ 0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
                // Atualiza os dias do mês de fevereiro para ano bisexto
                if (ano % 400 === 0 || ano % 4 === 0 && ano % 100 !== 0) {
                    diasNoMes[2] = 29
                }
                // Regras de validação:
                // Mês deve estar entre 1 e 12, e o dia deve ser maior que zero
                if (mes < 1 || mes > 12 || dia < 1) {
                    return false;
                }else if (dia > diasNoMes[mes]) { // Valida número de dias do mês
                    return false;
                }
                return true // Passou nas validações
            }

            function pegaCor(Cor, Opr){
                document.getElementById("guardacor").value = Cor;
                document.getElementById("guardaOpr").value = document.getElementById("selecOpr"+Opr).value;
                document.getElementById("guardatrigrama").value = document.getElementById("corOpr0"+Opr).innerHTML;


//                document.getElementById("guardaid").value = document.getElementById("selecOpr1").value;
            }

            function insereCor(id, Dia, Mes, Ano, Hora){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=marcaescala&codigo="+document.getElementById("guardaOpr").value
                    +"&dia="+Dia
                    +"&mes="+Mes
                    +"&ano="+Ano
                    +"&coluna="+Hora
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(document.getElementById(id).style.backgroundColor == document.getElementById("guardacor").value){
                                    document.getElementById(id).style.backgroundColor = "#FFFFFF"; // fundo branco
                                    document.getElementById(id).innerHTML = "&nbsp;"; // espaço
                                }else{
                                    document.getElementById(id).style.backgroundColor = document.getElementById("guardacor").value;
                                    document.getElementById(id).innerHTML = document.getElementById("guardatrigrama").value;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function fechaModal(){
                document.getElementById("relacMostraEscala").style.display = "none";
            }
            function abreModal(){
                document.getElementById("relacMostraEscala").style.display = "block";
            }
        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'livroreg'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_adm");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_adm (
            id SERIAL PRIMARY KEY, 
            hora0000_0030 BIGINT NOT NULL DEFAULT 0, 
            hora0030_0100 BIGINT NOT NULL DEFAULT 0, 
            hora0100_0130 BIGINT NOT NULL DEFAULT 0, 
            hora0130_0200 BIGINT NOT NULL DEFAULT 0, 
            hora0200_0230 BIGINT NOT NULL DEFAULT 0, 
            hora0230_0300 BIGINT NOT NULL DEFAULT 0, 
            hora0300_0330 BIGINT NOT NULL DEFAULT 0, 
            hora0330_0400 BIGINT NOT NULL DEFAULT 0, 
            hora0400_0430 BIGINT NOT NULL DEFAULT 0,   
            hora0430_0500 BIGINT NOT NULL DEFAULT 0,  
            hora0500_0530 BIGINT NOT NULL DEFAULT 0,  
            hora0530_0600 BIGINT NOT NULL DEFAULT 0,  
            hora0600_0630 BIGINT NOT NULL DEFAULT 0,  
            hora0630_0700 BIGINT NOT NULL DEFAULT 0, 
            hora0700_0730 BIGINT NOT NULL DEFAULT 0, 
            hora0730_0800 BIGINT NOT NULL DEFAULT 0, 
            hora0800_0830 BIGINT NOT NULL DEFAULT 0, 
            hora0830_0900 BIGINT NOT NULL DEFAULT 0, 
            hora0900_0930 BIGINT NOT NULL DEFAULT 0, 
            hora0930_1000 BIGINT NOT NULL DEFAULT 0, 
            hora1000_1030 BIGINT NOT NULL DEFAULT 0, 
            hora1030_1100 BIGINT NOT NULL DEFAULT 0, 
            hora1100_1130 BIGINT NOT NULL DEFAULT 0, 
            hora1130_1200 BIGINT NOT NULL DEFAULT 0, 
            hora1200_1230 BIGINT NOT NULL DEFAULT 0, 
            hora1230_1300 BIGINT NOT NULL DEFAULT 0, 
            hora1300_1330 BIGINT NOT NULL DEFAULT 0, 
            hora1330_1400 BIGINT NOT NULL DEFAULT 0, 
            hora1400_1430 BIGINT NOT NULL DEFAULT 0, 
            hora1430_1500 BIGINT NOT NULL DEFAULT 0, 
            hora1500_1530 BIGINT NOT NULL DEFAULT 0, 
            hora1530_1600 BIGINT NOT NULL DEFAULT 0, 
            hora1600_1630 BIGINT NOT NULL DEFAULT 0, 
            hora1630_1700 BIGINT NOT NULL DEFAULT 0, 
            hora1700_1730 BIGINT NOT NULL DEFAULT 0, 
            hora1730_1800 BIGINT NOT NULL DEFAULT 0, 
            hora1800_1830 BIGINT NOT NULL DEFAULT 0, 
            hora1830_1900 BIGINT NOT NULL DEFAULT 0, 
            hora1900_1930 BIGINT NOT NULL DEFAULT 0, 
            hora1930_2000 BIGINT NOT NULL DEFAULT 0, 
            hora2000_2030 BIGINT NOT NULL DEFAULT 0, 
            hora2030_2100 BIGINT NOT NULL DEFAULT 0, 
            hora2100_2130 BIGINT NOT NULL DEFAULT 0, 
            hora2130_2200 BIGINT NOT NULL DEFAULT 0, 
            hora2200_2230 BIGINT NOT NULL DEFAULT 0, 
            hora2230_2300 BIGINT NOT NULL DEFAULT 0, 
            hora2300_2330 BIGINT NOT NULL DEFAULT 0, 
            hora2330_2400 BIGINT NOT NULL DEFAULT 0, 
            pessoas_id BIGINT NOT NULL DEFAULT 0,
            dataescala date DEFAULT '3000-12-31',
            ativo smallint DEFAULT 1 NOT NULL, 
            usuins integer DEFAULT 0 NOT NULL,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit integer DEFAULT 0 NOT NULL,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            ) 
         ");

//         pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_eft");
         pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_eft (
            id SERIAL PRIMARY KEY, 
            mes VARCHAR(2),
            ano VARCHAR(4),
            opr smallint NOT NULL DEFAULT 0, 
            oprcor VARCHAR(10) DEFAULT '#FFFFFF',
            poslog_id BIGINT NOT NULL DEFAULT 0,
            trigr VARCHAR(4), 
            ativo smallint DEFAULT 1 NOT NULL, 
            usuins integer DEFAULT 0 NOT NULL,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit integer DEFAULT 0 NOT NULL,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            ) 
         ");

         $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_eft LIMIT 3");
         $row = pg_num_rows($rs);
         if($row == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escala_eft (mes, ano, opr, oprcor, poslog_id, trigr, ativo, usuins, datains) VALUES ('07', '2024', 1, 'red', 153, 'PIC', 1, 153, NOW() )");
            pg_query($Conec, "INSERT INTO ".$xProj.".escala_eft (mes, ano, opr, oprcor, poslog_id, trigr, ativo, usuins, datains) VALUES ('07', '2024', 2, 'blue', 6, 'PEN', 1, 153, NOW() )");
            pg_query($Conec, "INSERT INTO ".$xProj.".escala_eft (mes, ano, opr, oprcor, poslog_id, trigr, ativo, usuins, datains) VALUES ('07', '2024', 3, 'green', 33, 'ARI', 1, 153, NOW() )");
            pg_query($Conec, "INSERT INTO ".$xProj.".escala_eft (mes, ano, opr, oprcor, poslog_id, trigr, ativo, usuins, datains) VALUES ('07', '2024', 4, 'yellow', 30, 'POR', 1, 153, NOW() )");
         }

         date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg
         require_once("../calendario/functions.php");
         $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
         $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for
         $ParamIni = date("n/Y"); // n - repres o mês sem os zeros iniciais

         //Mantem a tabela pelo menos 60 dias na frente, considerando que o loop pode começar no fim do mês
         for($i = 0; $i < 90; $i++){
            $Amanha = strtotime("+1 day", $DiaIni);
            $DiaIni = $Amanha;
            $Data = date("Y/m/d", $Amanha); // data legível
            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE dataescala = '$Data' ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".escala_adm (dataescala) VALUES ('$Data')");
            }
         }
         echo "<br>";


//         $mesAno = date("F Y", $monthTime);
         $Ingl = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
         $Port = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
//         $Trad = str_replace($Ingl, $Port, $mesAno);
//         $startDate = strtotime("last sunday", $monthTime);
         
//    $admIns = parAdm("insevento", $Conec, $xProj);   // nível para inserir evento no calendário
//    $admEdit = parAdm("editevento", $Conec, $xProj); // nível para editar evento no calendário


        $OpUsuAnt = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE lro = 1 And Ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomecompl"); // And codsetor = 
        $OpcoesEscMes = pg_query($Conec, "SELECT EXTRACT(MONTH FROM ".$xProj.".escala_adm.dataescala)::text ||'/'|| EXTRACT(YEAR FROM ".$xProj.".escala_adm.dataescala)::text 
        FROM ".$xProj.".escala_adm GROUP BY 1 ORDER BY 1 DESC ");

        $OpUsu01 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu02 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu03 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu04 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu05 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu06 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu07 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu08 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu09 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
//        $OpUsu10 = pg_query($Conec, "SELECT poslog_id, nomecompl FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escala_eft ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escala_eft.poslog_id WHERE ".$xProj.".escala_eft.ativo = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomecompl"); 
        $OpUsu10 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl"); 
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardacor" value="" />
        <input type="hidden" id="guardamesano" value="<?php echo $ParamIni; ?>" />
        <input type="hidden" id="guardaid" value="" />
        <input type="hidden" id="guardaOpr" value="" />
        <input type="hidden" id="guardatrigrama" value="&nbsp;" />
        
        <label>Selecione o mês: </label>
        <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
            <option value=""></option>
                <?php 
                    if($OpcoesEscMes){
                        while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                            <?php 
                        }
                    }
                ?>
        </select>
        <div style="position: relative; float: right; padding-right: 20px;">
            <button class="botpadrblue" onclick="abreModal();">Participantes</button>
        </div>

        <div style="position: fixed; top: 105px; left: 300px; background-color: white; opacity: .8; margin: 20px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">

            <table style="margin: 0 auto; width: 60%;">
                <tr>
                    <td><div id="corOpr01" class="box quadroEscolha" style="background-color: red;" onclick="pegaCor('red', '1');">XXX</div></td>
                    <td><div id="corOpr02" class="box quadroEscolha" style="background-color: blue;" onclick="pegaCor('blue', '2');">XXX</div></td>
                    <td><div id="corOpr03" class="box quadroEscolha" style="background-color: yellow;" onclick="pegaCor('yellow', '3');">XXX</div></td>
                    <td><div id="corOpr04" class="box quadroEscolha" style="background-color: green;" onclick="pegaCor('green', '4');">XXX</div></td>
                    <td><div id="corOpr05" class="box quadroEscolha" style="background-color: #FF00FF;" onclick="pegaCor('#FF00FF', '5');">XXX</div></td>
                    <td><div id="corOpr06" class="box quadroEscolha" style="background-color: #FF9933;" onclick="pegaCor('#FF9933', '6');">XXX</div></td>
                    <td><div id="corOpr07" class="box quadroEscolha" style="background-color: #CC9966;" onclick="pegaCor('#CC9966', '7');">XXX</div></td>
                    <td><div id="corOpr08" class="box quadroEscolha" style="background-color: #99CC66;" onclick="pegaCor('#99CC66', '8');">XXX</div></td>
                    <td><div id="corOpr09" class="box quadroEscolha" style="background-color: #6633CC;" onclick="pegaCor('#6633CC', '9');">XXX</div></td>
                    <td><div id="corOpr10" class="box quadroEscolha" style="background-color: #009933;" onclick="pegaCor('#009933', '10');">XXX</div></td>
                </tr>
            </table>
        </div>

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                <td>
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro"></div>
                        <div class="col quadro" style="text-align: center;"></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="position: relative; float: rigth; text-align: right;"></div> 
                    </div>
                </div>
                    </td>
                </tr>
            </table>
            <div id="escala"></div>
        </div>

        <!-- div modal para registrar leitura  -->
        <div id="relacMostraEscala" class="relacmodal">
            <div class="modal-content-Escala">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h4 id="titulomodal" style="text-align: center; color: #666;">Participantes da Escala</h4>
                <div style="border: 2px solid blue; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td colspan="2" class="etiq aCentro">A</td>
                        <td colspan="2" class="etiq aCentro">B</td>
                        <td colspan="2" class="etiq aCentro">C</td>
                        <td colspan="2" class="etiq aCentro">D</td>
                        <td colspan="2" class="etiq aCentro">E</td>
                        <td colspan="2" class="etiq aCentro">F</td>
                        <td colspan="2" class="etiq aCentro">G</td>
                        <td colspan="2" class="etiq aCentro">H</td>
                        <td colspan="2" class="etiq aCentro">I</td>
                        <td colspan="2" class="etiq aCentro">J</td>
                    </tr>
                    <tr>
                        <td>
                            <select id="selecOpr1" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu01){
                                        while ($Opcoes = pg_fetch_row($OpUsu01)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>


                            <!--
                            <input type="color" list="presetColors">
                            <datalist id="presetColors">
                                <option>#ff0000</option>/>
                                <option>#00ff00</option>
                                <option>#0000ff</option>
                            </datalist>
                            -->

                        </td>


                        <td><div id="corOpr01Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: red;" onclick="pegaCor('red', '1');">XXX</div></td>

       
                        <td>
                        <select id="selecOpr2" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu02){
                                        while ($Opcoes = pg_fetch_row($OpUsu02)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr02Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: blue;" onclick="pegaCor('blue', '2');">XXX</div></td>
                        <td>
                        <select id="selecOpr3" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu03){
                                        while ($Opcoes = pg_fetch_row($OpUsu03)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr03Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: yellow;" onclick="pegaCor('yellow', '3');">XXX</div></td>

                        <td>
                        <select id="selecOpr4" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu04){
                                        while ($Opcoes = pg_fetch_row($OpUsu04)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr04Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: green;" onclick="pegaCor('green', '4');">XXX</div></td>

                        <td>
                        <select id="selecOpr5" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu05){
                                        while ($Opcoes = pg_fetch_row($OpUsu05)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr05Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #FF00FF;" onclick="pegaCor('#FF00FF', '5');">XXX</div></td>

                        <td>
                        <select id="selecOpr6" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu06){
                                        while ($Opcoes = pg_fetch_row($OpUsu06)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr06Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #FF9933;" onclick="pegaCor('#FF00FF', '6');">XXX</div></td>

                        <td>
                        <select id="selecOpr7" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu07){
                                        while ($Opcoes = pg_fetch_row($OpUsu07)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr07Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #CC9966;" onclick="pegaCor('#FF00FF', '7');">XXX</div></td>

                        <td>
                        <select id="selecOpr8" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu08){
                                        while ($Opcoes = pg_fetch_row($OpUsu08)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr08Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #99CC66;" onclick="pegaCor('#FF00FF', '8');">XXX</div></td>

                        <td>
                        <select id="selecOpr9" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu09){
                                        while ($Opcoes = pg_fetch_row($OpUsu09)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr09Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #6633CC;" onclick="pegaCor('#FF00FF', '9');">XXX</div></td>

                        <td>
                        <select id="selecOpr10" style="font-size: 1rem; width: 28px;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu10){
                                        while ($Opcoes = pg_fetch_row($OpUsu10)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                        </td>
                        <td><div id="corOpr10Prev" class="box" style="position: relative; float: left; border: 1px solid; border-radius: 5px; text-align: center; padding: 5px; width: 40px; background-color: #009933;">XXX</div></td>

                    </tr>
                </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <div id="engrenagem" class="modalEngr" style="display: none;">
         <div class="modalEngr-content">
            <img src="imagens/Engrenagens.gif" width="70" height="50" draggable="false" style="padding-top: 2px; padding-left: 2px;"/>
         </div>
      </div>

    </body>
</html>