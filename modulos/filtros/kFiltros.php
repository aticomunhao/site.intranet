<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="comp/js/plotly.min.js"></script>
        <title></title>
        <script type="text/javascript">

        </script>
    </head>
    <body>
        <div  style="text-align: center;"><label class="titRelat">Agenda<label></div>
        <?php

        $PrazoNotif = 40; 
        
        $rs1 = pg_query($Conec, "SELECT ".$xProj.".filtros.id, numapar, descmarca, desctipo, localinst, TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'YYYY') 
        FROM ".$xProj.".filtros_tipos INNER JOIN (".$xProj.".filtros INNER JOIN ".$xProj.".filtros_marcas ON ".$xProj.".filtros.codmarca = ".$xProj.".filtros_marcas.id) ON ".$xProj.".filtros.tipofiltro =  ".$xProj.".filtros_tipos.id 
        WHERE datavencim < CURRENT_DATE + interval '$PrazoNotif days' And ".$xProj.".filtros.ativo = 1 or
        TO_CHAR(datavencim, 'YYYY') = '3000' And ".$xProj.".filtros.ativo = 1 
        ORDER BY datavencim, numapar ");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                $Num = $tbl1[1];
                $Marca = $tbl1[2];
                $Tipo = $tbl1[3];
                ?>
                <div style="margin-bottom: 8px; padding-top: 3px; border: 1px solid gray; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 120%; font-weight: bold; width: 15%; text-align: center;"><div style="border: 1px solid; border-radius: 10px;"> <?php echo str_pad($Num, 3, 0, STR_PAD_LEFT); ?> </div></td>
                            <td colspan="3" style="text-align: left;"><?php echo $tbl1[4]; ?></td>
                        </tr>
                    </table>
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td colspan="3" style="font-size: 80%; text-align: right; padding-right: 3px;"><?php if($tbl1[6] == "3000"){echo "<label style='color: red;'>Definir vencimento do elemento filtrante</label>";}else{echo "<label style='color: red; font-weight: bold;'>Substituir elemento filtrante até</label>";} ?></td>
                            <td style="font-size: 80%; font-weight: bold;"><?php if($tbl1[6] != "3000"){echo $tbl1[5];} ?> </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-bottom: 2px;"></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
        }else{
            ?>
            <div style="border: 1px solid; border-radius: 10px">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td colspan="4" style="padding-bottom: 2px; text-align: center;">Tudo em Ordem</td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
    </body>
</html>