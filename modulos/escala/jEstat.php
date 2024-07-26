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
    '12' => 'Dezembro',
);
    function SomaCarga($Hora, $Min){
        $M = $Min%60;
        if($M == 0){
            $M = "00";
        }
        $H = floor($Min/60);
        $Hora = $Hora+$H;
        return $Hora."h ".$M."min";
    }

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
    ?>
<!--    <div style="border: 1px solid blue; border-radius: 15px; margin: 40px; padding: 20px; min-height: 200px; text-align: center;"> -->
    <div style="text-align: center;">
        <h5><?php echo $Mes_Extract[$Mes].'/'.$Ano; ?></h5>
        <!-- Mensal -->
        <table style="margin: 0 auto;">
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 15px;">Carga Mensal</td>
            </tr>
            <tr>
                <td class="etiq aEsq">Participante</td>
                <td class="etiq aCentro">Carga Mensal</td>
            </tr>
            <?php
            $rs = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
            $row = pg_num_rows($rs);
            if($row > 0){
                while($tbl = pg_fetch_row($rs)){
                    $Cod = $tbl[0];
                    $Nome = $tbl[2];
                    if(is_null($tbl[2]) || $tbl[2] == ""){
                        $Nome = $tbl[1];
                    }
                    $Carga = 0;
                    $Carga1 = 0;
                    $Carga2 = 0;
                    $Carga3 = 0;
                    $Carga4 = 0;
                    $CargaMin = 0;
                    $Carga1Min = 0;
                    $Carga2Min = 0;
                    $Carga3Min = 0;
                    $Carga4Min = 0;

                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno1_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        while($tbl1 = pg_fetch_row($rs1)){
                            $Carga1 = $Carga1+$tbl1[0];
                            $Carga1Min = $Carga1Min+$tbl1[1];
                        }
                    }
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim2, horaini2), 'HH24'), TO_CHAR(AGE(horafim2, horaini2), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno2_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        while($tbl2 = pg_fetch_row($rs2)){
                            $Carga2 = $Carga2+$tbl2[0];
                            $Carga2Min = $Carga2Min+$tbl2[1];
                        }
                    }
                    $rs3 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim3, horaini3), 'HH24'), TO_CHAR(AGE(horafim3, horaini3), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno3_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Carga3 = $Carga3+$tbl3[0];
                            $Carga3Min = $Carga3Min+$tbl3[1];
                        }
                    }
                    $rs4 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim4, horaini4), 'HH24'), TO_CHAR(AGE(horafim4, horaini4), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno4_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row4 = pg_num_rows($rs4);
                    if($row4 > 0){
                        while($tbl4 = pg_fetch_row($rs4)){
                            $Carga4 = $Carga4+$tbl4[0];
                            $Carga4Min = $Carga4Min+$tbl4[1];
                        }
                    }

                    $Carga = $Carga+$Carga1+$Carga2+$Carga3+$Carga4;
                    $CargaMin = $CargaMin+$Carga1Min+$Carga2Min+$Carga3Min+$Carga4Min;
                    $CargaHoraria = SomaCarga($Carga, $CargaMin);

                    if($Carga > 0 || $CargaMin > 0){
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $Nome; ?></td>
                            <td style="text-align: right; padding-left: 10px;"><?php echo $CargaHoraria; ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
        </table>
        

        <!-- Semanal -->
        <table style="margin: 0 auto;">
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 15px;">Jornada Semanal</td>
            </tr>
            <?php
            //Seleciona as semanas do mês e ano para os escalados do grupo
            $rs = pg_query($Conec, "SELECT DISTINCT TO_CHAR(dataescala, 'WW') FROM ".$xProj.".escalas 
            WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY TO_CHAR(dataescala, 'WW') ");
            $row = pg_num_rows($rs);

            while($tbl = pg_fetch_row($rs)){
                $SemanaNum = $tbl[0]; // número da semana no ano

                //Dia de início dessa Semana
                $a = new DateTime();
                $a->setISODate($Ano, $SemanaNum);
                $DiaIniSem = $a->format('d/m');

                //Dia final dessa semana
                $b = new DateTime();
                $b->setISODate($Ano, $SemanaNum, 7);
                $DiaFimSem = $b->format('d/m');

                ?>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 15px;"></td>
                </tr>
                <tr>
                    <td class="etiq aEsq">Semana: <?php echo $DiaIniSem." a ".$DiaFimSem; ?></td>
                    <td class="etiq aCentro">Carga Semanal</td>
                </tr>
                <?php
                $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
                $row0 = pg_num_rows($rs0);
                if($row0 > 0){
                    while($tbl0 = pg_fetch_row($rs0)){
                        $Cod1 = $tbl0[0];
                        $Nome = $tbl0[2];
                        if(is_null($tbl0[2]) || $tbl0[2] == ""){
                            $Nome = $tbl0[1];
                        }
                        $CargaHorariaSemanal = 0;
                        $CargaSemanalHora = 0;
                        $CargaSemanalMin = 0;

                        $CargaHora1 = 0;
                        $CargaMin1 = 0;
                        $CargaHora2 = 0;
                        $CargaMin2 = 0;
                        $CargaHora3 = 0;
                        $CargaMin3 = 0;
                        $CargaHora4 = 0;
                        $CargaMin4 = 0;
                        //Carga Semanal turno1
                        $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI') FROM ".$xProj.".escalas 
                        WHERE turno1_id = $Cod1 And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'WW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            while($tbl1 = pg_fetch_row($rs1)){
                                $CargaHora1 = $CargaHora1+$tbl1[0];
                                $CargaMin1 = $CargaMin1+$tbl1[1];
                            }
                        }
                        //Carga Semanal turno2
                        $rs2 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim2, horaini2), 'HH24'), TO_CHAR(AGE(horafim2, horaini2), 'MI') FROM ".$xProj.".escalas 
                        WHERE turno2_id = $Cod1 And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'WW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            while($tbl2 = pg_fetch_row($rs2)){
                                $CargaHora2 = $CargaHora2+$tbl2[0];
                                $CargaMin2 = $CargaMin2+$tbl2[1];
                            }
                        }
                        //Carga Semanal turno3
                        $rs3 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim3, horaini3), 'HH24'), TO_CHAR(AGE(horafim3, horaini3), 'MI') FROM ".$xProj.".escalas 
                        WHERE turno3_id = $Cod1 And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'WW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            while($tbl3 = pg_fetch_row($rs3)){
                                $CargaHora3 = $CargaHora3+$tbl3[0];
                                $CargaMin3 = $CargaMin3+$tbl3[1];
                            }
                        }
                        //Carga Semanal turno4
                        $rs4 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim4, horaini4), 'HH24'), TO_CHAR(AGE(horafim4, horaini4), 'MI') FROM ".$xProj.".escalas 
                        WHERE turno4_id = $Cod1 And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'WW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row4 = pg_num_rows($rs4);
                        if($row4 > 0){
                            while($tbl4 = pg_fetch_row($rs4)){
                                $CargaHora4 = $CargaHora4+$tbl4[0];
                                $CargaMin4 = $CargaMin4+$tbl4[1];
                            }
                        }

                        $CargaSemanalHora = $CargaSemanalHora+$CargaHora1+$CargaHora2+$CargaHora3+$CargaHora4;
                        $CargaSemanalMin = $CargaSemanalMin+$CargaMin1+$CargaMin2+$CargaMin3+$CargaMin4;
                        $CargaHorariaSemanal = SomaCarga($CargaSemanalHora, $CargaSemanalMin);
                    ?>        
                       <tr>
                            <td style="text-align: left;"><?php echo $Nome; ?></td>
                            <td style="text-align: right; padding-left: 10px;"><?php echo $CargaHorariaSemanal; ?></td>
                        </tr>
                    <?php
                }
            }
        }
        ?>
        </table>
    </div>