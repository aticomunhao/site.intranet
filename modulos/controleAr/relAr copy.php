<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <script type="text/javascript">
            new DataTable('#idTabela', {
                lengthMenu: [
                    [100, 200, 500],
                    [100, 200, 500]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            tableUsu = new DataTable('#idTabela');
            tableUsu.on('click', 'tbody tr', function () {
                let data = tableUsu.row(this).data();
                $id = data[0];
                document.getElementById("guardaid").value = $id;
                carregaModal();
            });

        </script>
    </head>
    <body>
        <?php
            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "todos";
            }
            $Condic = "".$xProj.".controle_ar.id != 0 And num_ap IS NOT NULL ";
            $rs0 = pg_query($Conec, "SELECT id, num_ap, localap, to_char(data01, 'DD/MM/YYYY'), to_char(data02, 'DD/MM/YYYY'), to_char(data03, 'DD/MM/YYYY'), to_char(data04, 'DD/MM/YYYY'), to_char(data05, 'DD/MM/YYYY'), to_char(data06, 'DD/MM/YYYY'), to_char(data07, 'DD/MM/YYYY'), to_char(data08, 'DD/MM/YYYY'), to_char(data09, 'DD/MM/YYYY'), to_char(data10, 'DD/MM/YYYY'), to_char(data11, 'DD/MM/YYYY'), to_char(data12, 'DD/MM/YYYY') FROM ".$xProj.".controle_ar WHERE $Condic ORDER BY num_ap"); 
            $row0 = pg_num_rows($rs0);
        ?>

        <table id="idTabela" class="display" style="width:85%; font-size: 85%;">
            <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th>Aparelho</th>
                        <th>Local</th>
                        <th style="text-align: center;">Jan</th>
                        <th style="text-align: center;">Fev</th>
                        <th style="text-align: center;">Mar</th>
                        <th style="text-align: center;">Abr</th>
                        <th style="text-align: center;">Mai</th>
                        <th style="text-align: center;">Jun</th>
                        <th style="text-align: center;">Jul</th>
                        <th style="text-align: center;">Ago</th>
                        <th style="text-align: center;">Set</th>
                        <th style="text-align: center;">Out</th>
                        <th style="text-align: center;">Nov</th>
                        <th style="text-align: center;">Dez</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($row0 > 0){
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0]; // id
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td style="text-align: center; font-size: 90%; font-weight: bold;" onclick="carregaCel(0);"><?php echo str_pad($tbl0[1], 3, 0, STR_PAD_LEFT); ?></td>
                            <td class="etiqCel" onclick="carregaCel(0);"><?php echo $tbl0[2]; ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('01');"><?php if($tbl0[3] == "01/01/1500"){echo "";}else{ echo $tbl0[3];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('02');"><?php if($tbl0[4] == "01/01/1500"){echo "";}else{ echo $tbl0[4];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('03');"><?php if($tbl0[5] == "01/01/1500"){echo "";}else{ echo $tbl0[5];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('04');"><?php if($tbl0[6] == "01/01/1500"){echo "";}else{ echo $tbl0[6];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('05');"><?php if($tbl0[7] == "01/01/1500"){echo "";}else{ echo $tbl0[7];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('06');"><?php if($tbl0[8] == "01/01/1500"){echo "";}else{ echo $tbl0[8];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('07');"><?php if($tbl0[9] == "01/01/1500"){echo "";}else{ echo $tbl0[9];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('08');"><?php if($tbl0[10] == "01/01/1500"){echo "";}else{ echo $tbl0[10];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('09');"><?php if($tbl0[11] == "01/01/1500"){echo "";}else{ echo $tbl0[11];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('10');"><?php if($tbl0[12] == "01/01/1500"){echo "";}else{ echo $tbl0[12];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('11');"><?php if($tbl0[13] == "01/01/1500"){echo "";}else{ echo $tbl0[13];} ?></td>
                            <td class="etiqCel" style="font-size: 80%;" onclick="carregaCel('12');"><?php if($tbl0[14] == "01/01/1500"){echo "";}else{ echo $tbl0[14];} ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <br><br>
    </body>
</html>