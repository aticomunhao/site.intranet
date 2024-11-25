<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $Adm = $_SESSION["AdmUsu"];
            $UsuLogadoId = $_SESSION["usuarioID"];
            $UsuLogadoNome = $_SESSION["NomeCompl"];

            if(isset($_REQUEST["selec"])){
                $Selec = $_REQUEST["selec"];
            }else{
                $Selec = 0;
                $DescSit = "";
            }
            if(isset($_REQUEST["numtarefa"])){ // vem da pág inicial ao clicar em tem mensagem nas Tarefas
                $NumTarefa = $_REQUEST["numtarefa"];
            }else{
                $NumTarefa = 0;
            }

            $Condic = $xProj.".tarefas.ativo > 0";
            if($Selec > 0){
                $Condic = $xProj.".tarefas.ativo > 0 And sit = $Selec";
                if($Selec == 1){
                    $DescSit = "Designada";
                }
                if($Selec == 2){
                    $DescSit = "Aceita";
                }
                if($Selec == 3){
                    $DescSit = "em Andamento";
                }
                if($Selec == 4){
                    $DescSit = "Terminada";
                }
                if($Selec == 5){
                    $DescSit = "em Minhas Tarefas";
                    $Condic = $xProj.".tarefas.ativo > 0 And usuexec = $UsuLogadoId";
                    if($NumTarefa > 0){
                        $Condic = $xProj.".tarefas.ativo > 0 And idTar = $NumTarefa";
                    }
                }
                if($Selec == 6){
                    $DescSit = "em Meus Pedidos";
                    $Condic = $xProj.".tarefas.ativo > 0 And ".$xProj.".tarefas.usuins = $UsuLogadoId";
                    if($NumTarefa > 0){
                        $Condic = $xProj.".tarefas.ativo > 0 And idTar = $NumTarefa";
                    }
                }
            }

            $admEdit = parAdm("edittarefa", $Conec, $xProj); // nível para editar
            $VerTarefas = parAdm("vertarefa", $Conec, $xProj); // ver tarefas   1: todos - 2: só mandante e executante - 3: visualização por setor 
            $CodSetorUsu = parEsc("grupotarefa", $Conec, $xProj, $_SESSION["usuarioID"]);
            $rs7 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $CodSetorUsu");
            $row7 = pg_num_rows($rs7);
            if($row7 > 0){
                $tbl7 = pg_fetch_row($rs7);
                $SiglaSetor = $tbl7[0];
            }
            ?>
            <div style='margin: 20px; border: 3px solid green; border-radius: 10px;'>
            <?php

            if($Adm > 10){ // Superusuários - confusão
                $resultT = pg_query($Conec, "SELECT nomecompl, idtar as chaveTar, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.usuexec, tittarefa, textotarefa, sit, ".$xProj.".tarefas.ativo, to_char(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI') AS DataInsert, to_char(datasit1, 'DD/MM/YYYY HH24:MI') AS DataVista, prio, to_char(datasit2, 'DD/MM/YYYY HH24:MI'), to_char(datasit3, 'DD/MM/YYYY HH24:MI'), to_char(datasit4, 'DD/MM/YYYY HH24:MI')  
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                WHERE $Condic 
                ORDER BY ".$xProj.".tarefas.ativo, prio, ".$xProj.".tarefas.datains DESC, nomecompl");
            }else{
                if($VerTarefas == 1){ // 1 = Todos - Liberar a visualização das tarefas para todos
                    $resultT = pg_query($Conec, "SELECT nomecompl, idtar as chaveTar, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.usuexec, tittarefa, textotarefa, sit, ".$xProj.".tarefas.ativo, to_char(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI') AS DataInsert, to_char(datasit1, 'DD/MM/YYYY HH24:MI') AS DataVista, prio, to_char(datasit2, 'DD/MM/YYYY HH24:MI'), to_char(datasit3, 'DD/MM/YYYY HH24:MI'), to_char(datasit4, 'DD/MM/YYYY HH24:MI') 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                    WHERE $Condic 
                    ORDER BY ".$xProj.".tarefas.ativo, prio, ".$xProj.".tarefas.datains DESC, nomecompl");
                }
                if($VerTarefas == 2){  // visualização só mandante e executante
                    $resultT = pg_query($Conec, "SELECT nomecompl, idtar as chaveTar, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.usuexec, tittarefa, textotarefa, sit, ".$xProj.".tarefas.ativo, to_char(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI') AS DataInsert, to_char(datasit1, 'DD/MM/YYYY HH24:MI') AS DataVista, prio, to_char(datasit2, 'DD/MM/YYYY HH24:MI'), to_char(datasit3, 'DD/MM/YYYY HH24:MI'), to_char(datasit4, 'DD/MM/YYYY HH24:MI') 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                    WHERE $Condic And ".$xProj.".tarefas.usuexec = $UsuLogadoId Or $Condic And ".$xProj.".tarefas.usuins = $UsuLogadoId
                    ORDER BY ".$xProj.".tarefas.ativo, prio, ".$xProj.".tarefas.datains DESC, nomecompl");
                }
                if($VerTarefas == 3){  // visualização por setor 
                    $resultT = pg_query($Conec, "SELECT nomecompl, idtar as chaveTar, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.usuexec, tittarefa, textotarefa, sit, ".$xProj.".tarefas.ativo, to_char(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI') AS DataInsert, to_char(datasit1, 'DD/MM/YYYY HH24:MI') AS DataVista, prio, to_char(datasit2, 'DD/MM/YYYY HH24:MI'), to_char(datasit3, 'DD/MM/YYYY HH24:MI'), to_char(datasit4, 'DD/MM/YYYY HH24:MI') 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                    WHERE $Condic And ".$xProj.".tarefas.setorins = $CodSetorUsu Or $Condic And ".$xProj.".tarefas.setorexec = $CodSetorUsu
                    ORDER BY ".$xProj.".tarefas.ativo, prio, ".$xProj.".tarefas.datains DESC, nomecompl");
                }
            }

            $row = pg_num_rows($resultT);
            if($row > 0){ echo "<label class='etiqAzul' style='padding-left: 10px;'> Arraste o quadro amarelo para a direita &#8594;</label>"; } 
            ?>
            <table style="margin: 0 auto; border: 0; width: 90%;" >
                <?php
                echo "<tr>";
                echo "<td></td>";
                echo "<td style='text-align: center; font-weight: 600;'>Tarefa Designada</td>";
                echo "<td></td>";
                echo "<td style='text-align: center; font-weight: 600;'>Tarefa Aceita</td>";
                echo "<td></td>";
                echo "<td style='text-align: center; font-weight: 600;'>Tarefa em Andamento</td>";
                echo "<td></td>";
                echo "<td style='text-align: center; font-weight: 600;'>Tarefa Terminada</td>";
                echo "<td></td>";
                echo "</tr>";

                if($row > 0){
                    While ($tbl = pg_fetch_row($resultT)){
                        $idTar = $tbl[1];   // idtar
                        $usuIns = $tbl[2];  // usuins
                        $usuExec = $tbl[3]; // usuexec
                        $Status = $tbl[6];  // sit
                        $Titulo = $tbl[4];  // TitTarefa
                        $Texto = $tbl[5];   // TextoTarefa
                        $Ativo = $tbl[7];   // ativo  0 = Apagado   1 = Ativo   2 = arquivado
                        $DataInsert = $tbl[8];  //DataInsert
                        $DataVisu = $tbl[9];  //DataVista
                        if($DataVisu == "31/12/3000 00:00"){
                            $DataVisu = "";
                        }
                        $Priorid = $tbl[10];  //Prio
                        $DataSit2= $tbl[11];
                        if($DataSit2 == "31/12/3000 00:00"){
                            $DataSit2 = "";
                        }
                        $DataSit3= $tbl[12];
                        if($DataSit3 == "31/12/3000 00:00"){
                            $DataSit3 = "";
                        }
                        $DataSit4= $tbl[13];
                        if($DataSit4 == "31/12/3000 00:00"){
                            $DataSit4 = "";
                        }

                        $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $usuIns"); //mandante
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            $Proc1 = pg_fetch_row($rs1);
                            $NomeIns = $Proc1[1];
                            if(is_null($Proc1[1]) || $Proc1[1] == ""){
                                $NomeIns = $Proc1[0];
                            }
                        }else{
                            $NomeIns = "";
                        }

                        $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $usuExec"); // executor
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            $Proc2 = pg_fetch_row($rs2);
                            $NomeExec = $Proc2[1];
                            if(is_null($Proc2[1]) || $Proc2[1] == ""){
                                $NomeExec = $Proc2[0];
                            }
                        }else{
                            $NomeExec = "";
                        }

                        $rs3 = pg_query($Conec, "SELECT idmsg FROM ".$xProj.".tarefas_msg WHERE idtarefa = $idTar");
                        $TemMsg = pg_num_rows($rs3); // ver se tem mensagem para essa tarefa

                        $row4 = 0;
                        $row6 = 0;
                        $rs3 = pg_query($Conec, "SELECT usuins FROM ".$xProj.".tarefas WHERE idtar = $idTar And usuins = ".$_SESSION["usuarioID"]);
                        $row3 = pg_num_rows($rs3); // ver se foi o usu logado que inseriu a tarefa
                        if($row3 > 0){ // foi o usuário logado que inseriu
                            $rs4 = pg_query($Conec, "SELECT inslido FROM ".$xProj.".tarefas_msg WHERE idtarefa = $idTar And inslido = 0"); // procura mensagens não lidas como usuIns para essa tarefa
                            $row4 = pg_num_rows($rs4); // quantid mensagens não lidas como usuIns
                        }
                        $rs5 = pg_query($Conec, "SELECT usuexec FROM ".$xProj.".tarefas WHERE idtar = $idTar And usuexec = ".$_SESSION["usuarioID"]);
                        $row5 = pg_num_rows($rs5); // ver se foi o usu logado que recebeu a tarefa
                        if($row5 > 0){ // foi o usuário logado que recebeu
                            $rs6 = pg_query($Conec, "SELECT execlido FROM ".$xProj.".tarefas_msg WHERE idtarefa = $idTar And execlido = 0"); // procura mensagens não lidas como usuIns para essa tarefa
                            $row6 = pg_num_rows($rs6); // quantid mensagens não lidas como usuExec
                        }

                        echo "<tr>";  //Primeira coluna à esquerda - data e nomes
                            echo "<td style='vertical-align: top;'><div style='padding-bottom: 8px; padding-top: 2px;' title='Tarefa expedida para $NomeExec'><sub>Em $DataInsert para:</sub></div>";
                                echo "<div class='etiqLat'>";
                                    echo "<div style='position: relative; font-size: 2.5em; text-align: center;'>" . $NomeExec . "</div>";
                                echo "</div>";
                                echo "<div title='Tarefa expedida por $NomeIns'><sup>de: " . $NomeIns . "</sup></div>";
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                if($Status == 1 && $Ativo != 2){
                                    echo "<div class='etiqueta etiqAtiva' draggable='true' droppable='true' ondrag='PegaCod($idTar, $Ativo, $usuExec);' ondrop='drop(event)' ondragover='allowDrop(event)' title='Tarefa Designada'>$Titulo</div>";
                                }elseif($Status == 1 && $Ativo == 2){
                                    echo "<div class='etiqueta etiqInativa' draggable='false' droppable='false'>$Titulo</div>";
                                }else{
                                    echo "<div class='etiqueta etiqInat' draggable='false' droppable='true' ondrop='drop(event, 1)' ondragover='allowDrop(event)'> </div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center; width: 30px;' title='Arraste o quadro amarelo para a direita'>&#10144;</td>";

                            echo "<td style='text-align: center;'>";
                                if($Status == 2 && $Ativo != 2){
                                    echo "<div class='etiqueta etiqAtiva' draggable='true' droppable='true' ondrag='PegaCod($idTar, $Ativo, $usuExec);' ondrop='drop(event)' ondragover='allowDrop(event)' title='Tarefa Aceita'>$Titulo</div>";
                                }elseif($Status == 2 && $Ativo == 2){
                                    echo "<div class='etiqueta etiqInativa' draggable='false' droppable='false'>$Titulo</div>";
                                }else{
                                    echo "<div class='etiqueta etiqInat' draggable='false' droppable='true' ondrop='drop(event, 2)' ondragover='allowDrop(event)'>   </div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center; width: 30px;' title='Arraste o quadro amarelo para a direita'>&#10144;</td>";

                            echo "<td style='text-align: center;'>";
                                if($Status == 3 && $Ativo != 2){
                                    echo "<div class='etiqueta etiqAtiva' draggable='true' droppable='true' ondrag='PegaCod($idTar, $Ativo, $usuExec);' ondrop='drop(event)' ondragover='allowDrop(event)' title='Tarefa em Andamento'>$Titulo</div>";
                                }elseif($Status == 3 && $Ativo == 2){
                                    echo "<div class='etiqueta etiqInativa' draggable='false' droppable='false'>$Titulo</div>";
                                }else{
                                    echo "<div class='etiqueta etiqInat' draggable='false' droppable='true' ondrop='drop(event, 3)' ondragover='allowDrop(event)'>   </div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center; width: 30px;' title='Arraste o quadro amarelo para a direita'>&#10144;</td>";

                            echo "<td style='text-align: center;'>";
                                if($Status == 4 && $Ativo != 2){
                                    echo "<div class='etiqueta etiqAtiva' draggable='true' droppable='true' ondrag='PegaCod($idTar, $Ativo, $usuExec);' ondrop='drop(dr)' ondragover='allowDrop(event)' title='Tarefa Terminada'>$Titulo</div>";
                                }elseif($Status == 4 && $Ativo == 2){
                                    echo "<div class='etiqueta etiqInativa' draggable='false' droppable='false' title='Tarefa Terminada'>$Titulo</div>";
                                }else{
                                    // Info URGENTE, IMPORTANTE no quadro do statos4 (Terminado)
                                    echo "<div class='etiqueta etiqInat' draggable='false' droppable='true' ondrop='drop(event, 4)' ondragover='allowDrop(event)'>";
                                    if($Priorid == 0){
                                        echo "<br><p class='blink' style='font-family: Trebuchet MS, Verdana, sans-serif; letter-spacing: 5px; color: red; font-size: 1.5em; font-weigth: bold; margin: 0; padding: 0;'>URGENTE</p>";
                                    }
                                    if($Priorid == 1){
                                        echo "<p style='font-family: Trebuchet MS, Verdana, sans-serif; letter-spacing: 5px; color: red; font-size: 1.2em; font-weigth: bold; margin: 0; padding: 0;'><br>MUITO IMPORTANTE</p>";
                                    }
                                    if($Priorid == 2){
                                        echo "<p style='font-family: Trebuchet MS, Verdana, sans-serif; letter-spacing: 5px; color: red; font-size: 1.2em; font-weigth: bold; margin: 0; padding: 0;'><br>IMPORTANTE</p>";
                                    }
                                    echo "</div>";
                                }
                            echo "</td>";

                            echo "<td>";  
                                if($Adm >= $admEdit && $usuIns == $UsuLogadoId || $usuExec == $UsuLogadoId && $usuExec == $usuIns){ // Adm >= nível estipulado nos parâmetros e usuins igual ao logado, executante é o mesmo do ins
                                    echo "<div title='Editar' style='cursor: pointer;' onclick='carregaModal($idTar);'>&#9997;</div>";
                                }
                                echo "<div title='Mensagens' style='cursor: pointer;' onclick='carregaMsg($idTar);'>";
                                    if($row4 > 0 || $row6 > 0){
                                        echo "<p class='blink'>&#9993;</p>";
                                    }else{
                                        echo "<p>&#9993;</p>";
                                    }
                                    echo "</div>";
                            echo "</td>";
                        echo "</tr>";

                        //Entrelinhas - informação de datas nas entrelinhas
                        echo "<tr>";
                            echo "<td><div></div>";
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                if($DataVisu != ""){
                                    echo "<div style='font-size: 70%; font-weight: bold;'><sup>Ciência: ".$DataVisu."</sup></div>";
                                }else{
                                    echo "<div style='font-size: 70%;'><sup>Designada: ".$DataInsert."</sup></div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                echo "<div></div>";
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                if($DataSit2 != ""){
                                    echo "<div style='font-size: 70%; font-weight: bold;'><sup>Aceita: ".$DataSit2."</sup></div>";
                                }else{
                                    echo "<div style='font-size: 70%;'><sup>Aceita</sup></div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                echo "<div></div>";
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                if($DataSit3 != ""){
                                    echo "<div style='font-size: 70%; font-weight: bold;'><sup>em Andamento: ".$DataSit3."</sup></div>";
                                }else{
                                    echo "<div style='font-size: 70%;'><sup>em Andamento</sup></div>";
                                }

                            echo "</td>";
                            echo "<td style='text-align: center;'>";
                                echo "<div> </div>";
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                                if($DataSit4 != ""){
                                    echo "<div style='font-size: 70%; font-weight: bold;'><sup>Terminada: ".$DataSit4."</sup></div>";
                                }else{
                                    echo "<div style='font-size: 70%;'><sup>Terminada</sup></div>";
                                }
                            echo "</td>";

                            echo "<td style='text-align: center;'>";
                            echo "<div></div>";
                            echo "</td>";

                        echo "</tr>";
                    }
                }else{
                    echo "<tr>";
                        echo "<td colspan='8' style='text-align: center; font-weight: 800; color: blue; padding: 10px;'>Nenhuma tarefa $DescSit</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </body>
</html>