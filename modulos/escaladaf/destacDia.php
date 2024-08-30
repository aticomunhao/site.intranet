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
        <div style="position: relative; float: left; width: 30%; text-align: center;">&nbsp;</div>
        <div style="position: relative; float: left; width: 30%; text-align: center;">Destacar Dia</div>
        <div style="position: relative; float: left; width: 30%; text-align: left;">&nbsp;
            <div class="quadroletra" style="position: relative; float: left; width: 25px; background-color: yellow;"> &nbsp; </div>
        </div>

        <?php
            if(isset($_REQUEST["mesano"])){
                $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
                $Proc = explode("/", $Busca);
                $Mes = $Proc[0];
                if(strLen($Mes) < 2){
                    $Mes = "0".$Mes;
                }
                $Ano = $Proc[1];
            }else{
                return false;
            }
        ?>
        <div style="margin: 30px; padding: 20px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
            $rs3 = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD/MM/YYYY'), marcadaf 
            FROM ".$xProj.".escaladaf 
            WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano'
            ORDER BY dataescala");
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;" id="_mensagemQuadroHorario"></div>
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td style="display: none;"></td>
                    <td>Marca</td>
                    <td>Dia</td>
                </tr>
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td><input type="checkbox" id="ev" title="marca para destacar" onClick="MarcaDia(<?php echo $Cod ?>);" <?php if($tbl3[2] == 1) {echo "checked";} ?> ></td>
                        <td><div class="quadrodia"><?php echo $tbl3[1]; ?></div></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
        </div>
    </body>
</html>