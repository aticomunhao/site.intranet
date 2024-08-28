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
        <div style="margin-top: 15px;">Horários de Trabalho</div>

        <div style="margin: 10px; padding: 20px; border: 2px solid green; border-radius: 15px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;">
                    <?php
                    $rs3 = pg_query($Conec, "SELECT id, letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra <= 5 ORDER BY letra");
                    ?>
                    <table class="display" style="margin: 0 auto; width:85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                                <td><div class="quadroletra"><?php echo $tbl3[1]; ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="text-align: center;">
                    <?php
                    $rs3 = pg_query($Conec, "SELECT id, letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 5 And ordemletra <= 10 ORDER BY ordemletra");
                    ?>
                    <table class="display" style="margin: 0 auto; width:85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                                <td><div class="quadroletra"><?php echo $tbl3[1]; ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- espaçamento entre colunas  -->

                <div class="col" style="margin: 0 auto; text-align: center;">
                    <?php
                    $rs3 = pg_query($Conec, "SELECT id, letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 10 And ordemletra <= 15 ORDER BY letra");
                    ?>
                    <table class="display" style="margin: 0 auto; width:85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                                <td><div class="quadroletra"><?php echo $tbl3[1]; ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>

                </div> <!-- quadro -->

                <div class="col" style="margin: 0 auto; text-align: center;">
                    <?php
                    $rs3 = pg_query($Conec, "SELECT id, letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 15 And ordemletra <= 20 ORDER BY letra");
                    ?>
                    <table class="display" style="margin: 0 auto; width:85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                                <td><div class="quadroletra"><?php echo $tbl3[1]; ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="margin: 0 auto; text-align: center;">
                    <div class='bContainer corFundo' onclick='abreEditHorario()'> Editar </div>

                </div> <!-- quadro -->
            </div>
        </div>
    </body>
</html>