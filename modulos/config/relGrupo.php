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
                $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $Cod ORDER BY nomecompl ");
            ?>
            <table class="display" style="width:85%">
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    ?>
                    <tr>
                        <td style="font-size: 80%; padding-left: 20px;"><?php echo $tbl3[0]; ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>