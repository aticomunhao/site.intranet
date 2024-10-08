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
           new DataTable('#idTabela', {
                columnDefs: [
                    {
                        target: 2,
                        orderable: false
                    }
                ],
                lengthMenu: [
                    [50, 100, 200, 500],
                    [50, 100, 200, 500]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });
            table = new DataTable('#idTabela');

        </script>
    </head>
    <body> 
        <?php
            //numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
            //Função para Extract no postgres
            $Semana_Extract = array(
                '0' => 'Dom',
                '1' => 'Seg',
                '2' => 'Ter',
                '3' => 'Qua',
                '4' => 'Qui',
                '5' => 'Sex',
                '6' => 'Sab',
                'xª'=> ''
            );

            $Cod = (int) filter_input(INPUT_GET, 'codigo');
            $rs = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'bensachados'");
            $row = pg_num_rows($rs);
            if($row == 0){
                die("Faltam tabelas. Informe à ATI");
                return false;
            }
        ?>
         <!-- Apresenta os usuários do setor com o nível administrativo -->
        <div style="padding: 10px;">
            <?php
            $rs0 = pg_query($Conec, "SELECT ".$xProj.".bensachados.id, to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, usuguarda, usurestit, usucsg, usuarquivou, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, usudestino, CURRENT_DATE-datareceb, codusuins, date_part('dow', datareceb)  
            FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".bensachados.ativo = 1 And AGE(".$xProj.".bensachados.datareceb, CURRENT_DATE) <= '1 YEAR' 
            ORDER BY ".$xProj.".bensachados.datareceb DESC");

            $Edit = 0;
            $Impr = 0; // impressão do relatório
            $admIns = parAdm("insbens", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editbens", $Conec, $xProj); // nível administrativo para editar
            $Marca = parEsc("bens", $Conec, $xProj, $_SESSION["usuarioID"]); // ver se está marcado no cadastro de usu
            $SoInsBens = parEsc("soinsbens", $Conec, $xProj, $_SESSION["usuarioID"]); // está marcado no cadastro de usuários

            if($Marca == 1 && $_SESSION["AdmUsu"] >= $admIns || $_SESSION["AdmUsu"] > 6){
                $Edit = 1;
                if($_SESSION["AdmUsu"] >= $admEdit || $_SESSION["AdmUsu"] > 6){
                    $Impr = 1;
                }
            }
            $Impr = 1; // relatório liberado 

            if($Marca == 0 && $SoInsBens == 1){ // só para registrar (portaria nos fins de semana) - Só mostra os do dia
                $rs0 = pg_query($Conec, "SELECT ".$xProj.".bensachados.id, to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, usuguarda, usurestit, usucsg, usuarquivou, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, usudestino, CURRENT_DATE-datareceb, codusuins, date_part('dow', datareceb) 
                FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".bensachados.ativo = 1 And usucsg = 0 And ".$xProj.".bensachados.datareceb = CURRENT_DATE 
                ORDER BY ".$xProj.".bensachados.datareceb DESC");
            }

            ?>
            <table id="idTabela" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th>Data</th>
                        <th>Sem</th>
                        <th style="text-align: center;">Nº Processo</th>
                        <th style="text-align: center;">Descrição do Bem</th>
                        <th style="text-align: center;" title="Dias decorridos desde o registro">Dias</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $UsuIns = $tbl0[11]; // usuário que inseriu só para dar o status do processo 
                        $SobGuarda = $tbl0[4];
                        $Restit = $tbl0[5];
                        $GuardaCSG = $tbl0[6];
                        $Destino = $tbl0[9];
                        $Arquivado = $tbl0[7];
                        $Intervalo = (int) $tbl0[8];
                        $Dias = str_pad(($tbl0[10]), 2, "0", STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td><?php echo $tbl0[1]; ?></td> <!-- data -->
                            <td><?php echo $Semana_Extract[$tbl0[12]]; ?></td> <!-- dia semana -->
                            <td style="text-align: center;"><?php echo $tbl0[2]; ?></td> <!-- num processo -->
                            <td><?php echo nl2br($tbl0[3]); ?>
                            <hr style="margin: 0; padding: 0px;">
                            <?php
                            if($Edit == 1){
//                                if($_SESSION["usuarioID"] == 86){
                                    if($Edit == 1 && $SobGuarda == 0 && $GuardaCSG == 0 && $Restit == 0 && $Arquivado == 0){
                                        echo "<button class='botTable fundoAmarelo' onclick='verRegistroRcb($tbl0[0]);' title='Editar o registro de recebimento'>Editar</button>";
                                    }else{
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro'>Editar</button>";
                                    }
//                                }
                                if($Edit == 1 && $Restit == 0 && $GuardaCSG == 0 && $Arquivado == 0 ){
                                    echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 3, $Restit);' title='Encaminhamento para guarda da CSG'>CSG</button>";
                                }else{
                                    echo "<button disabled class='botTable fundoCinza corAzulClaro'>CSG</button>";
                                }
//                                if($Edit == 1 && $SobGuarda == 0 && $Restit == 0 && $Arquivado == 0){
//                                    echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 1, $Restit);'  title='Transferir para a guarda da DAF'>Guarda</button>";
//                                } else{
//                                    echo "<button disabled class='botTable fundoCinza corAzulClaro'>Guarda</button>";
//                                }
                                echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 2, $Restit);' title='Formuário de restituição ao proprietário'>Restituição</button>";

                                if($Edit == 1 && $Arquivado == 0 && $Intervalo > 2){
                                    echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 4, $Restit);' title='Destinação após 90 dias'>Destinação</button>";
                                }else{
                                    echo "<button disabled class='botTable fundoCinza corAzulClaro'>Destinação</button>";
                                }
                                if($Impr == 1){ // nível adm para editar
                                    echo "<button class='botTable fundoAmarelo' onclick='imprProcesso($tbl0[0]);' title='Gerar PDF do processo'>PDF</button>";
                                }
                                echo "<br>";
                            }
                            ?>

                            <?php
//                            echo "<div class='etiqResult' style='border: 0px;' title='Situação do processo'>Situação: </div>";
                            if($UsuIns > 0){
                                echo "<div class='etiqResult' title='Registro inicial'>Registrado</div>";
                            }
                            if($GuardaCSG > 0){
                                echo "<div class='etiqResult' title='Sob guarda da CSG'>CSG</div>";
                            }
//                            if($SobGuarda > 0){
//                                echo "<div class='etiqResult' title='Entregue à DAF'>DAF</div>";
//                            }
                            if($Restit > 0){
                                echo "<div class='etiqResult'style='border-color: red;' title='Bem restituído'>Restituído</div>";
                            }

                            if($Destino > 0){
                                echo "<div class='etiqResult' title='Bem já destinado'>Destinado</div>";
                            }
                            if($Arquivado > 0){
                                echo "<div class='etiqResult' style='border-color: red;' title='Processo arquivado'>Arquivado</div>";
                            }
                            ?>
                            </td> <!-- descrição do bem -->
 
                            <td style="text-align: center; font-size: 80%;"><?php
                                if($Arquivado == 0){
                                    echo "<div title='$Dias dias decorridos desde o registro'> $Dias </div>";
                                }
                                ?>
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