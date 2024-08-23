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
        <?php
//            $Cod = (int) filter_input(INPUT_GET, 'codigo');
        ?>
         <!-- Apresenta os usuários do grupo -->
        <div style="padding: 10px;">
            <?php
                $rs3 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, daf_turno, daf_marca, letra, horaturno 
                FROM ".$xProj.".poslog LEFT JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".poslog.daf_turno = ".$xProj.".escaladaf_turnos.id
                WHERE eft_daf = 1 And ".$xProj.".poslog.ativo = 1 ORDER BY nomeusual, nomecompl ");
            ?>
            <table class="display" style="margin: 0 auto; width:85%;">
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td><input type="checkbox" value="ev" id="ev" title="marca para transferir." onClick="MarcaPartic(<?php echo $Cod ?>);" <?php if($tbl3[4] == 1) {echo "checked";} ?> ></td>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><div class="quadrgrupo"><?php if(is_null($tbl3[2]) || $tbl3[2] == ""){echo "&nbsp;";}else{echo $tbl3[2];} ?></div></td>
                        <td><div class="quadrgrupo"><?php echo $tbl3[1]; ?></div></td>
                        <td>
                            <select id="buscaturno" onchange="mudaTurno(<?php echo $Cod; ?>, value);" style="font-family: Lucida Sans Typewriter; font-size: .9rem; font-weight: bold; width: 200px;" title="Selecione um turno.">
                                <option value="<?php echo $tbl3[3]; ?>"><?php echo $tbl3[5]." - ".$tbl3[6]; ?></option>
                                <?php 
                                $OpTurnos = pg_query($Conec, "SELECT id, letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 ORDER BY letra");
                                if($OpTurnos){
                                    while ($Opcoes = pg_fetch_row($OpTurnos)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]." - ".$Opcoes[2]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>