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

    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(is_null($MesSalvo) || $MesSalvo == ""){
        $MesSalvo = date("m")."/".date("Y");
    }
    $Proc = explode("/", $MesSalvo);
    if(is_null($Proc[1])){
        $Mes = date("m");
    }else{
        $Mes = $Proc[0];
    }
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    if(is_null($Proc[1])){
        $Ano = date("Y");
        }else{
        $Ano = $Proc[1];
    }

    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }
    ?>
    <div style="text-align: center;">
        <h5>Carga Mensal e Semanal</h5>
        <div style="margin: 10px; padding: 20px; border: 2px solid green; border-radius: 15px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;">

                    <!-- Mensal -->
                    <table style="margin: 0 auto;">
                        <tr>
                            <td colspan="2" style="text-align: center; padding-top: 15px;">Carga Mensal</td>
                        </tr>

                        <?php
                        $rs = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
                        $row = pg_num_rows($rs);
                        if($row > 0){
                            while($tbl = pg_fetch_row($rs)){
                                $Cod = $tbl[0];
                                $Nome = substr($tbl[2], 0, 13);
                                if(is_null($tbl[2]) || $tbl[2] == ""){
                                    $Nome = substr($tbl[1], 0, 13);
                                }
                                $CargaMes = 0;
                                $rs1 = pg_query($Conec, "SELECT TO_CHAR(SUM(cargatime), 'HH24:MI') 
                                FROM ".$xProj.".escaladaf_ins 
                                WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' And grupo_ins = $NumGrupo ");
                                $row1 = pg_num_rows($rs1);
                                if($row1 > 0){
                                    $tbl1 = pg_fetch_row($rs1);
                                    $CargaMes =  $tbl1[0]; 
                                }
                                ?>
                                <tr>
                                    <td style="text-align: left; font-size: 90%;"><div class='quadrodia' style="padding-left: 3px; padding-right: 3px; font-size: 90%;"><?php echo $Nome; ?></div></td>
                                    <td><div class='quadrodia' style="text-align: center; padding-left: 3px; padding-right: 3px; font-size: 90%;"><?php if($CargaMes == 0){echo "&nbsp;";}else{echo $CargaMes;} ?></div></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>

                <!-- Semanal -->
                <?php
                //Seleciona as semanas do mês e ano para os escalados do grupo
                //WW 	número da semana do ano (1–53) (a primeira semana começa no primeiro dia do ano)
                //IW 	número da semana do ano de numeração de semanas ISO 8601 (01–53; a primeira quinta-feira do ano é na semana 1)
                $rs = pg_query($Conec, "SELECT DISTINCT TO_CHAR(dataescala, 'IW') FROM ".$xProj.".escaladaf 
                WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo ORDER BY TO_CHAR(dataescala, 'IW') ");
                $row = pg_num_rows($rs);

                while($tbl = pg_fetch_row($rs)){
                    $SemanaNum = $tbl[0]; // número da semana no ano
//echo $SemanaNum." ";
                    ?>
                    <div class="col" style="margin: 0 auto; text-align: left;">
                        <table style="margin: 0 auto;">
                            <tr>
                                <td colspan="2" style="text-align: center; padding-top: 15px;">Jornada Semanal</td>
                            </tr>
                            <?php

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
                                <td colspan='2' class="etiq aEsq">Semana: <?php echo $DiaIniSem." a ".$DiaFimSem; ?></td>
                            </tr>
                            <?php
                            $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
                            $row0 = pg_num_rows($rs0);
                            if($row0 > 0){
                                while($tbl0 = pg_fetch_row($rs0)){
                                    $Cod1 = $tbl0[0];
                                    $Nome = substr($tbl0[2], 0, 13);
                                    if(is_null($tbl0[2]) || $tbl0[2] == ""){
                                        $Nome = substr($tbl0[1], 0, 13);
                                    }

                                    $CargaHoraCor =  0;

                                    //Carga Semanal turno1
                                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(SUM(cargatime), 'HH24:MI') 
                                    FROM ".$xProj.".escaladaf LEFT JOIN ".$xProj.".escaladaf_ins ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id 
                                    WHERE poslog_id = $Cod1 And TO_CHAR(dataescala, 'IW') = '$SemanaNum' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        $tbl1 = pg_fetch_row($rs1);
                                        $CargaHoraCor =  $tbl1[0]; 
                                    }
                                    ?>
                                    <tr>
                                        <td style="text-align: left; font-size: 90%;"><div class='quadrodia' style="padding-left: 3px; padding-right: 3px; font-size: 90%;"><?php echo $Nome; ?></div></td>
                                        <td><div class='quadrodia' style="text-align: center; padding-left: 3px; padding-right: 3px; font-size: 90%;"><?php if($CargaHoraCor == 0){echo "&nbsp;";}else{echo $CargaHoraCor;} ?></div></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>
                <?php
                }
                ?>
                
            </div>
        </div>
    </div>