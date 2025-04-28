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
            .fixed {
                table-layout: fixed;
                border-collapse: collapse;
            }
            .fixed th {
                text-decoration: underline;
            }
            .fixed th,
            .fixed td {
                min-width: 20px;
            }

            .fixed thead {
                background-color: #FFFAFA;
            }
            .fixed thead tr {
                display: block;
                position: relative;
            }

            .fixed tbody {
                display: block;
                overflow: auto;
                width: 100%;
                height: 800px;
                overflow-y: scroll;
                overflow-x: hidden;
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
                    },
                    {
                        target: 4,
                        orderable: false
                    }
                ],
                lengthMenu: [
                    [200, 500, 1000, 2000],
                    [200, 500, 1000, 2000]
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
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                document.getElementById("guardacod").value = $id; 
                if($id !== ""){
                    mostraModal($id);
                }
            });
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
        ?>
         <!-- Apresenta os usuários do setor com o nível administrativo -->
        <div style="margin-top: 34px; padding: 10px; border-top: 1px solid gray; border-radius: 20px;">
            <?php
            //mostrando 3 meses - os anterior podem ser vistos no PDF
            $rs0 = pg_query($Conec, "SELECT ".$xProj.".livroreg.id, to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), turno, descturno, numrelato, nomeusual, usuant, enviado, codusu, ocor, date_part('dow', dataocor), 
            lidofisc 
            FROM ".$xProj.".livroreg INNER JOIN ".$xProj.".poslog ON ".$xProj.".livroreg.codusu = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".livroreg.ativo = 1 And (CURRENT_DATE-dataocor) <= 60 
            ORDER BY ".$xProj.".livroreg.dataocor DESC, ".$xProj.".livroreg.turno DESC, ".$xProj.".livroreg.numrelato DESC");
            $row0 = pg_num_rows($rs0);
            // AGE(".$xProj.".livroreg.dataocor, CURRENT_DATE) <= '1 YEAR'
            ?>
            <table id="idTabela" class="display" style="width:85%;">
                <caption><?php echo "Total ".$row0." registros"; ?></caption>
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq">Data</th>
                        <th class="etiq">Sem</th>
                        <th class="etiq">Turno</th>
                        <th class="etiq" style="text-align: center;">Número</th>
                        <th class="etiq" style="text-align: left;">Registrado</th>
                        <th class="etiq" style="display: none;"></th>
                        <th class="etiq" style="display: none;"></th>
                        <th class="etiq" style="text-align: center;" title="Houve ocorrências?">Ocor</th>
                        <th class="etiq" style="text-align: center;" title="Visto da Administração">Adm</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0]; // id
                        $Data = $tbl0[1]; 
                        $Turno = $tbl0[2];
                        $Visto = $tbl0[11]; 
//                        if(is_null($tbl0[12]) || $tbl0[12] == ""){ // relato do fiscal 
//                            $RelFiscal = 0;
//                        }else{
//                            $RelFiscal = 1;
//                        }
                        $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno And id != $Cod");
                        $row1 = pg_num_rows($rs1);

                        $rs2 = pg_query($Conec, "SELECT relatofisc FROM ".$xProj.".livroreg WHERE id = $Cod");
                        $tbl2 = pg_fetch_row($rs2);
                        if(is_null($tbl2[0]) || $tbl2[0] == ""){ // relato do fiscal 
                            $RelFiscal = 0;
                        }else{
                            $RelFiscal = 1;
                        }
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td title="Data do Registro"><?php echo $tbl0[1]; ?></td> <!-- data -->
                            <td style="font-size: 80%;" title="Dia da Semana"><?php echo $Semana_Extract[$tbl0[10]]; ?></td> <!-- dia semana data -->
                            <td style="<?php if($row1 > 0){echo 'color: red;'; } ?>" title="Turno" ><?php echo $tbl0[3]."<label style='color: gray; font-size: 70%; padding-left: 3px;'> (".$tbl0[2]."º)</label>"; ?></td> <!-- turno -->
                            <td style="text-align: center;" title="Número do Registro"><?php echo $tbl0[4]; ?></td> <!-- numocor -->
                            <td style="text-align: left;" title="Nome Funcionário"><?php echo $tbl0[5]; ?></td> <!-- ususvc -->
                            <td style="display: none;"><?php echo $tbl0[7]; ?></td> <!-- relato já enviado -->
                            <td style="display: none;"><?php echo $tbl0[8]; ?></td> <!-- codusu - quem inseriu o relato -->
                            <td style="font-size: 80%; text-align: center; <?php if($tbl0[9] == 1){echo "color: red; font-weight: bold;";}else{echo "font-weight: normal;";} ?>" title="Houve Ocorrência?"><?php if($tbl0[9] == 1){echo "Sim";}else{echo "Não";} ?></td>

                            <td style="font-size: 80%; text-align: center;" title="Visto da Administração"> 
                                <?php 
                                if($Visto == 1){
                                    echo "<img src='imagens/ok.png' height='14px;' title='Visto'>"; 
                                }
                                if($RelFiscal == 1){
                                    echo "<img src='imagens/lapisBranco.png' height='10px;' title='Anotação da administração'>";
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