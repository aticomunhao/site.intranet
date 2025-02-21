<?php
	session_start();
    if(!isset($_SESSION['usuarioID'])){
        session_destroy();
        header("Location: index.php");
     }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>CEsB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="imagens/LogoComunhao.png" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/superfish/css/superfish.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" /> <!-- depois do css do superfish porque a mudança de cores está aqui -->
        <script src="class/tinymce5/tinymce.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/superfish/js/hoverIntent.js"></script>
        <script src="class/superfish/js/superfish.js"></script>
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/eventos.js"></script>
        <style>
            .bContainer{ 
                position: absolute; 
                right: 30px;
                margin-top: -20px; 
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .divTemTarefa{
                display: none; 
                margin-bottom: 20px; 
                color: white; font-weight: bold; 
                background-color: #DC143C; 
                text-align: center; padding: 10px; border-radius: 10px;
                cursor: pointer;
            }
            .divTemBens{
                display: none; 
                margin-bottom: 0px; 
                color: white; font-weight: bold; 
                background-color: #A52A2A; 
                text-align: center; padding: 10px; border-radius: 10px;
                cursor: pointer;
            }
            .divTemBensPrazo{
                display: none; 
                margin-bottom: 20px; 
                color: white; font-weight: bold; 
                background-color: red; text-align: center; padding: 10px; border-radius: 10px;
                cursor: pointer;
            }
            .divTemContrato{
                display: none; 
                margin-bottom: 0px; 
                color: white; font-weight: bold; 
                background-color: #363636; /* grey21 */
                text-align: center; padding: 10px; border: 2px solid; border-radius: 10px;
                cursor: pointer;
            }
            .divTemExtintor{
                display: none; 
                margin-bottom: 0px; 
                color: white; font-weight: bold; 
                background-color:rgb(182, 9, 9);
                text-align: center; padding: 10px; border: 2px solid; border-radius: 10px;
                cursor: pointer;
            }
            .blink{
                animation: blink 1.2s infinite;
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
                $('#CorouselPagIni').load('modulos/carousel.php');
                if(parseInt(document.getElementById("guardaAdm").value) === 0 || document.getElementById("guardaAdm").value === null){// perdeu as variáveis
                    location.replace('modulos/cansei.php');
                }
                document.getElementById("temTarefa").style.display = "none";
                document.getElementById("tarefa").style.display = "none";
                document.getElementById("temBens").style.display = "none";
                document.getElementById("temBensPrazo").style.display = "none";
                document.getElementById("temContrato").style.display = "none";
                document.getElementById("temExtintor").style.display = "none";
                
                $('#container1').load('modulos/cabec.php');
                $('#container2').load('modulos/menufim.php?diasemana='+document.getElementById('guardadiasemana').value);
                $('#container4').load('modulos/rodape.php');
                $('#container7').load('modulos/conteudo/carPagIni.php');

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscaacesso&param=1", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    if(parseInt(Resp.marca) === 1){
                                        document.getElementById("frase2").innerHTML = Resp.msg;
                                        document.getElementById("modalComemorat").style.display = "block";
                                    }
                                    if(parseInt(Resp.temTarefa) > 0){
                                        document.getElementById("temTarefa").innerHTML = Resp.msgTar;
                                        document.getElementById("temTarefa").style.display = "block";
                                        document.getElementById("tarefa").style.display = "block";
                                    }
                                    if(parseInt(Resp.temRecado) > 0){
                                        document.getElementById("TemRecado").innerHTML = Resp.recadoTar;
                                        document.getElementById("numTarefa").value = Resp.CodTarefa;
                                        document.getElementById("selecionar").value = Resp.selecionar;
                                        document.getElementById("TemRecado").style.display = "block";
                                    }
                                    if(parseInt(Resp.bens) > 0){
                                        if(parseInt(Resp.bens) === 1){
                                            document.getElementById("temBens").innerHTML = "1 registro a processar em Achados e Perdidos.";
                                        }else{
                                            document.getElementById("temBens").innerHTML = Resp.bens+" registros a processar em Achados e Perdidos.";
                                        }
                                        document.getElementById("temBens").style.display = "block";
                                    }
                                    if(parseInt(Resp.bensdestinar) > 0){
                                        document.getElementById("temBensPrazo").innerHTML = "Há registro em Achados e Perdidos superando o prazo de 90 dias.";
                                        document.getElementById("temBensPrazo").style.display = "block";
                                    }
                                    if(parseInt(Resp.contrato1) > 0 || parseInt(Resp.contrato2) > 0){
                                        if(parseInt(Resp.contrato1) > 0){
                                            document.getElementById("guardaContrato").value = 1; // a casa contrata
                                        }else{
                                            document.getElementById("guardaContrato").value = 2; // a casa é contratada
                                        }
                                        document.getElementById("temContrato").innerHTML = "Há contrato com prazo para notificação.";
                                        document.getElementById("temContrato").style.display = "block";
                                    }
                                    if(parseInt(Resp.temExtintor) > 0){
                                        document.getElementById("temExtintor").innerHTML = "Há extintor com prazo de validade expirando.";
                                        document.getElementById("temExtintor").style.display = "block";
                                    }
                                }else{
                                    alert("Houve erro ao salvar");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            });

            tinymce.init({
                selector : "textarea",
                language: 'pt_BR',
                height: 420,
                branding: false,
                menubar: false,
                plugins: ['image imagetools'],
                images_upload_handler: image_upload_handler,
//                menubar: 'edit format table tools',
                fontsize_formats: '8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 26pt 28pt 30pt 36pt 48pt',
                toolbar1: 'undo redo | styleselect | fontselect| fontsizeselect | outdent indent | link image',
                toolbar2: 'bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify |',
                content_style: 'body { font-family:Arial,Helvetica,sans-serif; font-size:14px }'
            });
            function image_upload_handler (blobInfo, success, failure, progress){
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', 'postAcceptor.php');
                xhr.upload.onprogress = function (e){
                   progress(e.loaded / e.total * 100);
                };
                xhr.onload = function(){
                    var json;
                    if(xhr.status === 403){
                      failure('HTTP Error: ' + xhr.status, { remove: true });
                      return;
                    }
                    if(xhr.status < 200 || xhr.status >= 300){
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);
                    if(!json || typeof json.location !== 'string'){
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                xhr.onerror = function () {
                    failure('O carregamento da imagem falhou. Código: ' + xhr.status);
                };
                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            };

            function carregaPag(){ // atalho no aviso da página inicial
                $('#container3').load('modulos/conteudo/pagTarefas.php?selec=5');
            }
            function carregaMsgTar(){ // carrega tarefa com mensagem não lida
                $('#container3').load('modulos/conteudo/pagTarefas.php?selec='+document.getElementById('selecionar').value+'&numtarefa='+document.getElementById("numTarefa").value);
            }
            function carregaBens(Valor){
                $('#container3').load('modulos/bensEncont/pagBens.php?acao='+Valor);
            }
            function carregaContrato(Valor){
                if(parseInt(Valor) === 1){
                    $('#container3').load('modulos/contratos/contratosA.php'); // a casa contrata
                }else{
                    $('#container3').load('modulos/contratos/contratosB.php'); // a casa é contratada
                }
            }
            function carregaExtintor(){
                $('#container3').load('modulos/extintores/pagExtint.php?acao=vencervencidos');
            }
            function fechaComemorat(){
                document.getElementById("modalComemorat").style.display = "none";
            }
            function checaLogFim(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=checaLogFim", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }
            function checaCalend(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=msgAviso&param=1", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.Quant) > 0){
                                    if(parseInt(Resp.avisocalend) === 1){
                                        document.getElementById("guardamsgcalend").value = 0;
                                        mostraMsg(Resp.msg);
                                    }
                                    if(parseInt(Resp.avisocalend) === 2){ // mensagem com aviso obrigatório
                                        document.getElementById("guardamsgcalend").value = Resp.codMsg;
                                        mostraMsg(Resp.msg);
                                    }
                                }
                                //Aproveitando o temporizador
                                if(parseInt(Resp.temTarefa) > 0){
                                    document.getElementById("textoTit").innerHTML = "Tarefa";
                                    if(parseInt(Resp.temTarefa) === 1){
                                        document.getElementById("textoMsg").innerHTML = "1 Tarefa Designada.";
                                    }else{
                                        document.getElementById("textoMsg").innerHTML = Resp.temTarefa+" Tarefas Designadas.";
                                    }
                                    document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }

            function mostraMsg(Msg){
                $.confirm({
                    title: 'Evento do Calendário',
                    content: Msg,
                    buttons: {
                        notificUser: {
                            text: 'Não Mostrar mais por hoje',
                            action: function () {
                                ajaxIni();
                                if(ajax){
                                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=semAvisoHoje&codigomsg="+document.getElementById("guardamsgcalend").value, true);
                                    ajax.onreadystatechange = function(){
                                    };
                                    ajax.send(null);
                                } 
                            }
                        },
                        Fechar: function () {
                        }
                    }
                });
            }

            function salvaModalIni(){
                tinyMCE.triggerSave(true,true); // importante
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/regconfig.php?acao=salvaTextoIni&textopaginaini="+encodeURIComponent(document.getElementById("textopaginaini").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    $('#container7').load('modulos/conteudo/carPagIni.php');
                                    document.getElementById("modalEditPagIni").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreEdit(){
                document.getElementById("modalEditPagIni").style.display = "block";
            }
            function fechaModal(){
                document.getElementById("modalEditPagIni").style.display = "none";
            }
            //para veriricar se usuário está on line
            checaFim = setInterval("checaLogFim()", 60000); // 1 minuto
            //Aviso de eventos do calendário
            AvisoCalend = setInterval("checaCalend()", 3600000);  // 3600000 = 3 600 000 milessegundos -> 1 hora;  1 800 000 -> 1/2 hora; 900000 -> 15 minutos; 300000 -> 5 minutos;

            seg = 0;
            document.addEventListener("mousemove", function(){
                seg = 0;
                document.getElementById("mensagemtempo").style.display = "none"; // está em modais.php
            });

            //guardatempo - tempo definido em parâmetros do sistema
            contaTempo = setInterval(function(){
                if(parseInt(document.getElementById("guardatempo").value) > 0){ // 0 tempo infinito
                    seg = seg + 1;
                    if(parseInt(seg) >= parseInt(document.getElementById("guardatempo").value) - 16){
                        document.getElementById("mensagemtempo").style.display = "block";
                        document.getElementById("textoTempo").innerHTML = "Tempo de inatividade será atingido em "+(parseInt(document.getElementById("guardatempo").value) - parseInt(seg))+" segundos. <br> Movimente o mouse para revalidar o uso do sistema.";
                    }
                    if(parseInt(seg) == parseInt(document.getElementById("guardatempo").value)){
                        location.replace('modulos/cansei.php');
                        clearInterval(contaTempo);
                    }
                }else{
                    clearInterval(contaTempo);
                }
                document.getElementById("teste").value = seg;
            }, 1000);

        </script>
    </head>
    <body>
        <?php
            require_once("modulos/config/modais.php");
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
            $diaSemana = 1;

            require_once("modulos/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT textopag FROM ".$xProj.".setores WHERE codset = 1");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $Proc0 = pg_fetch_row($rs0);
                $TextoPag = html_entity_decode($Proc0[0]);
            }else{
                $TextoPag = "";
            }
            $admEdit = parAdm("editpagini", $Conec, $xProj); // nível para editar
            $TempoInat  = parAdm("tempoinat", $Conec, $xProj); // tempo de ociosidade
        ?>
        <input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>
        <input type="hidden" id="guardaAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>"/> <!-- nível administrativo do usuário logado -->
        <input type="hidden" id="guardamsgcalend" value="0"/>
        <input type="hidden" id="guardatempo" value="<?php echo $TempoInat; ?>"/>
        <input type="hidden" id="teste" value=""/>
        <input type="hidden" id="numTarefa" value = "0"/>
        <input type="hidden" id="selecionar" value = "0"/>
        <input type="hidden" id="guardaContrato" value = "0"/>

        <div id="container0" class="container-fluid"> <!-- página toda -->
            <div id="container1" class="container-fluid corFundo"></div> <!-- cabec.php banner superior dividido em 3 -->
            <div id="container2" class="container-fluid fontSiteFamily corFundoMenu-dia<?php echo $diaSemana; ?>"></div> <!-- Menu -->
            <section style="height: 95vh;">
            <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->
                <!-- Carrosel  -->
                <div id="CorouselPagIni" class="carousel slide carousel-fade" data-bs-ride="carousel" style="text-align: center;"></div>

                <div id="container5" style="width: 25%;"> <!--  containers 5 e 6 dentro do container 3 -->
                    <div class='divaniv' style="text-align: center; border: 2px solid blue; border-radius: 10px; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;">
                        <span style="font-weight: bold;">Aniversariantes</span>
                        <?php
                            require_once("modulos/aniverIni.php");
                        ?>
                    </div>
                </div>

                <div id="container6" style="width: 70%; padding-left: 80px; padding-right: 100px; text-align: center;">
                    <?php
                        if($_SESSION["AdmUsu"] >= $admEdit){ // botão editar página
                            echo "<div class='bContainer corFundo' onclick='abreEdit()'> Editar </div>";
                        }
                    ?>
                    <!-- tarja vermelha aviso de tarefa  -->
                    <div id="tarefa" class="blink" onclick="carregaPag();" style="display: none; font-family: Trebuchet MS, Verdana, sans-serif; letter-spacing: 10px; color: red; font-size: 1.5em; font-weigth: bold; text-align: center; padding: 10px; border-radius: 10px;">TAREFA</div>
                    <div id="temTarefa" class="divTemTarefa" onclick="carregaPag();"></div>
                    <div id="TemRecado" class="divTemTarefa" onclick="carregaMsgTar();"></div>
                    <div id="temBens" class="divTemBens" onclick="carregaBens('Guardar');"></div>
                    <div id="temBensPrazo" class="divTemBensPrazo" onclick="carregaBens('Destinar');"></div>
                    <div id="temContrato" class="divTemContrato" onclick="carregaContrato(document.getElementById('guardaContrato').value);"></div>
                    <div id="temExtintor" class="divTemExtintor" onclick="carregaExtintor('Vencido');"></div>

                    <!-- texto da página inicial  -->
                    <div id="container7" style="padding-left: 10px; padding-right: 10px;"></div>
                </div>
            </div>
            </section>
            <!-- Rodapé -->
            <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>

            <!-- div modal para edição da página inicial -->
            <div id="modalEditPagIni" class="relacmodal">
                <div class="modal-content-EditPagIni">
                    <span class="close" onclick="fechaModal();">&times;</span>
                    <h3 id="titulomodal" style="text-align: center; color: #666;">Edição da Página Inicial</h3>
                    <div style="border: 2px solid blue; border-radius: 10px;">
                        <table style="margin: 0 auto;">
                            <tr>
                                <td style="width: 1500px;"><textarea id="textopaginaini"><?php echo $TextoPag; ?></textarea></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                            <tr>
                                <td style="text-align: center; padding-right: 30px;"><button class="resetbot" onclick="salvaModalIni();">Salvar</button></td>
                            </tr>
                        </table>
                    </div>
               </div>
            </div> <!-- Fim Modal-->

            <!-- div modal comemorat  -->
            <div id="modalComemorat" class="relacmodal">
                <div class="modal-content-matrix">
                    <span class="closeB" style="font-size: 5em; padding-right: 20px;" onclick="fechaComemorat();">&times;</span>
                    <br><br><br>
                    <h1 id="frase1" style="text-align: center; color: #F9F9FF; font-family: tahoma, arial, cursive, sans-serif; font-variant: small-caps; padding-left: 50px;"> Parabéns</h1>
                    <h1 id="frase2" style="text-align: center; color: #F9F9FF; font-family: tahoma, arial, cursive, sans-serif; font-variant: small-caps;"> </h1>
                    <img src="comp/css/images/palmas.gif" width="60" height="60" draggable="false"/>
                </div>
            </div> <!-- Fim Modal-->
        </div>
    </body>
</html>