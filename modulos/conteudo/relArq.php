<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style type="text/css">
            .cContainer{ /* encapsula uma frase no topo de uma div em reArq.php e PagDir.php */
                position: absolute; 
                left: 20px;
                margin-top: -20px; 
                border: 1px solid blue; 
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .bContainer{ /* botão upload */
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
            .listaArq{
                float: left; color: #23527c; background-color: #FFF8DC; text-align: left; cursor: pointer; border: 1px solid; border-radius: 5px; padding: 3px; padding-left: 5px; padding-right: 5px;
            }
            #descarq:hover {
                text-decoration: underline;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#relArquivos").load("modulos/conteudo/carRelArq.php?admins="+document.getElementById("admIns").value);

                $('#arquivo').click(function(){
                    document.getElementById('bottsubmit').disabled = false;
                    document.getElementById("bottsubmit").style.visibility = "visible";
                });
                $('#bottsubmit').click(function(){
                    if(document.getElementById("arquivo").value !== ""){
                        document.getElementById("bottsubmit").style.visibility = "hidden";
                    }
                });
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

            function mostraArq(CodArq){
                ajaxIni();
                if(ajax){
                   ajax.open("POST", "modulos/conteudo/regconfig.php?acao=selectarquivo&codigo="+CodArq, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4){
//alert(ajax.responseText);
                            Resp = eval("(" + ajax.responseText + ")");
                            if(parseInt(Resp.coderro) === 1){
                                alert("Houve um erro desconhecido no servidor.");
                            }else if(parseInt(Resp.coderro) === 2){
                                alert("O arquivo não foi encontrado ou está corrompido."); 
                            }else{
                                window.open("modulos/conteudo/arquivos/"+Resp.arquivo, '_blank');
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaArqDir(Cod){
                $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar este arquivo?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/conteudo/regconfig.php?acao=apagaarquivo&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro desconhecido no servidor.");
                                            }else if(parseInt(Resp.coderro) === 2){
                                                alert("O arquivo não foi encontrado ou está corrompido.");
                                                $("#relArquivos").load("modulos/conteudo/carRelArq.php?admins="+document.getElementById("admIns").value);
                                            }else{
                                                $("#relArquivos").load("modulos/conteudo/carRelArq.php?admins="+document.getElementById("admIns").value);
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

            function fechaDiv(){
                document.getElementById("arquivo").value = "";
                document.getElementById("formSubmit").style.display = "none";
            }
            function carregaArq(){
                if(document.getElementById("formSubmit").style.display === "none"){
                    document.getElementById("arquivo").value = "";
                    document.getElementById("formSubmit").style.display = "block";
//                    Envia = setInterval("fechaDiv()", 60000);
                }else{
                    document.getElementById("formSubmit").style.display = "none";
                }
            }
        </script>
    </head>
    <body>
        <div class="cContainer corFundo">Arquivos do Setor</div>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $admIns = parAdm("insarq", $Conec, $xProj);   // nível para inserir
            $_SESSION['msgarq'] = ""; // mensagem que vem de uploadTraf.php

            if($_SESSION["CodSubSetorUsu"] == 1){ // está na diretoria
                if($_SESSION["CodSetorUsu"] == $_SESSION["PagDir"] && $_SESSION["AdmUsu"] >= $admIns){ // botão upload arquivos
                    echo "<div class='bContainer corFundo' onclick='carregaArq()'> UpLoad &#8673;</div>";
                }
            }else{ // subdiretorias foram retiradas 02/10/2023
                if($_SESSION["CodSubSetorUsu"] == $_SESSION["PagSubDir"] && $_SESSION["AdmUsu"] >= $admIns){
                    echo "<div class='bContainer corFundo' onclick='carregaArq()'> UpLoad &#8673;</div>";
                }
            }
        ?>
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir arquivos -->

        <!-- formulário para envio de arquivo -->
        <div id="formSubmit" style="display: none; margin: 10px; padding: 5px; text-align: center;">
            <div style="padding: 10px; text-align: left; border: 1px solid; border-radius: 15px;">
                <form action="#" class="form-horizontal">
                    <div class="form-group" style="font-size: .8em;">
                       <div class="col-sm-12" style="overflow: hidden;">
                           <input type="file" name="arquivo" id="arquivo" class="custom-file-input">
                           <button type="submit" style="border-radius: 5px;" id="bottsubmit">Carregar</button>
                       </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10" style="margin: 5px;">
                            <div class="progress progress-striped active">
                                <div id="barraprogress" class="progress-bar" style="width: 0%;"></div>
                            </div>
                            <span id="msg"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- faz o envio através do arquivo arqUpload.php -->
        <script>
            document.getElementById("bottsubmit").disabled = true;
            $('#arquivo').change(function(){
                document.getElementById("bottsubmit").disabled = false;
            });
            $('#bottsubmit').click(function(){
//                document.getElementById("bottsubmit").disabled = true;  //para evitar segundo click
            });
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
                        $form.find('.progress-bar').addClass('progress-bar-success').html('...');
                        //Atualizar a página após o upload completo
                        setTimeout("$('#relArquivos').load('modulos/conteudo/carRelArq.php?admins="+document.getElementById('admIns').value+"')", 1000);
                        setTimeout("$form.find('.progress-bar').width( 0 + '%').html( 0 + '%')", 1000); //faz voltar a barra de progresso

                        $('#msg').fadeIn(2000);
                        $('#msg').load('modulos/conteudo/carSesArq.php'); // contém a variável de sessão que guarda o sucesso do upload
                        $('#msg').fadeOut(2000);
                        document.getElementById("arquivo").value = "";
                    });
                    request.open("post", "modulos/conteudo/arqUpload.php");  //Arquivo responsável em fazer o upload
                    request.send(formdata);
                    document.getElementById('arquivo').value = "";
                }
            });
        </script>

        <div id="relArquivos"></div>  <!-- div para mostrar a relação dos arquivos carregados -->

        <!-- Modal bootstrap para confirmação - sem uso -->
        <div class="modal fade" id="deletaModal" tabindex="-1" aria-labelledby="deletaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletaModalLabel">Apagar Arquivo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">Não haverá possibilidade de recuperação. Continua?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Não </button>
                        <button type="button" class="btn btn-primary" onclick='apagaArq()' data-bs-dismiss="modal"> Sim </button>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>