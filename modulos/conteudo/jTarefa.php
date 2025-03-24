<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <script>
            function enviaMsg(idTar, idUsu){
                if(document.getElementById("novamensagem").value === ""){
                    $('#jmensagem').fadeIn("slow");
                    document.getElementById("jmensagem").innerHTML = "Digite uma mensagem";
                    $('#jmensagem').fadeOut(2000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=salvaMensagem&numtarefa="+idTar+"&numusuario="+idUsu+"&textoExt="+encodeURIComponent(document.getElementById('novamensagem').value), true);
                        ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText); 
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    $("#faixacentralMsg").load("modulos/conteudo/jTarefa.php?numtarefa="+idTar+"&usulogadoid="+idUsu);
                                }else{
                                    alert("Houve um erro no servidor.");
                                    document.getElementById("relacmodalMsg").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaMsg(Cod, idTar, idUsu){
                $.confirm({
                    title: 'Apagar mensagem.',
                    content: 'Confirma apagar esta mensagem?',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=apagaMensagem&numMsg="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText); 
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 0){
                                                $("#faixacentralMsg").load("modulos/conteudo/jTarefa.php?numtarefa="+idTar+"&usulogadoid="+idUsu);
                                            }else{
                                                alert("Houve um erro no servidor.");
                                                document.getElementById("relacmodalMsg").style.display = "none";
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                        }
                    }
                });
            }
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $UsuLogado = "";
            $IdTarefa = $_REQUEST["numtarefa"];
            $UsuLogadoId = $_REQUEST["usulogadoid"];
            // marcar como lidas as mensagens da tarefa
            pg_query($Conec, "UPDATE ".$xProj.".tarefas_msg SET inslido = 1 WHERE idtarefa = $IdTarefa And usuinstar = $UsuLogadoId");
            pg_query($Conec, "UPDATE ".$xProj.".tarefas_msg SET execlido = 1 WHERE idtarefa = $IdTarefa And usuexectar = $UsuLogadoId");
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td style="width: 20%;"></td>
                <td style="width: 70%; text-align: center; font-weight: bold;">- Mensagens -</td>
                <td style="width: 10%;"></td>
            </tr>
            <?php
            $rs = pg_query($Conec, "SELECT idmsg, ".$xProj.".tarefas_msg.iduser, nomecompl, idtarefa, textomsg, to_char(".$xProj.".tarefas_msg.datamsg, 'DD/MM/YYYY HH24:MI') AS DataMensagem, nomeusual 
            FROM ".$xProj.".poslog INNER JOIN (".$xProj.".tarefas INNER JOIN ".$xProj.".tarefas_msg ON ".$xProj.".tarefas.idtar = ".$xProj.".tarefas_msg.idtarefa) ON ".$xProj.".poslog.pessoas_id = ".$xProj.".tarefas_msg.idUser 
            WHERE ".$xProj.".tarefas_msg.elim = 0 And idtarefa = $IdTarefa ORDER BY ".$xProj.".tarefas_msg.datamsg");
            $row = pg_num_rows($rs);
            if($row > 0){
                While ($tbl = pg_fetch_row($rs)){
                    $Cod = $tbl[0];  // idMsg
                    $MsgUser = $tbl[1]; // id de quem inseriu a mensagem

                    $Nome = $tbl[6];
                    if(is_null($tbl[6]) || $tbl[6] == ""){
                        $Nome = $tbl[2];
                    }
                    $DataMsg = $tbl[5];  // DataMensagem
                    $Msg = nl2br($tbl[4]); // textoMsg

                    echo "<tr>";
                    if($MsgUser == $UsuLogadoId){ // quem escreveu a msg pode apagar
                        echo "<td style='font-size: .7rem; text-align: center; color: #828282'>$DataMsg <br>  $Nome</td>";
                        echo "<td><div style='border: 1px outset; border-radius: 5px; padding: 4px; color: #828282'>$Msg</div></td>";
                        echo "<td style='text-align: center;'><div style='cursor: pointer;' onclick='apagaMsg($Cod, $IdTarefa, $UsuLogadoId);' title='Apagar mensagem'> &#128465; </div></td>"; // Wastebasket &#128465;
                    }else{
                        echo "<td style='font-size: .7rem; text-align: center;'>$DataMsg <br>  $Nome</td>";
                        echo "<td><div style='border: 1px outset; border-radius: 5px; padding: 4px;'>$Msg</div></td>";
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            }else{
                echo "<tr>";
                echo "<td></td>";
                echo "<td style='text-align: center;'>Nenhuma mensagem.</td>";
                echo "<td></td>";
                echo "</tr>";
            }
            ?>
            <tr>
                <td class="etiq">Mensagem:</td>
                <td>
                    <div class="col-xs-6">
                        <textarea class="form-control" id='novamensagem' placeholder='Mensagem' rows='2' cols='65'></textarea>
                    </div>
                </td>
                <td style="text-align: center;"><input type="button" class="resetbot" style="color: blue; font-weight: bold; font-size: .7rem;" id="botenviar" value="Enviar" onclick="enviaMsg(<?php echo $IdTarefa; ?>, <?php echo $UsuLogadoId; ?>);" title="Enviar mensagem."></td>
            </tr>
            <tr>
                <td></td>
                <td><div id="jmensagem" style="color: red; font-weight: bold; text-align: center;"></div></td>
                <td></td>
            </tr>
        </table>
    </body>
</html>