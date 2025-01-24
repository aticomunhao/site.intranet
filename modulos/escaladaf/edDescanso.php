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
        <style>
            .etiqAzul90{
                color: #036; font-style: italic; font-size: 90%; padding-right: 1px; padding-bottom: 1px;
            }
        </style>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center; font-weight: bold;">Horários de Descanso<br></div>
        <div style="margin: 10px; padding: 10px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
                $Semana_Extract = array(
                    '0' => 'D',
                    '1' => '2ª',
                    '2' => '3ª',
                    '3' => '4ª',
                    '4' => '5ª',
                    '5' => '6ª',
                    '6' => 'S',
                    'xª'=> ''
                );

            $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
            $Busca = addslashes($MesSalvo); 
            $Proc = explode("/", $Busca);
            $Mes = $Proc[0];
            if(strLen($Mes) < 2){
                $Mes = "0".$Mes;
            }
            $Ano = $Proc[1];

            $rsEft = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $NumGrupo");
            $Eft = pg_num_rows($rsEft); // Número de escalados

            $rs0 = pg_query($Conec, "SELECT ".$xProj.".escaladaf_ins.id, TO_CHAR(dataescala, 'DD'), feriado, nomeusual, turnoturno, horafolga, ".$xProj.".escaladaf_ins.poslog_id, ".$xProj.".escaladaf_ins.turnos_id, nomecompl, date_part('dow', dataescala) 
            FROM ".$xProj.".poslog INNER JOIN (".$xProj.".escaladaf INNER JOIN ".$xProj.".escaladaf_ins ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id) ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escaladaf_ins.poslog_id 
            WHERE ".$xProj.".escaladaf_ins.ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And esc_grupo = $NumGrupo ORDER BY dataescala, nomeusual");
//            $Dias = pg_num_rows($rs0);
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;" id="mensagemQuadroHorario"></div>
            <table style="margin: 0 auto; width: 95%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td class="etiqAzul aCentro">Dia</td>
                    <td></td>
                    <td class="etiqAzul aEsq">Nome</td>
                    <td class="etiqAzul aCentro">Turno</td>
                    <td class="etiqAzul aCentro">Interv</td>
                    <td class="etiqAzul aCentro">Descanso</td>
                </tr>
                <?php 
                $ContDia = 1;
                while($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0]; // id em escaladaf_ins
                    $PoslogId = $tbl0[6]; // pessas_id em poslog
                    $TurnosId = $tbl0[7]; // turnos_id em escaladaf_ins
                    $Dia = $tbl0[1];
                    $HoraFolga = $tbl0[5];
                    if(is_null($tbl0[3]) || $tbl0[3] == ""){
                        $Nome = substr($tbl0[8], 0, 20); // nomecompl
                    }else{
                        $Nome = substr($tbl0[3], 0, 22); //nomeusual
                    }

                    if(is_null($HoraFolga) || $HoraFolga == ""){
                        $rs1 = pg_query($Conec, "SELECT horafolga 
                        FROM ".$xProj.".escaladaf_ins 
                        WHERE horafolga IS NOT NULL And ativo = 1 And poslog_id = $PoslogId And grupo_ins = $NumGrupo ORDER BY dataescalains DESC");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $HoraFolga = $tbl1[0];
                            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_ins SET horafolga = '$HoraFolga' WHERE id = $Cod");
                        }
                    }
                    $rs2 = pg_query($Conec, "SELECT horaturno, interv 
                    FROM ".$xProj.".escaladaf_turnos 
                    WHERE id = $TurnosId And interv IS NOT NULL And ativo = 1 ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Interv = $tbl2[1];
                    }else{
                        $Interv = "";
                    }

                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                        <td class="etiqAzul90 aCentro"><?php if($ContDia == 1){echo "<div style='border: 1px solid; border-radius: 3px;'>".$tbl0[1]."</div>";} ?></td> <!-- Dia -->
                        <td class="etiqAzul aCentro" title="Dia da semana"><?php if($ContDia == 1){echo $Semana_Extract[$tbl0[9]];} ?></td>
                        <td class="etiqAzul90 aEsq" style="padding-left: 5px;"><?php echo "<div style='border: 1px solid; border-radius: 3px;'>".$Nome."</div>"; ?></td> <!-- Nome -->
                        <td class="etiqAzul aCentro"><?php echo $tbl0[4]; ?></td> <!-- Turno escalado -->
                        <td class="etiqAzul aCentro"><?php echo $Interv; ?></td> <!-- Intervalo de Turno - vem de escaladaf_turnos -->
                        <td><input type="text" value="<?php echo $HoraFolga; ?>" style="width: 120px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaFolga(<?php echo $Cod; ?>, value);" title="Período de descanso no formato 00:00/00:00"/></td>
                    </tr>
                    <?php
                    $ContDia++;
                    if($ContDia > $Eft){
                        $ContDia = 1;
                    }
                }
                ?>
            </table>
            <br>
        </div>
    </body>
</html>