<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <div style="margin-top: 15px;">Feriados</div>
        <?php
            $EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
            if(isset($_REQUEST["ano"])){
                $Ano = $_REQUEST["ano"];
            }else{
                $Ano = date('Y');
            }
        ?>
        <div style="margin: 10px; padding: 10px; border: 2px solid green; border-radius: 15px;">
            <div class="row">
                <div class="col" style="margin: 0 auto; text-align: left;">
                    <?php
                    $rs3 = pg_query($Conec, "SELECT id, TO_CHAR(dataescalafer, 'DD/MM'), descr FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 ORDER BY dataescalafer");
                    ?>
                    <table class="display" style="margin: 0 auto; text-align: center; width: 100%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                                <td><div class="quadroletra"><?php if($tbl3[1] == ""){echo "&nbsp;";}else{echo $tbl3[1];} ?></div></td>
                                <td><div class="quadroletra" style="text-align: left; padding-left: 3px;"><?php echo $tbl3[2]; ?></div></td>
                            </tr>
                            <?php
                        }
                    ?>
                    <tr>
                        <td colspan="4" style="text-align: right;">
                            <?php
                            if($EscalanteDAF == 1){
                            ?>
                                <button class="botpadrblue" onclick="abreEditFeriados();">Editar</button>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    </table>
                </div> <!-- quadro -->                    
            </div>
        </div>
    </body>
</html>