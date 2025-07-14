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
                    [500, 1000],
                    [500, 1000]
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
                document.getElementById("apagaRegEletric").style.visibility = "hidden";
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("apagaRegEletric").style.visibility = "visible";
                }
                $('#insdata').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
            });
        </script>
    </head>
    <body> 
        <?php 
            date_default_timezone_set('America/Sao_Paulo');
            $admIns = parAdm("insleituraeletric", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editleituraeletric", $Conec, $xProj); // nível para editar

            //Ver quantos lançamentos já tem - se for o primeiro conta esse lançamento como consumo
            $rsLan = pg_query($Conec, "SELECT id FROM ".$xProj.".leitura_eletric WHERE colec = 5 And ativo = 1 ");
            $rowLan = pg_num_rows($rsLan);

            // Para checar se está faltando algum dia na lista - põe o próximo dia em vermelho
            $rsDia1 = pg_query($Conec, "SELECT MAX(dataleitura5) FROM ".$xProj.".leitura_eletric WHERE colec = 5 And ativo = 1 ");
            $rowDia1 = pg_num_rows($rsDia1);
            if($rowDia1 > 0){
                $tblDia1 = pg_fetch_row($rsDia1);
                $DiaIni = $tblDia1[0]; // pega a data do último lançamento 
            }

            $rs = pg_query($Conec, "SELECT valorinieletric5, TO_CHAR(datainieletric5, 'YYYY/MM/DD'), TO_CHAR(datainieletric5, 'YYYY') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $ValorIni = $tbl[0];
                $DataIni = $tbl[1];
//                if($tbl[2] == "3000"){
//                    $DataIni = date('Y/m/d');
//                }
            }

//            if($ValorIni == 0 || is_null($DataIni)){
            if(is_null($ValorIni)){
                echo "<div style='text-align: center;'>É necessário inserir os valores iniciais da medição nos parâmetros do sistema.</div>";
                echo "<div style='text-align: center;'>Informe à ATI.</div>";
                return false;
            }
            
            $Condic = "colec = 5 And ativo = 1";
            if(isset($_REQUEST["mesano"])){
                $MesAno = addslashes(filter_input(INPUT_GET, 'mesano')); 
                if($MesAno != ""){
                    $Proc = explode("/", $MesAno);
                    $Mes = $Proc[0];
                    if(strLen($Mes) < 2){
                        $Mes = "0".$Mes;
                    }
                    $Ano = $Proc[1];
                    $Condic = "colec = 5 And ativo = 1 And DATE_PART('MONTH', dataleitura5) = '$Mes' And DATE_PART('YEAR', dataleitura5) = '$Ano'";
                }else{
                    $Condic = "colec = 5 And ativo = 1";
                }
            }
            if(isset($_REQUEST["ano"])){
                $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
                if($Ano != ""){
                    $Condic = "colec = 5 And ativo = 1 And DATE_PART('YEAR', dataleitura5) = '$Ano'";
                }else{
                    $Condic = "colec = 5 And ativo = 1";
                }
            }
            $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura5, 'DD/MM/YYYY'), date_part('dow', dataleitura5), leitura5, dataleitura5 FROM ".$xProj.".leitura_eletric WHERE $Condic ORDER BY dataleitura5 DESC ");
            $Cont = 0;
            $Leit24Ant = 0;
            ?>
            <div  style="text-align: center;"><label class="titRelat corPreta">Leituras Medidor Eletricidade - Viaturas<label></div>
                <table id="idTabela" class="display" style="margin: 0 auto; width: 95%;">
                    <thead>
                        <tr>
                            <th style="display: none;"></th>
                            <th style="display: none;"></th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Dia</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Sem</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Leitura Diária</th>
                            <th style="border-bottom: 1px solid gray; font-size: 70%; text-align: center;">Consumo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($tbl0 = pg_fetch_row($rs0)){

                                $Cod = $tbl0[0];
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
                                $DataLinha = $tbl0[4];

                                if(strtotime($DataLinha) == strtotime($DataIni)){ // DataIni tem que ser igual ao dia da primeira leitura anotado em Parâmetros do Sistema
                                    $Leit24Ant = $ValorIni;  //1696.485;
                                }else{
                                    $rs1 = pg_query($Conec, "SELECT leitura5 FROM ".$xProj.".leitura_eletric WHERE dataleitura5 = (date '$DataLinha' - 1) And colec = 5 And ativo = 1");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        $tbl1 = pg_fetch_row($rs1);
                                        $Leit24Ant = $tbl1[0];
                                    }
                                }
                                $Leit07 = $tbl0[3];
                                if($Leit07 == 0){
                                    $ConsDia = 0;
                                }else{
                                    $ConsDia = ($Leit07-$Leit24Ant);
                                }

                                //Preenche coluna consdiario5 para o gráfico
                                pg_query($Conec, "UPDATE ".$xProj.".leitura_eletric SET consdiario5 = $ConsDia WHERE id = $Cod");
                            ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="display: none;"><?php echo $tbl0[0]; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%; <?php if(strtotime(date($tbl0[4])) != strtotime(date($DiaIni))){echo 'color: red; font-weight: bold;'; $DiaIni = date('Y/m/d', strtotime($DiaIni. '- 1 day'));}else{echo 'font-weight: normal;';} ?>" title="Data">
                                <?php
                                echo $tbl0[1];
                                ?>
                            </td> <!-- Data -->
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 70%;" title="Dia da Semana"><?php echo $Sem; ?></td> <!-- dia da semana --> 
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%; <?php if($Leit07 == 0){echo 'color: red;';} ?>" title="Leitura"><?php echo $Leit07; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 80%;" title="Consumo do dia"><?php echo $ConsDia." kWh"; ?></td>
                        </tr>
                        <?php
                            $Cont = $Cont + $tbl0[3];
                            $DiaIni = date('Y/m/d', strtotime($DiaIni. '- 1 day')); //segue voltando um dia
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
            <div class="modal-content-Eletric corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Registrar Leitura Medidor Eletricidade</h5>
                <div style="border: 2px solid blue; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq" style="width: 120px;">Data</td>
                        <td class="etiq">Sem</td>
                        <td class="etiq" style="width: 150px;">Leitura</td>
                    </tr>
                    <tr>
                        <td><input type="text" style="text-align: center; border: 1px solid; border-radius: 4px;" id="insdata" width="150" onchange="checaData();" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('insleitura5');return false;}"/></td>
                        <td style="text-align: center;"><label id="insdiasemana" style="font-size: 80%;"></label></td>
                        <td style="width: 120px;"><input type="text" style="text-align: center; width: 100%;" id="insleitura5" onchange="modif();" placeholder="Leitura" onkeypress="if(event.keyCode===13){javascript:foco('botsalvar');return false;}"/></td>
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