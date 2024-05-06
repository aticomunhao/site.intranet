<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>

        <style type="text/css">
            .etiq{
                text-align: right; color: #9C9C9C; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
        </style>

        <script type="text/javascript">
            $(document).ready(function(){
                $("#dataocor").mask("99/99/9999");
            })

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

            function allowDrop(ev) {
                ev.preventDefault();
            }

            function drag(ev) {
                ev.dataTransfer.setData("text", ev.target.id);
            }
            function drop(ev, col) {
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/ocorrencias/salvaOcor.php?acao=salvaIdeogr&source="+document.getElementById("guardasrc").value+"&codOcorr="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("mudou").value = "1";
                                    document.getElementById("temImagem").value = parseInt(document.getElementById("temImagem").value) + 1;
                                    $("#mostraideogr").load("modulos/ocorrencias/carIdeogr.php?codocor="+document.getElementById("guardacod").value);
                                    ev.preventDefault();
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
        <?php
        date_default_timezone_set('America/Sao_Paulo');
        $hoje = date('d/m/Y');
        ?>
        <input type="hidden" id="temImagem" value="0" />
        <div style="margin: 6px; padding: 5px; text-align: center; font-family: tahoma, arial, cursive, sans-serif;">
            <div id='mostraideogr' droppable='true' ondrop='drop(event);' ondragover='allowDrop(event);' title="Arraste uma imagem significativa da ocorrência para este quadro" style='text-align: center; padding: 10px; height: 100px; border: 1px solid; border-radius: 10px; margin-top: 10px;'></div>
            <div class="box" style="position: absolute; left: 30px; top: 3px;">
                <label class="etiq">Data da Ocorrência: </label>
                <input type="text" id="dataocor" value="<?php echo $hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">
                <label class="etiq" style="padding-left: 10px;" title="Imagens podem representar a ocorrência. Arraste uma imagem do quadro à esquerda para o quadro abaixo.">Ideogramas (emojis) podem representar a ocorrência</label>
            </div>
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 6px; height: 155px; text-align: left;">
                <div class="box" style="position: relative; float: left;">
                    <label class="etiq">Descrição da Ocorrência: </label><br>
                </div>
                <textarea rows="4" cols="75" id="textoocorrencia" ondrop="return false;" onchange="modif();"></textarea>
                <br>
           </div>
           <label class="etiq" id="etiqnomeusuins"></label>
        </div>
    </body>
</html>