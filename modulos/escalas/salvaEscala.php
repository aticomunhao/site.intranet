<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

date_default_timezone_set('America/Sao_Paulo'); 

function prevMonth($time){
    //return date('Y-m-d', strtotime('-1 month', $time));
    return date('F Y', strtotime('-1 month', $time));
}
    
function nextMonth($time){
    //return date('Y-m-d', strtotime('+1 month', $time));
    return date('F Y', strtotime('+1 month', $time));
}
function prevNumMes($time){
    return date('m', strtotime('-1 month', $time));
}
function nextNumMes($time){
    return date('m', strtotime('+1 month', $time));
}

function CalcTempo($Min){
//    $TotMin = $Min+($Hor*60)+($Dia*1440)+($Mes*43200)+($Ano*518499);
    $Med = floor(($Min));

    $Min = $Med%60;
    $Horas = floor($Med/60);
    $Hor = ($Horas%24);
    $Dias = floor($Horas/24);
    $Dia = $Dias%30;
    $Meses = floor($Dias/30);
    $Mes = $Meses%12;
    $Ano = floor($Meses/12);

    if($Ano == 0){
        $Ano = "";
    }else{
        if($Ano == 1){
            $Ano = "01ano";
        }else{
            $Ano = str_pad($Ano, 2,0, STR_PAD_LEFT)."anos";
        }
    }
    if($Mes == 0){
        $Mes = "";
    }else{
        if($Mes == 1){
            $Mes = "01mês";
        }else{
            $Mes = str_pad($Mes, 2,0, STR_PAD_LEFT)."meses";
        }
    }
    if($Dia == 0){
        $Dia = "";
    }else{
        if($Dia == 1){
            $Dia = "01dia";
        }else{
            $Dia = str_pad($Dia, 2,0, STR_PAD_LEFT)."dias";
        }
    }
    if($Hor == 0){
        $Hor = "00h";
    }else{
        $Hor = str_pad($Hor, 2,0, STR_PAD_LEFT)."h";
    }
    if($Min == 0){
        $Min = "00min";
    }else{
        $Min = str_pad($Min, 2,0, STR_PAD_LEFT)."min";
    }

    $Tempo = $Ano." ".$Mes." ".$Dia." ".$Hor." ".$Min;

    return $Tempo;
}
function CalcHoras($Min, $Conec, $xProj, $Cod, $Mes, $Ano){
    $Med = floor(($Min));
    $Min = $Med%60;
    $Hor = floor($Med/60);
    if($Hor == 0){
        $Hor = "00h";
    }else{
        $Hor = str_pad($Hor, 2,0, STR_PAD_LEFT)."h";
    }
    if($Min == 0){
        $Min = "00min";
        $Min2 = "00";
    }else{
        $Min = str_pad($Min, 2,0, STR_PAD_LEFT)."min";
        $Min2 = "30"; //str_pad($Min, 2,0, STR_PAD_LEFT);
    }
    $Tempo = $Hor." ".$Min;
    $Tempo2 = $Hor.$Min2;

    //Salva em escala_eft
    pg_query($Conec, "UPDATE ".$xProj.".escala_eft SET tempomensal = '$Tempo' WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");

    return $Tempo2;
}

