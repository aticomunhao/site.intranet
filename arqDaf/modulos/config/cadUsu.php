<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start();
require_once("abrealas.php");
if(!isset($_SESSION["usuarioCPF"])){
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="../class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="../class/superfish/js/jquery.js"></script>  <!-- versão 1.12.1 veio com o superfish - tem que usar esta, a versão 3.6 não recarrega a página-->
        <script src="../class/dataTable/datatables.min.js"></script>
        <script src="../comp/js/jquery-confirm.min.js"></script> 
        <style>
            .modal-content-Usu{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 80%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-Chaves{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
                max-width: 900px;
            }
        </style>
        <script>
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
                document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir usuário
                if(parseInt(document.getElementById("UsuAdm").value) > 1){ // administrador 
                    document.getElementById("botinserir").style.visibility = "visible";
                }
                $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");

                modalEdit = document.getElementById('relacmodalUsu'); //span[0]
                spanEdit = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalEdit){
                        modalEdit.style.display = "none";
                    }
                };
            });
            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }

            function carregaModal(id){
                document.getElementById("salvar").disabled = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscausu&numero="+id+"&cpf="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 2){
                                    alert("CPF não encontrado.");
                                }
                                if(parseInt(Resp.coderro) === 0){
                                    if(Resp.usuario == null){
                                        alert("Sem contato com a tabela pessoas. CPF nulo")
                                    }
                                    if(Resp.usuarioNome == null){
                                        alert("Sem contato com a tabela pessoas. Usuário nulo")
                                    }
                                    document.getElementById("usulogin").value = format_CnpjCpf(Resp.usuario);
                                    document.getElementById("usuarioNome").value = Resp.usuarioNome;
                                    document.getElementById("nomecompl").value = Resp.nomecompl;
                                    document.getElementById("ultlog").value = Resp.ultlog;
                                    document.getElementById("acessos").value = Resp.acessos;
                                    document.getElementById("flAdm").value = Resp.usuarioAdm;
                                    document.getElementById("setor").value = Resp.setor;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    document.getElementById("titulomodal").innerHTML = "Edição de Usuários";
                                    document.getElementById("ressetsenha").disabled = false;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalUsu").style.display = "block";
                                    document.getElementById("usulogin").disabled = true;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    if(Resp.usuario == null){
                                        alert("Sem contato com a tabela pessoas. CPF nulo")
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModal(){
                if(document.getElementById("usulogin").value === ""){
                    return false;
                }
                if(document.getElementById("flAdm").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Nível Administrativo</u> do usuário";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("setor").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Diretório</u> do usuário";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaUsu&numero="+document.getElementById("guardaid_click").value
                        +"&cpf="+encodeURIComponent(document.getElementById("usulogin").value)
                        +"&guardaidpessoa="+document.getElementById("guardaidpessoa").value
                        +"&usulogado="+document.getElementById("guarda_usulogado_id").value
                        +"&usuarioNome="+encodeURIComponent(document.getElementById("usuarioNome").value)
                        +"&ativo="+document.getElementById("guardaAtiv").value
                        +"&setor="+document.getElementById("setor").value
                        +"&flAdm="+document.getElementById("flAdm").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 2){
                                        $('#mensagem').fadeIn("slow");
                                        document.getElementById("mensagem").innerHTML = "Esse nome <u>JÁ EXISTE</u>.";
                                        $('#mensagem').fadeOut("slow");
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("guardaid_click").value = 0;
                                        document.getElementById("relacmodalUsu").style.display = "none";
                                    }
                                    $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("mudou").value = "0";
                    document.getElementById("relacmodalUsu").style.display = "none";
                }
            }

            function checaEntrada(){
                checaLogin();
                if(validaCPF(document.getElementById("usulogin").value)){
                    checaLogin();
                }else{
                    document.getElementById("relacmodalUsu").style.display = "none";
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "CPF inválido.";
                    $('#mensagem').fadeOut(3000);
                    insUsu();
                }
            }
            function checaLogin(){
                document.getElementById("guardaid_cpf").value = 0;
                document.getElementById("salvar").disabled = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=checaLogin&valor="+encodeURIComponent(document.getElementById("usulogin").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    if(parseInt(Resp.quantiUsu) > 0){
                                        if(parseInt(Resp.quantiUsu) > 1){
                                            $('#mensagem').fadeIn("slow");
                                            document.getElementById("mensagem").innerHTML = "Encontrados "+Resp.quantiUsu+" registros desse CPF. Verifique seus arquivos.";
                                            $('#mensagem').fadeOut(13000);
                                            document.getElementById("usulogin").value = "";
                                        }else{
                                            document.getElementById("usulogin").value = format_CnpjCpf(Resp.cpf);
                                            document.getElementById("usuarioNome").value = Resp.nomeusual;
                                            document.getElementById("nomecompl").value = Resp.nomecompl;
                                            if(parseInt(Resp.ativo) === 1){
                                                document.getElementById("atividade1").checked = true;
                                            }else{
                                                document.getElementById("atividade2").checked = true;
                                            }
                                            document.getElementById("flAdm").value = Resp.adm;
                                            document.getElementById("ultlog").value = Resp.ultlog;
                                            document.getElementById("acessos").value = Resp.acessos;
                                            document.getElementById("setor").value = Resp.setor;
                                            document.getElementById("guardaidpessoa").value = Resp.idpessoa;
                                            document.getElementById("usulogin").disabled = true;
                                            if(parseInt(Resp.jatem) === 1){
                                                document.getElementById("guardaid_click").value = Resp.idpessoa; // para salvar modif se for procurado por inserção ao invés de click
                                                if(parseInt(Resp.adm < 2)){ // superusuário invisível
                                                    $('#mensagemCima').fadeIn("slow");
                                                    document.getElementById("mensagemCima").innerHTML = "Usuário já cadastrado no site.";
                                                    $('#mensagemCima').fadeOut(5000);
                                                }
                                            }
                                        }
                                    }
                                }
                                if(parseInt(Resp.coderro) === 2){
                                    $('#mensagem').fadeIn("slow");
                                    document.getElementById("mensagem").innerHTML = "CPF ("+format_CnpjCpf(document.getElementById("usulogin").value)+") não encontrado na base de dados.";
                                    $('#mensagem').fadeOut(10000);
                                    document.getElementById("usulogin").value = "";
                                }
                                if(parseInt(Resp.coderro) === 3){
                                    document.getElementById("usulogin").value = format_CnpjCpf(Resp.cpf);
                                    document.getElementById("usuarioNome").value = Resp.nomecompl;
                                    document.getElementById("nomecompl").value = Resp.nomecompl;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    document.getElementById("ultlog").value = Resp.ultlog;
                                    document.getElementById("acessos").value = Resp.acessos;
                                    document.getElementById("flAdm").value = Resp.adm;
                                    document.getElementById("setor").value = Resp.setor;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insUsu(){
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("usulogin").disabled = false;
                document.getElementById("usulogin").value = "";
                document.getElementById("usuarioNome").value = "";
                document.getElementById("nomecompl").value = "";
                document.getElementById("setor").value = "";
                document.getElementById("acessos").value = "-";
                document.getElementById("ultlog").value = "-";
                document.getElementById("flAdm").value = "1";
                document.getElementById("atividade1").checked = true;
                document.getElementById("titulomodal").innerHTML = "Inserção de Usuário";
                document.getElementById("ressetsenha").disabled = true;
                document.getElementById("relacmodalUsu").style.display = "block";
                document.getElementById("usulogin").focus();
            }

            function salvaAtiv(Valor){
                document.getElementById("guardaAtiv").value = Valor;
                document.getElementById("mudou").value = "1";
            }

            function deletaModal(){
                let Conf = confirm("Não haverá possibilidade de recuperação.\nConfirma deletar os dados deste usuário?");
                if(Conf){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=deletausu&numero="+document.getElementById("guardaid_click").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodalUsu").style.display = "none";
                                        $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
            }

            function mostraBotChave(Obj, Cod){
                document.getElementById("mudou").value = "1";
                if(Obj.checked === true){
                    document.getElementById("botaoChaves").style.visibility = "visible";
                }else{
                    document.getElementById("botaoChaves").style.visibility = "hidden";
                }
            }

            function fechaModal(){
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("relacmodalUsu").style.display = "none";
            }

            function foco(id){
                document.getElementById(id).focus();
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }

            function resetSenha(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'A senha deste usuário será modificada para o CPF. Prossegue?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=resetsenha&numero="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("textoMsg").innerHTML = "Senha modificada para o CPF";
                                                document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                                setTimeout(function(){
                                                    document.getElementById("relacmensagem").style.display = "none";
                                                }, 2000);
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {}
                    }
                });
            }

            function resumoUsu(){
                window.open("modulos/config/imprUsu.php?acao=listaUsuarios", "ListaUsu");
            }

            function validaCPF(cpf) {
                var Soma = 0
                var Resto
                var strCPF = String(cpf).replace(/[^\d]/g, '')
                if (strCPF.length !== 11)
                    return false
                if ([
                    '00000000000',
                    '11111111111',
                    '22222222222',
                    '33333333333',
                    '44444444444',
                    '55555555555',
                    '66666666666',
                    '77777777777',
                    '88888888888',
                    '99999999999',
                ].indexOf(strCPF) !== -1)
                return false
                for (i=1; i<=9; i++)
                    Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
                    Resto = (Soma * 10) % 11
                    if ((Resto == 10) || (Resto == 11)) 
                        Resto = 0
                    if (Resto != parseInt(strCPF.substring(9, 10)) )
                    return false
                    Soma = 0
                    for (i = 1; i <= 10; i++)
                        Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i)
                        Resto = (Soma * 10) % 11
                        if ((Resto == 10) || (Resto == 11)) 
                            Resto = 0
                        if (Resto != parseInt(strCPF.substring(10, 11) ) )
                            return false
                return true
            }

            function mostraUsu(Valor){
                $("#faixacentral").load("modulos/config/jUsu.php?acao="+Valor);
            }

        </script>
    </head>
    <body>
        <?php
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            if(isset($_REQUEST["tipo"])){
                $Tipo = $_REQUEST["tipo"];
            }else{
                $Tipo = 1;
            }
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'daf_poslog'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
                return false;
            }

            require_once("modais.php");
            //Para carregar os select de dia e mês
            $OpcoesMes = pg_query($Conec, "SELECT esc1 FROM ".$xProj.".escolhas WHERE codesc < 14 ORDER BY esc1");
            $OpcoesDia = pg_query($Conec, "SELECT esc1 FROM ".$xProj.".escolhas ORDER BY esc1");

            $AdmUsu = arqDafAdm("adm", $Conec, $xProj, $_SESSION["usuarioCPF"]);
            $usuarioID = arqDafAdm("pessoas_id", $Conec, $xProj, $_SESSION["usuarioCPF"]);

            if($AdmUsu == 3){ // superusuário
                $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".daf_usugrupos WHERE ativo = 1 ");
            }else{
                $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".daf_usugrupos WHERE ativo = 1 And adm_fl < 3 ORDER BY adm_fl");
            }
            $OpcoesSetor = pg_query($Conec, "SELECT CodSet, siglasetor FROM ".$xProj.".daf_setores WHERE codset > 1 ORDER BY siglasetor");

        ?>

        <input type="hidden" id="UsuAdm" value="<?php echo $AdmUsu ?>" />
        <input type="hidden" id="guardaSiglaSetor" value="<?php echo addslashes($_SESSION["SiglaSetor"]) ?>" />
        <input type="hidden" id="guardaid_click" value="0" />
        <input type="hidden" id="guardaid_cpf" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guarda_usulogado_id" value="<?php echo $usuarioID; ?>" />
        <input type="hidden" id="guardausu_cpf" value="<?php echo $_SESSION["usuarioCPF"]; ?>" />
        <input type="hidden" id="guardaidpessoa" value="0" />
        <input type="hidden" id="guardaAtiv" value="1" />
        <input type="hidden" id="guardaLro" value="0" />
        <input type="hidden" id="guardaBens" value="0" />

        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" value="Inserir Novo" onclick="insUsu();">
                <label style="padding-left: 20px;"></label>
