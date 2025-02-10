<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"> 
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style TYPE="text/css">
            .etiq{
               text-align: right; color: #808080; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
            .etiqueta{
                text-align: center;
                display: inline-block; 
                vertical-align: middle;
                cursor: move;
                font-weight: bold;
                width: 200px;
                height: 90px;
                border: 1px solid;
                margin: 0 auto;
                padding: 2px;
                font-size: .8rem;
                border-radius: 7px;
                padding: 2px;
                padding-right: 4px;
                overflow: auto;
            }
            .etiqLat{
                font-size: .55rem; 
                font-weight: bold;
                margin: 0;
                text-align: center;
                overflow-y: auto;
                min-width: 150px;
                border: 1px outset; /* outset, borda em alto relevo  - inset, borda em baixo relevo */
                border-radius: 10px;
            }
            .etiqInt{
                border: 2px solid;
            }
            .etiqAtiva{
                background-color: yellow;
            }
            .etiqInat{
                background-color: #F5F5F5;
            }
            .etiqInativa{
                border: 3px solid blue;
                background-color: #C6E2FF;
            }
            .divbot{ /* botão */
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px;
                font-size: 80%;
                text-align: center;
            }
            .relacmodal{
               display: none; /* oculto default */
                position: fixed;
                z-index: 200;
                left: 0;
                top: 0;
                width: 100%; /* largura total */
                height: 100%; /* altura total */
                overflow: auto; /* autoriza scroll se necessário */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }
            /* caixa do Modal Content */
            .modalTarefa-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%; /* acertar de acordo com a tela */
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%; /* acertar de acordo com a tela */
            }
            .modalTransf-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            .modalTarefas-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            /* Botão fechar */
            .close{
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                text-align: right;
            }
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
            .blink{
                animation: blink 1.4s infinite;
            }
            @keyframes blink {
                0% {
                    opacity: 1;
                }
                100% {
                    opacity: 0;
                    color: blue;
                }
            }

        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                document.getElementById("imgTarefasconfig").style.visibility = "hidden"; // configurar grupos para tarefas
                document.getElementById("imgOrgTarefasConfig").style.visibility = "hidden"; // configurar níveis para tarefas baseado no organograma
                if(parseInt(document.getElementById("UsuAdm").value) >= 6){ // nível revisor
                    if(parseInt(document.getElementById("guardaGrupoTar").value) === 3){
                        document.getElementById("imgTarefasconfig").style.visibility = "visible"; //gerenciar setores
                    } 
                    if(parseInt(document.getElementById("guardaGrupoTar").value) === 4){
                        document.getElementById("imgOrgTarefasConfig").style.visibility = "visible"; // gerenciar posição no organograma
                    }
                }

                if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admIns").value)){ // nível administrativo
                    document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir
                }
                if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admEdit").value)){ // nível administrativo
                    document.getElementById("botimprTarefas").style.visibility = "hidden"; // botão de inserir
                }

                //Para mensagens não lidas nas Tarefas  - Tem um comando desses em indexb.php aproveitando o temporizador em checaCalend() a cada hora
                document.getElementById("verTipo"+document.getElementById("guardaSelecSit").value).checked = true;
                document.getElementById("verSetor"+document.getElementById("guardaSelecSetor").value).checked = true;
                document.getElementById("verSetorImpr"+document.getElementById("guardaSelecSetor").value).checked = true;
                
