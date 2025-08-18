<?php
    session_name("arqAdm"); // sessão diferente da CEsB
	session_start();
    if(!isset($_SESSION['usuarioCPF'])){
        header("Location: indexc.php");
     }
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Arquivos DAF</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="../../imagens/Logo1.png" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" media="screen" href="../../class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../../class/superfish/css/superfish.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../../comp/css/indlog.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../../comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../../comp/css/jquery-confirm.min.css" />
        <script src="../../class/tinymce5/tinymce.min.js"></script>
        <script src="../../comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="../../class/superfish/js/hoverIntent.js"></script>
        <script src="../../class/superfish/js/superfish.js"></script>
        <script src="../../class/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="js/eventosArqDaf.js"></script>
        <style>
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

            .tricol0{
                margin: 10px; padding: 20px; min-height: 300px;
            }

			@media (max-width: 742px){
                .divAniver{
                    border: 0;
                }
                #tricoluna0{
                    margin-top: 100px;
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

//		var versaoJquery = $.fn.jquery; 
//		alert(versaoJquery);
//document.getElementById("guardaAdm").value = 0;

            $(document).ready(function(){
                if(parseInt(document.getElementById("guardaAdm").value) === 0 || document.getElementById("guardaAdm").value === null){// se perdeu as variáveis
                    location.replace('./canseiArqDaf.php');
                }
                $('#container1').load('cabecArqDaf.php');
                $('#container2').load('menufimArqDaf.php?diasemana='+document.getElementById('guardadiasemana').value);
                $('#container4').load('rodapeArqDaf.php');

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
                $('#container3').load('modulos/conteudo/pagTarefas.php?selec=1');
            }

            function checaLogFim(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "config/registrArqDaf.php?acao=checaLogFim", true);
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

            //para veriricar se usuário está on line
            checaFim = setInterval("checaLogFim()", 60000); // 1 minuto
            //Aviso de eventos do calendário

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
                        location.replace('./canseiArqDaf.php');
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
            require_once("config/modaisArqDaf.php");
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
//            $diaSemana = 6;
            require_once("config/abrealasArqDaf.php");
            if(!isset($Conec)){
                require_once("msgErro.php");
                return false;
            }
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                require_once("modulos/arqDaf/msgErro.php");
                return false;
            }

            $TempoInat  = parAdm("tempoinat", $Conec, $xProj); // tempo de ociosidade em site-intranet
            $AdmUsu = arqDafAdm("adm", $Conec, $xProj, $_SESSION["usuarioCPF"]);
        ?>
        <input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>
        <input type="hidden" id="guardaAdm" value="<?php echo $AdmUsu; ?>"/> <!-- nível administrativo do usuário logado -->
        <input type="hidden" id="guardamsgcalend" value="0"/>
        <input type="hidden" id="guardatempo" value="<?php echo $TempoInat; ?>"/>
        <input type="hidden" id="teste" style="font-size: 200%; color: black;" value=""/>
        <input type="hidden" id="numTarefa" value = "0"/>
        <input type="hidden" id="selecionar" value = "0"/>
        <input type="hidden" id="guardaContrato" value = "0"/>

        <div id="container0" class="container-fluid"> <!-- página toda -->
            <div id="container1" class="container-fluid corFundo" style="margin-top: 3px;"></div> <!-- cabec.php banner superior dividido em 3 -->
            <div id="container2" class="container-fluid fontSiteFamily corFundoMenu-dia<?php echo $diaSemana; ?>"></div> <!-- Menu -->

            <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->
                <div style="text-align: left; padding: 50px;">
                <img src='imagens/folder4.jpg' height='200px;'>
            </div>

                <div id="tricoluna0" class="tricol0" style="height: 50vh;">
                    <div id="tricoluna1" class="box" style="position: relative; float: left; width: 30%;;"></div>
                    <div id="tricoluna2" class="box" style="position: relative; float: left; width: 2%; text-align: center;"></div> <!-- Separador -->
                    <div id="tricoluna3" class="box" style="position: relative; float: right; width: 68%; text-align: right;">
                        <div id="container7" style="padding-left: 1px; padding-right: 1px;"></div> <!-- Carrega pag inicial -->

                    </div>  <!-- Texto pág inicial -->
                </div>
                <!-- Rodapé -->
                <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>
            </div>

        </div>
    </body>
</html>