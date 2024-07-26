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
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
$Semana_Extract = array(
    '0' => 'Dom',
    '1' => '2ª',
    '2' => '3ª',
    '3' => '4ª',
    '4' => '5ª',
    '5' => '6ª',
    '6' => 'Sab',
    'xª'=> ''
);

    if(isset($_REQUEST["mesano"])){
        if($_REQUEST["mesano"] != ""){
            $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
            $Proc = explode("/", $Busca);
            $Mes = $Proc[0];
            if(strLen($Mes) < 2){
                $Mes = "0".$Mes;
            }
            if($Proc[1] == ""){
                return false;
            }
            $Ano = $Proc[1];
            $Data = date('01/'.$Mes.'/'.$Ano);
        }else{
            $Mes = date("m");
            $Ano = date("Y");
        }
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
    $Escalante = parEsc("esc_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // escalante do grupo
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

<!--    <div style="border: 1px solid; border-radius: 15px; padding: 5px; min-height: 200px; text-align: center;"> -->
    <div style="text-align: center;">
        <h5><?php echo $Mes_Extract[$Mes].'/'.$Ano; ?></h5>
        <?php 
        $rs = pg_query($Conec, "SELECT id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI'), turno2_id, TO_CHAR(horaini2, 'HH24:MI'), TO_CHAR(horafim2, 'HH24:MI'), turno3_id, TO_CHAR(horaini3, 'HH24:MI'), 
        TO_CHAR(horafim3, 'HH24:MI'), turno4_id, TO_CHAR(horaini4, 'HH24:MI'), TO_CHAR(horafim4, 'HH24:MI'), date_part('dow', dataescala), TO_CHAR(horafim1 - horaini1, 'HH24:MI'), TO_CHAR(horafim2 - horaini2, 'HH24:MI'), TO_CHAR(horafim3 - horaini3, 'HH24:MI'), TO_CHAR(horafim4 - horaini4, 'HH24:MI') 
        FROM ".$xProj.".escalas WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
        $row = pg_num_rows($rs);
        
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td class="etiq aCentro">Data</td>
                <td class="etiq aCentro">Sem</td>
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
                            <td><div class="quadrinho"> <?php echo $Semana_Extract[$tbl[15]]; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if(is_null($tbl[4]) || $tbl[4] == 0 && is_null($tbl[5]) || $tbl[5] == 0){echo "&nbsp;";}else{echo $tbl[4];}; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if(is_null($tbl[5]) || $tbl[5] == 0){echo "&nbsp;";}else{echo $tbl[5];}; ?> </div> </td>
                            <?php
                            $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic1;");
                            $row1 = pg_num_rows($rs1);
                            if($row1 > 0){
                                $tbl1 = pg_fetch_row($rs1);
                                $Nome1 = $tbl1[1];
                                if(is_null($tbl1[1]) || $tbl1[1] == ""){
                                    $Nome1 = $tbl1[0];    
                                }
                            }else{
                                $Nome1 = "&nbsp;";
                            }

                            ?>
                            <td><div <?php if($Escalante == 1){echo "class='quadrinhoClick'";}else{echo "class='quadrinho'";} ?> style="text-align: left; font-weight: bold;" onclick="abreParticip(1, <?php echo $Cod; ?>, <?php echo $CodPartic1; ?>, '<?php echo $tbl[2]; ?>', '<?php echo $Nome1; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome1; ?> </div> </td>
                            <td class="etiq" title="Horas do turno"><?php if(!is_null($tbl[16]) && $tbl[16] != 0){echo $tbl[16]."h";} ?></td>


                            <?php
                            if($Turnos >= 2){
                            ?>
                            <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                            <td><div class="quadrinho"> <?php if($tbl[7] == 0 && $tbl[8] == 0){echo "&nbsp;";}else{echo $tbl[7];}; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if($tbl[8] == 0){echo "&nbsp;";}else{echo $tbl[8];}; ?> </div> </td>
                            <?php
                            $CodPartic2 = $tbl[6];
                            $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic2;");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $Nome2 = $tbl2[1];
                                if(is_null($tbl2[1]) || $tbl2[1] == ""){
                                    $Nome2 = $tbl2[0];    
                                }
                            }else{
                                $Nome2 = "&nbsp;";
                            }
                            ?>

                            <?php
                            if($CodPartic1 != 0){ //Quadrinho anterior
                            ?>
                                <td><div <?php if($Escalante == 1){echo "class='quadrinhoClick'";}else{echo "class='quadrinho'";} ?> style="text-align: left; font-weight: bold;" onclick="abreParticip(2, <?php echo $Cod; ?>, <?php echo $CodPartic2; ?>, '<?php echo $tbl[2]; ?>', '<?php echo $Nome2; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome2; ?> </div> </td>
                            <?php
                            }else{
                            ?>
                                <td><div class="quadrinho" title="Preencha o turno anterior"><?php echo $Nome2; ?></div> </td>
                            <?php
                            }
                            ?>
                                <td class="etiq" title="Horas do turno"><?php if(!is_null($tbl[17]) && $tbl[17] != 0){echo $tbl[17]."h";} ?></td>
                            <?php
                            }
                            ?>



                            <?php
                            if($Turnos >= 3){
                            ?>
                                <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                                <td><div class="quadrinho"> <?php if($tbl[10] == 0 && $tbl[11] == 0){echo "&nbsp;";}else{echo $tbl[10];}; ?> </div> </td>
                                <td><div class="quadrinho"> <?php if($tbl[11] == 0){echo "&nbsp;";}else{echo $tbl[11];}; ?> </div> </td>
                            <?php
                            $CodPartic3 = $tbl[9];
                            $rs3 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic3;");
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                $tbl3 = pg_fetch_row($rs3);
                                $Nome3 = $tbl3[1];
                                if(is_null($tbl3[1]) || $tbl3[1] == ""){
                                    $Nome3 = $tbl3[0];    
                                }
                            }else{
                                $Nome3 = "&nbsp;";
                            }
                            ?>

                            <?php
                            if($CodPartic2 != 0){ //Quadrinho anterior
                            ?>
                                <td><div <?php if($Escalante == 1){echo "class='quadrinhoClick'";}else{echo "class='quadrinho'";} ?> style="text-align: left; font-weight: bold;" onclick="abreParticip(3, <?php echo $Cod; ?>, <?php echo $CodPartic3; ?>, '<?php echo $tbl[2]; ?>', '<?php echo $Nome3; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome3; ?> </div> </td>
                            <?php
                            }else{
                                ?>
                                <td><div class="quadrinho" title="Preencha o turno anterior"><?php echo $Nome3; ?></div> </td>
                                <?php
                            }
                            ?>
                            <td class="etiq" title="Horas do turno"><?php if(!is_null($tbl[18]) && $tbl[18] != 0){echo $tbl[18]."h";} ?></td>
                            <?php
                            }
                            ?>



                            <?php
                            if($Turnos >= 4){
                            ?>
                                <td><label style="padding-left: 30px;"></label></td> <!-- separador -->
                                <td><div class="quadrinho"> <?php if($tbl[13] == 0 && $tbl[14] == 0){echo "&nbsp;";}else{echo $tbl[13];}; ?> </div> </td>
                                <td><div class="quadrinho"> <?php if($tbl[14] == 0){echo "&nbsp;";}else{echo $tbl[14];}; ?> </div> </td>
                            <?php
                            $CodPartic4 = $tbl[12];
                            $rs4 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic4;");
                            $row4 = pg_num_rows($rs4);
                            if($row4 > 0){
                                $tbl4 = pg_fetch_row($rs4);
                                $Nome4 = $tbl4[1];
                                if(is_null($tbl4[1]) || $tbl4[1] == ""){
                                    $Nome4 = $tbl4[0];    
                                }
                            }else{
                                $Nome4 = "&nbsp;";
                            }
                            ?>
                            
                            <?php
                            if($CodPartic3 != 0){ //Quadrinho anterior
                            ?>
                                <td><div <?php if($Escalante == 1){echo "class='quadrinhoClick'";}else{echo "class='quadrinho'";} ?> style="text-align: left; font-weight: bold;" onclick="abreParticip(4, <?php echo $Cod; ?>, <?php echo $CodPartic4; ?>, '<?php echo $tbl[2]; ?>', '<?php echo $Nome4; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome4; ?> </div> </td>
                            <?php
                            }else{
                                ?>
                                <td><div class="quadrinho" title="Preencha o turno anterior"><?php echo $Nome4; ?></div> </td>
                                <?php
                            }
                            ?>
                            <td class="etiq" title="Horas do turno"><?php if(!is_null($tbl[19]) && $tbl[19] != 0){echo $tbl[19]."h";} ?></td>
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