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



        </script>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center; font-weight: bold;">Horários de Descanso<br></div>
        <div style="margin: 10px; padding: 10px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
            $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
            $Busca = addslashes($MesSalvo); 
            $Proc = explode("/", $Busca);
            $Mes = $Proc[0];
            if(strLen($Mes) < 2){
                $Mes = "0".$Mes;
            }
            $Ano = $Proc[1];

            $rs0 = pg_query($Conec, "SELECT ".$xProj.".escaladaf_ins.id, TO_CHAR(dataescala, 'DD'), feriado, nomeusual, turnoturno, horafolga 
            FROM ".$xProj.".poslog INNER JOIN (".$xProj.".escaladaf INNER JOIN ".$xProj.".escaladaf_ins ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id) ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escaladaf_ins.poslog_id 
            WHERE ".$xProj.".escaladaf_ins.ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And esc_grupo = $NumGrupo ORDER BY dataescala, nomeusual");
            $Dias = pg_num_rows($rs0);

            $rs = pg_query($Conec, "SELECT ".$xProj.".escaladaf.id, TO_CHAR(dataescala, 'DD') 
            FROM ".$xProj.".escaladaf INNER JOIN ".$xProj.".escaladaf_ins ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id
            WHERE ".$xProj.".escaladaf_ins.ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo 
            GROUP BY ".$xProj.".escaladaf.id 
            ORDER BY dataescala");
            
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0]; 
echo $Cod." ";
            }
            
            ?>
            <div style="position: relative; float: right; color: red; font-weight: bold;" id="mensagemQuadroHorario"></div>
            <table style="margin: 0 auto; width: 95%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td class="etiqAzul aCentro">Dia</td>
                    <td  class="etiqAzul aEsq">Nome</td>
                    <td class="etiqAzul aCentro">Turno</td>
                    <td class="etiqAzul aCentro">Descanso</td>
                </tr>
                <?php 
                $ContDia = "01";
                while($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0]; // id em escaladaf_ins
                    $Dia = $tbl0[1];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                        <td><?php echo $tbl0[1]; ?></td>
                        <td style="text-align: left;"><?php echo $tbl0[3]; ?></td> <!-- Nome -->
                        <td class="etiqAzul aCentro"><?php echo $tbl0[4]; ?></td> <!-- Turno escalado -->
                        <td><input type="text" value="<?php echo $tbl0[5]; ?>" style="width: 120px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaFolga(<?php echo $Cod; ?>, value);" title="Período de descanso no formato 00:00/00:00"/></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <br>
        </div>
    </body>
</html>