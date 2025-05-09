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
        <?php
            $EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
            $MeuGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            if(isset($_REQUEST["numgrupo"])){
                $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
            }else{
                $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
            }
            if($NumGrupo == 0 || $NumGrupo == ""){
                $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            }

            $rsGr = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_esc WHERE usu_id = ".$_SESSION["usuarioID"]." And ativo = 1");
            $rowGr = pg_num_rows($rsGr); // quantidade de grupos em que é escalante

            // seleciona os turnos 
            $rs1 = pg_query($Conec, "SELECT id, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1 And infotexto = 0 ORDER BY letra");
            $row1 = pg_num_rows($rs1);
 
            //Seleciona os textos informativos
            $rs2 = pg_query($Conec, "SELECT id, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1 And infotexto = 1 ORDER BY letra");
            $row2 = pg_num_rows($rs2);
        ?>
        <div style="margin: 10px; padding: 20px; border: 2px solid green; border-radius: 15px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->

                <div class="col" style="margin: 0 auto; text-align: left; padding: 5px;">
                    <?php
                    if($row1 > 20){
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra <= 6 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra <= 5 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }
                    ?>
                    <table class="display" style="margin: 0 auto; width:85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
<!--                                <td style="font-size: 80%; color: gray;"><?php echo "<sup>".$tbl3[6]."</sup>"; ?></td> -->
                                <td><div <?php if($tbl3[3] == 0){echo "class='quadroletra'";
                                }else{
                                    if($tbl3[3] == 1){
                                        echo "class='quadroletraYellow'";
                                    }
                                    if($tbl3[3] == 2){
                                        echo "class='quadroletraBlue'";
                                    }
                                    if($tbl3[3] == 3){
                                        echo "class='quadroletraGreen'";
                                    }
                                } ?> >
                                <?php if($tbl3[1] == ""){echo "&nbsp;";}else{echo $tbl3[1];
                                    } ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                                <td>
                                    <?php if($tbl3[5] == 0){ ?>
                                        <div class="quadroletra quadroCinza" title="Carga horária do turno"><?php echo $tbl3[4]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="text-align: center; padding: 5px;">
                    <?php
                    if($row1 > 20){
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 6 And ordemletra <= 12 And grupo_turnos = $NumGrupo ORDER BY ordemletra");
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 5 And ordemletra <= 10 And grupo_turnos = $NumGrupo ORDER BY ordemletra");
                    }
                    ?>
                    <table class="display" style="margin: 0 auto; width: 85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
<!--                                <td style="font-size: 80%; color: gray;"><?php echo "<sup>".$tbl3[6]."</sup>"; ?></td> -->
                                <td><div 
                                    <?php 
                                    if($tbl3[3] == 0){
                                        echo "class='quadroletra'";
                                    }else{
                                        if($tbl3[3] == 1){
                                            echo "class='quadroletraYellow'";
                                        }
                                        if($tbl3[3] == 2){
                                            echo "class='quadroletraBlue'";
                                        }
                                        if($tbl3[3] == 3){
                                            echo "class='quadroletraGreen'";
                                        }
                                    }?>  >
                                    <?php if($tbl3[1] == ""){
                                        echo "&nbsp;";
                                        }else{
                                            echo $tbl3[1];} ?>
                                    </div>
                                </td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                                <td>
                                    <?php if($tbl3[5] == 0){ ?>
                                        <div class="quadroletra quadroCinza" title="Carga horária do turno"><?php echo $tbl3[4]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- espaçamento entre colunas  -->

                <div class="col" style="margin: 0 auto; text-align: center; padding: 5px;">
                    <?php
                    if($row1 > 20){
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 12 And ordemletra <= 18 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 10 And ordemletra <= 15 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }
                    ?>
                    <table class="display" style="margin: 0 auto; width: 85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
<!--                                <td style="font-size: 80%; color: gray;"><?php echo "<sup>".$tbl3[6]."</sup>"; ?></td> -->
                                <td><div <?php if($tbl3[3] == 0){echo "class='quadroletra'";
                                }else{
                                    if($tbl3[3] == 1){
                                        echo "class='quadroletraYellow'";
                                    }
                                    if($tbl3[3] == 2){
                                        echo "class='quadroletraBlue'";
                                    }
                                    if($tbl3[3] == 3){
                                        echo "class='quadroletraGreen'";
                                    }
                                }?>  ><?php if($tbl3[1] == ""){echo "&nbsp;";}else{echo $tbl3[1];} ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                                <td>
                                    <?php if($tbl3[5] == 0){ ?>
                                        <div class="quadroletra quadroCinza" title="Carga horária do turno"><?php echo $tbl3[4]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="margin: 0 auto; text-align: center; padding: 5px;">
                    <?php
                    if($row1 > 20){
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 18 And ordemletra <= 24 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 15 And ordemletra <= 20 And grupo_turnos = $NumGrupo ORDER BY letra");
                    }
                    ?>
                    <table class="display" style="margin: 0 auto; width: 85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
<!--                                <td style="font-size: 80%; color: gray;"><?php echo "<sup>".$tbl3[6]."</sup>"; ?></td> -->
                                <td><div <?php if($tbl3[3] == 0){echo "class='quadroletra'";
                                }else{
                                    if($tbl3[3] == 1){
                                        echo "class='quadroletraYellow'";
                                    }
                                    if($tbl3[3] == 2){
                                        echo "class='quadroletraBlue'";
                                    }
                                    if($tbl3[3] == 3){
                                        echo "class='quadroletraGreen'";
                                    }
                                    } ?>  ><?php if($tbl3[1] == ""){echo "&nbsp;";}else{echo $tbl3[1];} ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                                <td>
                                    <?php if($tbl3[5] == 0){ ?>
                                        <div class="quadroletra quadroCinza" title="Carga horária do turno"><?php echo $tbl3[4]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="margin: 0 auto; text-align: center; padding: 5px;">
                    <?php
                        if($row1 >= 20){
                            $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 20 And ordemletra <= 30 And grupo_turnos = $NumGrupo ORDER BY letra");
                        }else{
                            $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, destaq, TO_CHAR(cargacont, 'HH24:MI'), infotexto, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And ordemletra > 20 And ordemletra <= 25 And grupo_turnos = $NumGrupo ORDER BY letra");
                        }
                    ?>
                    <table class="display" style="margin: 0 auto; width: 85%;">
                        <?php 
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Cod = $tbl3[0];
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl3[0]; ?></td>
<!--                                <td style="font-size: 80%; color: gray;"><?php echo "<sup>".$tbl3[6]."</sup>"; ?></td> -->
                                <td><div <?php if($tbl3[3] == 0){echo "class='quadroletra'";
                                }else{
                                    if($tbl3[3] == 1){
                                        echo "class='quadroletraYellow'";
                                    }
                                    if($tbl3[3] == 2){
                                        echo "class='quadroletraBlue'";
                                    }
                                    if($tbl3[3] == 3){
                                        echo "class='quadroletraGreen'";
                                    }
                                    } ?>  ><?php if($tbl3[1] == ""){echo "&nbsp;";}else{echo $tbl3[1];} ?></div></td>
                                <td><div class="quadroletra"><?php echo $tbl3[2]; ?></div></td>
                                <td>
                                    <?php if($tbl3[5] == 0){ ?>
                                        <div class="quadroletra quadroCinza" title="Carga horária do turno"><?php echo $tbl3[4]; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </table>
                </div> <!-- quadro -->

                <div class="col" style="margin: 0 auto; text-align: center;">
                    <?php
                        if($EscalanteDAF == 1 && $MeuGrupo == $NumGrupo || $rowGr > 1 || $_SESSION["usuarioID"] == 83 || $_SESSION["usuarioID"] == 8){ // Provisório Wil e Luzinólia
                            ?>
                            <div class='bContainer corFundo' style="margin-top: 5px;" onclick='abreEditHorario()'> Editar </div>
                            <br>
                            <div class='bContainer corFundo' style="margin-top: 15px;" onclick='abreEditDescanso()' title="Escala para descanso."> Descanso </div>
                            <br>
                            <div class='bContainer corFundo' style="margin-top: 25px;" onclick='imprNotasFunc()' title="Gera PDF com as anotações diárias nos turnos."> Anotações </div>
                            <?php
                        }
                    ?>
                </div> <!-- quadro -->
            </div>
        </div>
    </body>
</html>