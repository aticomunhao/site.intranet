<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 

$Mes_Extract = array(
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
);

    if(isset($_REQUEST["mesano"])){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $Data = date('01/'.$Mes.'/'.$Ano);
    }else{
        $Mes = date("m");
        $Ano = date("Y");
    }
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = (int) filter_input(INPUT_GET, 'numgrupo');
    }
    $rsGr = pg_query($Conec, "SELECT qtd_turno FROM ".$xProj.".escalas_gr WHERE id = '$NumGrupo' ");
    $rowGr = pg_num_rows($rsGr);
    if($rowGr > 0){
        $tblGr = pg_fetch_row($rsGr);
        $Turnos = $tblGr[0];
    }else{
        $Turnos = 1;
    }
    //Salva o último mês acessado
    pg_query($Conec, "UPDATE ".$xProj.".escalas_gr set guardaescala = '".$_REQUEST["mesano"]."' WHERE id = $NumGrupo ");

    $Ini = strtotime($Ano.'/'.$Mes.'/01'); //dia 1 do mês e ano selecionados
    $DiaIni = strtotime("-1 day", $Ini); // para começar com o dia 1 no loop          
    for($i = 0; $i < 31; $i++){ // completar os dias que faltam no mês            
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível            
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escalas WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escalas (dataescala, grupo_id, usuins, datains) VALUES ('$Data', $NumGrupo, ".$_SESSION["usuarioID"].", NOW() )");
        }
    }
    ?>

    <div style="border: 1px solid; border-radius: 15px; padding: 5px; min-height: 200px; text-align: center;">
        <?php 
        echo $Mes_Extract[$Mes].'/'.$Ano; 
        $rs = pg_query($Conec, "SELECT id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, horaini1, horafim1, turno2_id, horaini2, horafim2, turno3_id, horaini3, 
         horafim3, turno4_id, horaini4, horafim4, turno5_id, horaini5, horafim5, turno6_id, horaini6, horafim6
         FROM ".$xProj.".escalas WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
        $row = pg_num_rows($rs);
        
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td class="etiq aCentro">Data</td>
                <td class="etiq aCentro">Início</td>
                <td class="etiq aCentro">Fim</td>
                <td class="etiq aCentro">Escala</td>
                <td></td>
                <td></td>

                <?php
                    if($Turnos >= 2){
                ?>
                <td class="etiq aCentro">Início</td>
                <td class="etiq aCentro">Fim</td>
                <td class="etiq aCentro">Escala</td>
                    <td></td>
                    <td></td>
                <?php
                    }
                ?>
                <?php
                    if($Turnos >= 3){
                ?>
                <td class="etiq aCentro">Início</td>
                <td class="etiq aCentro">Fim</td>
                <td class="etiq aCentro">Escala</td>
                    <td></td>
                    <td></td>
                <?php
                    }
                ?>
                <?php
                    if($Turnos >= 4){
                ?>
                <td class="etiq aCentro">Início</td>
                <td class="etiq aCentro">Fim</td>
                <td class="etiq aCentro">Escala</td>
                <td></td>
                <?php
                    }
                ?>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0]; // id de escalas
                        $CodPartic1 = $tbl[3]; // pessoas_id de poslog - salvo em salvaEsc.php
                        ?>
                        <tr>
                            <td><div class="quadrinho"> <?php echo $tbl[2]; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if($tbl[4] == 0 && $tbl[5] == 0){echo "&nbsp;";}else{if($tbl[4] < 10){echo "0".$tbl[4].":00";}else{echo $tbl[4].":00";}}; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if($tbl[5] == 0){echo "&nbsp;";}else{if($tbl[5] < 10){echo "0".$tbl[5].":00";}else{echo $tbl[5].":00";}}; ?> </div> </td>
                            <?php
                            $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic1;");
                            $row1 = pg_num_rows($rs1);
                            if($row1 > 0){
                                $tbl1 = pg_fetch_row($rs1);
                                $Nome1 = $tbl1[0];
                            }else{
                                $Nome1 = "&nbsp;";
                            }
                            ?>
                            <td><div class="quadrinhoClick" style="text-align: left;" onclick="abreParticip(1, <?php echo $Cod; ?>, <?php echo $CodPartic1; ?>, '<?php echo $tbl[2]; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome1; ?> </div> </td>
                            <td class="etiq" title="Horas do turno"><?php if(($tbl[5]-$tbl[4]) > 0){echo ($tbl[5]-$tbl[4])."h";} ?></td>


                            <?php
                            if($Turnos >= 2){
                            ?>
                            <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                            <td><div class="quadrinho"> <?php if($tbl[7] == 0 && $tbl[8] == 0){echo "&nbsp;";}else{if($tbl[7] < 10){echo "0".$tbl[7].":00";}else{echo $tbl[7].":00";}}; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if($tbl[8] == 0){echo "&nbsp;";}else{if($tbl[8] < 10){echo "0".$tbl[8].":00";}else{echo $tbl[8].":00";}}; ?> </div> </td>
                            <?php
                            $CodPartic2 = $tbl[6];
                            $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic2;");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $Nome2 = $tbl2[0];
                            }else{
                                $Nome2 = "&nbsp;";
                            }
                            ?>

                            <?php
                            if($CodPartic1 != 0){
                            ?>
                                <td><div class="quadrinhoClick" style="text-align: left;" onclick="abreParticip(2, <?php echo $Cod; ?>, <?php echo $CodPartic2; ?>, '<?php echo $tbl[2]; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome2; ?> </div> </td>
                            <?php
                            }else{
                            ?>
                                <td><div class="quadrinhoClick" title="Preencha o turno anterior"><?php echo $Nome2; ?></div> </td>
                            <?php
                            }
                            ?>
                                <td class="etiq" title="Horas do turno"><?php if(($tbl[8]-$tbl[7]) > 0){echo ($tbl[8]-$tbl[7])."h";} ?></td>
                            <?php
                            }
                            ?>



                            <?php
                            if($Turnos >= 3){
                            ?>
                                <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                                <td><div class="quadrinho"> <?php if($tbl[10] == 0 && $tbl[11] == 0){echo "&nbsp;";}else{if($tbl[10] < 10){echo "0".$tbl[10].":00";}else{echo $tbl[10].":00";}}; ?> </div> </td>
                                <td><div class="quadrinho"> <?php if($tbl[11] == 0){echo "&nbsp;";}else{if($tbl[11] < 10){echo "0".$tbl[11].":00";}else{echo $tbl[11].":00";}}; ?> </div> </td>
                            <?php
                            $CodPartic3 = $tbl[9];
                            $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic3;");
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                $tbl3 = pg_fetch_row($rs3);
                                $Nome3 = $tbl3[0];
                            }else{
                                $Nome3 = "&nbsp;";
                            }
                            ?>

                            <?php
                            if($CodPartic2 != 0){
                            ?>
                                <td><div class="quadrinhoClick" style="text-align: left;" onclick="abreParticip(3, <?php echo $Cod; ?>, <?php echo $CodPartic3; ?>, '<?php echo $tbl[2]; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome3; ?> </div> </td>
                            <?php
                            }else{
                                ?>
                                <td><div class="quadrinhoClick" title="Preencha o turno anterior"><?php echo $Nome3; ?></div> </td>
                                <?php
                            }
                            ?>
                            <td class="etiq" title="Horas do turno"><?php if(($tbl[11]-$tbl[10]) > 0){echo ($tbl[11]-$tbl[10])."h";} ?></td>
                            <?php
                            }
                            ?>



                            <?php
                            if($Turnos >= 4){
                            ?>
                                <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                                <td><div class="quadrinho"> <?php if($tbl[13] == 0 && $tbl[14] == 0){echo "&nbsp;";}else{if($tbl[13] < 10){echo "0".$tbl[13].":00";}else{echo $tbl[13].":00";}}; ?> </div> </td>
                                <td><div class="quadrinho"> <?php if($tbl[14] == 0){echo "&nbsp;";}else{if($tbl[14] < 10){echo "0".$tbl[14].":00";}else{echo $tbl[14].":00";}}; ?> </div> </td>
                            <?php
                            $CodPartic4 = $tbl[12];
                            $rs4 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic4;");
                            $row4 = pg_num_rows($rs4);
                            if($row4 > 0){
                                $tbl4 = pg_fetch_row($rs4);
                                $Nome4 = $tbl4[0];
                            }else{
                                $Nome4 = "&nbsp;";
                            }
                            ?>
                            
                            <?php
                            if($CodPartic3 != 0){
                            ?>
                                <td><div class="quadrinhoClick" style="text-align: left;" onclick="abreParticip(4, <?php echo $Cod; ?>, <?php echo $CodPartic4; ?>, '<?php echo $tbl[2]; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome4; ?> </div> </td>
                            <?php
                            }else{
                                ?>
                                <td><div class="quadrinhoClick" title="Preencha o turno anterior"><?php echo $Nome4; ?></div> </td>
                                <?php
                            }
                            ?>
                            <td class="etiq" title="Horas do turno"><?php if(($tbl[14]-$tbl[13]) > 0){echo ($tbl[14]-$tbl[13])."h";} ?></td>
                            <?php
                            }
                            ?>


                        </tr>
                        <?php
                    }
                }
            ?>
        </table>
        <br><br>
    </div>

    <br><br>