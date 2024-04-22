<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/tinymce5/tinymce.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <style type="text/css">
            .modal-content-Trocas{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 5% auto; /* 10% do topo e centrado */
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 75%; /* acertar de acordo com a tela */
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
            function insTroca(){
                tinyMCE.activeEditor.setContent(""); // importante
                document.getElementById("titulomodal").innerHTML = "Inserção de Anúncio";
                document.getElementById("relacmodalTrocas").style.display = "block";
                document.getElementById("guardaTroca").value = "0";
            }
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $admIns = parAdm("instroca", $Conec, $xProj);   // nível para inserir
            $admEdit = parAdm("edittroca", $Conec, $xProj); // nível para editar - atravessado para relTrocas.php
        ?>
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" />
        <div class="container-fluid" style="margin: 10px; text-align: center;">
            <h3>Material para Troca ou Descarte</h3>
            <div class="box" style="position: absolute; top: 10px; left: 10px; width: 10%; text-align: left;">
            <?php
            if($_SESSION["AdmUsu"] >= $admIns){ // botão inserir
                echo "<input type='button' id='botinserir' class='resetbot' value='Inserir' onclick='insTroca();' title='Apagar este anúncio.'>";
            }
            ?>
            </div>
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
    </body>
</html>