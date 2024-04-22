<?php
	session_start();
    if(!isset($_SESSION['usuarioID'])){
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
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/superfish/js/hoverIntent.js"></script>
        <script src="class/superfish/js/superfish.js"></script>
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/eventos.js"></script>
<!--        <script src="modulos/config/icustomb.js"></script> -->
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
                $('#container1').load('modulos/cabec.php');
                $('#container2').load('modulos/menufim.php?diasemana='+document.getElementById('guardadiasemana').value);
                $('#container4').load('modulos/rodape.php');

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
                                    }else{
                                        document.getElementById("temTarefa").style.display = "none";
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
            function carregaPag(){ // atalho no aviso da página inicial
                $('#container3').load('modulos/conteudo/tarefas.php');
            }
            function fechaModal(){
                document.getElementById("modalComemorat").style.display = "none";
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
//                    autoClose: 'Fechar|15000',
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
            //Aviso de eventos do calendário
            AvisoCalend = setInterval("checaCalend()", 3600000);  // 3 600 000 milessegundos- > 1 hora; 1 800 000 1/2 hora; 900000 15 minutos; 300000 5 minutos;
        </script>
    </head>
    <body>
        <?php
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
            $diaSemana = 1;
        ?>
        <input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>
        <input type="hidden" id="guardaAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>"/> <!-- nível administrativo do usuário logado -->
        <input type="hidden" id="guardamsgcalend" value="0"/>

        <div id="container0" class="container-fluid"> <!-- página toda -->
            <div id="container1" class="container-fluid corFundo"></div> <!-- cabec.php banner superior dividido em 3 -->
            <div id="container2" class="container-fluid fontSiteFamily corFundoMenu-dia<?php echo $diaSemana; ?>"></div> <!-- Menu -->
            <section style="height: 95vh;">
            <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->
                <!-- Carrosel  -->
                <div id="CorouselPagIni" class="carousel slide carousel-fade" data-bs-ride="carousel" style="text-align: center;"></div>

                <div id="container5" style="width: 25%;"> <!--  containers 5 e 6 dentro do container 3 -->
                    <div style="text-align: center; border: 2px solid blue; border-radius: 10px; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;">
                        <span style="font-weight: bold;">Aniversariantes</span>
                        <?php
                            require_once("modulos/aniverIni.php");
                        ?>
                    </div>
                </div>

                <div id="container6" style="width: 70%; padding-left: 80px; padding-right: 100px;">
                    <div id="temTarefa" onclick="carregaPag();" style="display: none; margin-bottom: 20px; color: white; font-weight: bold; background-color: red; text-align: center; padding: 10px; border-radius: 10px; width: 400px;"></div>
                    <div style="padding-left: 20px;">
                        <h3>Comunhão Espírita de Brasília</h3>
                    </div>
                    <br>
                    <div style="text-align: justify;">
                        <p>&nbsp; &nbsp; &nbsp; A Casa Espírita de excelência na sua organização, na geração de conhecimento, na educação, na difusão doutrinária, na assistência espiritual e social, com estímulo à vivência cristã.</p>
                    </div>
                    <div style="text-align: center;">
                        <h5>Fora da caridade não há salvação.</h5>
                    </div>
                </div>
            </div>
            </section>
            <!-- Rodapé -->
            <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>

            <!-- div modal comemorat  -->
            <div id="modalComemorat" class="relacmodal">
                <div class="modal-content-matrix">
                    <span class="close" style="font-size: 5em; padding-right: 20px;" onclick="fechaModal();">&times;</span>
                    <br><br><br>
                    <h1 id="frase1" style="text-align: center; color: #F9F9FF; font-family: tahoma, arial, cursive, sans-serif; font-variant: small-caps; padding-left: 50px;"> Parabéns</h1>
                    <h1 id="frase2" style="text-align: center; color: #F9F9FF; font-family: tahoma, arial, cursive, sans-serif; font-variant: small-caps;"> </h1>
                    <img src="comp/css/images/palmas.gif" width="60" height="60" draggable="false"/>
                </div>
            </div> <!-- Fim Modal-->
        </div>
    </body>
</html>