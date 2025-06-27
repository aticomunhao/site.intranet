<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(isset($_REQUEST["numgrupo"])){
    $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
}else{
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
}
if(isset($_REQUEST["mesano"])){
    $MesSalvo = $_REQUEST["mesano"]; // quando vem do fiscal
}else{
    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <?php
            $rs0 = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo And ativo = 1");
            $row0 = pg_num_rows($rs0);
            $tbl0 = pg_fetch_row($rs0);
            $SiglaGrupo = $tbl0[0];
        ?>
        <div style="text-align: center;">
            <label style="font-weight: bold;"><?php echo $SiglaGrupo; ?></label><br>
            <label class="etiqAzul" style="padding-right: 10px;">Geral:</label>
            <input type="button" class="resetbot fundoAzul2" style="font-size: 80%;" value="Notas <?php echo $MesSalvo; ?>" onclick="imprNotasFunc()";>
            <hr style="margin-bottom: 0; margin-top: 4px; margin-left: 20px; margin-right: 20px;">
            <div style="padding: 10px; text-align: center;">
                <label class="etiqAzul">Individual - Selecione:</label>
                <select id="selectNotasIndiv" style="font-size: 1rem; width: 250px;" title="Selecione o usuário." onchange="imprNotasIndiv(value);">
                    <option value = ""></option>
                    <?php
//                    $OpcoesTodos = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 And eft_daf = 1 And esc_grupo = $NumGrupo ORDER BY nomecompl");
                    $OpcoesTodos = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE eft_daf = 1 And esc_grupo = $NumGrupo ORDER BY nomecompl");
                    if($OpcoesTodos){
                        while ($Opcoes = pg_fetch_row($OpcoesTodos)){ ?>
                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                            <?php 
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </body>
</html>