<!--                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="resumoUsu();">PDF</button> -->
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Usuários Cadastrados</h3>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('todos');">Todos</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('online');">On Line</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('dehoje');">Usuários de Hoje</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('inativos');">Inativos</button>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: left;"></div>

            <div id="faixacentral"></div>

        </div>

        <!-- div modal para edição  -->
        <div id="relacmodalUsu" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Usu">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição de Usuários</h3>
                <div id="mensagemCima" style="text-align: center; color: red; font-weight: bold;"></div>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td id="etiqNomelog" class="etiq">Login:</td>
                        <td><input type="text" disabled id="usulogin" style="text-align: center;" placeholder="Login" onchange="checaEntrada();" ></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq">Último Acesso: </label>
                            <input type="text" disabled id="ultlog" style="text-align: center; font-size: .8rem;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNome" class="etiq">Nome Usual</td>
                        <td><input type="text" id="usuarioNome" placeholder="Nome usual" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('nomecompl');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq">Nº acessos: </label>
                            <input type="text" disabled id="acessos" style="text-align: center; font-size: .8rem; width: 100px;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNomeCompl" class="etiq">Nome Completo</td>
                        <td style="width: 50%;"><input type="text" disabled id="nomecompl" style="width: 100%;" placeholder="Nome completo" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('usulogin');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label style="font-size: 12px;" title="Ativo ou inativo">Situação: </label>
                            <input type="radio" name="atividade" id="atividade1" value="1" title="Ativo no sistema" onclick="salvaAtiv(value);"><label for="atividade1" style="font-size: 12px; padding-left: 3px;"> Ativo</label>
                            <input type="radio" name="atividade" id="atividade2" value="0" title="Bloqueado" onclick="salvaAtiv(value);"><label for="atividade2" style="font-size: 12px; padding-left: 3px;"> Bloqueado</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq">Diretório</td>
                        <td>
                            <select id="setor" style="font-size: 1rem;" title="Selecione um diretório de trabalho. Se for diferente de Todos, o usuário só verá os arquivos desse diretório" onchange="modif();">
                                <option value="1">Todos</option>
                                <?php 
                                if($OpcoesSetor){
                                    while ($Opcoes = pg_fetch_row($OpcoesSetor)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;"><button disabled id="ressetsenha" class="resetbot" style="font-size: .9rem;" onclick="resetSenha();">Reiniciar Senha</button></td> <!-- https://www.dicionarioinformal.com.br/ressetar/ -->
                    </tr>
                    <tr>
                        <td class="etiq">Nível Administrativo</td>
                        <td>
                            <select id="flAdm" style="font-size: 1rem;" title="Selecione o nível administrativo do usuário." onchange="modif();">
                            <?php 
                            if($OpcoesAdm){
                                while ($Opcoes = pg_fetch_row($OpcoesAdm)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td></td>
                        <td></td>

                        <td colspan="2" style="text-align: right;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><hr style="margin: 3px; padding: 2px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 95%">
                    <tr>
                        <td colspan="6" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="color: red; text-align: left;"></td> 
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right; padding-right: 30px;"><input type="button" class="resetbot" id="salvar" value="Salvar" onclick="salvaModal();"></td>
                    </tr>
                </table>
           </div>
        </div> <!-- Fim Modal-->

    </body>
</html>