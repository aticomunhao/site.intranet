<?php 
    session_start(); 
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    $admIns = (int) filter_input(INPUT_GET, 'admins'); // vem de relArq.php
    $Dir = $_SESSION["PagDir"]; // página apresentada

    //Ver arquivos dos subsetores - o usuário logado tem que ter o CodSubSetor = 1 (não pertence a nenhum subsetor)
    $VerSubSet = 1;

    $Sql = "SELECT codarq, descarq, codsetor, codsubsetor FROM ".$xProj.".arqsetor WHERE codsetor = ".$_SESSION["CodSetorUsu"]." And codsubsetor = ".$_SESSION["CodSubSetorUsu"]." And ativo = 1 And codsetor = $Dir ORDER BY datains DESC";
    if($VerSubSet == 1){ // ver os arquivos dos subsetores
        if($_SESSION["CodSubSetorUsu"] == 1){  // se o usuário logado não pertence a nenhum subsetor
     $Sql = "SELECT codarq, descarq, codsetor, codsubsetor FROM ".$xProj.".arqsetor WHERE codsetor = ".$_SESSION["CodSetorUsu"]." And codsubsetor > 0 And ativo = 1 And codsetor = $Dir ORDER BY datains DESC";
        }
    }
//echo $Sql;
    $DescSetor = $_SESSION["SiglaSetor"]."-"; // para retirar do nome do arquivo ao listar
    $TamSetor = strlen($DescSetor);
    $rs = pg_query($Conec, $Sql);
    echo "<div style='text-align: center; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;'>";
        echo "<table style='margin: 0 auto; width: 95%;'>";
            while ($Tbl = pg_fetch_row($rs)){
                $CodArq = $Tbl[0];  //CodArq
                $Setor = $Tbl[2];   //CodSetor
                $SubSetor = $Tbl[3]; //CodSubSetor
                $DescArq = $Tbl[1]; // descArq

                $StrUniq = substr($DescArq, 0, 14);
                $NomeArq1 = str_replace($StrUniq, "", $DescArq); // tira o nome do setor
                if(substr($NomeArq1, 0, $TamSetor) == $DescSetor){
                    $NomeArq = str_replace($DescSetor, "", $NomeArq1);
                }else{
                    $NomeArq = $NomeArq1;
                }
                if($CodArq != 0){
                    echo "<tr>";
                        echo "<td><div style='display: none;'>$CodArq</div></td>";
                        echo "<td><div id='descarq' onclick='mostraArq($CodArq)' class='listaArq arqMouseOver'>$NomeArq</div>";
                        if($Setor == $_SESSION["CodSetorUsu"] && $SubSetor == $_SESSION["CodSubSetorUsu"] && $_SESSION["AdmUsu"] >= $admIns){ // $VerSubSet = 1 pode ver mas não pode apagar
                            echo "<div><span data-bs-toggle='modal' data-bs-target='#deletaModal' onclick='guardaArq($CodArq)' title='Apagar este arquivo' style='padding-left: 3px; padding-right: 10px; color: #aaa; top: 0px; float: left; font-size: 16px; font-weight: bold; font-variant-position: super; cursor: pointer;'>&times;</span></div>";
                        }
                        echo "</td>";
                    echo "</tr>";
                }
            }
        echo "</table>";
    echo "</div>";