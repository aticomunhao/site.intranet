<?php
//Monta a lista lateral esquerda
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
$monthTime = $_REQUEST["monthTime"];
$startDate = strtotime("last sunday", $monthTime);

$date = new DateTime("@$startDate");
$Dia = $date->format('Y-m-d');   //  $var = $date->format('U = Y-m-d H:i:s'); // U é o timestamp unix

$rs0 = pg_query($Conec, "SELECT idev, evnum, to_char(dataini, 'DD/MM/YYYY') as DataInicial, titulo, cor FROM ".$xProj.". calendev 
WHERE Ativo = 1 And Fixo = 0 And AGE(dataini, CURRENT_DATE) >= '0 day' ORDER BY dataIni, evNum");   //WHERE Ativo = 1 And dataIni >= (DATE_ADD(CURDATE(), INTERVAL 0 DAY)) ORDER BY dataIni, evNum");
$row0 = pg_num_rows($rs0);
if($row0 > 0){
    echo "<table style='margin: 0 auto; border: 0px;'>";
    while ($tbl0 = pg_fetch_row($rs0)){
        $Cod = $tbl0[0];
        $evNum = $tbl0[1];
        $Data = $tbl0[2];
        $Tit = $tbl0[3];
        $Cor = $tbl0[4];
        echo "<tr>";
        echo "<td class='zeroBorda'><div style='font-size: .8em; padding-left: 5px; padding-right: 5px; border: 1px solid; border-radius: 3px; background-color: $Cor;'>";
        echo $Data;
        echo "</div></td>";
        echo "<td class='zeroBorda'><div style='font-size: .8em; padding-left: 5px; padding-right: 5px; border: 1px solid; border-radius: 3px; background-color: $Cor;'>";
        echo $Tit;
        echo "</div></td>";
        echo "<tr>";
    }
    echo "</table>";
}else{
    echo "<div style='font-size: .8em; font-weight: bold; text-align: center;'>Nenhum evento encontrado</div>";
}