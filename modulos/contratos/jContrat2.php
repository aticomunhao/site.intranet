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
            new DataTable('#idTabela2', {
                paging: false,
                //scrollY: 100,
                scrollX: true,
                searching: false,
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            table = new DataTable('#idTabela2');
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
        ?>
        <div style="margin: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%; text-align: left;">
                <?php
                if($Contr == 1){
                ?>
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Novo" title="Inserir novo contrato onde a Comunhão é contratada." onclick="insContrato(2);">
                <?php
                }else{
                    echo "&nbsp;";
                }
                ?>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Contratantes</h5>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: right;">
            <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprContratantes();" title="Gera um arquivo pdf com a relação das contratantes." >PDF</button>
            <label style="padding-left: 5px;"></label>
            </div>
        </div>

         <!-- Empresas contrantes da comunhão -->
        <?php
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr, 
        CASE WHEN dataaviso <= CURRENT_DATE AND datavencim >= CURRENT_DATE THEN 'aviso' END 
        FROM ".$xProj.".contratos2 WHERE ativo = 1 ORDER BY dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="padding: 10px;">
            <table id="idTabela2" class="display" style="width:95%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq aCentro">Assinatura</th>
                        <th class="etiq aCentro">N°</th>
                        <th class="etiq aCentro">Vencimento</th>
                        <th class="etiq aCentro">Empresa</th>
                        <th class="etiq aCentro">Objeto</th>
                        <th class="etiq aCentro">Setor</th>
                        <th class="etiq aCentro" title="Vigência do contrato">Vig</th>
                        <th class="etiq aCentro" title="Notificação sobre a não prorrogação do contrato?">Not</th>
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
                        <td><div class="quadrinhoClick" onclick="editContrato(2, <?php echo $Cod; ?>);" title="Data Assinatura"><?php echo $tbl[2]; ?></div></td>
                        <td><div class="quadrinhoClick" style="font-size: 65%;" onclick="editContrato(2, <?php echo $Cod; ?>);" title="Número do contrato"><?php echo $tbl[1]; ?></div></td>
                        <td><div class="quadrinhoClick" style="<?php if($tbl[10] == 'aviso'){echo 'color: red;';}else{echo 'color: black;';} ?>" onclick="editContrato(2, <?php echo $Cod; ?>);" title="Data Vencimento"><?php echo $tbl[3]; ?></div></td>
                        <?php
                            }else{
                            ?>
                        <td><div class="quadrinho" title="Data Assinatura"><?php echo $tbl[2]; ?></div></td>
                        <td><div class="quadrinho" style="font-size: 65%;" title="Número do contrato"><?php echo $tbl[1]; ?></div></td>
                        <td><div class="quadrinho" style="<?php if($tbl[10] == 't'){echo 'color: red;';}else{echo 'color: black;';} ?>" title="Data Vencimento"><?php echo $tbl[3]; ?></div></td>

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
                            <div class="quadrinho" style="font-size: 80%;" title="Objeto: <?php echo $tbl[9]; ?>"><?php echo $DescEmpr." - ".$tbl[10]; ?></div>
                        </td>
                        <td><div class="quadrinho" style="font-size: 70%;"><?php echo $tbl[9]; ?></div></td>
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
                        <td><div class="quadrinho" style="font-size: 70%;" title="Vigência do contrato em meses."><?php echo $tbl[7]; ?></div></td>
                        <td>
                            <?php
                                if($tbl[8] == 0){
                                    echo "<img src='imagens/likeNo.png' height='18px;' title='Não é necessário aviso de interrupção/não prorrogação de contrato.'>";
                                }else{
                                    echo "<img src='imagens/likeYes.png' height='18px;' title='É preciso avisar sobre a não prorrogação de contrato.'>";
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