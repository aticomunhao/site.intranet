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
        <script type="text/javascript">
            LargTela = $(window).width();
            if(parseInt(document.getElementById("modificavel").value) === 1){
                $('#tabelaEquipe tbody').sortable({
                   helper: function(e, ui){
                      ui.children().each(function () {
                         $(this).width($(this).width());
                      });
                      return ui;
                   },
                   scroll: true,
                   update: function(event, ui){
                      serial = $(this).sortable('serialize');
                      $.ajax({
                         url: "modulos/escaladaf/salvaEscDaf.php?acao=salvaDragEquipe&numgrupo="+document.getElementById("guardagrupo").value,
                         type: "POST",
                         data: serial,
                         success: function(response){
//alert(response);
                            Resp = eval("(" + response + ")");
                            if(parseInt(Resp.coderro) === 1){
                                alert("Esta operação requer nível administrativo superior.", "Permissão");
                            }
                            $("#relacaoParticip").load("modulos/escaladaf/equipe.php?diaid="+document.getElementById("guardaDiaId").value+"&numgrupo="+document.getElementById("guardagrupo").value);
                            $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardagrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                         },
                         error: function(){
                            alert("Houve erro do AJAX");
                         }
                      });
                   }
                }).disableSelection();
            }
        </script>
    </head>
    <body> 
        <!-- Apresenta os usuários do grupo -->
        <div style="padding: 10px;">
            <?php
                if(isset($_REQUEST["numgrupo"])){
                    $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
                }else{
                    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
                }
                if($NumGrupo == 0 || $NumGrupo == ""){
                    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
                }

                if(isset($_REQUEST["diaid"])){
                    //Salva em poslog a última sequência de letras inserida
                    $DiaId = $_REQUEST["diaid"];
                    $rs4 = pg_query($Conec, "SELECT poslog_id, turnos_id FROM ".$xProj.".escaladaf_ins WHERE escaladaf_id = $DiaId");
                    $row4 = pg_num_rows($rs4);
                    if($row4 > 0){
                        While ($tbl4 = pg_fetch_row($rs4)){
                            $Cod = $tbl4[0];
                            $TurnoIns = $tbl4[1];                            
                            pg_query($Conec, "UPDATE ".$xProj.".poslog SET daf_turno = $TurnoIns WHERE pessoas_id = $Cod ;");
                        }
                    }
                }
                $Modificavel = 1;

                $rs3 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, daf_turno, daf_marca, letra, horaturno, ordem_daf, destaq, valeref 
                FROM ".$xProj.".poslog LEFT JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".poslog.daf_turno = ".$xProj.".escaladaf_turnos.id
                WHERE eft_daf = 1 And esc_grupo = $NumGrupo ORDER BY ordem_daf, nomeusual, nomecompl ");
                // And ".$xProj.".poslog.ativo = 1
            ?>
            <input type="hidden" id="guardagrupo" value="<?php echo $NumGrupo; ?>" />
            <input type="hidden" id="guardaDiaId" value="<?php echo $DiaId; ?>" />
            <input type="hidden" id="modificavel" value="<?php echo $Modificavel; ?>" />

            <div style="text-align: left; padding-left: 30px;">
                <label class="etiqAzul">Esta sequência pode ser modificada arrastando os nomes com o mouse, para cima ou pra baixo</label>
            </div>
            <table id="tabelaEquipe" class="display" style="margin: 0 auto; width:95%;">
                <tbody id="sortabletab">
                    <?php 
                    while($tbl3 = pg_fetch_row($rs3)){
                        $Cod = $tbl3[0];
                        $Destaq = $tbl3[8];
                        $ValeRef = $tbl3[9];
                        ?>
                        <tr id="posicao_<?php echo $Cod; ?>"> <!-- vai criar um array posicao  -->
                            <td style="min-width: 20px;"><div style="font-size: 80%; color: gray; text-align: center; padding-left: 2px; padding-right: 2px; border: 1px solid; border-radius: 3px;"><?php echo $tbl3[7]; ?><div></td>
                            <td></td>
                            <td><input type="checkbox" value="ev" id="ev" title="marca para transferir." onClick="MarcaPartic(<?php echo $Cod ?>);" <?php if($tbl3[4] == 1) {echo "checked";} ?> ></td>
                            <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                            <td><div style="color: black; cursor: pointer;"><?php if(is_null($tbl3[2]) || $tbl3[2] == ""){echo "&nbsp;";}else{echo $tbl3[2];} ?></div></td>
                            <td><div style="color: black; cursor: pointer;"><?php echo $tbl3[1]; ?></div></td>
                            <td style="">
                                <select id="buscaturno" onchange="mudaTurno(<?php echo $Cod; ?>, value);" style="font-family: Lucida Sans Typewriter; font-size: .9rem; font-weight: bold; width: 200px;" title="Selecione um turno.">
                                    <option value="<?php echo $tbl3[3]; ?>"><?php echo $tbl3[5]." - ".$tbl3[6]; ?></option>
                                    <?php 
                                    $OpTurnos = pg_query($Conec, "SELECT id, letra, horaturno, destaq, valeref FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And grupo_turnos = $NumGrupo ORDER BY letra");
                                    if($OpTurnos){
                                        while ($Opcoes = pg_fetch_row($OpTurnos)){ ?>
                                            <option <?php 
                                            if($Opcoes[3] == 1){echo "class='quadroletraYellow'";} 
                                            if($Opcoes[3] == 2){echo "class='quadroletraBlue'";} 
                                            if($Opcoes[3] == 3){echo "class='quadroletraGreen'";} 
                                            if($Opcoes[4] == 0){echo "class='corVerm' title='Sem Vale Refeição'";} 
                                            ?> 
                                            value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]." - ".$Opcoes[2]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <label style="font-size: 80%; color: blue; cursor: pointer; text-decoration: underline;" onclick="abreAnot(<?php echo $Cod; ?>);">Nota</label>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>