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
        <script>
            function insereData(){
                document.getElementById("inserirData").style.display = "block";
                document.getElementById("abreinsData").style.visibility = "hidden";
            }
            function fechaInsData(){
                document.getElementById("inserirData").style.display = "none";
                document.getElementById("abreinsData").style.visibility = "visible";
            }
            $("#insdata").mask("99/99");
        </script>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center; font-weight: bold;">Feriados</div>
        <div style="margin: 10px; padding: 10px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
            $rs3 = pg_query($Conec, "SELECT id, TO_CHAR(dataescalafer, 'DD/MM'), descr FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 ORDER BY dataescalafer");
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;"></div>
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td>Data</td>
                    <td>Feriado</td>
                    <td></td>
                </tr>
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><input type="text" value="<?php echo $tbl3[1]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaDataFer(<?php echo $Cod; ?>, value);"/></td>
                        <td><input type="text" value="<?php echo $tbl3[2]; ?>" style="width: 300px; text-align: left; border: 1px solid; border-radius: 3px;" onchange="editaDescrFer(<?php echo $Cod; ?>, value);" /></td>
                        <td style="text-align: center; padding-left: 5px;"><img src='imagens/lixeiraPreta.png' height='15px;' style='cursor: pointer; padding-right: 3px;' onclick='apagaDataFer(<?php echo $Cod; ?>);' title='Apagar esta data.'></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
            <div id="inserirData" style="display: none; border: 1px solid; border-radius: 10px; padding: 2px;">
                <span class="close" style="position: relative; float: rigth; top: -10px; font-size: 200%; color: black;" onclick="fechaInsData();">&times;</span>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td>Data</td>
                        <td>Feriado</td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;">0</td>
                        <td><input type="text" id="insdata" value="" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" /></td>
                        <td><input type="text" id="insdescr" value="" style="width: 300px; text-align: left; border: 1px solid; border-radius: 3px;" /></td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <button class="botpadrblue" onclick="salvaDataFer();">Salvar</button>
            </div>

            <br>
            <button id="abreinsData" class="botpadrblue" onclick="insereData();">Inserir Feriado</button>
        </div>
    </body>
</html>