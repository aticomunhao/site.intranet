<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <style></style>
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
                document.getElementById("botinserir").style.visibility = "hidden";
                document.getElementById("botimpr").style.visibility = "hidden";
                document.getElementById("botagenda1").style.visibility = "hidden";
                document.getElementById("botagenda2").style.visibility = "hidden";
                document.getElementById("imgChavesconfig").style.visibility = "hidden";
                
                if(parseInt(document.getElementById("registrachaves").value) === 1 || parseInt(document.getElementById("editachaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
                    $("#faixamostra").load("modulos/claviculario/kChave1.php?acao=todos");
                    $("#faixaagenda").load("modulos/claviculario/agChave1.php?acao=todos");
                    if(parseInt(document.getElementById("editachaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ 
                        document.getElementById("botinserir").style.visibility = "visible";
                        document.getElementById("botimpr").style.visibility = "visible";
                        document.getElementById("botagenda1").style.visibility = "visible";
                        document.getElementById("botagenda2").style.visibility = "visible";
                        document.getElementById("imgChavesconfig").style.visibility = "visible";
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }

                $('#carregaTema').load('modulos/config/carTema.php?carpag=clavic1');


            });

        </script>

    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        ?>

        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />

        
        <!-- div três colunas -->
        <div id="tricoluna0" style="margin: 10px; padding: 10px; border: 2px solid; border-radius: 10px; min-height: 52px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 17%;">
                <img src="imagens/settings.png" height="20px;" id="imgChavesconfig" style="cursor: pointer; padding-right: 30px;" onclick="abreChavesConfig();" title="Configurar o acesso às chaves no claviculário da Portaria">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir Nova Chave" onclick="insChave();">
            </div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 55%; text-align: center;">
                <h5>Controle de Chaves Portaria</h5>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: left; width: 25%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 8px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-right: 5px;"></label>
                </div>
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="abreImprChaves();">PDF</button>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                <br><br><br>Usuário não cadastrado.
            </div>
        </div>




        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>