<?php 
    session_name("arqAdm");
    session_start(); 
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    $admIns = (int) filter_input(INPUT_GET, 'admins'); // vem de relArq.php
    $Dir = $_SESSION["PagDirDaf"]; // página apresentada
//    $VerArq = parAdm("verarquivos", $Conec, $xProj); // ver arquivos   1: qquer um pode ver os arquivos - 2: só os usuários da Diretoria/Assessoria

    $VerArq = 1;
    if($VerArq == 1){
        if($_SESSION["CodSetorUsuDaf"] == 1){
            $Sql = "SELECT codarq, descarq, codsetor, TO_CHAR(datains, 'DD/MM/YYYY HH24:MI') FROM ".$xProj.".daf_arqsetor WHERE codsetor = $Dir And ativo = 1 ORDER BY datains DESC";
        }else{
            $Sql = "SELECT codarq, descarq, codsetor, TO_CHAR(datains, 'DD/MM/YYYY HH24:MI') FROM ".$xProj.".daf_arqsetor WHERE codsetor = $Dir And ativo = 1 And codsetor = ".$_SESSION["CodSetorUsuDaf"]." ORDER BY datains DESC";
        }
    }
//echo $Sql;

    $rs = pg_query($Conec, $Sql);
    echo "<div style='text-align: center; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;'>";
        echo "<table style='margin: 0 auto; width: 95%;'>";
            while ($Tbl = pg_fetch_row($rs)){
                $CodArq = $Tbl[0];  //CodArq
                $DescArq = $Tbl[1]; // descArq
                $Setor = $Tbl[2];   //CodSetor
                $DataIns = $Tbl[3];

                $StrUniq = substr($DescArq, 0, 14);
                $NomeArq1 = str_replace($StrUniq, "", $DescArq); // tira o nome do setor
                $NomeArq = $DataIns." ".$NomeArq1;
                if($CodArq != 0){
                    echo "<tr>";
                        echo "<td><div style='display: none;'>$CodArq</div></td>";
                        echo "<td><div id='descarq' onclick='mostraArq($CodArq)' class='listaArq arqMouseOver'>$NomeArq</div>";
                        if($_SESSION["CodSetorUsuDaf"] == 1){ // setor1 = todos
                            echo "<div><span onclick='apagaArqDir($CodArq)' title='Apagar este arquivo' style='padding-left: 3px; padding-right: 10px; color: #aaa; top: 0px; float: left; font-size: 16px; font-weight: bold; font-variant-position: super; cursor: pointer;'>&times;</span></div>";
                        }
                        echo "</td>";
                    echo "</tr>";
                }
            }
        echo "</table>";
    echo "</div>";