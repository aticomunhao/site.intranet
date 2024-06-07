<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta name="mobile-web-app-capable" content="yes">
        <title>Login</title>
            <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <style>
            body{ font: 14px sans-serif; }
            .caixalog{
                padding: 20px; 
                margin: 0 auto; border: 2px solid red; border-radius: 10px;
            }            
            .resetbot{
                border-radius: 5px;
            }
        </style>
        <script>
            $(document).ready(function(){
                document.getElementById('olhoSecaoSenha').addEventListener('mousedown', function(){
                  document.getElementById('senha').type = 'text';
               });
               document.getElementById('olhoSecaoSenha').addEventListener('mouseup', function(){
                  document.getElementById('senha').type = 'password';
               });
               // Para que o password não fique exposto após mover a imagem.
               document.getElementById('olhoSecaoSenha').addEventListener('mousemove', function(){
                  document.getElementById('senha').type = 'password';
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
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }
            function fechaModalLog(){
                document.getElementById("relacmodalLog").style.display = "none";
            }
            function logModal(){
                if(document.getElementById("usuario").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>USUÁRIO</u>";
                    document.getElementById("usuario").focus();
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("senha").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Senha</u>";
                    document.getElementById("senha").focus();
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=loglog&usuario="+encodeURIComponent(document.getElementById("usuario").value)+"&senha="+encodeURIComponent(document.getElementById("senha").value), true);
                    ajax.onreadystatechange = function(){ // loglog = dbname=pessoal
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0 && parseInt(Resp.coderro) < 5){
                                    $('#mensagem').fadeIn("slow");
                                    document.getElementById("mensagem").innerHTML = Resp.msg;
                                    $('#mensagem').fadeOut(3000);
                                    return false;
                                }else if(parseInt(Resp.coderro) === 5){
                                    document.getElementById("relacmodalLog").style.display = "none";
                                    document.getElementById("relactrocaSenha").style.display = "block";
                                    document.getElementById("novasenha").focus();
                                }else if(parseInt(Resp.coderro) === 6){
                                    $('#mensagem').fadeIn("slow");
                                    document.getElementById("mensagem").innerHTML = Resp.msg;
                                    $('#mensagem').fadeOut(3000);
                                    return false;                                    
                                }else{
                                  document.getElementById("relacmodalLog").style.display = "none";
                                  location.replace("indexb.php"); // location.replace(-> abre na mesma aba
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function fechatrocaSenha(){
                document.getElementById("relacmodalLog").style.display = "none";
                document.getElementById("relactrocaSenha").style.display = "none";
            }
            function salvaTrocaSenha(){
                if(document.getElementById("novasenha").value !== document.getElementById("repetsenha").value){
                    $('#mensagemTroca').fadeIn("slow");
                    document.getElementById("mensagemTroca").innerHTML = "As senhas são diferentes";
                    $('#mensagemTroca').fadeOut(5000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=trocasenha&novasenha="+encodeURIComponent(document.getElementById('novasenha').value)+"&repetsenha="+encodeURIComponent(document.getElementById('repetsenha').value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("textoMsg").innerHTML = "Senha modificada com sucesso.";
                                    document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                    document.getElementById("relactrocaSenha").style.display = "none";
                                    setTimeout(function(){                                        
                                        document.getElementById("relacmensagem").style.display = "none";
                                        location.replace("indexb.php"); // location.replace(-> abre na mesma aba
                                    }, 3000);
                                }else if(parseInt(Resp.coderro) === 4){
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "Houve um erro no servidor...";
                                    $('#mensagemTroca').fadeOut(3000);
                                    alert("Houve um erro no servidor. Infome a ATI");
                                }else if(parseInt(Resp.coderro) === 5){
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "Mínimo de 6 caracteres na senha.";
                                    $('#mensagemTroca').fadeOut(3000);
                                }else if(parseInt(Resp.coderro) === 3){
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "Senha igual ao login.";
                                    $('#mensagemTroca').fadeOut(3000);
                                }else if(parseInt(Resp.coderro) === 2){
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "Senha com sequência numérica";
                                    $('#mensagemTroca').fadeOut(3000);
                                    return false;
                                }else if(parseInt(Resp.coderro) === 1){
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "As senhas são diferentes";
                                    $('#mensagemTroca').fadeOut(3000);
                                    return false;
                                }else{
                                    $('#mensagemTroca').fadeIn("slow");
                                    document.getElementById("mensagemTroca").innerHTML = "Senha em branco";
                                    $('#mensagemTroca').fadeOut(3000);
                                    return false;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function foco(id){
                document.getElementById(id).focus();
            }
        </script>
    </head>
    <body>
        <?php
        require_once("modais.php");
        ?>
         <div id="relacmodalLog" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Login">
                <span class="close" style="padding-right: 10px;" onclick="fechaModalLog();">&times;</span>
                <div class="caixalog">
                    <h2><img src="imagens/LogoComunhao.png" height="40px;"> Login</h2>
                    <p>Por favor, preencha os campos abaixo.</p>
                    <div class="mb-3">
                        <label>Usuário</label>
                        <input type="text" id="usuario" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('senha');return false;}">
                    </div>
                    <table style="margin: 0 auto; width: 100%">
                        <tr style="padding-top: 5px;">
                            <td><label>Senha</label></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><input type="password" id="senha" class="form-control" value="" title="Termine com Enter" onkeypress="if(event.keyCode===13){logModal();}"></td>
                            <td style="text-align: center;"><img id="olhoSecaoSenha" style="cursor: pointer;" title="Mantenha clicado para visualizar a senha inserida." src="imagens/olhosenha.png" alt="" width="25" height="15" draggable="false"></td>
                    </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                            <td colspan="2" style="text-align: center; padding-top: 10px;"><input type="button" class="btn btn-primary resetbot" id="entrar" value="Entrar" onclick="logModal();"></td>
                        </tr>
                    </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <div id="relactrocaSenha" class="relacmodal">  <!-- para trocar a senha inicial -->
            <div class="modal-content-trocaSenha">
                <span class="close" onclick="fechatrocaSenha();">&times;</span>
                <div class="caixalog">
                    <h2><img src="imagens/LogoComunhao.png" height="40px;">Nova Senha</h2>
                    <p>Mudança da senha de acesso.</p>
                    <div>
                        <label>Senha Anterior</label>
                        <input type="password" id="senhaant" class="form-control" disabled value="123456789">
                    </div>
                    <div style="padding-top: 5px;">
                        <label>Senha</label>
                        <input type="password" id="novasenha" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('repetsenha');return false;}">
                        <span class="invalid-feedback"></span>
                    </div>
                    <div style="padding-top: 5px;">
                        <label>Senha</label>
                        <input type="password" id="repetsenha" class="form-control" value="" onkeypress="if(event.keyCode===13){salvaTrocaSenha();}">
                        <span class="invalid-feedback"></span>
                    </div>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td style="text-align: center; padding-top: 5px;"><div id="mensagemTroca" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                            <td style="text-align: center; padding-top: 10px;"><input type="button" class="btn btn-primary resetbot" id="salvar" value="Salvar" onclick="salvaTrocaSenha();"></td>
                        </tr>
                    </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>