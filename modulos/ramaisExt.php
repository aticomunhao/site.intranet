<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="class/superfish/js/jquery.js"></script><!-- versão 1.12.1 veio com o superfish - Tem que usar esta, a versão 3.6 não recarrega a página-->
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <script>
            //Config do DataTable
            new DataTable('#idTabela', {
                columnDefs: [
                {
                    targets: [0],
                    orderData: [0, 1]
                },
                {
                    targets: [1],
                    orderData: [1, 0]
                },
                {
                    targets: [4],
                    orderData: [4, 0]
                }
                ],
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

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                document.getElementById("guardaid_click").value = $id;
                if($id !== ""){
                    if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admEdit").value)){
                        document.getElementById("SiglaEmpresa").disabled = true;
                        document.getElementById("NomeEmpresa").disabled = true;
                        document.getElementById("Setor").disabled = true;
						document.getElementById("ContatoNome").disabled = true;
                        document.getElementById("TelefoneFixo").disabled = true;
                        document.getElementById("TelefoneCel").disabled = true;
                        document.getElementById("botsalvar").style.visibility = "hidden"; // botão salvar
                        document.getElementById("titulomodal").innerHTML = "Telefones Úteis";
                    }
//                    if(parseInt(document.getElementById("UsuAdm").value) > 0){ //após login
                        carregaModal($id);
//                    }
                    
                }
            });
            
            function ajaxIni(){
                try{
                ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                catch(e){
                try{
                   ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                   catch(ex) {
                   try{
                       ajax = new XMLHttpRequest();}
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }

            $(document).ready(function(){
                if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admIns").value)){ // acima de Registrado
                    document.getElementById("botapagar").style.visibility = "hidden"; // botão para apagar
                    document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir
                    document.getElementById("botsalvar").style.visibility = "hidden"; // botão salvar
                }
                modalEdit = document.getElementById('relacmodal'); //span[0]
                spanEdit = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalEdit){
                        modalEdit.style.display = "none";
                    }
                };

                $("#TelefoneCel").mask("(99) 99999-9999");
            });

            function carregaModal(id){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/salvaRamais.php?acao=buscaRamal&tipo=2&numero="+id, true); // tipo 1 = ramal interno
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("SiglaEmpresa").value = Resp.SiglaEmpresa;
                                document.getElementById("NomeEmpresa").value = Resp.NomeEmpresa;
                                document.getElementById("Setor").value = Resp.Setor;
								document.getElementById("ContatoNome").value = Resp.ContatoNome;
                                document.getElementById("TelefoneFixo").value = Resp.TelefoneFixo;
                                document.getElementById("TelefoneCel").value = Resp.TelefoneCel;
                                document.getElementById("titulomodal").innerHTML = "Edição de Telefones Úteis";
                                document.getElementById("botapagar").disabled = false;
                                document.getElementById("mudou").value = "0";
                                document.getElementById("relacmodal").style.display = "block";
                                document.getElementById("SiglaEmpresa").focus();
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaModal(){
                if(document.getElementById("SiglaEmpresa").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo Sigla ou Nome";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("NomeEmpresa").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo Nome da Empresa/Instituição ou pessoa";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                //Se houve alguma modificação
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/salvaRamais.php?acao=salvaRamal&tipo=2&numero="+document.getElementById("guardaid_click").value
                        +"&SiglaEmpresa="+document.getElementById("SiglaEmpresa").value
                        +"&NomeEmpresa="+document.getElementById("NomeEmpresa").value
    					+"&ContatoNome="+document.getElementById("ContatoNome").value
                        +"&Setor="+document.getElementById("Setor").value
                        +"&TelefoneFixo="+document.getElementById("TelefoneFixo").value
                        +"&TelefoneCel="+document.getElementById("TelefoneCel").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 2){
                                        $('#mensagem').fadeIn("slow");
                                        document.getElementById("mensagem").innerHTML = "Esse cadastro já existe.";
                                        $('#mensagem').fadeOut("slow");
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodal").style.display = "none";
                                        $('#container3').load('modulos/ramaisExt.php?tipo='+document.getElementById("tipo_acesso").value);
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("mudou").value = "0";
                    document.getElementById("relacmodal").style.display = "none";
                }
            }
            function fechaModal(){
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("relacmodal").style.display = "none";
            }
            function InsRamais(){
                document.getElementById("SiglaEmpresa").value = "";
                document.getElementById("NomeEmpresa").value = "";
                document.getElementById("Setor").value = "";
				document.getElementById("ContatoNome").value = "";
                document.getElementById("TelefoneFixo").value = "";
//				document.getElementById("TelefoneCel").value = "";
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("botapagar").disabled = true;
                document.getElementById("titulomodal").innerHTML = "Inserção de Telefones Úteis";
                document.getElementById("relacmodal").style.display = "block";
            }
            function deletaModal(){
                $.confirm({
                    title: 'Apagar lançamento.',
                    content: 'Confirma apagar este número?',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/salvaRamais.php?acao=deletaRamal&tipo=2&numero="+document.getElementById("guardaid_click").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("mudou").value = "0";
                                                document.getElementById("relacmodal").style.display = "none";
                                                $('#container3').load('modulos/ramaisExt.php?tipo='+document.getElementById("tipo_acesso").value);
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function foco(id){
                document.getElementById(id).focus();
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal
                document.getElementById("mudou").value = "1";
            }
            //máscara para telefone fixo
            const handlePhone = (event) => {
                let input = event.target;
                input.value = phoneMask(input.value);
            }
            const phoneMask = (value) => {
                if (!value) return ""
                    value = value.replace(/\D/g,'');
                    value = value.replace(/(\d{2})(\d)/,"($1) $2");
                    value = value.replace(/(\d)(\d{4})$/,"$1-$2");
                    return value;
                }
        </script>
    </head>
    <body>
        <?php
        require_once("config/abrealas.php");
        if(!$Conec){
            echo "Sem contato com o PostGresql";
        }
        $rs = pg_query($Conec, "SELECT * FROM information_schema.tables WHERE table_schema = 'cesb';");
        $row = pg_num_rows($rs);
        if($row == 0){
            die("<br>Faltam tabelas. Informe à ATI");
            return false;
        }
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        if($Tipo == 2){
            $idAdm = $_SESSION["AdmUsu"];
        }else{
            $idAdm = 0;
        }

        $admIns = parAdm("instelef", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("edittelef", $Conec, $xProj); // nível para editar

        $rs0 = pg_query($Conec, "SELECT codtel, siglaempresa, nomeempresa, contatonome, codsetor, setor, telefonefixo, telefonecel FROM ".$xProj.".ramais_ext WHERE siglaempresa != '' And ativo = 1 ORDER BY siglaempresa");
        $row0 = pg_num_rows($rs0);
        ?>
        <input type="hidden" id="tipo_acesso" value="<?php echo $Tipo; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $idAdm; ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->

        <input type="hidden" id="guardaid_click" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" value="Inserir Novo" onclick="InsRamais();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Telefones Úteis</h3>
            </div>

            <table id="idTabela" class="display" style="width:85%;">
                <thead>
	                <tr>
                        <th style="display:none;">-</th> <!--Para não indexar pelo campo seguinte, que pode aparecer ou não, conforme a OM do usuário  -->
                        <th style="display:none;">Cod</th>
                        <th id="jSigla" class="jindice" title="Clique para indexar por esta coluna">Sigla</th>
                        <th id="jNomeCompl" class="jindice" title="Clique para indexar por esta coluna">Nome</th>
                        <th id="jRamal" class="jindice" title="Clique para indexar por esta coluna">Telefone</th>
                        <th id="jCel" class="jindice" title="Clique para indexar por esta coluna">Celular</th>
                        <th id="jSetor" class="jindice" title="Clique para indexar por esta coluna">Setor</th>
				        <th id="jContato" class="jindice" title="Clique para indexar por esta coluna">Nome Contato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($tbl = pg_fetch_row($rs0)){
                        $Cod = $tbl[0];
                    ?>
	                <tr>
                        <td style="display:none;">-</td> <!--Para não indexar pelo campo seguinte, que pode aparecer ou não, conforme a OM do usuário  -->
                        <td style="display:none;"><?php echo $tbl[0]; ?></td>
                        <td title="Clique aqui para editar"><?php echo $tbl[1]; ?> </td>
                        <td title="Clique aqui para editar"><?php echo $tbl[2]; ?> </td>
                        <td style="text-align: center;" title="Clique aqui para editar"> <?php echo $tbl[6]; ?> </td>
                        <td style="text-align: center;" title="Clique aqui para editar"> <?php echo $tbl[7]; ?> </td>
                        <td style="text-align: center;" title="Clique aqui para editar"> <?php echo $tbl[5]; ?> </td>
				        <td style="text-align: center;" title="Clique aqui para editar"> <?php echo $tbl[3]; ?> </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

       <!-- div modal para edição  -->
       <div id="relacmodal" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Telef">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Edição de Ramal Telefônico</h5>
                <table style="margin: 0 auto;">
                    <tr>
                        <td id="etiqNome" class="etiq">Sigla/Nome</td>
                        <td><input type="text" id="SiglaEmpresa" style="width: 99%;" placeholder="Sigla/Nome" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('NomeEmpresa');return false;}"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td id="etiqNomeCompl" class="etiq">Nome Compl</td>
                        <td colspan="3"><input type="text" id="NomeEmpresa" style="width: 99%;" placeholder="Nome" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('TelefoneFixo');return false;}"></td>
                    </tr>
                    <tr>
                        <td id="etiqRamal" class="etiq">Telefone</td>
                        <td><input type="tel" id="TelefoneFixo" style="width: 99%;" placeholder="Telefone" onkeyup="handlePhone(event);" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('TelefoneCel');return false;}"></td>
                        <td id="etiqCelular" class="etiq">Celular</td>
                        <td><input type="tel" id="TelefoneCel" style="width: 99%;" placeholder="Celular" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('Setor');return false;}"></td>
                    </tr>
                    <tr>
                        <td id="etiqSetor" class="etiq">Setor</td>
                        <td><input type="text" id="Setor" name="Setor" style="width: 99%;" placeholder="Setor" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('ContatoNome');return false;}"></td>
                        <td id="etiqContato" class="etiq">Contato</td>
                        <td><input type="text" id="ContatoNome" style="width: 99%;" placeholder="Nome" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('SiglaEmpresa');return false;}"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                    </tr>                    
                    <tr>
                        <td class="etiq" style="text-align: left;"><input type="button" class="resetbotred" id="botapagar" value="Apagar" onclick="deletaModal();"></td>
                        <td colspan="3" style="text-align: right; padding-right: 50px;"><input type="button" class="resetbotazul" id="botsalvar" value="Salvar" onclick="salvaModal();"></td>
                    </tr>
                </table>
           </div>
        </div>
    </body>
</html>