//                $('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value+"&numtarefa="+document.getElementById("selecTarefa").value);

                //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value+"&numtarefa="+document.getElementById("selecTarefa").value);
                ContaTarefa();

                //Fecha caixa ao clicar na página
                modalMsg = document.getElementById('relacmodalMsg'); //span[0]
                spanMsg = document.getElementsByClassName("close")[0];
                modalHelp = document.getElementById('relacHelpTarefas'); //span[1]
                spanHelp = document.getElementsByClassName("close")[1];
                window.onclick = function(event){
                    if(event.target === modalMsg){
                        modalMsg.style.display = "none";
                    }
                    if(event.target === modalHelp){
                        modalHelp.style.display = "none";
                    }
                };

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecMandante").value = "";
                    document.getElementById("selecExecutante").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/conteudo/imprTarefas.php?acao=listamesTarefa&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value)+"&area="+document.getElementById("guardaSelecSetorImpr").value, document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprTarefas").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    document.getElementById("selecMandante").value = "";
                    document.getElementById("selecExecutante").value = "";
                    document.getElementById("selecsit").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/conteudo/imprTarefas.php?acao=listaanoTarefa&ano="+encodeURIComponent(document.getElementById("selecAno").value)+"&area="+document.getElementById("guardaSelecSetorImpr").value, document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprTarefas").style.display = "none";
                    }
                });

                $("#selecMandante").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecExecutante").value = "";
                    document.getElementById("selecsit").value = "";
                    if(document.getElementById("selecMandante").value != ""){
                        window.open("modulos/conteudo/imprTarefas.php?acao=listaMandante&codigo="+document.getElementById("selecMandante").value+"&area="+document.getElementById("guardaSelecSetorImpr").value, document.getElementById("selecMandante").value);
                        document.getElementById("selecMandante").value = "";
                        document.getElementById("relacimprTarefas").style.display = "none";
                    }
                });

                $("#selecExecutante").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecMandante").value = "";
                    document.getElementById("selecsit").value = "";
                    if(document.getElementById("selecExecutante").value != ""){
                        window.open("modulos/conteudo/imprTarefas.php?acao=listaExecutante&codigo="+document.getElementById("selecExecutante").value+"&area="+document.getElementById("guardaSelecSetorImpr").value, document.getElementById("selecExecutante").value);
                        document.getElementById("selecExecutante").value = "";
                        document.getElementById("relacimprTarefas").style.display = "none";
                    }
                });
                $("#selecsit").change(function(){
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecMesAno").value = "";
                    document.getElementById("selecMandante").value = "";
                    document.getElementById("selecExecutante").value = "";
                    if(document.getElementById("selecsit").value != ""){
                        window.open("modulos/conteudo/imprTarefas.php?acao=listaSitTarefa&numero="+document.getElementById("selecsit").value+"&area="+document.getElementById("guardaSelecSetorImpr").value, document.getElementById("selecsit").value);
                        document.getElementById("selecsit").value = "";
                        document.getElementById("relacimprTarefas").style.display = "none";
                    }
                });

                $("#configselecSolicitante").change(function(){
                    if(document.getElementById("configselecSolicitante").value == ""){
                        document.getElementById("configcpfsolicitante").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=buscausuario&codigo="+document.getElementById("configselecSolicitante").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configcpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("configSelecSetor").value = Resp.grupotarefa;
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });
                $("#configcpfsolicitante").click(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    document.getElementById("configcpfsolicitante").value = "";
                    document.getElementById("configSelecSetor").value = "";
                });
                $("#configcpfsolicitante").change(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=buscacpfusuario&cpf="+encodeURIComponent(document.getElementById("configcpfsolicitante").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    document.getElementById("configSelecSetor").value = Resp.grupotarefa;
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configselecSolicitante").value = Resp.PosCod;
                                    }
                                    if(parseInt(Resp.coderro) === 3){
                                        document.getElementById("configcpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("configcpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configSelecSetor").change(function(){
                    if(document.getElementById("configSelecSetor").value == ""){
                        return false;
                    }
                    if(document.getElementById("configselecSolicitante").value == ""){
                        document.getElementById("configSelecSetor").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=salvagrupotar&codigo="+document.getElementById("configselecSolicitante").value+"&codgrupo="+document.getElementById("configSelecSetor").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Valor salvo.";
                                        $('#mensagemConfig').fadeOut(2000);
                                        //Se for atualizar o próprio grupo, reinicializar
                                        if(parseInt(document.getElementById("configselecSolicitante").value) === parseInt(document.getElementById("usu_Logado_id").value)){

                                            $.confirm({
                                                title: 'Mudança de Grupo',
                                                content: 'É preciso reiniciar o módulo para mostrar as tarefas desse Grupo. \nQuer fazer isso agora?',
                                                autoClose: 'Não|10000',
                                                draggable: true,
                                                buttons: {
                                                    Sim: function () {
                                                        //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                                                        $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
                                                        if(parseInt(document.getElementById("guardaGrupoTar").value) === 3){ // em grupo
                                                            document.getElementById("etiqGrupoTar").innerHTML = "Tarefas Grupo "+Resp.siglasetor;
                                                        }
                                                    },
                                                    Não: function () {
                                                    }
                                                }
                                            });
                                        }
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configselecUsuOrg").change(function(){
                    if(document.getElementById("configselecUsuOrg").value == ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=buscausuarioorg&codigo="+document.getElementById("configselecUsuOrg").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecOrg").value = Resp.orgtarefa;
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configSelecOrg").change(function(){
                    if(document.getElementById("configselecUsuOrg").value == ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=salvaorgtar&codigo="+document.getElementById("configselecUsuOrg").value+"&valororg="+document.getElementById("configSelecOrg").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        $('#mensagemConfigOrg').fadeIn("slow");
                                        document.getElementById("mensagemConfigOrg").innerHTML = "Valor salvo.";
                                        $('#mensagemConfigOrg').fadeOut(2000);
                                        //Se for atualizar o próprio nível, reinicializar
                                        if(parseInt(document.getElementById("configselecUsuOrg").value) === parseInt(document.getElementById("usu_Logado_id").value)){
                                            //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                                            $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value); 
                                        }
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });


            }); // fim do ready

            function escMultImprTarefas(){
                if(document.getElementById("selecMultExecutante").value == ""){
                    return false;
                }
//                if(document.getElementById("selecMultMes").value == ""){
//                    return false;
//                }
                if(document.getElementById("selecMultAno").value == ""){
                    return false;
                }
                if(document.getElementById("selecMultSit").value == ""){
                    return false;
                }
//                window.open("modulos/conteudo/imprTarIndiv.php?acao=imprIndiv&codigo="+document.getElementById("selecMultExecutante").value+"&mes="+encodeURIComponent(document.getElementById("selecMultMes").value)+"&ano="+document.getElementById("selecMultAno").value+"&sit="+encodeURIComponent(document.getElementById("selecMultSit").value), document.getElementById("selecMultExecutante").value);
                window.open("modulos/conteudo/imprTarIndiv.php?acao=imprIndiv&codigo="+document.getElementById("selecMultExecutante").value+"&ano="+document.getElementById("selecMultAno").value+"&sit="+encodeURIComponent(document.getElementById("selecMultSit").value), document.getElementById("selecMultExecutante").value);
            }

            function escMultImprCombo(){
                if(document.getElementById("selecComboMandante").value == ""){
                    return false;
                }
                if(document.getElementById("selecComboExecutante").value == ""){
                    return false;
                }
                if(document.getElementById("selecComboAno").value == ""){
                    return false;
                }
                window.open("modulos/conteudo/imprTarefas.php?acao=listaCombo&mandante="+document.getElementById("selecComboMandante").value+"&executante="+document.getElementById("selecComboExecutante").value+"&ano="+document.getElementById("selecComboAno").value+"&sit="+encodeURIComponent(document.getElementById("selecComboSit").value)+"&area="+document.getElementById("guardaSelecSetor").value, "Combo");
            }

            function relatTarefas(){
                window.open("modulos/conteudo/imprTarefas.php?acao=estatTarefas&area="+document.getElementById("guardaSelecSetorImpr").value, "Estatistica");
                document.getElementById("relacimprTarefas").style.display = "none";
            }

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
            function PegaCod(Cod, Ativo, UsuExec){
                document.getElementById("guardaid").value = Cod; //Pega e guarda o código do elemento dragado
                document.getElementById("guardaAtiv").value = Ativo;
                document.getElementById("guardaUsuExec").value = UsuExec;
            }

            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev) {
                ev.dataTransfer.setData("text", ev.target.id);
            }

            function drop(ev, col) {
                if(parseInt(col) > 0){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=mudaStatus&numero="+document.getElementById("guardaid").value
                        +"&novoStatus="+col
                        +"&usumodif="+document.getElementById("usu_Logado_id").value
                        +"&guardaativ="+document.getElementById("guardaAtiv").value
                        +"&usuexec="+document.getElementById("guardaUsuExec").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                                        $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
                                        ev.preventDefault();
                                        var data = ev.dataTransfer.getData("text");
                                        ev.target.appendChild(document.getElementById(data));
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
            }
            function carregaModal(Cod){
                document.getElementById("guardaidEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=buscaTarefa&numero="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("idExecSelect").value = Resp.usuExec;
                                document.getElementById("textoEvid").value = Resp.TitTarefa;
                                document.getElementById("textoExt").value = Resp.TextoTarefa;
                                document.getElementById("selecprio").value = Resp.priorid;
                                document.getElementById("selectStatus").value = Resp.sit;
                                if(parseInt(document.getElementById("usu_Logado_id").value) === parseInt(Resp.usuIns)){ // se for o usuário que inseriu a tarefa
                                    document.getElementById("selectStatus").disabled = false;
                                    document.getElementById("botapagar").style.visibility = "visible";
                                }
                                document.getElementById("verSetorIns"+Resp.tipotar).checked = true;
                                document.getElementById("titulomodal").innerHTML = "Edição de Tarefa";
                                document.getElementById("labelnomeIns").innerHTML = "Inserida por: "+Resp.NomeUsuIns;
                                document.getElementById("relacmodalTarefa").style.display = "block";
                                document.getElementById("textoEvid").focus();
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreModal(){ // inserir nova tarefa
                document.getElementById("idExecSelect").value = "";
                document.getElementById("textoEvid").value = "";
                document.getElementById("textoExt").value = "";
                document.getElementById("labelnomeIns").innerHTML = "";
                document.getElementById("selecprio").value = 3;                
                document.getElementById("guardaid").value = 0;
                document.getElementById("guardaidEdit").value = 0;
                if(parseInt(document.getElementById("guardaSelecSetor").value) === 0){
                    document.getElementById("verSetorIns1").checked = true;    
                }else{
                    document.getElementById("verSetorIns"+document.getElementById("guardaSelecSetor").value).checked = true;
                }
                document.getElementById("mudou").value = "1"; // vai inserir novo
                document.getElementById("botapagar").style.visibility = "hidden";
                document.getElementById("selectStatus").disabled = true;
                document.getElementById("titulomodal").innerHTML = "Inserção de Tarefa";
                document.getElementById("relacmodalTarefa").style.display = "block";
            }

            function salvaModal(){
                if(document.getElementById("idExecSelect").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escolha o <u>NOME</u> do destinatário da tarefa";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("textoEvid").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Tarefa</u> (descrição sucinta)";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                AreaTar = 1;
                if(document.getElementById("verSetorIns2").checked === true){
                    AreaTar = 2;
                }
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=salvaTarefa&numero="+document.getElementById("guardaidEdit").value
                        +"&usuLogado="+document.getElementById("usu_Logado_id").value
                        +"&idExecSelect="+document.getElementById("idExecSelect").value
                        +"&selectStatus="+document.getElementById("selectStatus").value
                        +"&priorid="+document.getElementById("selecprio").value
                        +"&areatar="+AreaTar
                        +"&textoEvid="+encodeURIComponent(document.getElementById("textoEvid").value)
                        +"&textoExt="+encodeURIComponent(document.getElementById("textoExt").value), true);
                        
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//if(document.getElementById("guardaUsuCpf").value == "13652176049"){
//alert(ajax.responseText);
//}
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodalTarefa").style.display = "none";
                                        $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec=6&area="+document.getElementById("guardaSelecSetor").value);
                                        ContaTarefa();
                                        document.getElementById("verTipo6").checked = true;
                                    }else if(parseInt(Resp.coderro) === 2){
                                        $.confirm({
                                            title: 'Atenção!',
                                            content: 'Esta tarefa já foi dada para este usuário e ainda não está terminada.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        alert("Houve um erro no servidor.");
                                        document.getElementById("relacmodalTarefa").style.display = "none";
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalTarefa").style.display = "none";
                }
            }
            function fechaModalTarefa(){
                document.getElementById("relacmodalTarefa").style.display = "none";
            }

            function carregaHelpTarefas(){
                document.getElementById("relacHelpTarefas").style.display = "block";
            }
            function fechaModalHelp(){
                document.getElementById("relacHelpTarefas").style.display = "none";
            }
            
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }

            function deletaModal(){
                $.confirm({
                    title: 'Apagar tarefa.',
                    content: 'Confirma apagar esse lançamento?',
                    autoClose: 'Não|8000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=deletaTarefa&numero="+document.getElementById("guardaidEdit").value+"&usuLogado="+document.getElementById("usu_Logado_id").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                                document.getElementById("relacmodalTarefa").style.display = "none";
                                            }else{
                                                document.getElementById("mudou").value = "0";
                                                document.getElementById("relacmodalTarefa").style.display = "none";
                                                //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                                                $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
                                                ContaTarefa();
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
            function carregaMsg(Cod){
                document.getElementById("guardaidEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=buscaMsg&numero="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("titTarefa").innerHTML = Resp.TitTarefa;
                                $("#faixacentral").load("modulos/conteudo/jTarefa.php?numtarefa="+Cod+"&usulogadoid="+document.getElementById('usu_Logado_id').value+"&usulogadonome="+encodeURIComponent(document.getElementById('nome_Logado').value));
                                document.getElementById("relacmodalMsg").style.display = "block";
                                document.getElementById("novamensagem").focus();
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaModalMsg(){ // marca mensagem como lida
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=marcalidas&numtarefa="+document.getElementById("guardaidEdit").value+"&nomeusuario="+document.getElementById("nome_Logado").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor ao fechar as mensagens. Informe à ATI.")
                                }
                                //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value); // para parar de piscar a ícone de tem mensagem
                                $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value); // para parar de piscar a ícone de tem mensagem
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("relacmodalMsg").style.display = "none";
            }

            function carregaTransf(){
                $("#faixacentralTransf").load("modulos/conteudo/transfTarefa.php");
                document.getElementById("relacmodalTransf").style.display = "block";
            }

            function tranfereTarefa(){
                if(document.getElementById("TransfUsuSelect").value == ""){
                    $.confirm({
                        title: 'Atenção!',
                        content: 'Selecione um usuário para transferir o acompanhamento das tarefas.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                //Procura se tem algum marcado
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=procuramarcas&codigo="+document.getElementById("TransfUsuSelect").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.contagem) > 0){
                                if(parseInt(Resp.marcas) === 0){
                                    $.confirm({
                                        title: 'Atenção!',
                                        content: 'Nenhuma tarefa foi marcada. Clique na caixinha à esquerda da tarefa para selecionar.',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    return false;
                                }else{
                                    $.confirm({
                                        title: 'Transferir Tarefas',
                                        content: 'Confirma tansferir as tarefas selecionadas para <br>'+Resp.nomecompleto+'?',
                                        autoClose: 'Não|10000',
                                        draggable: true,
                                        buttons: {
                                            Sim: function () {
                                                ajaxIni();
                                                if(ajax){
                                                        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=transferemarcas&codigo="+document.getElementById("TransfUsuSelect").value, true);
                                                        ajax.onreadystatechange = function(){
                                                            if(ajax.readyState === 4 ){
                                                                if(ajax.responseText){
//alert(ajax.responseText);
                                                                    document.getElementById("relacmodalTransf").style.display = "none";
                                                                    //$('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value);
                                                                    $('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
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
                            }else{
                                $.confirm({
                                    title: 'Atenção!',
                                    content: 'Nenhuma tarefa em andamento.',
                                    draggable: true,
                                    buttons: {
                                        OK: function(){}
                                    }
                                });
                                return false;
                            }
                            }
                        }
                    
                    };
                    ajax.send(null);
                }
            }

            function carregaTipo(Valor){
                document.getElementById("guardaSelecSit").value = Valor;
                $("#faixaTarefa").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
            }

            function carregaSetor(Valor){
                document.getElementById("guardaSelecSetor").value = Valor;
                document.getElementById("guardaSelecSetorImpr").value = Valor;
                document.getElementById("verSetorImpr"+document.getElementById("guardaSelecSetorImpr").value).checked = true;
                //para guardar o valor em poslog
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=salvaSetor&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                $('#faixaTarefa').load('modulos/conteudo/relTarefas.php?selec='+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value);
            }

            function carregaSetorImpr(Valor){
                document.getElementById("guardaSelecSetorImpr").value = Valor;
            } 

            function ContaTarefa(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=contaTarefas", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("quantMinhas").innerHTML = Resp.quantExecutante;
                                    document.getElementById("quantPagas").innerHTML = Resp.quantMandante;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreTarefasConfig(){
                document.getElementById("configcpfsolicitante").value = "";
                document.getElementById("configselecSolicitante").value = "";
                document.getElementById("configSelecSetor").value = "";
                document.getElementById("modalTarefasConfig").style.display = "block";
            }

            function abreOrgTarefasConfig(){
                document.getElementById("configSelecOrg").value = "";
                document.getElementById("configselecUsuOrg").value = "";
                document.getElementById("modalTarefasConfigOrg").style.display = "block";
            }
            
            function fechaModalConfig(){
                document.getElementById("modalTarefasConfig").style.display = "none";
            }
            function fechaOrgModalConfig(){
                document.getElementById("modalTarefasConfigOrg").style.display = "none";
            }
            function fechaModalTransf(){
                document.getElementById("relacmodalTransf").style.display = "none";
            }
            function escImprTarefas(){
                document.getElementById("relacimprTarefas").style.display = "block";
            }
            function fechaModalImpr(){
                document.getElementById("relacimprTarefas").style.display = "none";
            }
            function resumoGrupoTarefas(){
                window.open("modulos/conteudo/imprGruposTar.php?acao=imprGrupos", "Grupos");
            }
            function resumoOrgTarefas(){
                window.open("modulos/conteudo/imprGruposTar.php?acao=imprOrganogr", "Organogr");
            }
            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }
        </script>
    </head>
    <body>
        <?php
        require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
        $Adm = $_SESSION["AdmUsu"];
        $UsuLogadoId = $_SESSION["usuarioID"];
        $UsuLogadoNome = $_SESSION["NomeCompl"];

        if(isset($_REQUEST["selec"])){ // vem de indexb.php ao clicar em tem mensagem nas Tarefas
            $Sit = $_REQUEST["selec"];
        }else{
            $Sit = 5; // carregar página em Minhas Tarefas
        }
        if(isset($_REQUEST["numtarefa"])){ // vem da indexb.php ao clicar em tem mensagem nas Tarefas
            $NumTarefa = $_REQUEST["numtarefa"];
        }else{
            $NumTarefa = 0;
        }

        $rs = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'tarefas' ");
        $row = pg_num_rows($rs);
        if($row == 0){
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }

        $admIns = parAdm("instarefa", $Conec, $xProj);   // nível para inserir
        $admEdit = parAdm("edittarefa", $Conec, $xProj); // nível para editar
        $VerTarefas = parAdm("vertarefa", $Conec, $xProj); // ver tarefas   1: todos - 2: só mandante e executante - 3: visualização por setor - 4: por Organograma

        $AreaUsu = parEsc("areatar", $Conec, $xProj, $_SESSION["usuarioID"]); // Área a visualizar Manutenção ou administrativa 

        $CodSetorUsu = parEsc("grupotarefa", $Conec, $xProj, $_SESSION["usuarioID"]);
        $rs7 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $CodSetorUsu");
        $row7 = pg_num_rows($rs7);
        if($row7 > 0){
            $tbl7 = pg_fetch_row($rs7);
            $SiglaSetor = $tbl7[0];
        }
        $MeuOrg = parEsc("orgtarefa", $Conec, $xProj, $_SESSION["usuarioID"]); // nível no organograma

        // Preenche caixa de escolha mes/ano para impressão - ano antes para indexar primeiro pelo ano
        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(datains, 'MM'), '/', TO_CHAR(datains, 'YYYY')) 
        FROM ".$xProj.".tarefas GROUP BY TO_CHAR(datains, 'MM'), TO_CHAR(datains, 'YYYY') ORDER BY TO_CHAR(datains, 'YYYY') DESC, TO_CHAR(datains, 'MM') DESC ");
        $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".tarefas.datains)::text 
        FROM ".$xProj.".tarefas GROUP BY 1 ORDER BY 1 DESC ");

        if($VerTarefas == 1 || $VerTarefas == 2){
            //Relacionar usuários - adm <= $Adm - só paga tarefa para nível adm menor ou igual
            $OpcoesUsers = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And adm <= $Adm ORDER BY nomeusual, nomecompl");
            $OpcoesTransf = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And adm >= $admIns And pessoas_id != $UsuLogadoId ORDER BY nomeusual, nomecompl");
            $OpcoesUserMand = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
            $OpcoesUserExec = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
            $OpcoesUserData = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
            $OpcoesMandante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
            $OpcoesExecutante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        }
        if($VerTarefas == 3){ // 3 = visualização por setor 
            $OpcoesUsers = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And adm <= $Adm And grupotarefa = $CodSetorUsu ORDER BY nomeusual, nomecompl");
            $OpcoesTransf = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu And pessoas_id != $UsuLogadoId ORDER BY nomeusual, nomecompl");
            $OpcoesUserMand = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu ORDER BY nomecompl, nomeusual");
            $OpcoesUserExec = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu ORDER BY nomecompl, nomeusual");
            $OpcoesUserData = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu ORDER BY nomecompl, nomeusual");
            $OpcoesMandante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu ORDER BY nomecompl, nomeusual");
            $OpcoesExecutante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And grupotarefa = $CodSetorUsu ORDER BY nomecompl, nomeusual");
        }
        if($VerTarefas == 4){ // 4 = visualização pela posição no organograma 
            $OpcoesUsers = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomeusual, nomecompl");
            $OpcoesTransf = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And pessoas_id != $UsuLogadoId ORDER BY nomeusual, nomecompl");
            $OpcoesUserMand = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomecompl, nomeusual");
            $OpcoesUserExec = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomecompl, nomeusual");
            $OpcoesUserData = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomecompl, nomeusual");
            $OpcoesMandante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomecompl, nomeusual");
            $OpcoesExecutante = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 And orgtarefa >= $MeuOrg ORDER BY nomecompl, nomeusual");
        }

        //marca que foi visualizado nesta data - dataSit1
        pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit1 = NOW() WHERE usuexec = ".$_SESSION["usuarioID"]." And datasit1 = '3000/12/31' And ativo = 1");
        ?>
        <input type="hidden" id="guardaid" value="0" />
        <input type="hidden" id="guardaidEdit" value="0" />
        <input type="hidden" id="usu_Logado_id" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="nome_Logado" value="<?php echo $_SESSION["NomeCompl"]; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir tarefas -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar tarefas -->
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guardaAtiv" value="1" /> <!-- Guarda se a tarefa foi finalizada-->
        <input type="hidden" id="guardaUsuExec" value="0" />
        <input type="hidden" id="grupotarefa" value="1" />
        <input type="hidden" id="guardaUsuCpf" value="<?php echo $_SESSION["usuarioCPF"]; ?>" />
        <input type="hidden" id="guardaGrupoTar" value="<?php echo $VerTarefas; ?>" /> 
        <input type="hidden" id="guardaSelecSit" value="<?php echo $Sit; ?>" />
        <input type="hidden" id="selecTarefa" value="<?php echo $NumTarefa; ?>" />
        <input type="hidden" id="guardaSelecSetor" value="<?php echo $AreaUsu; ?>" />
        <input type="hidden" id="guardaSelecSetorImpr" value="<?php echo $AreaUsu; ?>" />

        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto; padding-top: 10px;">
            <div class="row">
                <div class="col" style="margin: 0 auto;"> 
                    <input type="button" class="botpadrblue" id="botinserir" value="Inserir Tarefa" onclick="abreModal();">
                    <img src="imagens/settings.png" height="20px;" id="imgTarefasconfig" style="cursor: pointer; padding-left: 20px;" onclick="abreTarefasConfig();" title="Configurar grupos de Tarefas">
                    <img src="imagens/settings.png" height="20px;" id="imgOrgTarefasConfig" style="cursor: pointer; padding-left: 20px;" onclick="abreOrgTarefasConfig();" title="Configurar Níveis de Usuários pelo Organograma">
                </div>

                <div class="col" style="text-align: center;">
                    <h4 id="etiqGrupoTar">Tarefas <?php if($VerTarefas == 3){ echo "Grupo ".$SiglaSetor; } ?> </h4>
                </div> <!-- Central - espaçamento entre colunas  -->
                <div class="col" style="margin: 0 auto; text-align: right;">
                    <button class="botpadrred" style="font-size: 80%;" id="botimprTarefas" onclick="escImprTarefas();">Gerar PDF</button>
                    <label style="padding-left: 20px;"></label>
                    <button class="botpadr" id="botTransfIns" onclick="carregaTransf();" title="Transferir tarefas designadas para acompanhamento por outro usuário">Transferir</button>
                    <label style="padding-left: 20px;"></label>
                    <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpTarefas();" title="Guia rápido">
                </div> 
            </div>
        </div>

    <!-- Selecionar área de tarefas: administrativa ou manutenção -->
        <table style="margin: 0 auto;">
            <tr>
                <td>
                    <div style="text-align: center; width: 100%; border: 1px solid #7D26CD; border-radius: 10px; padding-left: 10px; padding-right: 10px;">
                        <label class="etiqRoxa" style="padding-right: 10px;">Selecionar Área: </label>
                        <input type="radio" name="verSetor" id="verSetor2" value="2" onclick="carregaSetor(value);"><label for="verSetor2" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Administrativa</label>
                        <input type="radio" name="verSetor" id="verSetor1" value="1" onclick="carregaSetor(value);"><label for="verSetor1" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Manutenção</label>
                        <input type="radio" name="verSetor" id="verSetor0" value="0" CHECKED onclick="carregaSetor(value);"><label for="verSetor0" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Ambas</label>
                    </div>
                </td>
            </tr>
        </table>


        <div class="container" style="margin: 0 auto; padding-top: 2px; text-align: center;">
            <label class="etiqAzul" style="padding-right: 10px;">Visualizar Tarefas 
                <?php if($VerTarefas == 3){echo "(Grupos)";};
                      if($VerTarefas == 4){echo "(Organograma)";}; ?>
            </label>
            <input type="radio" name="verTipo" id="verTipo0" value="0" CHECKED onclick="carregaTipo(value);"><label for="verTipo0" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Todas</label>
            <input type="radio" name="verTipo" id="verTipo1" value="1" onclick="carregaTipo(value);"><label for="verTipo1" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Designadas</label>
            <input type="radio" name="verTipo" id="verTipo2" value="2" onclick="carregaTipo(value);"><label for="verTipo2" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> Aceitas</label>
            <input type="radio" name="verTipo" id="verTipo3" value="3" onclick="carregaTipo(value);"><label for="verTipo3" style="font-size: 12px; padding-left: 3px; padding-right: 10px;"> em Andamento</label>
            <input type="radio" name="verTipo" id="verTipo4" value="4" onclick="carregaTipo(value);"><label for="verTipo4" style="font-size: 12px; padding-left: 3px; padding-right: 25px;"> Terminadas</label>
            <input type="radio" name="verTipo" id="verTipo5" value="5" onclick="carregaTipo(value);"><label for="verTipo5" style="font-size: 12px; padding-left: 3px; color: #FF6600; font-weight: bold;"> Minhas Tarefas</label> <label id="quantMinhas" style="padding-right: 25px; font-size: 65%; color: #036; font-style: italic; vertical-align: super;" title="Minhas tarefas ainda não terminadas"></label>
            <input type="radio" name="verTipo" id="verTipo6" value="6" onclick="carregaTipo(value);"><label for="verTipo6" style="font-size: 12px; padding-left: 3px; color: #0000CD; font-weight: bold;"> Meus Pedidos</label> <label id="quantPagas" style="padding-right: 25px; font-size: 65%; color: #036; font-style: italic; vertical-align: super;" title="Meus pedidos ainda não terminados"></label>
            <input type="radio" name="verTipo" id="verTipo7" value="7" onclick="carregaTipo(value);"><label for="verTipo7" style="font-size: 12px; padding-left: 3px;"> com Mensagem</label>
        </div>

        <div id="faixaTarefa"></div>

        <!-- div modal para edição e inserção de tarefa -->
        <div id="relacmodalTarefa" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modalTarefa-content">
                <span class="close" onclick="fechaModalTarefa();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição de Tarefas</h3>
                <label id="labelnomeIns" class="etiq" style="padding-left: 10px;"></label>
                <table style="margin: 0 auto;">
                    <tr>
                        <td id="etiqIdExec" class="etiq">Tarefa para:</td>
                        <td colspan='2' >
                            <select id="idExecSelect" style="font-size: 1rem; width: 200px;" title="Selecione um usuário.">
                            <option value= ""></option>
                            <?php 
                            if($OpcoesUsers){
                                while ($Opcoes = pg_fetch_row($OpcoesUsers)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php if(!is_null($Opcoes[2]) && $Opcoes[2] != ""){ echo $Opcoes[2]." - ".$Opcoes[1];}else{echo $Opcoes[1];} ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>    
                        <td colspan='3' >
                            <div style="text-align: center; width: 100%; border: 1px solid #7D26CD; border-radius: 10px; padding-left: 10px; padding-right: 5px;">
                                <label class="etiqRoxa" style="padding-right: 10px;">Área: </label>
                                <input type="radio" name="verSetorIns" id="verSetorIns2" value="2"><label for="verSetorIns2" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 5px; font-weight: bold;"> Administrativa</label>
                                <input type="radio" name="verSetorIns" id="verSetorIns1" value="1"><label for="verSetorIns1" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 5px; font-weight: bold;"> Manutenção</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqTextoEvid" class="etiq">Tarefa:</td>
                        <td colspan='5' rowspan='3'>
                            <div class="col-xs-6">
                                <textarea class="form-control" id="textoEvid" rows="5" cols="70" placeholder="Descrição sucinta" onchange="modif();" style="font-size:95%; width: 60%; border: 1px solid blue; border-radius: 10px"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                    <td id="etiqTextoPrio" class="etiq">Prioridade:</td>
                        <td>  <!-- &nabla; -->
                            <select id="selecprio" title="Prioridade da tarefa" onchange="modif();">
                                <option value='0'>Urgente</option>
<!--                                <option value='1'>Muito Importante</option> -->
                                <option value='2'>Importante</option>
                                <option value='3'>Normal</option>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr> 
                    <tr>
                        <td></td>
                        <td></td>
                    </tr> 
                    <tr>
                        <td id="etiqTextoExt" class="etiq">Memória:</td>
                        <td colspan='5' rowspan='6'>
                            <div class="col-xs-6">
                                <textarea class="form-control" id="textoExt" rows='6' placeholder="Detalhes (opcional)" onchange="modif();" style="font-size: 95%; width: 98%; border: 1px solid blue; border-radius: 10px"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                    <td id="etiqSelecStatus" class="etiq">Mudar Status:</td>
                        <td>
                            <select id="selectStatus" onchange="modif();" title="Seleciona o novo status para essa tarefa">
                                <option value="1">Designada</option>
                                <option value="2">Recebida</option>
                                <option value="3">Andamento</option>
                                <option value="4">Terminada</option>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="text-align: left;"><input type="button" class="botpadrTijolo" id="botapagar" value="Apagar" onclick="deletaModal();"></td>
                        <td colspan='4' style="text-align: right; padding-right: 50px;"><input type="button" class="botpadrblue" id="salvar" value="Salvar" onclick="salvaModal();"></td>
                        <td></td>
                    </tr>
                </table>
           </div>
        </div> <!-- Fim Modal Tarefa-->

        <!-- div modal para leitura e inserção de mensagens -->
        <div id="relacmodalMsg" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modalMsg-content">
                <span class="close" onclick="fechaModalMsg();">&times;</span>
                <h3 id="titulomodalMsg" style="text-align: center; color: #666;">Mensagens</h3>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    <table>
                        <tr>
                            <td class="etiq">Tarefa: </td>
                            <td><div id='titTarefa'></div></td>
                        </tr>
                    </table>
                </div>
                <div id="faixacentral" style='border: 1px solid; border-radius: 10px;'></div> <!-- aqui entra jTarefa.php -->
            </div>
        </div>  <!-- Fim Modal Mensagens-->


        <!-- div modal para transferência das tarefas que designei para outro usário acompanhar -->
        <div id="relacmodalTransf" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modalTransf-content">
                <span class="close" onclick="fechaModalTransf();">&times;</span>
                <h3 id="tituloMsgTransf" style="text-align: center; color: #666;">Transferir Acompanhamento</h3>
                <div style="text-align: center;"><label class="etiqAqul">Transferir o acompanhamento das Tarefas para outro usuário</label></div>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px; text-align: center;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td class="etiq">Transferir para: </td>
                            <td>
                                <select id="TransfUsuSelect" style="font-size: 1rem; min-width: 300px;" title="Selecione um usuário.">
                                    <option value= ""></option>
                                    <?php 
                                    if($OpcoesTransf){
                                        while ($Opcoes = pg_fetch_row($OpcoesTransf)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php if(!is_null($Opcoes[2]) && $Opcoes[2] != ""){ echo $Opcoes[2]." - ".$Opcoes[1];}else{echo $Opcoes[1];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="text-align: center;"><button class="botpadr" id="botTransfTar" onclick="tranfereTarefa();" title="Transferir tarefas designadas para acompanhamento por outro usuário">Transferir</button></td>
                        </tr>
                    </table>
                </div>
                <div id="faixacentralTransf" style='border: 1px solid; border-radius: 10px;'></div> <!-- aqui entra jTarefa.php -->
            </div>
        </div>  <!-- Fim Modal Mensagens-->

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprTarefas" class="relacmodal">
            <div class="modal-content-imprLeitura">
                <span class="close" onclick="fechaModalImpr();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Relatórios Tarefas</h5>

                <table style="margin: 0 auto;">
                    <tr>
                        <td>
                            <div style="text-align: center; width: 100%; border: 1px solid #7D26CD; border-radius: 10px; padding-left: 10px; padding-right: 10px;">
                                <label class="etiqRoxa" style="padding-right: 10px;">Selecionar Área: </label>
                                <input type="radio" name="verSetorImpr" id="verSetorImpr2" value="2" onclick="carregaSetorImpr(value);"><label for="verSetorImpr2" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px; font-weight: bold;"> Administrativa</label>
                                <input type="radio" name="verSetorImpr" id="verSetorImpr1" value="1" onclick="carregaSetorImpr(value);"><label for="verSetorImpr1" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px; font-weight: bold;"> Manutenção</label>
                                <input type="radio" name="verSetorImpr" id="verSetorImpr0" value="0" onclick="carregaSetorImpr(value);"><label for="verSetorImpr0" class="etiqRoxa" style="font-size: 12px; padding-left: 3px; padding-right: 10px; font-weight: bold;"> Ambas</label>
                            </div>
                        </td>
                    </tr>
                </table>

                <div style="border: 2px solid #C6E2FF; border-radius: 10px; padding: 5px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Mensal - Selecione o Ano/Mês: </label></td>
                            <td>
                                <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o período.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscMes){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Anual - Selecione o Ano: </label></td>
                            <td>
                                <select id="selecAno" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscAno){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscAno)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Mandante - Selecione o Usuário: </label></td>
                            <td>
                                <select id="selecMandante" style="font-size: 1rem; width: 180px;" title="Selecione o Usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesUserMand){
                                        while ($Opcoes = pg_fetch_row($OpcoesUserMand)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Executante - Selecione o Usuário: </label></td>
                            <td>
                                <select id="selecExecutante" style="font-size: 1rem; width: 180px;" title="Selecione o Usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesUserExec){
                                        while ($Opcoes = pg_fetch_row($OpcoesUserExec)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Situação - Selecione a opção: </label></td>
                            <td>
                                <select id="selecsit" style="font-size: 1rem;" title="Selecione a situação.">
                                    <option value=""></option>
                                    <option value="1">Designada</option>
                                    <option value="2">Aceita</option>
                                    <option value="3">Andamento</option>
                                    <option value="4">Terminada</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 10px; text-align: center;">
                                <button class="resetbotazul" style="font-size: 80%;" onclick="relatTarefas();" title="Demonstrativo anual das tarefas expedidas">Relatório Anual</button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                    </table>


                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td colspan="3"><label class="etiqAzul">Combo Solicitante/Executante: </label></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size: 80%;">Solicitante:</td>
                            <td style="text-align: center;">
                                <select id="selecComboMandante" style="font-size: 1rem; width: 100%;" title="Selecione um Usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesMandante){
                                        while ($Opcoes = pg_fetch_row($OpcoesMandante)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                            <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>&nbsp;&nbsp;</td>
                        </tr> 
                        <tr>  
                            <td style="text-align: right; font-size: 80%;">Excutante:</td>
                            <td>
                                <select id="selecComboExecutante" style="font-size: 1rem; width: 100%;" title="Selecione um Usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesExecutante){
                                        while ($Opcoes = pg_fetch_row($OpcoesExecutante)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                            <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>   
                            <td colspan="3" style="text-align: center;"> <label style="font-size: 80%;">Ano/Situação:</label>
                                <select id="selecComboAno" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
                                    <option value=""></option>
                                    <?php
                                        $OpcoesComboAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".tarefas.datains)::text FROM ".$xProj.".tarefas GROUP BY 1 ORDER BY 1 DESC ");
                                        if($OpcoesComboAno){
                                            while ($Opcoes = pg_fetch_row($OpcoesComboAno)){ ?>
                                                <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                                <select id="selecComboSit" style="font-size: 1rem;" title="Selecione a situação.">
                                    <option value="0">Todas</option>
                                    <option value="1">Designada</option>
                                    <option value="2">Aceita</option>
                                    <option value="3">Andamento</option>
                                    <option value="4">Terminada</option>
                                </select>

                                <button class="botpadrred" style="font-size: 80%;" id="botimprCombo" onclick="escMultImprCombo();">Vai</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 0px;"><hr></td>
                        </tr>

                        <tr>
                            <td colspan="3"><label class="etiqAzul">Usuário/Ano/Situação: </label></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center;">
                            <label style="font-size: 80%;">Usuário: </label>
                                <select id="selecMultExecutante" style="font-size: 1rem; width: 300px;" title="Selecione um Usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesUserData){
                                        while ($Opcoes = pg_fetch_row($OpcoesUserData)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                            <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" style="text-align: center;">
                                <label style="font-size: 80%;">Ano/Situação:</label>
<!--                                <select id="selecMultMes" style="font-size: 1rem; width: 90px; text-align: center;" title="Selecione o Mês.">
                                    <option value=""></option>
                                    <?php
                                    $OpcoesMes = pg_query($Conec, "SELECT esc1, esc2 FROM ".$xProj.".escolhas WHERE codesc BETWEEN 2 And 13 ORDER BY codesc ");
                                    if($OpcoesMes){
                                        while ($Opcoes = pg_fetch_row($OpcoesMes)){ ?>
                                            <option style="text-align: center;" value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
-->
                                <select id="selecMultAno" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
                                    <option value=""></option>
                                    <?php
                                        $OpcoesAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".tarefas.datains)::text FROM ".$xProj.".tarefas GROUP BY 1 ORDER BY 1 DESC ");
                                        if($OpcoesAno){
                                            while ($Opcoes = pg_fetch_row($OpcoesAno)){ ?>
                                                <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                                <select id="selecMultSit" style="font-size: 1rem;" title="Selecione a situação.">
                                    <option value="0">Todas</option>
                                    <option value="1">Designada</option>
                                    <option value="2">Aceita</option>
                                    <option value="3">Andamento</option>
                                    <option value="4">Terminada</option>
                                </select>
                                <button class="botpadrred" style="font-size: 80%;" id="botimprTarefas" onclick="escMultImprTarefas();">Vai</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal escolha impressão -->

         <!-- Modal configuração-->
         <div id="modalTarefasConfig" class="relacmodal">
            <div class="modalTarefas-content">
                <span class="close" onclick="fechaModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="text-align: center; color: #666;">Grupos para Tarefas</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoGrupoTarefas();">Resumo em PDF</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver o Grupo de Tarefas a que pertence:</label>
                
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Procura nome: </td>
                        <td style="width: 100px;">
                            <select id="configselecSolicitante" style="max-width: 330px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
                                if($OpConfig){
                                    while ($Opcoes = pg_fetch_row($OpConfig)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                        <td>
                            <input type="text" id="configcpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configselecSolicitante');return false;}" title="Procura por CPF. Digite o CPF do usuário."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq" title="Selecione um setor para agrupar usuários de tarefas.">Participa do Grupo de Tarefas:</td>
                        <td colspan="4">
                            <select id="configSelecSetor" style="max-width: 430px;" onchange="modif();" title="Selecione um setor para agrupar usuários de tarefas.">
                                <?php 
                                $OpSetores = pg_query($Conec, "SELECT CodSet, siglasetor, descsetor FROM ".$xProj.".setores WHERE ativo = 1 ORDER BY siglasetor");
                                if($OpSetores){
                                    while ($Opcoes = pg_fetch_row($OpSetores)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; color: red; font-weight: bold;" id="mensagemConfig"></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- Modal configuração-->
         <div id="modalTarefasConfigOrg" class="relacmodal">
            <div class="modalTarefas-content">
                <span class="close" onclick="fechaOrgModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="textf-align: center;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"></div>
                        <div class="col" style="width: 50%; textf-align: center;"><h5 style="text-align: center; color: #666;">Config Tarefas</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoOrgTarefas();">Resumo em PDF</button></div> 
                    </div>
                </div>
                <div style="width: 100%; text-align: center; padding-bottom: 10px;"><label class="etiqAzul">Níveis tipo Organograma</label></div>
                <label class="etiqAzul">Selecione um usuário para configurar sua posição para tarefas:</label>
                
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center;">Busca Nome do Usuário</td>
                        <td style="text-align: center;">Organograma</td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Procura nome: </td>
                        <td style="width: 100px;">
                            <select id="configselecUsuOrg" style="max-width: 330px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                $OpConfigOrg = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
                                if($OpConfigOrg){
                                    while ($Opcoes = pg_fetch_row($OpConfigOrg)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="etiqAzul"><label class="etiqAzul">&nbsp;&nbsp;&nbsp; </label></td>
                        <td style="text-align: center;">
                            <select id="configSelecOrg" style="font-size: 1rem;" title="Selecione a posição do usuário no Organograma.">
                                <option value="10">Conselho</option>
                                <option value="20">Presidência</option>
                                <option value="30">Diretoria</option>
                                <option value="30">Assessoria</option>
                                <option value="40">Divisão</option>
                                <option value="50">Gerência</option>
                                <option value="60">Funcionário</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <div style="text-align: center; color: red; font-weight: bold;" id="mensagemConfigOrg"></div>
            </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para leitura instruções -->
        <div id="relacHelpTarefas" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h3 style="text-align: center; color: #666;">Informações</h3>
                <h4 style="text-align: center; color: #666;">Tarefas
                    <?php if($VerTarefas == 3){echo " Grupos";}; 
                        if($VerTarefas == 4){echo " Organograma";}; 
                    ?>
                </h4>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <?php
                            if($VerTarefas == 1){echo "<li>1 - Um usuário pode emitir tarefa para outros usuários do seu nível administrativo (do site) ou inferior.</li>";};  
                            if($VerTarefas == 2){echo "<li>1 - Um usuário pode emitir tarefa para outros usuários do seu nível administrativo (do site) ou inferior.</li>";};  
                            if($VerTarefas == 3){echo "<li>1 - Um usuário pode emitir tarefa para outros usuários do seu grupo de usuários. O grupo é definido pela administração.</li>";}; 
                            if($VerTarefas == 4){echo "<li>1 - Um usuário pode emitir tarefa para outros usuários do seu nível administrativo ou inferior, conforme o organograma.</li>";}; 
                        ?>
                        <?php
                            if($VerTarefas == 1){echo "<li>2 - Uma tarefa inserida aparece para todos os usuários.</li>";};  
                            if($VerTarefas == 2){echo "<li>2 - Uma tarefa inserida só aparece para o usuário que a inseriu e para o usuário designado para executá-la.</li>";};  
                            if($VerTarefas == 3){echo "<li>2 - Uma tarefa inserida só aparece para os usuários do grupo.</li>";}; 
                            if($VerTarefas == 4){echo "<li>2 - Uma tarefa inserida aparece para os usuários conforme os níveis definidos no organograma. A tarefa aparecerá se o Solicitante ou o Executante estiver no seu nível. Em consequência, os relatórios terão números diferentes, conforme o nível em que foi solicitado. A tarefa inserida por um usuário do nível mais alto para um usuário do nível mais baixo fica visível nos níveis intermediários.</li>";}; 
                        ?>

                        <li>3 - Apenas o usuário designado para a execução pode arrastar os quadros para a direita.</li>
                        <li>4 - Uma vez arrastados para a direita, os quadros não voltam. Mas o usuário que inseriu a tarefa, se tiver o nível administrativo adequado, pode editá-la e reposicioná-la nos quadros, mesmo se já estiver concluída.</li>
                        <li>5 - Mensagens podem ser trocadas entre os usuários. Elas são relativas a uma tarefa específica. Um ícone pisca para indicar que há mensagem não lida naquela tarefa. Uma mensagem aparece na página inicial informando que há mensagem não lida nas tarefas.</li>
                        <li>6 - As tarefas classificadas como urgentes se posicionam no topo da relação.</li>
                        <li>7 - As tarefas concluídas vão para o final da relação</li>
                        <li>8 - Um usuário pode emitir tarefa para si mesmo.</li>
                        <li>9 - Uma mensagem indicando que há tarefas ainda não vistas aparece na página inicial. Nas outras páginas aparece uma vez a cada hora.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>