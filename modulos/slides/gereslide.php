<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <style type="text/css">
            .etiq{
                text-align: right; color: #036; font-size: 80%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
            .mostraslide{
                position: relative; float:left; width: 25%; border: 1px solid; border-radius: 10px; text-align: center; padding: 10px;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#mostraSlides").load("modulos/slides/carSlides.php");
                if(parseInt(document.getElementById("guardamsg").value) === 1){
                    document.getElementById("slidecarregado").src = "modulos/conteudo/arquivos/"+document.getElementById("arqslide").value; //modulos/slides/imagens/
                    document.getElementById("modalEditaSlide").style.display = "block";
                }
                if(parseInt(document.getElementById("guardamsg").value) === 2){
                    document.getElementById("msg").style.color = "red";
                    document.getElementById("buscaArquivo").style.display = "block";    
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
                            catch(exc){
                                alert("Esse browser não tem recursos para uso do Ajax");
                                ajax = null;
                        }
                    }
                }
            }
            function Subst(Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/slides/salvaSlide.php?acao=guardaslide&slide="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("labelProcurar").innerHTML = "Substituir Slide "+Valor;
                document.getElementById("buscaArquivo").style.display = "block";
            }
            function fechaEditaSlide(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/slides/salvaSlide.php?acao=ressetasession&arquivo="+encodeURI(document.getElementById("arqslide").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                document.getElementById("guardamsg").value = 0;
                                document.getElementById("modalEditaSlide").style.display = "none";
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaSlide(Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/slides/salvaSlide.php?acao=acertaslide&valor="+Valor+"&arquivo="+encodeURI(document.getElementById("arqslide").value+"&numslide="+document.getElementById("guardanum").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0){
                                    alert("Houve um erro na substituição.")
                                }else{
                                    document.getElementById("guardamsg").value = 0;
                                    $("#mostraSlides").load("modulos/slides/carSlides.php");
                                    document.getElementById("modalEditaSlide").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
        </script>
    </head>
    <body>
        <input type="hidden" id="guardamsg" value="<?php if(isset($_SESSION['geremsg'])){ echo $_SESSION['geremsg'];}else{echo "0";} ?>" /> 
        <input type="hidden" id="arqslide" value="<?php if(isset($_SESSION['arquivo'])){ echo $_SESSION['arquivo'];}else{echo "";} ?>" />
        <input type="hidden" id="guardanum" value="<?php if(isset($_SESSION['gerenum'])){ echo $_SESSION['gerenum'];}else{echo "0";} ?>" /> 

        <div id="mostraSlides" style="margin: 20px auto; text-align: center; padding: 40px;"></div>

        <div id="buscaArquivo" style="position: relative; float: left; width: 99%; display: none; margin: 0 auto; text-align: center; padding: 30px;">
            <div style="width: 50%; padding: 15px; text-align: left; border: 1px solid; border-radius: 15px;">
            <form action="#" class="form-horizontal">
                <div class="form-group">
                   <label id="labelProcurar" class="col-sm-20 control-label">Procurar:</label>
                   <div class="col-sm-8">
                       <input type="file" name="arquivo" id="arquivo" class="custom-file-input">
                   </div>
                </div>
                <div class="form-group">
                   <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <div class="progress progress-striped active">
                            <div class="progress-bar" style="width: 0%;"></div>
                        </div>
                        <span id="msg"><?php echo $_SESSION["msg"]; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-info upload" id="bottsubmit">Carregar</button>
                    </div>
                </div>
            </form>
            </div>
        </div>

        <script>
            document.getElementById("bottsubmit").disabled = true;
            $('#arquivo').change(function(){
                document.getElementById("bottsubmit").disabled = false;
            });
//            $('#bottsubmit').click(function(){
//                document.getElementById("bottsubmit").disabled = true;  //para evitar segundo click
//            });
            $(document).on('submit', 'form', function(e){
                e.preventDefault();
                if(document.getElementById('arquivo').value !== ""){
                    //Receber os dados 
                    $form = $(this);
                    var formdata = new FormData($form[0]);
                    //Criar conexão com o servidor
                    var request = new XMLHttpRequest();
                    //Progresso do upload
                    request.upload.addEventListener('progress', function(e){
                        var percent = Math.round(e.loaded / e.total * 100);
                        $form.find('.progress-bar').width(percent + '%').html(percent + '%');
                    });
                    //limpar a barra de progresso
                    request.addEventListener('load', function(e){
                        $form.find('.progress-bar').addClass('progress-bar-success').html('upload completo...');
                        //Atualizar a página após o upload completo
                        setTimeout("$('#container3').load('modulos/slides/gereslide.php')", 2000);
                    });
                    //Arquivo responsável em fazer o upload do arquivo
                    request.open("post", "modulos/slides/slideUpload.php?numslide="+document.getElementById("guardanum").value);
                    request.send(formdata);
                }
            });
        </script>

        <div id="modalEditaSlide" class="relacmodal" style="display: none;">
            <div class="modalEditaSlide-content">
                <span id="fechaeditaslide" title="Fechar a caixa demonstrativa" onclick="fechaEditaSlide();" style="color: #aaa; top: 0px; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <div style="width: 99%; text-align: center;" >
                    <table>
                        <tr>
                            <td>
                            <div style='padding: 5px;'><img id="slidecarregado" src='' width='250px' height='140px' alt='Arquivo carregado'/></div>
                            </td>
                            <td>
                                <label id="labelConfirma">Confirma substituir o slide <?php if(isset($_SESSION['gerenum'])){ echo $_SESSION['gerenum'];}else{echo "";} ?> por esta imagem?</label>
                                <br>
                                <button class="resetbotred" onclick="salvaSlide(0);">Não</button>
                                <label style="padding-left: 10px;"></label>
                                <button class="resetbotazul" onclick="salvaSlide(1);">Sim</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>