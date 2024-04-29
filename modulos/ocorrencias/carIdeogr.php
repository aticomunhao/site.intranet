<?php 
    session_start(); 
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

    $CodOcor = $_REQUEST['codocor'];
    if($CodOcor == 0){
        $rsIdeo = pg_query($Conec, "SELECT codideo, descideo FROM ".$xProj.".ocorrideogr WHERE codprov = ".$_SESSION['usuarioID']." And coddaocor = 0 ORDER BY codideo");
    }else{
        $rsIdeo = pg_query($Conec, "SELECT codideo, descideo FROM ".$xProj.".ocorrideogr WHERE coddaocor = $CodOcor OR codprov = ".$_SESSION['usuarioID']." And coddaocor = 0 ORDER BY codideo");
    }
    $folder = "modulos/ocorrencias/imagens/";
    $rowIdeo = pg_num_rows($rsIdeo);
    if($rowIdeo > 0){
        while ($TblIdeo = pg_fetch_row($rsIdeo)){
            $Ideogr = $folder.$TblIdeo[1];
            $CodIdeo = $TblIdeo[0];
            echo "<div style='display: inline; padding: 2px;' title='Clique para apagar' onclick='apagaArq($CodIdeo)'><img src='$Ideogr' width='60px' height='60px;'>  </div>";
        }
    }