<?php 
    session_start(); 
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    $admIns = (int) filter_input(INPUT_GET, 'admins'); // vem de relArq.php
    $Dir = $_SESSION["PagDir"]; // página apresentada
    $VerArq = parAdm("verarquivos", $Conec, $xProj); // ver arquivos   1: qquer um pode ver os arquivos - 2: só os usuários da Diretoria/Assessoria

    if($VerArq == 1){
        $Sql = "SELECT codarq, descarq, codsetor, codsubsetor FROM ".$xProj.".arqsetor WHERE codsetor = $Dir And ativo = 1 ORDER BY datains DESC";
        $rs0 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $Dir And ativo = 1");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $tbl0 = pg_fetch_row($rs0);
            $DescSetor = $tbl0[0]."-"; // para retirar do nome do arquivo
        }
        // $Dir é o id dos setores
    }else{
        $Sql = "SELECT codarq, descarq, codsetor FROM ".$xProj.".arqsetor WHERE codsetor = ".$_SESSION["CodSetorUsu"]." And ativo = 1 And codsetor = $Dir ORDER BY datains DESC";
        $DescSetor = $_SESSION["SiglaSetor"]."-"; // para retirar do nome do arquivo ao listar
        // $_SESSION["SiglaSetor"] é o setor do usuário
    }

    $TamSetor = strlen($DescSetor);
    $rs = pg_query($Conec, $Sql);
    echo "<div style='text-align: center; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;'>";
        echo "<table style='margin: 0 auto; width: 95%;'>";
            while ($Tbl = pg_fetch_row($rs)){
                $CodArq = $Tbl[0];  //CodArq
                $DescArq = $Tbl[1]; // descArq
                $Setor = $Tbl[2];   //CodSetor

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
                        if($Setor == $_SESSION["CodSetorUsu"] && $_SESSION["AdmUsu"] >= $admIns){ 
                            echo "<div><span data-bs-toggle='modal' data-bs-target='#deletaModal' onclick='guardaArq($CodArq)' title='Apagar este arquivo' style='padding-left: 3px; padding-right: 10px; color: #aaa; top: 0px; float: left; font-size: 16px; font-weight: bold; font-variant-position: super; cursor: pointer;'>&times;</span></div>";
                        }
                        echo "</td>";
                    echo "</tr>";
                }
            }
        echo "</table>";
    echo "</div>";