<?php 
    session_start(); 
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    $rs = pg_query($Conec, "SELECT codtraf, descarq, usuins FROM ".$xProj.".trafego WHERE ativo = 1 ORDER BY datains DESC");
    $row = pg_num_rows($rs);
    echo "<div style='text-align: center; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;'>";
        echo "<table style='margin: 0 auto; width: 95%;'>";
        if($row > 0){
            while ($Tbl = pg_fetch_row($rs)){
                $CodArq = $Tbl[0];   // CodTraf
                $DescArq = $Tbl[1];  // descArq
                $StrUniq = substr($DescArq, 0, 14);
                $NomeArq = str_replace($StrUniq, "", $DescArq);
                if($CodArq != 0){
                    echo "<tr>";
                        echo "<td><div style='display: none;'>$CodArq</div></td>";
                        echo "<td><div id='descarq' onclick='mostraArq($CodArq)' class='listaArq arqMouseOver'>$NomeArq</div>";
                        echo "<div><span onclick='apagaArqTraf($CodArq)' title='Apagar este arquivo' style='padding-left: 3px; padding-right: 10px; color: #aaa; top: 0px; float: left; font-size: 16px; font-weight: bold; font-variant-position: super; cursor: pointer;'>&times;</span></div>";
                        echo "</td>";
                    echo "</tr>";
                }
            }
        }else{
            echo "<td><div style='font-size: 80%; text-align: center;'>Nenhum arquivo</div></td>";
        }
        echo "</table>";
    echo "</div>";