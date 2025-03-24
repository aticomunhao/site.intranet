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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <style type="text/css">
            .modal-content-mudaSenha{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%; /* acertar de acordo com a tela */
            }
            .caixalog{
                padding: 20px; 
                margin: 0 auto; border: 2px solid red; border-radius: 10px;
            }   
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                document.getElementById("relacmudaSenha").style.display = "block";
                document.getElementById("senhaant").focus();
                document.getElementById("novasenha").disabled = true;
                document.getElementById("repetsenha").disabled = true;

                document.getElementById('olhoSecaoSenha1').addEventListener('mousedown', function(){
                  document.getElementById('senhaant').type = 'text';
                });
                document.getElementById('olhoSecaoSenha1').addEventListener('mouseup', function(){
                  document.getElementById('senhaant').type = 'password';
                });
                // Para que o password não fique exposto após mover a imagem.
                document.getElementById('olhoSecaoSenha1').addEventListener('mousemove', function(){
                  document.getElementById('senhaant').type = 'password';
                });

                document.getElementById('olhoSecaoSenha2').addEventListener('mousedown', function(){
                  document.getElementById('novasenha').type = 'text';
                });
                document.getElementById('olhoSecaoSenha2').addEventListener('mouseup', function(){
                  document.getElementById('novasenha').type = 'password';
                });
                // Para que o password não fique exposto após mover a imagem.
                document.getElementById('olhoSecaoSenha2').addEventListener('mousemove', function(){
                  document.getElementById('novasenha').type = 'password';
                });

                document.getElementById('olhoSecaoSenha3').addEventListener('mousedown', function(){
                  document.getElementById('repetsenha').type = 'text';
                });
                document.getElementById('olhoSecaoSenha3').addEventListener('mouseup', function(){
                  document.getElementById('repetsenha').type = 'password';
                });
                // Para que o password não fique exposto após mover a imagem.
                document.getElementById('olhoSecaoSenha3').addEventListener('mousemove', function(){
                  document.getElementById('repetsenha').type = 'password';
                });

                $('#senhaant').change(function(){ //confere a senha anterior ao digitar
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=confsenhaant&valor="+encodeURIComponent(document.getElementById("senhaant").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro desconhecido no servidor.");
                                }else{
                                    if(parseInt(Resp.confere) === 0){
                                        $('#mensagemTroca').fadeIn("slow");
                                        document.getElementById("mensagemTroca").innerHTML = "Senha não confere";
                                        document.getElementById("senhaant").value = "";
                                        document.getElementById("senhaant").focus();
                                        $('#mensagemTroca').fadeOut(3000);
                                        return false;
                                    }else{
                                        document.getElementById("novasenha").disabled = false;
                                        document.getElementById("repetsenha").disabled = false;
                                        document.getElementById("novasenha").focus();
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });
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
            function salvaMudaSenha(){
                if(document.getElementById("senhaant").value === ""){
                    return false;
                }
                if(document.getElementById("novasenha").value !== document.getElementById("repetsenha").value){
                    $('#mensagemTroca').fadeIn("slow");
                    document.getElementById("mensagemTroca").innerHTML = "As senhas são diferentes";
                    $('#mensagemTroca').fadeOut(5000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=mudasenha&senhaant="+encodeURIComponent(document.getElementById('senhaant').value)+"&novasenha="+encodeURIComponent(document.getElementById('novasenha').value)+"&repetsenha="+encodeURIComponent(document.getElementById('repetsenha').value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("textoMsg").innerHTML = "Senha modificada com sucesso.";
                                    document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                    document.getElementById("relacmudaSenha").style.display = "none";
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
                                    $('#mensagemTroca').fadeOut(5000);
                                    return false;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function fechamudaSenha(){
                document.getElementById("relacmudaSenha").style.display = "none";
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
        <input type="hidden" id="guarda_usulogado_id" value="<?php echo $_SESSION["usuarioID"]; ?>" />

        <div id="relacmudaSenha" class="relacmodal">  <!-- para trocar a senha inicial -->
            <div class="modal-content-mudaSenha">
                <span class="close" style="padding-right: 10px;" onclick="fechamudaSenha();">&times;</span>
                <div class="caixalog">
                    <h2><img src="imagens/Logo1.png" height="40px;">Nova Senha</h2>
                    <p>Mudança da senha de acesso: <?php echo $_SESSION["NomeCompl"]; ?></p>
                    <div>
                        <label style="padding-right: 3px;">Senha atual:</label><img id="olhoSecaoSenha1" style="cursor: pointer;" title="Mantenha clicado para visualizar a senha inserida." src="imagens/olhosenha.png" alt="" width="25" height="15" draggable="false">
<!--                        <input type="password" id="senhaant" class="form-control" value=""onkeypress="if(event.keyCode===13){javascript:foco('novasenha');return false;}"> -->
                        <input type="password" id="senhaant" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('salvar');return false;}">
                    </div>
                    <div style="padding-top: 5px;">
                        <label style="padding-right: 3px;">Nova senha</label><img id="olhoSecaoSenha2" style="cursor: pointer;" title="Mantenha clicado para visualizar a senha inserida." src="imagens/olhosenha.png" alt="" width="25" height="15" draggable="false">
                        <input type="password" id="novasenha" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('repetsenha');return false;}">
                        <span class="invalid-feedback"></span>
                    </div>

                    <div style="padding-top: 5px;">
                        <label style="padding-right: 3px;">Confirme a nova senha</label><img id="olhoSecaoSenha3" style="cursor: pointer;" title="Mantenha clicado para visualizar a senha inserida." src="imagens/olhosenha.png" alt="" width="25" height="15" draggable="false">
                        <input type="password" id="repetsenha" class="form-control" value=""  onkeypress="if(event.keyCode===13){salvaMudaSenha();}"">
                        <span class="invalid-feedback"></span>
                    </div>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td style="text-align: center; padding-top: 5px;"><div id="mensagemTroca" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                            <td style="text-align: center; padding-top: 10px;"><input type="button" class="btn btn-primary resetbot" id="salvar" value="Salvar" onclick="salvaMudaSenha();"></td>
                        </tr>
                    </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>