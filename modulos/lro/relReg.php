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
                lengthMenu: [
                    [200, 500],
                    [200, 500]
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
            $Cod = (int) filter_input(INPUT_GET, 'codigo');

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
        <div style="padding: 10px;">
            <?php
            $rs0 = pg_query($Conec, "SELECT ".$xProj.".livroreg.id, to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), turno, descturno, numrelato, nomeusual, usuant, enviado, codusu, ocor, date_part('dow', dataocor) 
            FROM ".$xProj.".livroreg INNER JOIN ".$xProj.".poslog ON ".$xProj.".livroreg.codusu = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".livroreg.ativo = 1 And AGE(".$xProj.".livroreg.dataocor, CURRENT_DATE) <= '1 YEAR' 
            ORDER BY ".$xProj.".livroreg.dataocor DESC, ".$xProj.".livroreg.turno DESC, ".$xProj.".livroreg.numrelato DESC");
            ?>
            <table id="idTabela" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th>Data</th>
                        <th>Sem</th>
                        <th>Turno</th>
                        <th style="text-align: center;">Número</th>
                        <th style="text-align: center;">Registrado por:</th>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th style="text-align: center;" title="Houve ocorrências?">Ocor</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0]; // id
                        $Data = $tbl0[1]; 
                        $Turno = $tbl0[2]; 
                        $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno And id != $Cod");
                        $row1 = pg_num_rows($rs1);
                    ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td title="Data do Registro"><?php echo $tbl0[1]; ?></td> <!-- data -->
                            <td style="font-size: 80%;" title="Dia da Semana"><?php echo $Semana_Extract[$tbl0[10]]; ?></td> <!-- dia semana data -->
                            <td style="<?php if($row1 > 0){echo 'color: red;'; } ?>" title="Turno" ><?php echo $tbl0[2]." - ".$tbl0[3]; ?></td> <!-- turno -->
                            <td style="text-align: center;" title="Número do Registro"><?php echo $tbl0[4]; ?></td> <!-- numocor -->
                            <td style="text-align: center;" title="Nome Funcionário"><?php echo $tbl0[5]; ?></td> <!-- ususvc -->
                            <td style="display: none;"><?php echo $tbl0[7]; ?></td> <!-- relato já enviado -->
                            <td style="display: none;"><?php echo $tbl0[8]; ?></td> <!-- codusu - quem inseriu o relato -->
                            <td style="font-size: 80%; text-align: center; <?php if($tbl0[9] == 1){echo "color: red; font-weight: bold;";}else{echo "color: black; font-weight: normal;";} ?>" title="Houve Ocorrência?"><?php if($tbl0[9] == 1){echo "Sim";}else{echo "Não";} ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>