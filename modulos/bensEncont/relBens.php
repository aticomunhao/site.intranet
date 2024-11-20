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
            .caption-top {
                caption-side: top;
            }
        </style>

        <script>
           new DataTable('#idTabela', {
                columnDefs: [
                    {
                        target: 2,
                        orderable: false
                    },
                    {
                        target: 3,
                        orderable: false
                    }
                ],
                lengthMenu: [
                    [500, 1000],
                    [500, 1000]
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
        <div style="padding: 10px; padding-top: 2px;">
            <?php
            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "Todos";
            }

            $Condic = $xProj.".bensachados.ativo = 1 ";
            $vIndex = $xProj.".bensachados.datareceb DESC, id DESC";
            if($Acao == "Restituídos"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit > 0";
                $vIndex = $xProj.".bensachados.datareceb DESC";
            }
            if($Acao == "Destinados"){
                $Condic = $xProj.".bensachados.ativo = 1 And usuencdestino > 0 And (CURRENT_DATE-datareceb) >= 90";
                $vIndex = $xProj.".bensachados.dataencdestino DESC";
            }
            if($Acao == "Destinar"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit = 0 And usuencdestino = 0 And usudestino = 0 And (CURRENT_DATE-datareceb) >= 90 ";
                $vIndex = $xProj.".bensachados.datareceb DESC";
            }
            if($Acao == "Receber"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit = 0 And usuencdestino > 0 And usudestino = 0 And (CURRENT_DATE-datareceb) >= 90 ";
                $vIndex = $xProj.".bensachados.dataencdestino DESC";
            }
            if($Acao == "Guardar"){
                $Condic = $xProj.".bensachados.ativo = 1 And usucsg = 0 And usurestit = 0 ";
                $vIndex = $xProj.".bensachados.datareceb DESC";
            }
            if($Acao == "Recebidos"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit = 0 And usuencdestino > 0 And usudestino > 0 And (CURRENT_DATE-datareceb) >= 90 ";
                $vIndex = $xProj.".bensachados.dataencdestino DESC";
            }
            if($Acao == "Arquivar"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit = 0 And usuencdestino > 0 And usudestino > 0 And usuarquivou = 0 And (CURRENT_DATE-datareceb) >= 90 ";
                $vIndex = $xProj.".bensachados.dataencdestino DESC";
            }
            if($Acao == "Arquivados"){
                $Condic = $xProj.".bensachados.ativo = 1 And usurestit = 0 And usuencdestino > 0 And usudestino > 0 And usuarquivou > 0 And (CURRENT_DATE-datareceb) >= 90 Or ".$xProj.".bensachados.ativo = 1 And usurestit > 0 And usuencdestino = 0 And usudestino = 0 And usuarquivou > 0 ";
                $vIndex = $xProj.".bensachados.datareceb DESC";
            }

            $rs0 = pg_query($Conec, "SELECT ".$xProj.".bensachados.id, to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, usuguarda, usurestit, usucsg, usuarquivou, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, usudestino, CURRENT_DATE-datareceb, 
            codusuins, date_part('dow', datareceb), usuencdestino  
            FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
            WHERE $Condic 
            ORDER BY $vIndex ");

            //And AGE(CURRENT_DATE, ".$xProj.".bensachados.datareceb) <= '1 YEAR' 

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
                $rs0 = pg_query($Conec, "SELECT ".$xProj.".bensachados.id, to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, usuguarda, usurestit, usucsg, usuarquivou, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, usudestino, CURRENT_DATE-datareceb, 
                codusuins, date_part('dow', datareceb), usuencdestino 
                FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".bensachados.ativo = 1 And usucsg = 0 And ".$xProj.".bensachados.datareceb = CURRENT_DATE 
                ORDER BY ".$xProj.".bensachados.datareceb DESC");
            }
            $row0 = pg_num_rows($rs0);
            ?>
            <div style="text-align: center;"><label class="etiqAzul">Seleção: &nbsp;</label><label class="etiqAzul" id="ordemIndex"> <?php echo $Acao."  (".$row0." registros)"; ?></label></div>
            <table id="idTabela" class="display" style="width:85%;">
                <caption style="text-align: center;"><?php echo $row0." registros"; ?></caption>
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
                        $EncDestino = $tbl0[13];
                        $Arquivado = $tbl0[7];
                        $Dias = str_pad(($tbl0[10]), 2, "0", STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td><?php echo $tbl0[1]; ?></td> <!-- data -->
                            <td style="font-size: 80%;"><?php echo $Semana_Extract[$tbl0[12]]; ?></td> <!-- dia semana -->
                            <td style="text-align: center;"><?php echo $tbl0[2]; ?></td> <!-- num processo -->
                            <td><?php echo nl2br($tbl0[3]); ?>
                                <hr style="margin: 0; padding: 0px;">
                                <?php
                                if($Edit == 1){
                                    if($Edit == 1 && $SobGuarda == 0 && $GuardaCSG == 0 && $Restit == 0 && $Arquivado == 0){
                                        echo "<button class='botTable fundoAmarelo' onclick='verRegistroRcb($tbl0[0]);' title='Editar o registro de recebimento'>Editar</button>";
                                    }else{
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro'>Editar</button>";
                                    }
                                    if($Edit == 1 && $Restit == 0 && $GuardaCSG == 0 && $Arquivado == 0 ){
                                        echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 3, $Restit);' title='Encaminhamento para guarda do Setor de Serviços'>SSV</button>";
                                    }else{
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro'>SSV</button>";
                                    }

                                    if($EncDestino == 0){
                                        if($Restit == 0){
                                            echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 2, $Restit);' title='Formuário de restituição ao proprietário'>Restituição</button>";
                                        }else{
                                            echo "<button class='botTable fundoAmareloCl' onclick='mostraBem($tbl0[0], 2, $Restit);' title='Formuário de restituição preenchido'>Restituído</button>";
                                        }
                                    }else{
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro'>Restituição</button>";
                                    }

                                    if($Edit == 1 && $Restit == 0 && $Arquivado == 0 && $EncDestino == 0 && $Dias >= 90 && $_SESSION["AdmUsu"] >= 6){
                                        echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 4, $Restit);' title='Destinação após 90 dias'>Destinação</button>";
                                    }else{
                                        if($EncDestino == 0){
                                            echo "<button disabled class='botTable fundoCinza corAzulClaro'>Destinação</button>";
                                        }else{
                                            echo "<button disabled class='botTable fundoCinza corAzulClaro'>Destinado</button>";
                                        }
                                    }

                                    if($Edit == 1 && $Restit == 0 && $Arquivado == 0 && $EncDestino > 0 && $Destino == 0 && $Dias >= 90){
                                        echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 5, $Restit);' title='Recebimento no destino'>Recebimento</button>";
                                    }else{
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro' title='Recebimento no destino'>Recebimento</button>";
                                    }

                                    if($Edit == 1 && $Arquivado == 0 && $EncDestino > 0 && $Destino > 0 && $Dias >= 90 && $_SESSION["AdmUsu"] >= 6){
                                        echo "<button class='botTable fundoAmarelo' onclick='mostraBem($tbl0[0], 6, $Restit);' title='Nível Revisor.'>Arquivar</button>";
                                    }else{
                                        if($Arquivado == 0){
                                        echo "<button disabled class='botTable fundoCinza corAzulClaro' title='Nível Revisor.'>Arquivar</button>";
                                        }else{
                                            echo "<button disabled class='botTable fundoCinza corAzulClaro'>Arquivado</button>";
                                        }
                                    }

                                    if($Impr == 1){ // nível adm para editar
                                        echo "<button class='botTable fundoAmarelo' onclick='imprProcesso($tbl0[0]);' title='Gerar PDF do processo'>PDF</button>";
                                    }
                                    echo "<br>";
                                }

                                if($UsuIns > 0){
                                    echo "<div class='etiqResult' title='Registro inicial'>Registrado</div>";
                                }
                                if($GuardaCSG > 0){
                                    echo "<div class='etiqResult' title='Sob guarda do SSV'>SSV</div>";
                                }
                                if($Restit > 0){
                                    echo "<div class='etiqResult'style='border-color: red;' title='Bem restituído'>Restituído</div>";
                                }
                                if($EncDestino > 0){
                                    echo "<div class='etiqResult'style='border-color: red;' title='Bem restituído'>Destinado</div>";
                                }
                                if($Destino > 0){
                                    echo "<div class='etiqResult' title='Bem já destinado'>Recebido</div>";
                                }
                                if($Arquivado > 0){
                                    echo "<div class='etiqResult' style='border-color: red;' title='Processo arquivado'>Arquivado</div>";
                                }

                                if($Edit == 0 && $Impr == 1){
                                    echo "<div class='etiqResult' style='border-color: blue; cursor: pointer;' onclick='imprProcesso($tbl0[0]);' title='Gerar PDF do processo'>PDF</div>";
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