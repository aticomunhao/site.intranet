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
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/tinymce5/tinymce.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <!-- src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin" -->
        <style type="text/css">
            .modal-content-EditaPag{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%; /* acertar de acordo com a tela */
            }

            .listaArq{
                float: left; color: #23527c; background-color: #FFF8DC; text-align: left; cursor: pointer; border: 1px solid; border-radius: 5px; padding: 3px; padding-left: 5px; padding-right: 5px;
            }
            #descarq:hover {
                text-decoration: underline;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#contentPag").load("modulos/conteudo/carRelPag.php");   
            });
//            tinymce.init({selector:'textarea'});
            tinymce.init({
                selector : "textarea",
                language: 'pt_BR',
                height: 380,
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

            function salvaModal(){
                tinyMCE.triggerSave(true,true); // importante
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/regconfig.php?acao=salvaTexto&setorid="+document.getElementById("guarda_pagDir").value+"&textopagina="+encodeURIComponent(document.getElementById("textopagina").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    $("#contentPag").load("modulos/conteudo/carRelPag.php");
                                    document.getElementById("relacmodalEdit").style.display = "none";
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
                document.getElementById("relacmodalEdit").style.display = "block";
            }
            function fechaModal(){
                document.getElementById("relacmodalEdit").style.display = "none";
            }
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT textopag FROM ".$xProj.".setores WHERE codset = ".$_SESSION["PagDir"]);
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $Proc0 = pg_fetch_row($rs0);
                $TextoPag = $Proc0[0];
            }else{
                $TextoPag = "";
            }
        ?>
        <input type="hidden" id="guarda_pagDir" value="<?php echo $_SESSION["PagDir"]; ?>" />
        <div id="contentPag"></div>

        <!-- div modal para edição da página  -->
        <div id="relacmodalEdit" class="relacmodal">
            <div class="modal-content-EditaPag">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição da Página</h3>
                <div style="border: 2px solid blue; border-radius: 10px;">
                    <table style="margin: 0 auto;">
                        <tr>
                            <td style="width: 1500px;"><textarea id="textopagina"><?php echo $TextoPag; ?></textarea></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                            <td style="text-align: center; padding-right: 30px;"><button class="resetbot" onclick="salvaModal();">Salvar</button></td>
                        </tr>
                    </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>