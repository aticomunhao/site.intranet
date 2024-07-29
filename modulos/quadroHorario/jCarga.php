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
        if($Min < 0){
            $Min = ($Min+60); // $Min será negativo
            $Hora = ($Hora-1);
        }
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

    $Turnos = 1;
    
    ?>
    <div style="text-align: center;">
        <h5><?php echo $Mes_Extract[$Mes].'/'.$Ano; ?></h5>

        <!-- Mensal -->
        <table style="margin: 0 auto;">
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 15px;">Carga Mensal</td>
            </tr>
            <tr>
                <td class="etiq aEsq">Participante</td>
                <td class="etiq aCentro">Carga</td>
            </tr>
            <?php
            $rs = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
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
                    $CargaMin = 0;
                    $Carga1Min = 0;


                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI'), TO_CHAR(horafim1 - horaini1, 'HH24:MI')
                    FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
                    WHERE turno1_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        while($tbl1 = pg_fetch_row($rs1)){
                            $CargaCor =  $tbl1[0]; 
                            if($tbl1[2] >= "08:00"){
                                $CargaCor = ($tbl1[0]-1); // carga corrigida
                            }
                            $CargaMinCor = $tbl1[1];
                            if($tbl1[2] > "06:00" && $tbl1[2] < "08:00"){
                                $CargaMinCor = ($tbl1[1]-15);
                            }

//                            $Carga1 = $Carga1+$tbl1[0];
//                            $Carga1Min = $Carga1Min+$tbl1[1];
                            $Carga1 = $Carga1+$CargaCor;
                            $Carga1Min = $Carga1Min+$CargaMinCor;
                        }
                    }
                    
                    $Carga = $Carga+$Carga1;
                    $CargaMin = $CargaMin+$Carga1Min;
                    $CargaHoraria = SomaCarga($Carga, $CargaMin);
                    ?>
                    <tr>
                        <td style="text-align: left; font-size: 90%;"><?php echo $Nome; ?></td>
                        <td style="text-align: right; font-size: 90%; padding-left: 10px;"><?php echo $CargaHoraria; ?></td>
                    </tr>
                    <?php
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
                    <td class="etiq aCentro">Carga</td>
                </tr>
                <?php
                $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
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

                        //Carga Semanal turno1
                        $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI'), TO_CHAR(horafim1 - horaini1, 'HH24:MI') 
                        FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
                        WHERE turno1_id = $Cod1 And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'WW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            while($tbl1 = pg_fetch_row($rs1)){
                                $CargaHoraCor =  $tbl1[0]; 
                                if($tbl1[2] >= "08:00"){
                                    $CargaHoraCor = ($tbl1[0]-1); // carga corrigida
                                }
                                $CargaMinCor = $tbl1[1];
                                if($tbl1[2] > "06:00" && $tbl1[2] < "08:00"){
                                    $CargaMinCor = ($tbl1[1]-15); // carga minutos corrigida
                                }

                                $CargaHora1 = $CargaHora1+$CargaHoraCor;
                                $CargaMin1 = $CargaMin1+$CargaMinCor;
                            }
                        }
             

                        $CargaSemanalHora = $CargaSemanalHora+$CargaHora1;
                        $CargaSemanalMin = $CargaSemanalMin+$CargaMin1;
                        $CargaHorariaSemanal = SomaCarga($CargaSemanalHora, $CargaSemanalMin);
                    ?>        
                       <tr>
                            <td style="text-align: left; font-size: 90%;"><?php echo $Nome; ?></td>
                            <td style="text-align: right; padding-left: 10px; font-size: 90%;"><?php echo $CargaHorariaSemanal; ?></td>
                        </tr>
                    <?php
                }
            }
        }
        ?>
        </table>
    </div>