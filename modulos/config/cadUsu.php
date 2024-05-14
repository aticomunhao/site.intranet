<?php
session_start();
require_once("abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/superfish/js/jquery.js"></script><!-- versão 1.12.1 veio com o superfish - tem que usar esta, a versão 3.6 não recarrega a página-->
        <script src="class/dataTable/datatables.min.js"></script>
        <style>
            .modal-content-Usu{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
        </style>
        <script>
            new DataTable('#idTabelaUsu', {
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

            tableUsu = new DataTable('#idTabelaUsu');
            tableUsu.on('click', 'tbody tr', function () {
                let data = tableUsu.row(this).data();
                $id = data[2];//
                document.getElementById("guardaid_click").value = $id;
                $Cpf = data[1];//
                document.getElementById("guardaid_cpf").value = $Cpf;
                if($id !== ""){
                    if(parseInt(document.getElementById("UsuAdm").value) < 7){  // superusuário
                        if(parseInt(document.getElementById("UsuAdm").value) > 3 && parseInt(document.getElementById("admEditUsu").value) === 1){ // adminisetrador 
                            if(document.getElementById("guardaSiglaSetor").value === data[4]){ // sigla do usuário = sigla do administrador logado
                                document.getElementById("setor").disabled = true; // congela a escolha do setor
                                carregaModal($id);
                            }else{
                                document.getElementById("textoMsg").innerHTML = "Não pertence ao setor.";
                                document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                setTimeout(function(){
                                    document.getElementById("relacmensagem").style.display = "none";
                                }, 2000);
                            }
                        }
                    }else{
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
                document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir usuário
                if(parseInt(document.getElementById("UsuAdm").value) === 7){ // superusuário 
                    document.getElementById("botinserir").style.visibility = "visible";
                }
                if(parseInt(document.getElementById("UsuAdm").value) === 4){ // administador
                    if(parseInt(document.getElementById("admCadUsu").value) === 1){ // administardor cadastra usuário do seu setor 
                        document.getElementById("botinserir").style.visibility = "visible";
                    }
                }

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
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("usulogin").value = format_CnpjCpf(Resp.usuario);
                                    document.getElementById("usuarioNome").value = Resp.usuarioNome;
                                    document.getElementById("nomecompl").value = Resp.nomecompl;
                                    document.getElementById("diaAniv").value = Resp.diaAniv;
                                    document.getElementById("mesAniv").value = Resp.mesAniv;
                                    document.getElementById("ultlog").value = Resp.ultlog;
                                    document.getElementById("acessos").value = Resp.acessos;
                                    document.getElementById("flAdm").value = Resp.usuarioAdm;
                                    document.getElementById("setor").value = Resp.setor;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    if(parseInt(Resp.lroPortaria) === 1){
                                        document.getElementById("preencheLro").checked = true;
                                    }else{
                                        document.getElementById("preencheLro").checked = false;
                                    }
                                    if(parseInt(Resp.bens) === 1){
                                        document.getElementById("preencheBens").checked = true;
                                    }else{
                                        document.getElementById("preencheBens").checked = false;
                                    }
                                    document.getElementById("titulomodal").innerHTML = "Edição de Usuários";
                                    document.getElementById("ressetsenha").disabled = false;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalUsu").style.display = "block";
                                    document.getElementById("usulogin").disabled = true;
//                                    document.getElementById("usulogin").focus();
                                }else{
                                    alert("Houve um erro no servidor.")
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
                if(document.getElementById("usuarioNome").value === ""){
                    return false;
                }
                if(document.getElementById("setor").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Setor de Trabalho/Diretoria/Assessoria</u>";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("flAdm").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Nível Administrativo</u> do usuário";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                Lro = 0;
                if(document.getElementById("preencheLro").checked === true){
                    Lro = 1;
                }
                Bens = 0;
                if(document.getElementById("preencheBens").checked === true){
                    Bens = 1;
                }
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaUsu&numero="+document.getElementById("guardaid_click").value
                        +"&cpf="+encodeURIComponent(document.getElementById("usulogin").value)
                        +"&guardaidpessoa="+document.getElementById("guardaidpessoa").value
                        +"&usulogado="+document.getElementById("guarda_usulogado_id").value
                        +"&ativo="+document.getElementById("guardaAtiv").value
                        +"&setor="+document.getElementById("setor").value
                        +"&flAdm="+document.getElementById("flAdm").value
                        +"&lro="+Lro
                        +"&bens="+Bens, true);
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
                                    $('#container3').load('modulos/config/cadUsu.php');
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
                                            document.getElementById("usuarioNome").value = Resp.nomecompl;
                                            document.getElementById("nomecompl").value = Resp.nomecompl;
                                            document.getElementById("diaAniv").value = Resp.dianasc;
                                            document.getElementById("mesAniv").value = Resp.mesnasc;
                                            if(parseInt(Resp.ativo) === 1){
                                                document.getElementById("atividade1").checked = true;
                                            }else{
                                                document.getElementById("atividade2").checked = true;
                                            }
                                            document.getElementById("ultlog").value = Resp.ultlog;
                                            document.getElementById("acessos").value = Resp.acessos;
                                            document.getElementById("flAdm").value = Resp.adm;
                                            document.getElementById("setor").value = Resp.setor;
                                            document.getElementById("guardaidpessoa").value = Resp.idpessoa;
//                                            document.getElementById("guardaid_click").value = Resp.idpessoa; // para salvar modif se for procurado por inserção ao invés de click
                                            document.getElementById("usulogin").disabled = true;
                                            if(parseInt(Resp.jatem) === 1){
                                                document.getElementById("guardaid_click").value = Resp.idpessoa; // para salvar modif se for procurado por inserção ao invés de click
                                                $('#mensagem').fadeIn("slow");
                                                document.getElementById("mensagem").innerHTML = "Usuário já cadastrado no site.";
                                                $('#mensagem').fadeOut(10000);
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
                                    document.getElementById("diaAniv").value = Resp.dianasc;
                                    document.getElementById("mesAniv").value = Resp.mesnasc;
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
                if(parseInt(document.getElementById("UsuAdm").value) < 7){ 
                    if(parseInt(document.getElementById("admCadUsu").value) === 0){ // administrador não cadastra
                        return false;
                    }
                }
                if(document.getElementById("guardaSiglaSetor").value === "n/d"){
                    document.getElementById("textoMsg").innerHTML = "Administrador sem setor definido.";
                    document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                    setTimeout(function(){
                        document.getElementById("relacmensagem").style.display = "none";
                    }, 2000);
                    return false;
                }
                document.getElementById("usulogin").disabled = false;
                document.getElementById("usulogin").value = "";
                document.getElementById("usuarioNome").value = "";
                document.getElementById("nomecompl").value = "";
                document.getElementById("setor").value = "";
                document.getElementById("flAdm").value = "";
                document.getElementById("diaAniv").value = "";
                document.getElementById("mesAniv").value = "";
                document.getElementById("acessos").value = "-";
                document.getElementById("ultlog").value = "-";
                if(parseInt(document.getElementById("UsuAdm").value) < 7){
                    document.getElementById("setor").disabled = true;
                    document.getElementById("flAdm").value = 2; // usuário registrado
                    document.getElementById("flAdm").disabled = true;
                }
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
//    alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodalUsu").style.display = "none";
                                        $('#container3').load('modulos/config/cadUsu.php');
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
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
                let Conf = confirm("A senha deste usuário será modificada para o CPF. Prossegue?");
                if(Conf){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=resetsenha&numero="+document.getElementById("guardaid_cpf").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//    alert(ajax.responseText);
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
                }
            }

            function mudaSetor(){ // Qdo muda de setor desmarca 
                document.getElementById("mudou").value = "1";
                document.getElementById("preencheLro").checked = false;
                document.getElementById("preencheBens").checked = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=checaBoxes&param="+Valor+"&numero="+document.getElementById("guardaid_cpf").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 


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
        </script>
    </head>
    <body>
        <?php
            function formatCnpjCpf($value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                $CPF_LENGTH = 11;
                $cnpj_cpf = preg_replace("/\D/", '', $value);
                    if (strlen($cnpj_cpf) === $CPF_LENGTH) {
                        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
                } 
                return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
            }
        if(isset($_REQUEST["tipo"])){
            $Tipo = $_REQUEST["tipo"];
        }else{
            $Tipo = 1;
        }
        require_once("modais.php");
//dblink
//        $rs0 = pg_query($Conec, "SELECT * FROM dblink('host=127.0.0.1  user=postgres  password=postgres   dbname=pessoal ', 
//                'SELECT pessoas.id, cpf, nome_completo, ".$xProj.".poslog.ativo, ".$xProj.".poslog.logini, ".$xProj.".poslog.codsetor 
//                FROM pessoas INNER JOIN cesb.poslog ON pessoas.id = cesb.poslog.pessoas_id  ') 
//                t (id int, cpf text, nome_completo text, ativo text, logini text, codsetor int);" );

        $rs0 = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.id, ".$xPes.".pessoas.cpf, ".$xPes.".pessoas.nome_completo 
        FROM ".$xPes.".pessoas  
        WHERE pessoas.id != 0 And nome_completo IS NOT NULL ORDER BY nome_completo");
        $row0 = pg_num_rows($rs0);  // , ".$xPes.".pessoas.usuario 

        //Para carregar os select de dia e mês
        $OpcoesMes = pg_query($Conec, "SELECT Esc1 FROM ".$xProj.".escolhas WHERE CodEsc < 14 ORDER BY Esc1");
        $OpcoesDia = pg_query($Conec, "SELECT Esc1 FROM ".$xProj.".escolhas ORDER BY Esc1");

        if($_SESSION["AdmUsu"] == 7){
            $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 Or adm_fl = 7 ORDER BY adm_fl");
        }else{
            $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
        }
        $OpcoesSetor = pg_query($Conec, "SELECT CodSet, SiglaSetor FROM ".$xProj.".setores ORDER BY SiglaSetor");
        ?>

        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaSiglaSetor" value="<?php echo addslashes($_SESSION["SiglaSetor"]) ?>" />
        <input type="hidden" id="guardaCodSetor" value="<?php echo addslashes($_SESSION["CodSetorUsu"]) ?>" />
        <input type="hidden" id="guardaid_click" value="0" />
        <input type="hidden" id="guardaid_cpf" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guarda_usulogado_id" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="admCadUsu" value="<?php echo $_SESSION["AdmCad"]; ?>" />
        <input type="hidden" id="admEditUsu" value="<?php echo $_SESSION["AdmEdit"]; ?>" />
        <input type="hidden" id="guardaidpessoa" value="0" />
        <input type="hidden" id="guardaAtiv" value="1" />
        <input type="hidden" id="guardaLro" value="0" />
        <input type="hidden" id="guardaBens" value="0" />
        
        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" value="Inserir Novo" onclick="insUsu();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Usuários Cadastrados</h3>
            </div>
            <table id="idTabelaUsu" class="display" style="width:85%">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th>Login</th>
                        <th style="display: none;"></th>
                        <th>Nome Usual</th>
                        <th>Nome Completo</th>
                        <th style="text-align: center;">Setor</th>
                        <th style="text-align: center;">Último Login</th>
                        <th style="text-align: center;">Ativo</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0]; // id
                        $Cpf = $tbl0[1];
                        $rs1 = pg_query($Conec, "SELECT ".$xProj.".poslog.ativo, to_char(".$xProj.".poslog.logini, 'DD/MM/YYYY HH24:MI'), codsetor 
                        FROM ".$xProj.".poslog  
                        WHERE ".$xProj.".poslog.cpf = '$Cpf' ");   //  WHERE ".$xProj.".poslog.pessoas_id = $Cod");
                        $row1 = pg_num_rows($rs1);

                        // se constar da tabela poslog
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $Ativ = $tbl1[0]; // ativo
                            $DataLog = $tbl1[1];
                            $CodSetor = $tbl1[2];
                            $rs2 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $CodSetor");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescSetor = $tbl2[0];
                            }else{
                                $DescSetor = "n/d";
                            }
                            if($Ativ == 1){
                                $DescAtiv = "Ativo";
                            }else{
                                $DescAtiv = "Inativo";
                            }
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td><?php echo formatCnpjCpf($tbl0[1]); ?></td> <!-- cpf -->
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td><?php echo $tbl0[2]; ?></td> <!-- seria 6 - usuario -->
                            <td><?php echo $tbl0[2]; ?></td> <!-- nome completo -->
                            <td style="text-align: center;"><?php echo $DescSetor; ?></td> <!-- siglasetor -->
                            <td style="text-align: center;"><?php echo $DataLog; ?></td>  <!-- ultimologin formatado -->
                            <td style="text-align: center;"><?php echo $DescAtiv; ?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- div modal para edição  -->
        <div id="relacmodalUsu" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Usu">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição de Usuários</h3>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td id="etiqNomelog" class="etiq80">Login:</td>
                        <td><input type="text" disabled id="usulogin" style="text-align: center;" placeholder="Login" onchange="checaEntrada();" onkeypress="if(event.keyCode===13){javascript:foco('salvar');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq80">Último Acesso: </label>
                            <input type="text" disabled id="ultlog" style="text-align: center; font-size: .8rem;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNome" class="etiq80">Nome Usual</td>
                        <td><input type="text" disabled id="usuarioNome" placeholder="Nome usual" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('nomecompl');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq80">Nº acessos: </label>
                            <input type="text" disabled id="acessos" style="text-align: center; font-size: .8rem; width: 100px;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNomeCompl" class="etiq80">Nome Completo</td>
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
                        <td class="etiq80">Setor de Trabalho</td>
                        <td>
                            <select id="setor" style="font-size: 1rem;" title="Selecione um local de trabalho." onchange="mudaSetor();">
                            <?php 
                            if($OpcoesSetor){
                                while ($Opcoes = pg_fetch_row($OpcoesSetor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;"><button disabled id="ressetsenha" class="resetbot" style="font-size: .9rem;" onclick="resetSenha();">Ressetar Senha</button></td>
                    </tr>
                    <tr>
                        <td class="etiq80">Nível Administrativo</td>
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
                        </td>
                        <td></td>
                        <td></td>

                        <td colspan="2" style="text-align: right;">
                            <label class="etiq80">Aniversário: -Dia: </label>
                            <input type="text" disabled id="diaAniv" style="text-align: center; font-size: .8rem; width: 25px;">
                            <label class="etiq80"> -Mês: </label>
                            <input type="text" disabled id="mesAniv" style="text-align: center; font-size: .8rem; width: 25px;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><hr style="margin: 3px; padding: 2px;"></td>
                    </tr>  
                    <tr>
                        <td class="etiq80" title="Pode registrar ocorrências no LRO">Escala Portaria:</td>
                        <td colspan="5">
                            <input type="checkbox" id="preencheLro" title="Registrar ocorrências no LRO" onchange="modif();" >
                            <label for="preencheLro" title="Registrar ocorrências no LRO">acesso ao Livro de Registro de Ocorrências</label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiq80" title="Pode registrar ocorrências no LRO">Bens Achados:</td>
                        <td colspan="5">
                            <input type="checkbox" id="preencheBens" title="Registrar recebimento e destino de bens encontrados" onchange="modif();" >
                            <label for="preencheBens" title="Registrar recebimento e destino de bens encontrados">acesso ao registro de Bens Encontrados</label>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6"><hr style="margin: 3px; padding: 2px;"></td>
                    </tr>  
                    <tr>
                        <td colspan="6" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                    </tr>   
                    <tr>
                        <td class="etiq80" style="color: red; text-align: left;"></td> 
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