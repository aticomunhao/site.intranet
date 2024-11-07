<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/tinymce5/tinymce.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-confirm.min.js"></script>
        <style type="text/css">
            .modal-content-Trocas{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 75%; /* acertar de acordo com a tela */
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#contentPag").load("modulos/trocas/carTrocas.php?admEdit="+document.getElementById("admEdit").value);
            });
            tinymce.init({
                selector : "textarea",
                language: 'pt_BR',
                height: 380,
                branding: false,
                menubar: false,
                plugins: ['image imagetools'],
                images_upload_handler: image_upload_handler,
                automatic_uploads: true,
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
            function abreEdit(CodTroca){
                tinyMCE.triggerSave(true,true); // importante
                document.getElementById("guardaTroca").value = CodTroca; // guardar para salvar
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/trocas/salvaTrocas.php?acao=buscaTexto&numero="+CodTroca, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    tinyMCE.activeEditor.setContent(Resp.textotroca);  //importante
                                    document.getElementById("relacmodalTrocas").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaTroca(){
                tinyMCE.triggerSave(true,true); // importante
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/trocas/salvaTrocas.php?acao=salvaTroca&numero="+document.getElementById("guardaTroca").value+"&texto="+encodeURIComponent(document.getElementById("textotroca").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    $("#contentPag").load("modulos/trocas/carTrocas.php?admEdit="+document.getElementById("admEdit").value);
                                    document.getElementById("relacmodalTrocas").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function fechaModal(){
                document.getElementById("relacmodalTrocas").style.display = "none";
            }
            function guardaCod(CodTroca){
                document.getElementById("guardaTroca").value = CodTroca;
            }
            function apagTroca(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/trocas/salvaTrocas.php?acao=apagaTroca&numero="+document.getElementById("guardaTroca").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    $("#contentPag").load("modulos/trocas/carTrocas.php?admEdit="+document.getElementById("admEdit").value);
                                }else{
                                    alert("Houve um erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaTroca(Cod){
                $.confirm({
                    title: 'Confirmação!',
                    content: "Não haverá possibilidade de recuperação. Continua?",
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/trocas/salvaTrocas.php?acao=apagaTroca&numero="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 0){
                                                $("#contentPag").load("modulos/trocas/carTrocas.php?admEdit="+document.getElementById("admEdit").value);
                                            }else{
                                                alert("Houve um erro no servidor");
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

            function insTroca(){
                tinyMCE.activeEditor.setContent(""); // importante
                document.getElementById("titulomodal").innerHTML = "Inserção de Anúncio";
                document.getElementById("relacmodalTrocas").style.display = "block";
                document.getElementById("guardaTroca").value = "0";
            }
            function carregaHelpTrocas(){
                document.getElementById("relacHelpTrocas").style.display = "block";
            }
            function fechaHelpTrocas(){
                document.getElementById("relacHelpTrocas").style.display = "none";
            }
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $admIns = parAdm("instroca", $Conec, $xProj);   // nível para inserir
            $admEdit = parAdm("edittroca", $Conec, $xProj); // nível para editar - atravessado para relTrocas.php
        ?>
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" />
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" />

        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro">
                    <?php
                    if($_SESSION["AdmUsu"] >= $admIns){ // botão inserir
                        echo "<button class='botpadrGr fundoAzul' id='botinserir' onclick='insTroca();' >Inserir Anúncio</button>";
                    }
                    ?>
                </div>
                <div class="col quadro" style="text-align: center;"><h4>Material Disponível</h4></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col quadro" style="text-align: right;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpTrocas();" title="Guia rápido"></div> 
            </div>
        </div>
        <br>

        <div class="container-fluid" style="margin: 10px; text-align: center;">
            <div id="contentPag"></div>
        </div>

        <input type="hidden" id="guardaTroca" value="0" />

        <!-- div modal para edição da página  -->
        <div id="relacmodalTrocas" class="relacmodal">
            <div class="modal-content-Trocas">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição do Anúncio</h3>
                <div style="border: 2px solid blue; border-radius: 10px;">
                    <table style="margin: 0 auto;">
                        <tr>
                        <td><textarea id="textotroca" name="textotroca" style="width: 800px;" ></textarea></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                            <td style="text-align: center;"><button class="resetbot" onclick="salvaTroca();">Salvar</button></td>
                        </tr>
                    </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- Modal bootstrap para confirmação -->
        <div class="modal fade" id="deletaModal" tabindex="-1" aria-labelledby="deletaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletaModalLabel">Apagar Anúncio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">Não haverá possibilidade de recuperação. Continua?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Não </button>
                        <button type="button" class="btn btn-primary" onclick='apagTroca()' data-bs-dismiss="modal"> Sim </button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal Confirmação-->



        <!-- div modal para leitura instruções -->
        <div id="relacHelpTrocas" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaHelpTrocas();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Troca de Materiais</h5>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - Este módulo destina-se a informar que existe um material que será descartado.</li>
                        <li>2 - Os usuários com nível administrativo apropriado podem inserir um anúncio com a descrição do material e ajuntar fotografia.</li>
                        <li>3 - O setor que tiver interesse em receber o material, antes que ele seja doado, descartado ou destruído, deve entrar em contato com o setor anunciante e demostrar seu interesse.</li>
                        <li>4 - Ao dar destino ao material, o usuário que inseriu o anúncio, ou outro do mesmo nível administrativo e da mesma diretoria, pode deletar o anúncio.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>