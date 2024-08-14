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
        <?php
            $Cod = (int) filter_input(INPUT_GET, 'codigo');
        ?>
         <!-- Apresenta os usuários do grupo -->
        <div style="padding: 10px;">
            <?php
                $rs3 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, esc_horaini, esc_horafim FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $Cod ORDER BY nomeusual, nomecompl ");
            ?>
            <table class="display" style="margin: 0 auto; width:85%;">
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><div class="quadrgrupo"><?php if(is_null($tbl3[2]) || $tbl3[2] == ""){echo "&nbsp;";}else{echo $tbl3[2];} ?></div></td>
                        <td><div class="quadrgrupo" style="cursor: pointer;" title="clique para inserir" onclick="insParticip(<?php echo $Cod; ?>)"><?php echo $tbl3[1]; ?></div></td>
                        <td><div class="quadrgrupo"><?php if(is_null($tbl3[3]) || $tbl3[3] == ""){echo "&nbsp;";}else{echo $tbl3[3];} ?></td>
                        <td><div class="quadrgrupo"><?php if(is_null($tbl3[4]) || $tbl3[4] == ""){echo "&nbsp;";}else{echo $tbl3[4];} ?></div></td>
                        <td><div class="quadrgrupo" onclick="editaParticip(<?php echo $Cod; ?>);">
                        <label style="font-family: arial, verdana, sans-serif; font-size: 80%; color: blue; cursor: pointer;" onclick="editaParticip(<?php echo $Cod; ?>)" title="Clique para editar">Editar</label>
                        </div></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>