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
        <script type="text/javascript">

        </script>
    </head>
    <body> 
        <?php
            $rs0 = pg_query($Conec, "SELECT id, itemnum, itemverif FROM ".$xProj.".livrocheck WHERE ativo = 1 ORDER BY itemnum ");
        ?>
            <table style="width:85%;">
                <?php 
                while ($tbl0 = pg_fetch_row($rs0)){
                    if(strLen($tbl0[1]) < 2){
                        $Item = "0".$tbl0[1];
                    }else{
                        $Item = $tbl0[1];
                    }
                    ?>
                    <tr>
                        <td style="padding-left: 5px;"><?php echo $Item; ?></td>
                        <td><?php echo " - ".$tbl0[2]; ?></td>
                    </tr>
                    <?php
                    $Item++;
                }
                ?>
            </table>
        </div>
        <br><br>
    </body>
</html>