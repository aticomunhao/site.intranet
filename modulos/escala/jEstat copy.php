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
    ?>
    <div style="border: 1px solid blue; border-radius: 15px; margin: 20px; padding: 20px; min-height: 200px; text-align: center;">
        <table style="margin: 0 auto;">
            <tr>
                <td class="etiq aEsq">Participante</td>
                <td class="etiq aCentro">Carga Mensal</td>
            </tr>
            <?php
            echo "<h5>"; 
            echo $Mes_Extract[$Mes].'/'.$Ano; 
            echo "</h5>";

            $Carga1 = 0;
            $Carga2 = 0;
            $Carga3 = 0;
            $Carga4 = 0;

            $rs = pg_query($Conec, "SELECT turno1_id, nomecompl 
            FROM ".$xProj.".escalas INNER JOIN ".$xProj.".poslog ON ".$xProj.".escalas.turno1_id = ".$xProj.".poslog.pessoas_id 
            WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' 
            GROUP BY turno1_id, nomecompl
            ORDER BY nomecompl");
            $row = pg_num_rows($rs);
            if($row > 0){
                while($tbl = pg_fetch_row($rs)){
                    $Cod = $tbl[0];
                    
                    $rs1 = pg_query($Conec, "SELECT SUM(horafim1 - horaini1) FROM ".$xProj.".escalas 
                    WHERE turno1_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $tbl1 = pg_fetch_row($rs1);
                    $Carga1 = $tbl1[0];
                    if($Turnos >= 2){
                        $rs2 = pg_query($Conec, "SELECT SUM(horafim2 - horaini2) FROM ".$xProj.".escalas 
                        WHERE turno2_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl2 = pg_fetch_row($rs2);
                        $Carga2 = $tbl2[0];
                        echo "Carga2 = ".$Carga2;
                    }
                    if($Turnos >= 3){
                        $rs3 = pg_query($Conec, "SELECT SUM(horafim3 - horaini3) FROM ".$xProj.".escalas 
                        WHERE turno3_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        $Carga3 = $tbl3[0];
                    }
                    if($Turnos >= 4){
                        $rs4 = pg_query($Conec, "SELECT SUM(horafim4 - horaini4) FROM ".$xProj.".escalas 
                        WHERE turno4_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Carga4 = $tbl4[0];
                    }


                    $Carga = $Carga1+$Carga2+$Carga3+$Carga4;
                ?>
                <tr>
                    <td style="text-align: left;"><?php echo $tbl[1]; ?></td>
                    <td style="text-align: right; padding-left: 10px;"><?php echo $Carga." horas"; ?></td>
                </tr>
                <?php
                }
            }
            ?>
        </table>
    </div>
    <br>