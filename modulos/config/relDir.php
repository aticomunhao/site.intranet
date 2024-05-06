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
         <!-- Apresenta os usuários do setor com o nível administrativo -->
        <div style="padding: 10px;">
            <label class="etiqAzul">Usuários do Setor:</label>
            <?php
                $rs3 = pg_query($Conec, "SELECT nomecompl, adm, adm_nome FROM ".$xProj.".poslog INNER JOIN ".$xProj.".usugrupos ON ".$xProj.".poslog.adm = ".$xProj.".usugrupos.adm_fl 
                WHERE ".$xProj.".poslog.ativo = 1 And ".$xProj.".poslog.codsetor = $Cod 
                ORDER BY ".$xProj.".poslog.adm DESC, ".$xProj.".poslog.nomecompl ASC ");
            ?>
            <table class="display" style="width:85%">
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    ?>
                    <tr>
                        <td style="font-size: 80%; padding-left: 20px;"><?php echo $tbl3[0]." - ".$tbl3[2]; ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>