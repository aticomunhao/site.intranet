<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <script type="text/javascript">
            new DataTable('#idTabela', {
                info: false, // inform de pág sendo visualizada
                paging: false,  // paginação 
                lengthMenu: [
                    [50, 100, 200],
                    [50, 100, 200]
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
                    if(parseInt(document.getElementById("guardaEdit").value) === 1){
                        editaEquip($id);
                    }
                }
            });

        </script>
    </head>
    <body> 
        <?php
            date_default_timezone_set('America/Sao_Paulo');
            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "todos";
            }
            $Bebed = parEsc("bebed", $Conec, $xProj, $_SESSION["usuarioID"]);
            $FiscBebed = parEsc("bebed_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);

            $Condic = $xProj.".bebed.ativo = 1";
            if($Acao == "vencidos"){ // quando vem do aviso da página inicial 
                $Condic = $xProj.".bebed.ativo = 1 And notific = 1 And pararaviso = 0 And dataaviso <= CURRENT_DATE";
            }

            $rs0 = pg_query($Conec, "SELECT ".$xProj.".bebed.id, numapar, descmarca, desctipo, TO_CHAR(datatroca, 'DD/MM/YYYY'), TO_CHAR(datatroca, 'YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'YYYY'), localinst, modelo, 
            CASE WHEN dataaviso <= CURRENT_DATE AND notific = 1 THEN 'aviso' END
            FROM ".$xProj.".bebed_tipos INNER JOIN (".$xProj.".bebed INNER JOIN ".$xProj.".bebed_marcas ON ".$xProj.".bebed.codmarca = ".$xProj.".bebed_marcas.id) ON ".$xProj.".bebed.codtipo =  ".$xProj.".bebed_tipos.id 
            WHERE $Condic ORDER BY numapar ");

            ?>
            <div style="text-align: center; border: 1px solid; border-radius: 15px;">Bebedouros
                <table id="idTabela" class="display" style="margin: 0 auto; width: 95%;">
                    <thead>
                        <tr>
                            <th style="display: none;"></th>
                            <th style="display: none;"></th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Número de identificação na casa">Núm</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Marca do equipamento">Marca</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Modelo do equipamento">Modelo</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Tipo de elemento filtrante">Tipo</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Data da troca do elemento filtrante">Data Limpeza</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Data de vencimento do elemento filtrante">Próxima Limpeza</th>
                            <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;" title="Local de instalação do equipamento">Local Instalação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($tbl0 = pg_fetch_row($rs0)){
                            $Cod = $tbl0[0];
                            if($tbl0[5] == "3000"){
                                $DataTroca = "";
                            }else{
                                $DataTroca = $tbl0[4];
                            }
                            if($tbl0[7] == "3000"){
                                $DataVenc = "";
                            }else{
                                $DataVenc = $tbl0[6];
                            }
                            ?>
                            <tr>
                                <td style="display: none;"></td>
                                <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: center; font-weight: bold; padding-top: 25px; padding-bottom: 25px; <?php if($tbl0[7] == '3000'){echo 'color: red; font-weight: bold;';}else{echo 'font-weight: normal;';} ?>"><?php echo str_pad($tbl0[1], 3, 0, STR_PAD_LEFT); ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: left;" title="Marca"><?php echo $tbl0[2]; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: left;" title="Modelo"><?php echo $tbl0[11]; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: left;" title="Tipo de elemento filtrante"><?php echo $tbl0[3]; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: center;" title="Data troca"><?php echo $DataTroca; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: center; <?php if($tbl0[12] == 'aviso'){echo 'color: red; font-weight: bold;';}else{echo 'font-weight: normal;';} ?>" title="Vencimento"><?php echo  $DataVenc; ?></td>
                                <td style="border-bottom: 1px solid gray; text-align: left; font-size: 80%;" title="Local de instalação"><?php echo $tbl0[10]; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" id="guardacod" value="0" />
    </body>
</html>