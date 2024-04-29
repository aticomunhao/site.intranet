<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <script src="comp/js/jquery.mask.js"></script>
        <style type="text/css">
            .etiq{
                text-align: right; color: #036; font-size: .9em; font-weight: bold; padding: 3px;
            }
        </style>

        <script type="text/javascript">
            function ajaxIni(){
                try{
                    ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                    catch(e){
                        try{
                            ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                        catch(ex) {
                            try{
                                ajax = new XMLHttpRequest();}
                            catch(exc){
                                alert("Esse browser não tem recursos para uso do Ajax");
                                ajax = null;
                            }
                        }
                    }
                }
                $(document).ready(function(){
                    $("#dataIniLeitura").mask("99/99/9999");
                });
                function salvaParam(Valor, Param){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaParam&param="+Param+"&valor="+Valor, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    } 
                }
                function MarcaAdm(obj){
                    if(obj.checked === true){
                        Valor = 1;
                    }else{
                        Valor = 0;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaAdm&valor="+Valor+"&caixa="+obj.value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }

                function salvaLeitIni(Valor){
                    if(document.getElementById("valorIniLeitura").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=valorleitura&valor="+document.getElementById("valorIniLeitura").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaDataIni(Valor){
                    if(document.getElementById("dataIniLeitura").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=dataleitura&valor="+encodeURIComponent(document.getElementById("dataIniLeitura").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
        </script>
    </head>
    <body>
        <?php
            require_once("abrealas.php");

            $rsSis = pg_query($Conec, "SELECT admvisu, admedit, admcad, insevento, editevento, instarefa, edittarefa, insramais, editramais, instelef, edittelef, 
            editpagina, insarq, insaniver, editaniver, instroca, edittroca, insocor, editocor, insleitura, editleitura, TO_CHAR(datainileitura , 'DD/MM/YYYY'), valorinileitura 
            FROM ".$xProj.".paramsis WHERE idPar = 1");
            $ProcSis = pg_fetch_row($rsSis);
            $admVisu = $ProcSis[0]; // admVisu - administrador visualiza usuários
            $admEdit = $ProcSis[1]; // admEdit - administrador edita usuários
            $admCad = $ProcSis[2];  // admCad - administrador cadastra usuários
            $DataIniLeitura = $ProcSis[21]; // controle de consumo de água - leitura do hidrômetro
            $ValorIniLeitura = $ProcSis[22];  // controle de consumo de água - data inicial

            $insEvento = $ProcSis[3];   // insEvento - inserção de eventos no calendário
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insEvento");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsEvento = $Proc1[0];

            $editEvento = $ProcSis[4];   // editEvento - edição de eventos no calendário
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editEvento");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditEvento = $Proc2[0];

            $insLeitura = $ProcSis[19];   // insLeitura - inserção de leitura do hidrômetro
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLeitura");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsLeitura = $Proc1[0];

            $editLeitura = $ProcSis[20];   // editLeitura - edição de leitura
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editLeitura");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditLeitura = $Proc2[0];

            $insTarefa = $ProcSis[5];   // insTarefa - inserção de tarefas
            $rs3 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTarefa");
            $Proc3 = pg_fetch_row($rs3);
            $nomeInsTarefa = $Proc3[0];

            $editTarefa = $ProcSis[6];   // editTarefa - edição de tarefas
            $rs4 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTarefa");
            $Proc4 = pg_fetch_row($rs4);
            $nomeEditTarefa = $Proc4[0];
            
            $insRamais = $ProcSis[7];   // insRamais - edição de ramais internos
            $rs5 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insRamais");
            $Proc5 = pg_fetch_row($rs5);
            $nomeInsRamais = $Proc5[0];

            $editRamais = $ProcSis[8];   // editRamais - edição de ramais internos
            $rs6 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editRamais");
            $Proc6 = pg_fetch_row($rs6);
            $nomeEditRamais = $Proc6[0];

            $insTelef = $ProcSis[9];   // insTelef - edição de telefones
            $rs7 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTelef");
            $Proc7 = pg_fetch_row($rs7);
            $nomeInsTelef = $Proc7[0];

            $editTelef = $ProcSis[10];   // editTelef - edição de telefones
            $rs8 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTelef");
            $Proc8 = pg_fetch_row($rs8);
            $nomeEditTelef = $Proc8[0];

            $editPagina = $ProcSis[11];   // editPagina - edição das páginas das diretorias/assessorias
            $rs9 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editPagina");
            $Proc9 = pg_fetch_row($rs9);
            $nomeEditPagina = $Proc9[0];

            $insArq = $ProcSis[12];   // insArq - edição das páginas das diretorias/assessorias
            $rs10 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insArq");
            $Proc10 = pg_fetch_row($rs10);
            $nomeInsArq = $Proc10[0];

            $insAniver = $ProcSis[13];   // insAniver - edição das páginas das diretorias/assessorias
            $rs11 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insAniver");
            $Proc11 = pg_fetch_row($rs11);
            $nomeInsAniver = $Proc11[0];

            $editAniver = $ProcSis[14];   // editAniver - edição de telefones
            $rs12 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editAniver");
            $Proc12 = pg_fetch_row($rs12);
            $nomeEditAniver = $Proc12[0];
            
            $insTroca = $ProcSis[15];   // insTroca - edição de trocas
            $rs13 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTroca");
            $Proc13 = pg_fetch_row($rs13);
            $nomeInsTroca = $Proc13[0];

            $editTroca = $ProcSis[16];   // editTroca - edição de trocas
            $rs14 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTroca");
            $Proc14 = pg_fetch_row($rs14);
            $nomeEditTroca = $Proc14[0];

            $insOcor = $ProcSis[17];   // insOcor - registro de ocorrências
            $rs15 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insOcor");
            $Proc15 = pg_fetch_row($rs15);
            $nomeInsOcor = $Proc15[0];

            $editOcor = $ProcSis[18];   // editOcor
            $rs16 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editOcor");
            $Proc16 = pg_fetch_row($rs16);
            $nomeEditOcor = $Proc16[0];


            $OpAdmInsEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            
            $OpAdmInsLeit = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditLeit = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmEditPag = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmInsArq = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

        ?>

        <div style="margin: 0 auto; margin-top: 40px; padding: 20px; border: 2px solid blue; border-radius: 15px; width: 50%; min-height: 200px;">
            <div style="text-align: center;">
                <h4>Parâmetros do Sistema</h4>
            </div>


<!-- Calendário  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Calendário</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insEvento; ?>"><?php echo $nomeInsEvento; ?></option>
                            <?php 
                            if($OpAdmInsEv){
                                while ($Opcoes = pg_fetch_row($OpAdmInsEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editEvento; ?>"><?php echo $nomeEditEvento; ?></option>
                            <?php 
                            if($OpAdmEditEv){
                                while ($Opcoes = pg_fetch_row($OpAdmEditEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Leitura Hidrômetro  -->
<div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Controle do Consumo de Água - Leitura do Hidrômetro</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insLeitura');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insLeitura; ?>"><?php echo $nomeInsLeitura; ?></option>
                            <?php 
                            if($OpAdmInsLeit){
                                while ($Opcoes = pg_fetch_row($OpAdmInsLeit)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editLeitura');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editLeitura; ?>"><?php echo $nomeEditLeitura; ?></option>
                            <?php 
                            if($OpAdmEditLeit){
                                while ($Opcoes = pg_fetch_row($OpAdmEditLeit)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Data Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="dataIniLeitura" value="<?php echo $DataIniLeitura; ?>" onchange="salvaDataIni(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valorIniLeitura" value="<?php echo $ValorIniLeitura; ?>" onchange="salvaLeitIni(value);" style="width: 90px; text-align: center;"></td>
                    </tr>

                </table>
            </div>


<!-- Páginas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Páginas das Diretorias/Assessorias</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR arquivos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insArq');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insArq; ?>"><?php echo $nomeInsArq; ?></option>
                            <?php 
                            if($OpAdmInsArq){
                                while ($Opcoes = pg_fetch_row($OpAdmInsArq)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR página:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editPagina');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editPagina; ?>"><?php echo $nomeEditPagina; ?></option>
                            <?php 
                            if($OpAdmEditPag){
                                while ($Opcoes = pg_fetch_row($OpAdmEditPag)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Ramais  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Ramais Internos</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insRamais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insRamais; ?>"><?php echo $nomeInsRamais; ?></option>
                            <?php 
                            if($OpAdmInsRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmInsRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editRamais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editRamais; ?>"><?php echo $nomeEditRamais; ?></option>
                            <?php 
                            if($OpAdmEditRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmEditRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Ocorrrências  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Registro de Ocorrêncas</b>: <label style="color: gray; font-size: .8em;">Cada usuário só pode ver as ocorrências que inseriu</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insOcor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insOcor; ?>"><?php echo $nomeInsOcor; ?></option>
                            <?php 
                            if($OpAdmInsOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmInsOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editOcor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editOcor; ?>"><?php echo $nomeEditOcor; ?></option>
                            <?php 
                            if($OpAdmEditOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmEditOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Tarefas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Tarefas</b>: <label style="color: gray; font-size: .8em;">Cada nível insere tarefa para seu nível administrativo ou nível inferior</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTarefa; ?>"><?php echo $nomeInsTarefa; ?></option>
                            <?php 
                            if($OpAdmInsTar){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTarefa; ?>"><?php echo $nomeEditTarefa; ?></option>
                            <?php 
                            if($OpAdmEditTar){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Telefones  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Telefones Úteis</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTelef; ?>"><?php echo $nomeInsTelef; ?></option>
                            <?php 
                            if($OpAdmInsTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTelef; ?>"><?php echo $nomeEditTelef; ?></option>
                            <?php 
                            if($OpAdmEditTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Trocas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Trocas de Objetos</b>: <label style="color: gray; font-size: .8em;">É editável pelo setor que inseriu</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTroca; ?>"><?php echo $nomeInsTroca; ?></option>
                            <?php 
                            if($OpAdmInsTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTroca; ?>"><?php echo $nomeEditTroca; ?></option>
                            <?php 
                            if($OpAdmEditTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
<br><br><br>
<!--
            <div style="border: 1px solid; border-radius: 10px; padding: 15px;">
                - Usuários:<br>
                <input type="checkbox" id="admVisu" onclick="MarcaAdm(this, 'admVisu');" value="admVisu" <?php if($admVisu == 1) {echo "checked";} ?>>
                <label for="admVisu" class="etiq">Administradores podem acessar lista de usuários</label>
                <br>
                <input type="checkbox" id="admEdit" onclick="MarcaAdm(this, 'admEdit');" value="admEdit" <?php if($admEdit == 1) {echo "checked";} ?>>
                <label for="admEdit" class="etiq">Administradores podem EDITAR usuários</label>
                <br>
                <input type="checkbox" id="admCad" onclick="MarcaAdm(this, 'admCad');" value="admCad" <?php if($admCad == 1) {echo "checked";} ?>>
                <label for="admCad" class="etiq">Administradores podem CADASTRAR usuários</label>
            </div>
-->
        </div>
    </body>
</html>