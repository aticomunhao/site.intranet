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
            $(document).ready(function(){
                $('#insdata').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
            });
//            $("#insdata").mask("99/99/9999"); // esse tipo de datepicker não deixa digitar
        </script>

    </head>
    <body> 
        <div  style="text-align: center;"><label class="titRelat">Leituras Hidrômetro<label></div>
            <?php 
                date_default_timezone_set('America/Sao_Paulo');
                $admIns = parAdm("insleituraagua", $Conec, $xProj);   // nível para inserir 
                $admEdit = parAdm("editleituraagua", $Conec, $xProj); // nível para editar
                $hoje = date('d/m/Y');
                $rs = pg_query($Conec, "SELECT valoriniagua, TO_CHAR(datainiagua, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
                $row = pg_num_rows($rs);
                if($row > 0){
                    $tbl = pg_fetch_row($rs);
                    $ValorIni = $tbl[0];
                    $DataIni = $tbl[1];
                }
                if($ValorIni == 0 || is_null($DataIni)){
                    echo "É necessário inserir os valores iniciais da medição nos parâmetros do sistema. Informe à ATi,";
                    return false;
                }
                $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3, dataleitura FROM ".$xProj.".leitura_agua WHERE ativo = 1 ORDER BY dataleitura DESC ");
                $row0 = pg_num_rows($rs0);
                $Cont = 0;
                $Leit24Ant = 0;
                ?>
                <table id="idTabela" class="display" style="margin: 0 auto; width: 95%;">
                    <thead>
                        <tr>
                            <th style="display: none;"></th>
                            <th style="display: none;"></th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%;">Dia</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Sem</th>

                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Leitura 1<br>07h30</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;" title="Consumo">Cons</th>
                                
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Leitura 2<br>16h30</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;" title="Consumo">Cons</th>
                                
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Leitura 3<br>24h00</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;" title="Consumo">Cons</th>

                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;" title="Consumo Diário">Cons Diário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($tbl0 = pg_fetch_row($rs0)){
                                $Dow = $tbl0[2];
                                switch ($Dow){
                                    case 0:
                                        $Sem = "DOM";
                                        break;
                                    case 1:
                                        $Sem = "SEG";
                                        break;
                                    case 2:
                                        $Sem = "TER";
                                        break;
                                    case 3:
                                        $Sem = "QUA";
                                        break;
                                    case 4:
                                        $Sem = "QUI";
                                        break;
                                    case 5:
                                        $Sem = "SEX";
                                        break;
                                    case 6:
                                        $Sem = "SAB";
                                        break;
                                }
                                $DataLinha = $tbl0[6];

                                if(strtotime($DataLinha) == strtotime($DataIni)){ // "2024-03-01"
                                    $Leit24Ant = $ValorIni;  //1696.485;
                                }else{
                                    $rs1 = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_agua WHERE dataleitura = (date '$DataLinha' - 1) And ativo = 1");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        $tbl1 = pg_fetch_row($rs1);
                                        $Leit24Ant = $tbl1[0];
                                    }
                                }
                                $Leit07 = $tbl0[3];
                                $Leit16 = $tbl0[4];
                                $Leit24 = $tbl0[5];
                                if($Leit07 == 0){
                                    $Cons1 = 0;
                                }else{
                                    $Cons1 = ($Leit07-$Leit24Ant);
                                }
                                if($Leit16 == 0){
                                    $Cons2 = 0;
                                }else{
                                    $Cons2 = ($Leit16-$Leit07);
                                }
                                if($Leit24 == 0){
                                    $Cons3 = 0;    
                                }else{
                                    $Cons3 = ($Leit24-$Leit16);
                                }

                                if($Leit07 == 0){
                                    $Cons1 = 0;
                                    $Cons2 = 0;
                                    $Cons3 = 0;
                                }
                                if($Leit16 == 0){
                                    $Cons2 = 0;
                                    $Cons3 = 0;
                                }
                                $ConsDia = $Cons1+$Cons2+$Cons3;
                            ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php echo $tbl0[1]; ?></td> <!-- Data -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;"><?php echo $Sem; ?></td> <!-- dia da semana --> 

                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format($Leit07, 3, ",","."); ?></td> <!-- Leitura 1 -->
                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format($Cons1, 3, ",","."); ?></td>

                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format($Leit16, 3, ",","."); ?></td> <!-- Leitura 2 -->
                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format(($Cons2), 3); ?></td>

                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format($Leit24, 3, ",","."); ?></td> <!-- Leitura 3 -->
                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 80%;"><?php echo number_format(($Cons3), 3, ",","."); ?></td>

                            <td style="border-bottom: 1px solid gray; text-align: right; font-size: 90%;"><?php echo number_format(($ConsDia), 3, ",","."); ?></td>
                        </tr>
                        <?php
                            $Cont = $Cont + $tbl0[3];
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
        <div id="relacmodalLeitura" class="relacmodal">
            <div class="modal-content-Leitura">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h4 id="titulomodal" style="text-align: center; color: #666;">Registrar Leitura Hidrômetro</h4>
                <div style="border: 2px solid blue; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq" style="width: 100px;">Data</td>
                        <td class="etiq" style="width: 5ch;">Sem</td>
                        <td class="etiq" style="width: 120px;">Leitura 1 (07h30)</td>
                        <td class="etiq" style="width: 120px;">Leitura 2 (16h30)</td>
                        <td class="etiq" style="width: 120px;">Leitura 3 (24h00)</td>
                    </tr>
                    <tr>
                        <td><input type="text" style="text-align: center; width: 100%;" id="insdata" onchange="checaData();" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('insleitura1');return false;}"/></td>
                        <td style="text-align: center;"><label id="insdiasemana" style="font-size: 80%;"></label></td>
                        <td style="width: 120px;"><input type="text" style="text-align: center; width: 100%;" id="insleitura1" onchange="modif();" placeholder="Leitura 1" onkeypress="if(event.keyCode===13){javascript:foco('insleitura2');return false;}"/></td>
                        <td style="width: 120px;"><input type="text" style="text-align: center; width: 100%;" id="insleitura2" placeholder="Leitura 2" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('insleitura3');return false;}"/></td>
                        <td style="width: 120px;"><input type="text" style="text-align: center; width: 100%;" id="insleitura3" placeholder="Leitura 3" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('insleitura1');return false;}"/></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center; padding-top: 5px;"><div id="mensagemLeitura" style="color: red; font-weight: bold;"></div></td>
                    </tr>
                </table>

                    <div style="text-align: center; padding-bottom: 4px;">
                        <button class="botpadrblue" onclick="salvaModal();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>