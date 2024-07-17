<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 


$rs = pg_query($Conec, "SELECT id, siglagrupo, descgrupo, descescala, guardaescala, qtd_turno FROM ".$xProj.".escalas_gr ORDER BY siglagrupo");
$row = pg_num_rows($rs);
if($row > 0){
    echo "<table style='margin: 0 auto;''> ";
        echo "<tr>";
            echo "<td class='etiq aCentro' style='min-width: 50px;'>Sigla</td>";
            echo "<td></td>";
            echo "<td class='etiq aCentro'>Descrição</td>";
            echo "<td class='etiq'>Turnos</td>";
        echo "</tr>";
        while($tbl = pg_fetch_row($rs)){
            echo "<tr>";
                $Cod = $tbl[0];
                echo "<td class='aCentro' style='min-width: 50px; border-top: 1px solid #B0B0B0;' onclick='editaGrupo($Cod);' title='Clique para editar'>$tbl[1]</td>";
                echo "<td style='border-top: 1px solid #B0B0B0;'></td>";
                echo "<td style='min-width: 350px; border-top: 1px solid #B0B0B0; padding-left: 3px;' onclick='editaGrupo($Cod);' title='Clique para editar'>$tbl[2]</td>";
                echo "<td class='aDir' style='border-top: 1px solid #B0B0B0; padding-left: 3px;' title='Número de turnos para a escala'>$tbl[5]</td>";
            echo "</tr>";

            echo "<tr>";
                echo "<td class='aEsq'><div style='min-width: 50px;'></div></td>";
                echo "<td></td>";
                echo "<td colspan='2' class='etiq aEsq' style='min-width: 250px;' onclick='editaGrupo($Cod);' title='Clique para editar'>$tbl[3]</td>";
            echo "</tr>";
        }    
    echo "</table>";
}