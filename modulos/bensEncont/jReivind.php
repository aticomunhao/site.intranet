<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script>
            new DataTable('#idTabelaReiv', {
                info: false, // inform de pág sendo visualizada
                paging: false,  // paginação 
                lengthMenu: [
                    [100, 200, 300],
                    [100, 200, 300]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    zeroRecords: 'Nada foi encontrado'
                }
            });
            table = new DataTable('#idTabelaReiv');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                carregaReivind($id);
            });

        </script>
    </head>
    <body>
        <?php
        $TempoAviso  = parAdm("aviso_extint", $Conec, $xProj); // dias de antecedência para aviso
        $Condic = "ativo = 1";
         if(isset($_REQUEST["acao"])){
            $Acao = $_REQUEST["acao"];
        }else{
            $Acao = "todos";
            $CompDesc = "";
        }

        $rs0 = pg_query($Conec, "SELECT id, processoreiv, nome, email, descdobemperdeu, localperdeu, observ, TO_CHAR(datareiv, 'DD/MM/YYYY'), TO_CHAR(dataperdeu, 'DD/MM/YYYY'), encontrado, entregue 
        FROM ".$xProj.".bensreivind 
        WHERE $Condic ORDER BY datareiv DESC, processoreiv DESC");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="color: black; margin-top: 5px; padding: 5px; border-top: 2px solid blue; border-radius: 10px;">
            <div style="text-align: center;">
                <label style="font-size: 80%;">
                    <?php
                    if($row0 == 0){
                        echo "Nenhuma reivindicação";
                    } 
                    if($row0 == 1){
                        echo "1 reivindicação";
                    }
                    if($row0 > 1){
                        echo $row0." reivindicações"; 
                    }
                    ?>
                </label>
            </div>
            <table id="idTabelaReiv" class="display corPreta" style="width:99%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;">Processo</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Data</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Nome</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Descrição</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;"></th>
                        <th class="etiq" style="border-bottom: 1px solid gray;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($tbl = pg_fetch_row($rs0)) {
                        $Cod = $tbl[0];
                        $DataReiv = $tbl[7];
                        if($DataReiv == "31/12/3000"){
                            $DataReiv = "";
                        }
                        $DataPerda = $tbl[8];
                        if($DataPerda == "31/12/3000"){
                            $DataPerda = "";
                        }
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl[0]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center;"><?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></td>
                            <td style="border-bottom: 1px solid gray;"><?php echo $DataReiv; ?></td>
                            <td style="border-bottom: 1px solid gray;"><?php echo $tbl[2]; ?></td>
                            <td style="border-bottom: 1px solid gray;"><?php echo $tbl[4]; ?></td>
                            <td style="border-bottom: 1px solid gray;">
                            <?php
                                if($tbl[9] == 1){
                                    echo "<img src='imagens/ok.png' height='18px;' title='Objeto encontrado.'>";
                                }else{
                                    echo "<img src='imagens/iconNull.png' height='18px;' title='Objeto não encontrado.'>";
                                }
                            ?>
                            </td>
                            <td style="border-bottom: 1px solid gray;">
                            <?php
                            if($tbl[9] == 1){
                                if($tbl[10] == 1){
                                    echo "<img src='imagens/ok.png' height='18px;' title='Objeto entregue.'>";
                                }else{
                                    echo "<img src='imagens/iconNull.png' height='18px;' title='Objeto não entregue.'>";
                                }
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