function somaCarga($Conec, $xProj, $Cod, $Mes, $Ano){
    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0000_0030 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row1 = pg_num_rows($rs1);
    $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0030_0100 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row2 = pg_num_rows($rs2);
    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0100_0130 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row3 = pg_num_rows($rs3);
    $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0130_0200 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row4 = pg_num_rows($rs4);
    $rs5 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0200_0230 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row5 = pg_num_rows($rs5);
    $rs6 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0230_0300 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row6 = pg_num_rows($rs6);
    $rs7 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0300_0330 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row7 = pg_num_rows($rs7);
    $rs8 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0330_0400 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row8 = pg_num_rows($rs8);
    $rs9 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0400_0430 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row9 = pg_num_rows($rs9);
    $rs10 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0430_0500 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row10 = pg_num_rows($rs10);
    $rs11 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0500_0530 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row11 = pg_num_rows($rs11);
    $rs12 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0530_0600 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row12 = pg_num_rows($rs12);
    $rs13 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0600_0630 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row13 = pg_num_rows($rs13);
    $rs14 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0630_0700 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row14 = pg_num_rows($rs14);
    $rs15 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0700_0730 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row15 = pg_num_rows($rs15);
    $rs16 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0730_0800 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row16 = pg_num_rows($rs16);
    $rs17 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0800_0830 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row17 = pg_num_rows($rs17);
    $rs18 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0830_0900 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row18 = pg_num_rows($rs18);
    $rs19 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0900_0930 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row19 = pg_num_rows($rs19);
    $rs20 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora0930_1000 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row20 = pg_num_rows($rs20);
    $rs21 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1000_1030 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row21 = pg_num_rows($rs21);
    $rs22 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1030_1100 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row22 = pg_num_rows($rs22);
    $rs23 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1100_1130 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row23 = pg_num_rows($rs23);
    $rs24 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1130_1200 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row24 = pg_num_rows($rs24);
    $rs25 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1200_1230 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row25 = pg_num_rows($rs25);
    $rs26 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1230_1300 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row26 = pg_num_rows($rs26);
    $rs27 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1300_1330 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row27 = pg_num_rows($rs27);
    $rs28 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1330_1400 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row28 = pg_num_rows($rs28);
    $rs29 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1400_1430 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row29 = pg_num_rows($rs29);
    $rs30 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1430_1500 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row30 = pg_num_rows($rs30);
    $rs31 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1500_1530 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row31 = pg_num_rows($rs31);
    $rs32 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1530_1600 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row32 = pg_num_rows($rs32);
    $rs33 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1600_1630 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row33 = pg_num_rows($rs33);
    $rs34 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1630_1700 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row34 = pg_num_rows($rs34);
    $rs35 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1700_1730 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row35 = pg_num_rows($rs35);
    $rs36 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1730_1800 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row36 = pg_num_rows($rs36);
    $rs37 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1800_1830 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row37 = pg_num_rows($rs37);
    $rs38 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1830_1900 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row38 = pg_num_rows($rs38);
    $rs39 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1900_1930 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row39 = pg_num_rows($rs39);
    $rs40 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora1930_2000 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row40 = pg_num_rows($rs40);
    $rs41 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2000_2030 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row41 = pg_num_rows($rs41);
    $rs42 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2030_2100 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row42 = pg_num_rows($rs42);
    $rs43 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2100_2130 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row43 = pg_num_rows($rs43);
    $rs44 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2130_2200 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row44 = pg_num_rows($rs44);
    $rs45 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2200_2230 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row45 = pg_num_rows($rs45);
    $rs46 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2230_2300 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row46 = pg_num_rows($rs46);
    $rs47 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2300_2330 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row47 = pg_num_rows($rs47);
    $rs48 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE hora2330_2400 = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $row48 = pg_num_rows($rs48);
    $Soma = $row1+$row2+$row3+$row4+$row5+$row6+$row7+$row8+$row9+$row10+$row11+$row12+$row13+$row14+$row15+$row16+$row17+$row18+$row19+$row20+$row21+$row22+$row23+$row24
    +$row25+$row26+$row27+$row28+$row29+$row30+$row31+$row32+$row33+$row34+$row35+$row36+$row37+$row38+$row39+$row40+$row41+$row42+$row43+$row44+$row45+$row46+$row47+$row48;
    $Min = ($Soma * 30);

    $Total = CalcHoras($Min, $Conec, $xProj, $Cod, $Mes, $Ano);

    return $Total;
}


