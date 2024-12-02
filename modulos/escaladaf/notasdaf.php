<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center;">Notas aos Horários de Trabalho</div>
        <label style="position: relative; float: left; margin-top: -30px; padding-left: 10px;  color: red; font-weight: bold;"></label>
        <div style="margin: 10px; padding: 10px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
                $EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
                $rs3 = pg_query($Conec, "SELECT id, numnota, textonota, ativo FROM ".$xProj.".escaladaf_notas WHERE ativo = 1 And grupo_notas = $NumGrupo ORDER BY numnota");
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;" ></div>
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td>Nota</td>
                    <td>Texto</td>
                    <td></td>
                </tr>
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><div class="quadroletra"><?php echo $tbl3[1]; ?></div></td>
                        <td><div class="quadroletra" style="text-align: left; padding: 3px;"><?php echo $tbl3[2]; ?></div></td>
                        <td>
                            <?php
                            if($EscalanteDAF == 1){
                                ?>
                                <label style="font-family: arial, verdana, sans-serif; font-size: 80%; color: blue; cursor: pointer; padding-left: 5px; text-decoration: underline;" onclick="editaNota(<?php echo $Cod; ?>);" title="Clique para editar">Editar</label>
                                <?php
                            }else{
                                ?>
                                <label style="font-family: arial, verdana, sans-serif; font-size: 80%; padding-left: 5px; ">&nbsp;</label>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>