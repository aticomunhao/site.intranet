<?php
session_start();
require_once("abrealas.php");
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
            <label class="etiqAzul">Usuários do Grupo:</label>
            <?php
                $rs3 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, esc_horaini, esc_horafim FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $Cod ORDER BY nomeusual, nomecompl ");
            ?>
            <table class="display" style="width:85%">
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td style="font-size: 80%; padding-left: 20px;"><?php echo $tbl3[2]; ?></td>
                        <td style="font-size: 80%;"><?php echo $tbl3[1]; ?></td>
                        <td style="font-size: 80%;"><?php echo $tbl3[3]; ?></td>
                        <td style="font-size: 80%;"><?php echo $tbl3[4]; ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>