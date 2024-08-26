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
                lengthMenu: [
                    [50, 100, 200, 500],
                    [50, 100, 200, 500]
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
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                        carregaModal($id);
                    }
                }
            });

            $(document).ready(function(){''
                document.getElementById("apagaRegEletric").style.visibility = "hidden";
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("apagaRegEletric").style.visibility = "visible";
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
            $admIns = parAdm("insleituraeletric", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editleituraeletric", $Conec, $xProj); // nível para editar

            $ValorKwh = parAdm("valorkwh", $Conec, $xProj);

            $hoje = date('d/m/Y');
            $rs = pg_query($Conec, "SELECT valorinieletric, TO_CHAR(datainieletric, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $ValorIni = $tbl[0];
                $DataIni = $tbl[1];
            }
            if($ValorIni == 0 || is_null($DataIni)){
                echo "<div style='text-align: center;'>É necessário inserir os valores iniciais da medição nos parâmetros do sistema.</div>";
                echo "<div style='text-align: center;'>Informe à ATI.</div>";
                return false;
            }
            $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura4, 'DD/MM/YYYY'), date_part('dow', dataleitura4), leitura4, dataleitura4 FROM ".$xProj.".leitura_eletric WHERE colec = 4 And ativo = 1 ORDER BY dataleitura4 DESC ");

            ?>
            <div  style="text-align: center;"><label class="titRelat">Energia Injetada<label></div>
                <table id="idTabela" class="display" style="margin: 0 auto; width: 95%;">
                    <thead>
                        <tr>
                            <th style="display: none;"></th>
                            <th style="display: none;"></th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Dia</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Leitura Mensal</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Valor Mensal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($tbl0 = pg_fetch_row($rs0)){
                            ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php echo $tbl0[1]; ?></td> <!-- Data -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php echo number_format($tbl0[3], 0, ",",".")." kWh"; ?></td> <!-- Leitura 1 -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php echo "R$ ".number_format(($tbl0[3]*$ValorKwh), 2, ",","."); ?></td>                    
                        </tr>
                        <?php

                            }
                            ?>
                    </tbody>
                </table>
        
        </div>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value="0" /> <!-- id registro -->
        <input type="hidden" id="mudou" value="0" />

        <!-- div modal para registrar leitura  -->
        <div id="relacmodalEletric" class="relacmodal">
            <div class="modal-content-Eletric">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Registrar Energia Ativa Injetada</h5>
                <div style="border: 2px solid blue; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq" style="width: 120px;">Data</td>
                        <td class="etiq" style="width: 150px;">Leitura</td>
                    </tr>
                    <tr>
                        <td><input type="text" style="text-align: center; border: 1px solid; border-radius: 4px;" id="insdata" width="150" onchange="checaData();" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('insleitura1');return false;}"/></td>
                        <td style="width: 120px;"><input type="text" style="text-align: center; width: 100%;" id="insleitura1" onchange="modif();" placeholder="Valor informado" onkeypress="if(event.keyCode===13){javascript:foco('botsalvar');return false;}"/></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 5px;"><div id="mensagemLeitura" style="color: red; font-weight: bold;"></div></td>
                    </tr>
                </table>

                    <div style="text-align: center; padding-bottom: 4px;">
                        <button id="apagaRegEletric" class="botpadrred" onclick="apagaModalEletric();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botsalvar" class="botpadrblue" onclick="salvaModal();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>