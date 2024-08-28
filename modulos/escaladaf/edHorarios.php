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
            function insereLetra(){
                document.getElementById("inserirletra").style.display = "block";
                document.getElementById("abreinsletra").style.visibility = "hidden";
            }
            function fechaInsLetra(){
                document.getElementById("inserirletra").style.display = "none";
                document.getElementById("abreinsletra").style.visibility = "visible";
            }

        </script>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center;">Horários de Trabalho</div>
        <label style="position: relative; float: left; margin-top: -30px; padding-left: 10px;  color: red; font-weight: bold;" id="mensagemQuadroHorario"></label>
        <div style="margin: 10px; padding: 20px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
            $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 ORDER BY ordemletra");
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;" id="_mensagemQuadroHorario"></div>
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td>Ordem</td>
                    <td>Letra</td>
                    <td>Turno</td>
                    <td></td>
                </tr>
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><input type="text" value="<?php echo $tbl3[3]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaOrdem(<?php echo $Cod; ?>, value);"/></td>
                        <td><input type="text" value="<?php echo $tbl3[1]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaLetra(<?php echo $Cod; ?>, value);"/></td>
                        <td><input type="text" value="<?php echo $tbl3[2]; ?>" style="width: 170px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaTurno(<?php echo $Cod; ?>, value);"/></td>
                        <td style="text-align: center;"><img src='imagens/lixeiraPreta.png' height='15px;' style='cursor: pointer; padding-right: 3px;' onclick='apagaLetra(<?php echo $Cod; ?>);' title='Apagar esta letra.'></td>

                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
            <div id="inserirletra" style="display: none; border: 1px solid; border-radius: 10px; padding: 2px;">
                <span class="close" style="position: relative; float: rigth; top: -10px; font-size: 200%; color: black;" onclick="fechaInsLetra();">&times;</span>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td>Ordem</td>
                        <td>Letra</td>
                        <td>Turno</td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;">0</td>
                        <td><input type="text" id="insordem" value="" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" /></td>
                        <td><input type="text" id="insletra" value="" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" /></td>
                        <td><input type="text" id="insturno" value="" style="width: 170px; text-align: center; border: 1px solid; border-radius: 3px;" /></td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <button class="botpadrblue" onclick="salvaLetra();">Salvar</button>
            </div>

            <br>
            <button id="abreinsletra" class="botpadrblue" onclick="insereLetra();">Inserir Letra</button>
        </div>
    </body>
</html>