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
            $Condic = "".$xProj.".controle_ar.id != 0 And num_ap IS NOT NULL And ativo = 1";
            $rs0 = pg_query($Conec, "SELECT id, num_ap, localap FROM ".$xProj.".controle_ar WHERE $Condic ORDER BY num_ap"); 
            $row0 = pg_num_rows($rs0);
        ?>
        <br><br><br><br>
            <table id="idTabela" style="margin: 0 auto;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th title="Número do aparelho">Apar.</th>
                        <th title="Loca de instalação do aparelho">Local</th>
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
                            <td style="text-align: center; font-size: 90%; font-weight: bold;"><?php echo str_pad($tbl0[1], 3, 0, STR_PAD_LEFT); ?><br>
                                <div style='background-color: #F8F4E1; margin: 6px; cursor: pointer; padding: 0; position: relative; border: 1px solid #D1D8C5; border-radius: 5px; font-size: 70%;' title='Inserir visita técnica' onclick="insereData(<?php echo $tbl0[0]; ?>);">Visita</div>
                            </td>
                            <td class="etiqCel" onclick="editaLocal(<?php echo $tbl0[0]; ?>);"><?php echo $tbl0[2]; ?></td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '01' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "<div onclick='insereData($tbl1[0])'> - </div>";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
//                                                if($row1 > 1){
//                                                    echo "style='border-top: 1px solid red;'";
//                                                }
                                                if($tbl1[2] == 2){
                                                    echo "style='color: red;' title='Manutenção corretiva'";
                                                }
                                                echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }else{
//                                        echo "<div onclick='insereData($Cod)' style='color: white;'> --- </div>";
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '02' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '03' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '04' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '05' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '06' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '07' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '08' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '09' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '10' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '11' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
                                    }
                                ?>
                            </td>
                            <td class="etiqCel" style="font-size: 80%;">
                                <?php 
                                    $rs1 = pg_query($Conec, "SELECT id, to_char(datavis, 'DD/MM/YYYY'), tipovis 
                                    FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And to_char(datavis, 'MM') = '12' And to_char(datavis, 'YYYY') = '$Ano' And ativo = 1 ORDER BY datavis DESC");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        while ($tbl1 = pg_fetch_row($rs1)){
                                            if($tbl1[0] == "01/01/1500"){echo "";}else{
                                                echo "<div onclick='buscaData($tbl1[0])'";
                                                if($tbl1[2] == 2){echo "style='color: red;' title='Manutenção corretiva'";} echo ">".$tbl1[1]."</div>";
                                            }
                                        }
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