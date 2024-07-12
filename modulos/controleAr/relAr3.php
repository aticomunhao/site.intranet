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
                ordering: false, // desabilita ordenação das colunas
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
                },
                orderFixed: [1, 'asc'],
                rowGroup: {
                    dataSrc: 1
                }
            });

            tableUsu = new DataTable('#idTabela');
            tableUsu.on('click', 'tbody tr', function () {
                let data = tableUsu.row(this).data();
                $id = data[0];
                document.getElementById("guardaid").value = $id;
            });

        </script>
    </head>
    <body>
        <?php
            if(isset($_REQUEST["ano"])){
                $Ano = $_REQUEST["ano"];
            }else{
                $Ano = date("Y");
            }
//            $Ano = "2024";
            $Condic = "".$xProj.".controle_ar3.id != 0 And num_ap IS NOT NULL And ativo = 1";
            $rs0 = pg_query($Conec, "SELECT id, num_ap, localap FROM ".$xProj.".controle_ar3 WHERE $Condic ORDER BY num_ap"); 
            $row0 = pg_num_rows($rs0);
        ?>
        <br><br>
            <table id="idTabela" style="margin: 0 auto;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th title="Número do aparelho">Apar.</th>
                        <th title="Local de instalação do aparelho">Local</th>
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
                        $Cod = $tbl0[0]; // id controle_ar3
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td style="text-align: center; font-size: 90%; font-weight: bold;"><?php echo str_pad($tbl0[1], 3, 0, STR_PAD_LEFT); ?><br>
                                <div style='background-color: #F8F4E1; margin: 6px; cursor: pointer; padding: 0; position: relative; border: 1px solid #D1D8C5; border-radius: 5px; font-size: 70%;' title='Clique aqui para inserir visita técnica preventiva ou corretiva' onclick="insereData(<?php echo $tbl0[0]; ?>, 0);">Visita</div>
                            </td>
                            <td class="etiqCel" style="cursor: pointer;" onclick="editaLocal(<?php echo $tbl0[0]; ?>);" title="Clique aqui para inserir ou editar o local de instalação do condicionador"><?php echo $tbl0[2]; ?></td>

                            <td class="etiqCel" style="font-size: 80%; background-color: #E0E0E0;">
                                <?php //to_char(datavis, 'DD/MM/YYYY')
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '01' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){ // $tbl1[0] -> id de visitas_ar2
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '02' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '03' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '04' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '05' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '06' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%; background-color: #E0E0E0;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '07' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '08' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '09' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '10' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '11' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD'), tipovis, nometec 
                                    FROM ".$xProj.".visitas_ar2 WHERE controle_id = $Cod And to_char(datavis, 'MM') = '12' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            echo "<div onclick='buscaData($tbl1[0], 1);' style='border-top: 1px solid black; cursor: pointer;";
                                            if($tbl1[2] == 2){
                                                echo "color: red;' title='Manutenção corretiva - clique para editar'";
                                            }else{
                                                echo "color: black;' title='Manutenção preventiva - clique para editar'";
                                            }
                                            echo "><div style='font-weight: bold;'>".$tbl1[1]."</div>".$tbl1[3]."</div>";
                                        }
                                        echo "<div style='border-bottom: 1px solid black;'></div>"; // para fechar o traço
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <br><br><br><br>
    </body>
</html>