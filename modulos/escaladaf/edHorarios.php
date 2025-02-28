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
            $(document).ready(function(){
                $("#insletra").change(function(){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscaLetra&numgrupo="+document.getElementById("guardanumgrupo").value+"&letra="+document.getElementById("insletra").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    document.getElementById("insletra").value = "";
                                }else{
                                    if(parseInt(Resp.jatem) > 0){
                                        document.getElementById("insletra").value = "";
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Letra já existe',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                });
            });

            function insereLetra(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscaOrdem&numgrupo="+document.getElementById("guardanumgrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    document.getElementById("insordem").value = 0;
                                }else{
                                    if(parseInt(Resp.quantTurno) > 25){
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Número máximo de turnos (25) atingido',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        document.getElementById("insordem").value = Resp.ordem;
                                        document.getElementById("inserirletra").style.display = "block";
                                        document.getElementById("abreinsletra").style.visibility = "hidden";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaInsLetra(){
                document.getElementById("inserirletra").style.display = "none";
                document.getElementById("abreinsletra").style.visibility = "visible";
            }

        </script>
    </head>
    <body> 
        <div style="margin-top: 15px; text-align: center; font-weight: bold;">Horários de Trabalho<br>
            <label style="font-size: 90%; font-weight: normal;">Modificações aqui <b>NÃO</b> são passadas para a escala já inserida afim de preservar o passado.</label>
        </div>
        <div style="margin: 10px; padding: 10px; text-align: center; border: 2px solid green; border-radius: 15px;">
            <?php
            if(isset($_REQUEST["numgrupo"])){
                $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
            }else{
                $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
            }
            if($NumGrupo == 0 || $NumGrupo == ""){
                $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            }
            $rs3 = pg_query($Conec, "SELECT id, letra, horaturno, ordemletra, destaq, TO_CHAR(cargahora, 'HH24:MI'), TO_CHAR(cargacont, 'HH24:MI'), TO_CHAR(interv, 'HH24:MI'), infotexto, valeref FROM ".$xProj.".escaladaf_turnos WHERE ativo = 1 And grupo_turnos = $NumGrupo ORDER BY ordemletra");
            ?>
            <input type="hidden" id="guardanumgrupo" value="<?php echo $NumGrupo; ?>" />
            <div style="position: relative; float: right; color: red; font-weight: bold;" id="mensagemQuadroHorario"></div>
            <table style="margin: 0 auto; width: 95%;">
                <tr>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td title="Ordem de apresentação no quadro de horários. Organiza a apresentação na primeira tela.">Ordem</td>
                    <td>Letra</td>
                    <td title="Destaque fundo: Transparente - Amarelo - Azul - Verde">Destaque</td>
                    <td>Turno</td>
                    <td>Horas</td>
                    <td>Intervalo</td>
                    <td>Carga</td>
                    <td>Vale</td>
                    <td></td>
                </tr>
                <?php 
                while($tbl3 = pg_fetch_row($rs3)){
                    $Cod = $tbl3[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl3[0]; ?></td>
                        <td><input type="text" value="<?php echo $tbl3[3]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaOrdem(<?php echo $Cod; ?>, value);" title="Ordem de apresentação no quadro de horários. Organiza a apresentação na primeira tela."/></td>
                        <td><input type="text" value="<?php echo $tbl3[1]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px; 
                            <?php
                            if($tbl3[4] == 0){echo "background-color: white;";}  
                            if($tbl3[4] == 1){echo "background-color: yellow;";} 
                            if($tbl3[4] == 2){echo "background-color: #00BFFF;";} 
                            if($tbl3[4] == 3){echo "background-color: #00FF7F;";} 

                            if($tbl3[9] == 0){echo "border-width: 2px; border-color: red;";} // sem vale refeição

                            ?>" onchange="editaLetra(<?php echo $Cod; ?>, value);"/></td>
                        <td>
                            <input type="checkbox" id="ev" title="Sem destaque" onClick="marcaTurno(<?php echo $Cod ?>, 0);" <?php if($tbl3[4] == 0) {echo "checked";} ?> >
                            <input type="checkbox" id="ev" title="Marca para destacar Amarelo" onClick="marcaTurno(<?php echo $Cod ?>, 1);" <?php if($tbl3[4] == 1) {echo "checked";} ?> >
                            <input type="checkbox" id="ev" title="Marca para destacar Azul" onClick="marcaTurno(<?php echo $Cod ?>, 2);" <?php if($tbl3[4] == 2) {echo "checked";} ?> >
                            <input type="checkbox" style="border: 1px solid #00FF7F;" id="ev" title="Marca para destacar Verde" onClick="marcaTurno(<?php echo $Cod ?>, 3);" <?php if($tbl3[4] == 3) {echo "checked";} ?> >
                        </td>
                        <td>
                            <input type="text" value="<?php echo $tbl3[2]; ?>" style="width: 150px; text-align: center; border: 1px solid; border-radius: 3px;" onclick="abreQuadroTurnos(<?php echo $Cod; ?>);"/>
                        </td>
                        <?php
                        if($tbl3[8] == 1){ // infotexto: férias, inss, folga, etc
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                        }else{
                        ?>
                            <td style="width: 80px; text-align: center;"><?php echo $tbl3[5]; ?></td>
                            <td><input type="text" id="edinterv" value="<?php echo $tbl3[7]; ?>" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onchange="editaInterv(<?php echo $Cod; ?>, value);"/></td>
                            <td style="width: 70px; text-align: center;"><?php echo $tbl3[6]; ?> </td>
                            <td><input type="checkbox" id="valeRef" title="<?php if($tbl3[9] == 1){echo 'Turno com vale refeição';}else{echo 'Turno sem vale refeição.';} ?>" onClick="marcaVale(this, <?php echo $Cod ?>);" <?php if($tbl3[9] == 1) {echo "checked";} ?> ></td>
                        <?php
                        }
                        ?>
                        <td style="text-align: center; padding-left: 5px;"><img src='imagens/lixeiraPreta.png' height='15px;' style='cursor: pointer; padding-right: 3px;' onclick='apagaLetra(<?php echo $Cod; ?>);' title='Apagar esta letra.'></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <div style="text-align: left; padding-left: 10px;">
                <button class="botpadramarelo" style="font-size: 70%; padding: 1px;" onclick="renumeraLetras();">Renumerar</button>
            </div>
            <br>

            <div id="inserirletra" style="display: none; margin: 0 auto; width: 400px; text-align: center; border: 1px solid; border-radius: 10px; padding: 2px;">
                <span class="close" style="position: relative; float: rigth; top: -10px; font-size: 200%; color: black;" onclick="fechaInsLetra();">&times;</span>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td>Ordem</td>
                        <td>Letra</td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;">0</td>
                        <td><input type="text" id="insordem" value="" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onkeypress="if(event.keyCode===13){javascript:foco('insletra');return false;}" /></td>
                        <td><input type="text" id="insletra" value="" style="width: 70px; text-align: center; border: 1px solid; border-radius: 3px;" onkeypress="if(event.keyCode===13){javascript:foco('insordem');return false;}" /></td>
                    </tr>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <button class="botpadrblue" onclick="salvaLetra();">Salvar</button>
            </div>

            <br>
            <button id="abreinsletra" class="botpadrblue" onclick="insereLetra();">Inserir Letra</button>
        </div>
    </body>
</html>