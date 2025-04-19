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
            .resetbot{
                border-radius: 5px;
            }
        </style>
        <script>
            new DataTable('#idTabela1', {
                lengthMenu: [
                    [100, 200, 500],
                    [100, 200, 500]
                ],
//                paging: false,
                //scrollY: 100,
//                scrollX: true,
//                searching: false,
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

//            table = new DataTable('#idTabela1');
            table.on('click', 'tbody tr', function () {
//                data = table.row(this).data();
//                $id = data[1];
            });

            $(document).ready(function(){
            });

        </script>
    </head>
    <body>
        <?php
        $Contr = parEsc("contr", $Conec, $xProj, $_SESSION["usuarioID"]);

        $rsV = pg_query($Conec, "SELECT id FROM ".$xProj.".contratos1 WHERE ativo = 1 And emvigor = 1");
        $rowV = pg_num_rows($rsV);
        $ContrVigor = $rowV." em vigor";
        $rsT = pg_query($Conec, "SELECT id FROM ".$xProj.".contratos1 WHERE ativo = 1 And emvigor = 2");
        $rowT = pg_num_rows($rsT);
        if($rowT > 1){
            $ContrTerm = " - ".$rowT." terminados";
        }else{
            $ContrTerm = " - ".$rowT." terminado";
        }
        $rsR = pg_query($Conec, "SELECT id FROM ".$xProj.".contratos1 WHERE ativo = 1 And emvigor = 3");
        $rowR = pg_num_rows($rsR);
        if($rowR > 1){
            $ContrResc = " - ".$rowR." rescindidos";
        }else{
            $ContrResc = " - ".$rowR." rescindido";
        }
        ?>
        <div style="margin: 20px;">
            <div class="box" style="position: relative; float: left; width: 20%; text-align: left;">
                <?php
                if($Contr == 1){
                ?>
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Novo" title="Inserir novo contrato onde a Comunhão é contratante." onclick="insContrato(1);">
                <?php
                }else{
                    echo "&nbsp;";
                }
                ?>
            </div>
            <div class="box" style="position: relative; float: left; width: 58%; text-align: center;">
                <h5>Empresas Contratadas</h5>
                <label style="font-size: 80%;"><?php echo ($rowV+$rowT+$rowR)." Contratos: &nbsp;&nbsp;".$ContrVigor.$ContrTerm.$ContrResc; ?></label>
            </div>
            <div class="box" style="position: relative; float: left; width: 20%; text-align: right;">
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprContratadas();" title="Gera um arquivo pdf com a relação dos contratados.">PDF</button>
                <label style="padding-left: 5px;"></label>
            </div>
        </div>

         <!-- Empresas contratadas da comunhão -->
        <?php
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr,
        CASE WHEN dataaviso <= CURRENT_DATE AND datavencim >= CURRENT_DATE THEN 'aviso' END, emvigor 
        FROM ".$xProj.".contratos1 WHERE ativo = 1 ORDER BY emvigor, dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="padding: 5px;">
            <table id="idTabela1" class="display" style="width:95%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq aCentro">Assinatura</th>
                        <th class="etiq aCentro">N°</th>
                        <th class="etiq aCentro">Vencimento</th>
                        <th class="etiq">Empresa</th>
                        <th class="etiq">Objeto</th>
                        <th class="etiq aCentro">Setor</th>
                        <th class="etiq" title="Vigência do contrato">Vigência</th>
                        <th class="etiq aCentro" title="Notificação sobre a não prorrogação do contrato?">Notif</th>
                        <th class="etiq aCentro">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($tbl = pg_fetch_row($rs0)) {
                        $Cod = $tbl[0];
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl[0]; ?></td>
                        <?php
                            if($Contr == 1){
                        ?>
                        <td><div class="quadrinhoClick" onclick="editContrato(1, <?php echo $Cod; ?>);" title="Data Assinatura"><?php echo $tbl[2]; ?></div></td>
                        <td><div class="quadrinhoClick" style="font-size: 65%;" onclick="editContrato(1, <?php echo $Cod; ?>);" title="Número do contrato"><?php echo $tbl[1]; ?></div></td>
                        <td><div class="quadrinhoClick" style="<?php if($tbl[10] == 'aviso'){echo 'color: red; font-weight: bold;';}else{echo 'font-weight: normal;';} ?>" onclick="editContrato(1, <?php echo $Cod; ?>);" title="Data Vencimento"><?php echo $tbl[3]; ?></div></td>
                        <?php
                            }else{
                        ?>
                        <td><div class="quadrinho" title="Data Assinatura"><?php echo $tbl[2]; ?></div></td>
                        <td><div class="quadrinho" style="font-size: 65%;" title="Número do contrato"><?php echo $tbl[1]; ?></div></td>
                        <td><div class="quadrinho" style="<?php if($tbl[10] == 't'){echo 'color: red; font-weight: bold;';}else{echo 'font-weight: normal;';} ?>" title="Data Vencimento"><?php echo $tbl[3]; ?></div></td>
                        <?php
                            }
                        ?>
                        <td>
                            <?php
                                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $tbl[6]");
                                $row1 = pg_num_rows($rs1);
                                if($row1 > 0){
                                    $tbl1 = pg_fetch_row($rs1);
                                    $DescEmpr = $tbl1[0];
                                }else{
                                    $DescEmpr = "";
                                }
                            ?>
                            <div class="quadrinho" style="font-size: 80%; text-align: left; border: 0px;" title="Objeto: <?php echo $tbl[9]; ?>"><?php echo $DescEmpr; ?></div>
                        </td>
                        <td><div class="quadrinho" style="font-size: 70%; text-align: left; border: 0px;"><?php echo $tbl[9]; ?></div></td>
                        <td>
                            <?php
                                $rs2 = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $tbl[5]");
                                $row2 = pg_num_rows($rs2);
                                if($row2 > 0){
                                    $tbl2 = pg_fetch_row($rs2);
                                    $DescSetor = $tbl2[0];
                                }else{
                                    $DescSetor = "";
                                }
                            ?>
                            <div class="quadrinho" style="font-size: 70%;"><?php echo $DescSetor; ?></div>
                        </td>
                        <td>
                            <div class="quadrinho" style="font-size: 70%;" title="Vigência do contrato em meses."><?php echo $tbl[7]; ?></div>
                        </td>
                        <td>
                            <?php
                                if($tbl[8] == 0){
                                    echo "<img src='imagens/likeNo.png' height='18px;' title='Não é necessário aviso de interrupção/não prorrogação de contrato.'>";
                                }else{
                                    echo "<img src='imagens/likeYes.png' height='18px;' title='É preciso avisar sobre a não prorrogação de contrato.'>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if($tbl[11] == 1){
                                    echo "<img src='imagens/ok.png' height='18px;' title='Contrato em vigor.'>";
                                }
                                if($tbl[11] == 2){
                                    echo "<img src='imagens/okDev.png' height='18px;' title='Contrato terminado.'>";
                                }
                                if($tbl[11] == 3){
                                    echo "<img src='imagens/oknao.png' height='18px;' title='Contrato rescindido.'>";
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