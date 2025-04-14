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
        <script type="text/javascript">
            new DataTable('#idTabela', {
                info: false, // inform de pág sendo visualizada
                paging: false,  // paginação 
                searching: false, 
                lengthMenu: [
                    [100, 200, 500, 1000],
                    [100, 200, 500, 1000]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });
            tableLe = new DataTable('#idTabela');
            tableLe.on('click', 'tbody tr', function () {
                data = tableLe.row(this).data();
                $id = data[1];
                document.getElementById("guardacod").value = $id;                
                if($id !== 0){
                    if(parseInt(document.getElementById("editor").value) === 1){
                        carregaModal($id);
                    }
                }
            });

            $(document).ready(function(){''
                document.getElementById("apagaRegCombust").style.visibility = "hidden";
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("apagaRegCombust").style.visibility = "visible";
                }
                $("#insdata").mask("99/99/9999");
                $('#insdata').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
            });

        </script>
    </head>
    <body> 
        <?php
            $Menu1 = escMenu($Conec, $xProj, 1); //abre alas 
            date_default_timezone_set('America/Sao_Paulo');
            $Viatura = parEsc("viatura", $Conec, $xProj, $_SESSION["usuarioID"]);
            $FiscViat = parEsc("fisc_viat", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal

            $rs0 = pg_query($Conec, "SELECT ".$xProj.".viaturas.id, TO_CHAR(datacompra, 'DD/MM/YYYY'), volume, custo, desc_viatura, coddespesa 
            FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_tipo ON ".$xProj.".viaturas.codveiculo = ".$xProj.".viaturas_tipo.id 
            WHERE ".$xProj.".viaturas.ativo = 1 ORDER BY datacompra DESC ");

            ?>
            <div  style="text-align: center;"><label class="titRelat">Registros<label></div>
                <table id="idTabela" class="display" style="margin: 0 auto; width: 95%;">
                    <thead>
                        <tr>
                            <th style="display: none;"></th>
                            <th style="display: none;"></th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Dia</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Viatura</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Abast/Manut</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Custo</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Custo/l</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($tbl0 = pg_fetch_row($rs0)){
                                $CodDespesa = $tbl0[5];
                            ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;" title="Data"><?php echo $tbl0[1]; ?></td> <!-- Data -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;" title="Viatura"><?php echo $tbl0[4]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php if($CodDespesa == 1){echo number_format(($tbl0[2]/100), 2, ",",".")." litros";}else{echo "Manutenção";} ?></td> <!-- volume -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;" title="Despesa"><?php echo "R$ ".number_format(($tbl0[3]/100), 2, ",","."); ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php if($CodDespesa == 1){echo "R$ ".number_format(($tbl0[3]/$tbl0[2]), 3, ",",".");}else{echo "";}; ?></td>                  
                        </tr>
                        <?php

                            }
                            ?>
                    </tbody>
                </table>
        </div>
        <input type="hidden" id="guardacod" value="0" />
    </body>
</html>