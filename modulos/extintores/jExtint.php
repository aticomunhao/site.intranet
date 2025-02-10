<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>

        </style>
        <script>
            new DataTable('#idTabela', {
                paging: false,
                //scrollY: 100,
//                scrollX: true,
                searching: false,
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                document.getElementById("guardaid").value = $id;
                carregaExtintor($id);
            });

            $(document).ready(function(){

            });

        </script>
    </head>
    <body>
        <?php
        $rs0 = pg_query($Conec, "SELECT ".$xProj.".extintores.id, ext_num, ext_local, desc_tipo, ext_capac, ext_reg, ext_serie, TO_CHAR(datacarga, 'DD/MM/YYYY'), TO_CHAR(datavalid, 'DD/MM/YYYY'), TO_CHAR(datacasco, 'DD/MM/YYYY'), CASE WHEN datavalid <= CURRENT_DATE+30 THEN 'aviso' END, CASE WHEN datavalid <= CURRENT_DATE THEN 'vencido' END 
        FROM ".$xProj.".extintores INNER JOIN ".$xProj.".extintores_tipo ON ".$xProj.".extintores.ext_tipo = ".$xProj.".extintores_tipo.id
        WHERE ".$xProj.".extintores.ativo = 1 ORDER BY ext_num");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="padding: 10px;">
            <table id="idTabela" class="display" style="width:90%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq" style="text-align: center;">Número</th>
                        <th class="etiq">Tipo</th>
                        <th class="etiq">Capacidade</th>
                        <th class="etiq">Local</th>
                        <th class="etiq" style="text-align: center;">Revisado</th>
                        <th class="etiq" style="text-align: center;">Vencimento</th>
                        <th class="etiq"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($tbl = pg_fetch_row($rs0)) {
                        $Cod = $tbl[0];
                        $DataRevis = $tbl[7];
                        if($DataRevis == "31/12/3000"){
                            $DataRevis = "";
                        }
                        $DataValid = $tbl[8];
                        if($DataValid == "31/12/3000"){
                            $DataValid = "";
                        }
                        $DataCasco = $tbl[9];
                        if($DataCasco == "31/12/3000"){
                            $DataCasco = "";
                        }
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl[0]; ?></td>
                        <td style="text-align: center;"><?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></td>
                        <td><?php echo $tbl[3]; ?></td>
                        <td><?php echo $tbl[4]; ?></td>
                        <td><?php echo $tbl[2]; ?></td>
                        <td style="text-align: center;"><?php echo $DataRevis; ?></td>
                        <td style="text-align: center;<?php if($tbl[10] == 'aviso'){echo 'color: red; font-weight: bold;';}else{echo 'color: black; font-weight: normal;';} ?>"><?php echo $DataValid; ?></td>
                        <td style="text-align: center;"><?php if($tbl[11] == 'vencido'){echo "<img src='imagens/oknao.png' title='Vencido'>";} ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>