if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    if($Acao =="buscadata"){
        $time = filter_input(INPUT_GET, 'dataDia');
        $date = new DateTime("@$time");
        $Dia = $date->format('d-m-Y');   //  $var = $date->format('U = Y-m-d H:i:s'); // U é o timestamp unix

        $admIns = parAdm("insevento", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editevento", $Conec, $xProj); // nível para editar

        $InsEv = 0;
        $EditEv = 0;
        if($_SESSION["AdmUsu"] >= $admIns){
            $InsEv = 1;
        }
        if($_SESSION["AdmUsu"] >= $admEdit){
            $EditEv = 1;
        }
        $var = array("diaClick"=>$Dia, "insEv"=>$InsEv, "editEv"=>$EditEv);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="carregaOpr"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];

        //guarda último mês carregado
        pg_query($Conec, "UPDATE ".$xProj.".paramsis SET guardaescala = CONCAT('$Mes', '/', '$Ano') WHERE idpar = 1 ");

        $Erro = 0;
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 1 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId1 = $tbl[0];
           $Trig1 = $tbl[1];
           $Cor1 = $tbl[2];
           $Tempo1 = $tbl[3];
           $Tempo1 = str_replace('min', '', $Tempo1); // para caber no espaço
           $Tempo1 = str_replace('h ', 'h', $Tempo1);
        }else{
            $Erro = 1;
            $CodId1 = 0;
            $Trig1 = "";
            $Cor1 = "#FFFFFF";
            $Tempo1 = "00h";
        }
        
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 2 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId2 = $tbl[0];
           $Trig2 = $tbl[1];
           $Cor2 = $tbl[2];
           $Tempo2 = $tbl[3];
           $Tempo2 = str_replace('min', '', $Tempo2);
           $Tempo2 = str_replace('h ', 'h', $Tempo2);
        }else{
            $Erro = 1;
            $CodId2 = 0;
            $Trig2 = "";
            $Cor2 = "#FFFFFF";
            $Tempo2 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 3 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId3 = $tbl[0];
           $Trig3 = $tbl[1];
           $Cor3 = $tbl[2];
           $Tempo3 = $tbl[3];
           $Tempo3 = str_replace('min', '', $Tempo3);
           $Tempo3 = str_replace('h ', 'h', $Tempo3);
        }else{
            $Erro = 1;
            $CodId3 = 0;
            $Trig3 = "";
            $Cor3 = "#FFFFFF";
            $Tempo3 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 4 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId4 = $tbl[0];
           $Trig4 = $tbl[1];
           $Cor4 = $tbl[2];
           $Tempo4 = $tbl[3];
           $Tempo4 = str_replace('min', '', $Tempo4);
           $Tempo4 = str_replace('h ', 'h', $Tempo4);
        }else{
            $Erro = 1;
            $CodId4 = 0;
            $Trig4 = "";
            $Cor4 = "#FFFFFF";
            $Tempo4 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 5 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId5 = $tbl[0];
           $Trig5 = $tbl[1];
           $Cor5 = $tbl[2];
           $Tempo5 = $tbl[3];
           $Tempo5 = str_replace('min', '', $Tempo5);
           $Tempo5 = str_replace('h ', 'h', $Tempo5);
        }else{
            $Erro = 1;
            $CodId5 = 0;
            $Trig5 = "";
            $Cor5 = "#FFFFFF";
            $Tempo5 = "00h";
        }

        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 6 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId6 = $tbl[0];
           $Trig6 = $tbl[1];
           $Cor6 = $tbl[2];
           $Tempo6 = $tbl[3];
           $Tempo6 = str_replace('min', '', $Tempo6);
           $Tempo6 = str_replace('h ', 'h', $Tempo6);
        }else{
            $Erro = 1;
            $CodId6 = 0;
            $Trig6 = "";
            $Cor6 = "#FFFFFF";
            $Tempo6 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 7 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId7 = $tbl[0];
           $Trig7 = $tbl[1];
           $Cor7 = $tbl[2];
           $Tempo7 = $tbl[3];
           $Tempo7 = str_replace('min', '', $Tempo7);
           $Tempo7 = str_replace('h ', 'h', $Tempo7);
        }else{
            $Erro = 1;
            $CodId7 = 0;
            $Trig7 = "";
            $Cor7 = "#FFFFFF";
            $Tempo7 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 8 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId8 = $tbl[0];
           $Trig8 = $tbl[1];
           $Cor8 = $tbl[2];
           $Tempo8 = $tbl[3];
           $Tempo8 = str_replace('min', '', $Tempo8);
           $Tempo8 = str_replace('h ', 'h', $Tempo8);
        }else{
            $Erro = 1;
            $CodId8 = 0;
            $Trig8 = "";
            $Cor8 = "#FFFFFF";
            $Tempo8 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 9 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId9 = $tbl[0];
           $Trig9 = $tbl[1];
           $Cor9 = $tbl[2];
           $Tempo9 = $tbl[3];
           $Tempo9 = str_replace('min', '', $Tempo9);
           $Tempo9 = str_replace('h ', 'h', $Tempo9);
        }else{
            $Erro = 1;
            $CodId9 = 0;
            $Trig9 = "";
            $Cor9 = "#FFFFFF";
            $Tempo9 = "00h";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr, oprcor, tempomensal FROM ".$xProj.".escala_eft WHERE opr = 10 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId10 = $tbl[0];
           $Trig10 = $tbl[1];
           $Cor10 = $tbl[2];
           $Tempo10 = $tbl[3];
           $Tempo10 = str_replace('min', '', $Tempo10);
           $Tempo10 = str_replace('h ', 'h', $Tempo10);
        }else{
            $Erro = 1;
            $CodId10 = 0;
            $Trig10 = "";
            $Cor10 = "#FFFFFF";
            $Tempo10 = "00h";
        }

        $var = array("coderro"=>$Erro, "codOpr1"=>$CodId1, "trigr1"=>$Trig1, "cor1"=>$Cor1, "tempo1"=>$Tempo1, "codOpr2"=>$CodId2, "trigr2"=>$Trig2, "cor2"=>$Cor2, "tempo2"=>$Tempo2, "codOpr3"=>$CodId3, "trigr3"=>$Trig3, "cor3"=>$Cor3, "tempo3"=>$Tempo3, "codOpr4"=>$CodId4, "trigr4"=>$Trig4, "cor4"=>$Cor4, "tempo4"=>$Tempo4, "codOpr5"=>$CodId5, "trigr5"=>$Trig5, "cor5"=>$Cor5, "tempo5"=>$Tempo5, "codOpr6"=>$CodId6, "trigr6"=>$Trig6, "cor6"=>$Cor6, "tempo6"=>$Tempo6, "codOpr7"=>$CodId7, "trigr7"=>$Trig7, "cor7"=>$Cor7, "tempo7"=>$Tempo7, "codOpr8"=>$CodId8, "trigr8"=>$Trig8, "cor8"=>$Cor8, "tempo8"=>$Tempo8, "codOpr9"=>$CodId9, "trigr9"=>$Trig9, "cor9"=>$Cor9, "tempo9"=>$Tempo9, "codOpr10"=>$CodId10, "trigr10"=>$Trig10, "cor10"=>$Cor10, "tempo10"=>$Tempo10);

        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="marcaescala"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Dia = filter_input(INPUT_GET, 'dia');
        $Mes = filter_input(INPUT_GET, 'mes');
        $Ano = filter_input(INPUT_GET, 'ano');
        $Coluna = filter_input(INPUT_GET, 'coluna');
        $Data = $Ano."-".$Mes."-".$Dia;

        //verifica se tem alguém na coluna desta data
        $rs0 = pg_query($Conec, "SELECT $Coluna FROM ".$xProj.".escala_adm WHERE dataescala = '$Data'");
        $tbl0 = pg_fetch_row($rs0);
        $CodLocal = (int) $tbl0[0];
        $oprLocal = 0;
        $TotalLocal = 0;

        if($Cod == $CodLocal){ // já estava marcado com o mesmo código - só desmarca
            $rs = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET $Coluna = 0 WHERE dataescala = '$Data'");    
        }
        if($CodLocal == 0){ // não estava marcado = só marca
            $rs = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET $Coluna = $Cod WHERE dataescala = '$Data'");
        }
        if($CodLocal != 0 && $CodLocal != $Cod){ // tinha outro código no local - marca e calcula o tempo do outro que saiu
            $rs = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET $Coluna = $Cod WHERE dataescala = '$Data'");

            $rs1 = pg_query($Conec, "SELECT opr FROM ".$xProj.".escala_eft WHERE poslog_id = $CodLocal And mes = '$Mes' And ano = '$Ano'");
            $tbl1 = pg_fetch_row($rs1);
            $oprLocal = $tbl1[0]; // recalcular o tempo da carga mensal
            somaCarga($Conec, $xProj, $CodLocal, $Mes, $Ano); //salva no arquivo escala_eft

            //pega o tempo que foi salvo na função somaCarga() 
            $rs2 = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = $oprLocal And mes = '$Mes' And ano = '$Ano' ");
            $tbl2 = pg_fetch_row($rs2);
            $Tempoc = $tbl2[0]; 
            $Tempoc = str_replace('min', '', $Tempoc);
            $TotalLocal = str_replace('h ', 'h', $Tempoc);
        }
        if(!$rs){
            $Erro = 1;
        }
        $Total = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano); //salva no arquivo escala_eft

        $Opr = 0;
        $rs50 = pg_query($Conec, "SELECT opr FROM ".$xProj.".escala_eft WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");
        $tbl50 = pg_fetch_row($rs50);
        $Opr = $tbl50[0];

        $var = array("coderro"=>$Erro, "codigo"=>$Cod, "codigolocal"=>$CodLocal, "tempototal"=>$Total, "opr"=>$Opr, "tempototallocal"=>$TotalLocal, "oprlocal"=>$oprLocal);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="salvaOpr"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Opr = filter_input(INPUT_GET, 'opr');
        $Cor = filter_input(INPUT_GET, 'cor');
        $Trigr = filter_input(INPUT_GET, 'trigr');

        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];

        $rs0 = pg_query($Conec, "SELECT poslog_id FROM ".$xProj.".escala_eft WHERE poslog_id  = $Cod And ano = '$Ano' And mes = '$Mes'");
        $row0 = pg_num_rows($rs0);
        if($row0 > 1){ // tem mais de um no mesmo mês
            $Erro = 2;
        }else{
            if($row0 > 0){ // já tem
                 $rs1 = pg_query($Conec, "UPDATE ".$xProj.".escala_eft SET opr = $Opr, oprcor = '$Cor', trigr = UPPER('$Trigr'), ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");
            }else{ // não tem 
                $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escala_eft");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $CodigoNovo = ($Codigo+1);
                $rs1 = pg_query($Conec, "INSERT INTO ".$xProj.".escala_eft (id, mes, ano, poslog_id, opr, oprcor, trigr, ativo, usuins, datains) 
                VALUES ($CodigoNovo, '$Mes', '$Ano', $Cod, $Opr, '$Cor', UPPER('$Trigr'), 1, ".$_SESSION["usuarioID"].", NOW())");
            }
            if(!$rs1){
                $Erro = 1;
            }
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="apagadatas"){
        $Erro = 0;
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET 
        hora0000_0030 = 0, hora0030_0100 = 0, hora0100_0130 = 0, hora0130_0200 = 0, hora0200_0230 = 0,
        hora0230_0300 = 0, hora0300_0330 = 0, hora0330_0400 = 0, hora0400_0430 = 0, hora0430_0500 = 0, hora0500_0530 = 0, hora0530_0600 = 0, hora0600_0630 = 0, hora0630_0700 = 0, 
        hora0700_0730 = 0, hora0730_0800 = 0, hora0800_0830 = 0, hora0830_0900 = 0, hora0900_0930 = 0, hora0930_1000 = 0, hora1000_1030 = 0, hora1030_1100 = 0, hora1100_1130 = 0, hora1130_1200 = 0, 
        hora1200_1230 = 0, hora1230_1300 = 0, hora1300_1330 = 0, hora1330_1400 = 0, hora1400_1430 = 0, hora1430_1500 = 0, hora1500_1530 = 0, hora1530_1600 = 0, hora1600_1630 = 0, hora1630_1700 = 0, 
        hora1700_1730 = 0, hora1730_1800 = 0, hora1800_1830 = 0, hora1830_1900 = 0, hora1900_1930 = 0, hora1930_2000 = 0, hora2000_2030 = 0, hora2030_2100 = 0, hora2100_2130 = 0, hora2130_2200 = 0, 
        hora2200_2230 = 0, hora2230_2300 = 0, hora2300_2330 = 0, hora2330_2400 = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() 
        WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="apagaparticip"){
        $Erro = 0;
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $rs1 = pg_query($Conec, "DELETE FROM ".$xProj.".escala_eft WHERE mes = '$Mes' And ano= '$Ano' ");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="marcadia"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Dia = filter_input(INPUT_GET, 'dia');
        $Mes = filter_input(INPUT_GET, 'mes');
        $Ano = filter_input(INPUT_GET, 'ano');
        $Data = $Ano."-".$Mes."-".$Dia;

        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET 
        hora0800_0830 = $Cod, hora0830_0900 = $Cod, hora0900_0930 = $Cod, hora0930_1000 = $Cod, hora1000_1030 = $Cod, hora1030_1100 = $Cod, hora1100_1130 = $Cod, hora1130_1200 = $Cod, 
        hora1400_1430 = $Cod, hora1430_1500 = $Cod, hora1500_1530 = $Cod, hora1530_1600 = $Cod, hora1600_1630 = $Cod, hora1630_1700 = $Cod, 
        hora1700_1730 = $Cod, hora1730_1800 = $Cod, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() 
        WHERE dataescala = '$Data' ");
        if(!$rs1){
            $Erro = 1;
        }

        $Tempo = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano); //salva no arquivo escala_eft
        $Opr = 0;
        $rsOpr = pg_query($Conec, "SELECT opr FROM ".$xProj.".escala_eft WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");
        $tblOpr = pg_fetch_row($rsOpr);
        $Opr = $tblOpr[0];

        //salva as cargas horárias em cada um do mes/ano
        $rs3 = pg_query($Conec, "SELECT poslog_id, opr FROM ".$xProj.".escala_eft WHERE mes = '$Mes' And ano= '$Ano' ");
        while($tbl3 = pg_fetch_row($rs3)){
            $CodBusca = $tbl3[0];
            somaCarga($Conec, $xProj, $CodBusca, $Mes, $Ano); //salva no arquivo escala_eft
        }

        $Tempo1 = 0;
        $Tempo2 = 0;
        $Tempo3 = 0;
        $Tempo4 = 0;
        $Tempo5 = 0;
        $Tempo6 = 0;
        $Tempo7 = 0;
        $Tempo8 = 0;
        $Tempo9 = 0;
        $Tempo10 = 0;

        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 1 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo1 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 2 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo2 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 3 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo3 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 4 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo4 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 5 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo5 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 6 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo6 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 7 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo7 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 8 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo8 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 9 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo9 = str_replace('h ', 'h', $Tempoc);
        }
        $rs = pg_query($Conec, "SELECT tempomensal FROM ".$xProj.".escala_eft WHERE opr = 10 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Tempoc = $tbl[0];
            $Tempoc = str_replace('min', '', $Tempoc);
            $Tempo10 = str_replace('h ', 'h', $Tempoc);
        }

        $var = array("coderro"=>$Erro, "data"=>$Data, "codigo"=>$Cod, "cargaEscala"=>$Tempo, "opr"=>$Opr, "tempo1"=>$Tempo1, "tempo2"=>$Tempo2, "tempo3"=>$Tempo3, "tempo4"=>$Tempo4, "tempo5"=>$Tempo5, "tempo6"=>$Tempo6, "tempo7"=>$Tempo7, "tempo8"=>$Tempo8, "tempo9"=>$Tempo9, "tempo10"=>$Tempo10);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="calculatempos"){
        $Erro = 0;
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $Tempo1 = "00h";
        $Tempo2 = "00h";
        $Tempo3 = "00h";
        $Tempo4 = "00h";
        $Tempo5 = "00h";
        $Tempo6 = "00h";
        $Tempo7 = "00h";
        $Tempo8 = "00h";
        $Tempo9 = "00h";
        $Tempo10 = "00h";

        $rs = pg_query($Conec, "SELECT poslog_id, opr FROM ".$xProj.".escala_eft WHERE mes = '$Mes' And ano = '$Ano' And ativo = 1 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0];
                $Opr = $tbl[1];
                if($Opr == 1){
                    $Tempo1 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano); // salva em escala_eft
                }
                if($Opr == 2){
                    $Tempo2 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 3){
                    $Tempo3 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 4){
                    $Tempo4 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 5){
                    $Tempo5 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 6){
                    $Tempo6 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 7){
                    $Tempo7 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 8){
                    $Tempo8 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 9){
                    $Tempo9 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
                if($Opr == 10){
                    $Tempo10 = somaCarga($Conec, $xProj, $Cod, $Mes, $Ano);
                }
            }
        }

        $rs1 = pg_query($Conec, "SELECT opr, tempomensal FROM ".$xProj.".escala_eft WHERE mes = '$Mes' And ano = '$Ano' And ativo = 1 ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1)){
                $Opr = $tbl1[0];
                if($Opr == 1){
                    $Tempo1 = $tbl1[1];
                }
                if($Opr == 2){
                    $Tempo2 = $tbl1[1];
                }
                if($Opr == 3){
                    $Tempo3 = $tbl1[1];
                }
                if($Opr == 4){
                    $Tempo4 = $tbl1[1];
                }
                if($Opr == 5){
                    $Tempo5 = $tbl1[1];
                }
                if($Opr == 6){
                    $Tempo6 = $tbl1[1];
                }
                if($Opr == 7){
                    $Tempo7 = $tbl1[1];
                }
                if($Opr == 8){
                    $Tempo8 = $tbl1[1];
                }
                if($Opr == 9){
                    $Tempo9 = $tbl1[1];
                }
                if($Opr == 10){
                    $Tempo10 = $tbl1[1];
                }
            }
       }
        $var = array("coderro"=>$Erro, "tempo1"=>$Tempo1, "tempo2"=>$Tempo2, "tempo3"=>$Tempo3, "tempo4"=>$Tempo4, "tempo5"=>$Tempo5, "tempo6"=>$Tempo6, "tempo7"=>$Tempo7, "tempo8"=>$Tempo8, "tempo9"=>$Tempo9, "tempo10"=>$Tempo10 );
        $responseText = json_encode($var);
        echo $responseText;
    }
    
}