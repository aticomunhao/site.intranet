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
        <script src="class/superfish/js/jquery.js"></script><!-- versão 1.12.1 veio com o superfish - tem que usar esta, a versão 3.6 não recarrega a página-->
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <script>
            // config DataTable
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
                    [100, 200, 500],
                    [100, 200, 500]
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
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admInsEdit").value)){ // nível adm
                        carregaModal($id);
                    }
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
                if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admInsEdit").value)){
                    document.getElementById("botapagar").style.visibility = "hidden"; // botão para apagar
                    document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir
                }
                modalEdit = document.getElementById('relacmodal'); //span[0]
                spanEdit = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalEdit){
                        modalEdit.style.display = "none";
                    }
                };
                $("#ramal").mask("(99) 9 9999-9999");
                $("#ramal2").mask("(99) 9 9999-9999");
            });

            function carregaModal(id){
                document.getElementById("codnomecompl").disabled = true;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/salvaRamais.php?acao=buscaRamal&tipo="+document.getElementById("tipo_acesso").value+"&numero="+id, true); // tipo 3 = celulares corporativos
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("usuario").value = Resp.usuario;
                                document.getElementById("codnomecompl").value = Resp.idposlog;
                                document.getElementById("nomecompleto").value = Resp.nomecompleto;
                                document.getElementById("setor").value = Resp.setor;
                                document.getElementById("ramal").value = Resp.ramal;
                                document.getElementById("ramal2").value = Resp.ramal2;
                                document.getElementById("titulomodal").innerHTML = "Edição de Celular Corporativo";
                                document.getElementById("botapagar").style.visibility = "visible";
                                document.getElementById("mudou").value = "0";
                                if(parseInt(Resp.sit_ativo) === 0){ // estava deletado
                                    document.getElementById("mudou").value = "1"; // para salvar sem haver mudanças
                                }
                                document.getElementById("relacmodal").style.display = "block";
                                document.getElementById("usuario").focus();
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModal(){
                if(document.getElementById("usuario").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo Nome Usual";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("nomecompleto").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo Nome Completo";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("ramal").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo número do telefone";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                //Se houve alguma modificação
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/salvaRamais.php?acao=salvaRamal&tipo="+document.getElementById("tipo_acesso").value+"&numero="+document.getElementById("guardaid_click").value
                        +"&usuario="+encodeURIComponent(document.getElementById("usuario").value)
                        +"&codnomecompl="+document.getElementById("codnomecompl").value
                        +"&codsetor="+document.getElementById("guardaCodSetor").value
                        +"&setor="+document.getElementById("setor").value
                        +"&nomecompleto="+encodeURIComponent(document.getElementById("nomecompleto").value)
                        +"&ramal="+encodeURIComponent(document.getElementById("ramal").value)
                        +"&ramal2="+encodeURIComponent(document.getElementById("ramal2").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 2){
                                        $('#mensagem').fadeIn("slow");
                                        document.getElementById("mensagem").innerHTML = "Esse nome já existe.";
                                        $('#mensagem').fadeOut("slow");
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodal").style.display = "none";
                                        $('#container3').load('modulos/ramaisCel.php?tipo='+document.getElementById("tipo_acesso").value);
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
                document.getElementById("codnomecompl").disabled = false;
                document.getElementById("codnomecompl").value = "0";
                document.getElementById("usuario").value = "";
                document.getElementById("nomecompleto").value = "";
                document.getElementById("setor").value = "";
                document.getElementById("ramal").value = "";
                document.getElementById("ramal2").value = "";
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("botapagar").style.visibility = "hidden";
                document.getElementById("titulomodal").innerHTML = "Inserção de Celular Corporativo";
                document.getElementById("relacmodal").style.display = "block";
            }

            function buscaNome(Arq){
                document.getElementById("guardaid_click").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/salvaRamais.php?acao=buscaNome&tipo="+document.getElementById("tipo_acesso").value+"&arquivo="+Arq+"&numero="+document.getElementById("codnomecompl").value, true); // tipo 3 = celulares corporativos
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("usuario").value = Resp.nomeusual;
                                document.getElementById("ramal").value = Resp.ramal;
                                document.getElementById("ramal2").value = Resp.ramal2;
                                document.getElementById("nomecompleto").value = Resp.nomecompleto;
                                document.getElementById("setor").value = Resp.siglasetor;
                                document.getElementById("guardaCodSetor").value = Resp.codsetor;
                                document.getElementById("guardaid_click").value = Resp.codtel;
                                if(parseInt(Resp.sit_ativo) === 0){ // estava deletado
                                    document.getElementById("mudou").value = "1"; // para salvar sem haver mudanças
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function buscaSetor(){
                document.getElementById("mudou").value = "1";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/salvaRamais.php?acao=buscaDescSetor&tipo=1&numero="+document.getElementById("codsetor").value, true);  // tipo 1 serve
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("setor").value = Resp.descsetor;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
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
                                ajax.open("POST", "modulos/salvaRamais.php?acao=deletaRamal&tipo="+document.getElementById("tipo_acesso").value+"&numero="+document.getElementById("guardaid_click").value, true);
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
                                                $('#container3').load('modulos/ramaisCel.php?tipo='+document.getElementById("tipo_acesso").value);
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
        $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'ramais_cel'");
        $rowSis = pg_num_rows($rsSis);
        if($rowSis == 0){
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".ramais_cel (
                codtel SERIAL PRIMARY KEY, 
                poslog_id bigint NOT NULL DEFAULT 0,
                nomeusu character varying(50),
                nomecompl character varying(100),
                codsetor integer NOT NULL DEFAULT 0,
                setor character varying(20),
                ramal character varying(20),
                ramal2 character varying(20),
                ativo smallint NOT NULL DEFAULT 1, 
                usuins integer NOT NULL DEFAULT 0,
                datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                usumodif integer NOT NULL DEFAULT 0,
                datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
                usuinat integer NOT NULL DEFAULT 0,
                datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP
                ) 
            ");
        }
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $admInsEdit = parAdm("inseditcelcorp", $Conec, $xProj);   // nível para inserir/editar

        $BuscaNomes = 0;
        $OpNomes = pg_query($Conec, "SELECT id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $BuscaNomes = 1;    

        $OpSetor = pg_query($ConecPes, "SELECT id, sigla FROM ".$xPes.".setor WHERE dt_fim IS NULL ORDER BY sigla");
        if(!isset($_SESSION["AdmUsu"])){
            $_SESSION["AdmUsu"] = 0;
        }
        $rs0 = pg_query($Conec, "SELECT codtel, nomeusu, nomecompl, ramal, setor, ramal2 FROM ".$xProj.".ramais_cel WHERE ativo = 1 ORDER BY nomeusu, nomecompl");
        $row0 = pg_num_rows($rs0);
        ?>
        <input type="hidden" id="tipo_acesso" value="<?php echo $Tipo; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="admInsEdit" value="<?php echo $admInsEdit; ?>" /> <!-- nível mínimo para inserir/editar -->
        <input type="hidden" id="guardaid_click" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guardaCodSetor" value="0" />

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" value="Inserir Novo" onclick="InsRamais();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Telefones Celulares Corporativos</h3>
            </div>

            <table id="idTabela" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th>Nome Usual</th>
                        <th style="display: none;"></th>
                        <th>Nome</th>
                        <th style="text-align: center;">Telefone</th>
                        <th style="text-align: center;">Setor</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($tbl = pg_fetch_row($rs0)){
                        $Cod = $tbl[0]; // CodTel
                        if(is_null($tbl[4]) || $tbl[4] == "undefined"){
                            $DescSetor = "";
                        }else{
                            $DescSetor = $tbl[4];
                        }
                    ?>
                        <tr>
                            <td><?php echo $tbl[1]; ?></td> <!-- nomeusu -->
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td><?php echo $tbl[2]; ?></td> <!-- nomecompl -->
                            <td style="text-align: center;"><?php echo $tbl[3]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tbl[5]; ?></td> <!-- ramal -->
                            <td style="text-align: center;"><?php echo $DescSetor; ?></td> <!-- setor -->
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- div modal para edição  -->
        <div id="relacmodal" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Celulares">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição de Celular Corporativo</h3>
                <table style="margin: 0 auto;">
                    <tr>
                        <td id="etiqNome" class="etiqAzul">Nome Usual</td>
                        <td><input type="text" id="usuario" name="usuario" style="width: 50%; border-radius: 7px; padding-left: 3px;" placeholder="Nome usual" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('setor');return false;}"></td>
                    </tr>
                    <tr>
                        <td id="etiqNomeCompl" class="etiqAzul">Nome Completo:
                            <select id="codnomecompl" onchange="buscaNome(<?php echo $BuscaNomes; ?>);" style="font-size: 1rem; width: 22px;" title="Selecione um usuário.">
                                <option value="0"></option>
                                <?php 
                                if($OpNomes){
                                    while ($Opcoes = pg_fetch_row($OpNomes)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="nomecompleto" style="width: 90%; border-radius: 7px; padding-left: 3px;" placeholder="Nome completo ou nome do setor" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('ramal');return false;}">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqSetor" class="etiqAzul">Setor:
                            <select id="codsetor" onchange="buscaSetor();" style="font-size: 1rem; width: 22px;" title="Selecione um setor.">
                                <option value="0"></option>
                                <?php 
                                if($OpSetor){
                                    while ($Opcoes = pg_fetch_row($OpSetor)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="setor" style="width: 50%; border-radius: 7px; padding-left: 3px;" placeholder="Setor" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('ramal');return false;}">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqRamal" class="etiqAzul">Celular1:</td>
                        <td>
                            <input type="text" id="ramal" style="width: 40%; border: 1px solid; border-radius: 7px; text-align: center;" placeholder="Celular" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('usuario');return false;}">
                            <label class="etiqAzul">&nbsp;Celular2: </label>
                            <input type="text" id="ramal2" style="width: 40%; border: 1px solid; border-radius: 7px; text-align: center;" placeholder="Celular" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('usuario');return false;}">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                    <tr>
                        <td style="text-align: left;"><input type="button" class="resetbotred" style="font-size: 70%;" id="botapagar" value="Apagar" onclick="deletaModal();"></td>
                        <td style="text-align: right; padding-right: 20px; width: 400px;"><input type="button" class="resetbotazul" id="salvar" value="Salvar" onclick="salvaModal();"></td>
                    </tr>
                </table>
           </div>
        </div>
    </body>
</html>