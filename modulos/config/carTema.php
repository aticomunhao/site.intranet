<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <style type="text/css">
            .corEscura{
                color: #FFFFFF;
                background-color: #101418;
            }
            .corClara{
                color: #000000;
                background-color: #FFFAFA;
            }
        </style>
        <script>
            LargTela = $(window).width(); // largura da tela ao abrir o módulo
            mudaTema(document.getElementById("guardaTema").value);

            function mudaTema(Valor){
                document.getElementById("guardaTema").value = Valor;
                if(parseInt(Valor) === 0){ // corClara
                    document.getElementsByTagName("body")[0].style.background = "#FFFAFA";
                    var element = document.getElementById("containerCabec");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");
                    var element = document.getElementById("container3");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");
                    var element = document.getElementById("tricoluna0");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");
                    var element = document.getElementById("tricoluna1");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");
                    var element = document.getElementById("tricoluna2");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");
                    var element = document.getElementById("tricoluna3");
                    element.classList.remove("corEscura");
                    element.classList.add("corClara");

                    if(document.getElementById("guardaPagina").value == "tarefas"){
                        var element = document.getElementById("faixaCentral");
                        element.classList.remove("corEscura");
                        element.classList.add("corClara");
                        var element = document.getElementById("menuTop1");
                        element.classList.remove("corEscura");
                        element.classList.add("corClara");
                        $("#faixaCentral").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                    }
                    if(document.getElementById("guardaPagina").value == "viaturas"){
                        var element = document.getElementById("container5");
                        element.classList.remove("corEscura");
                        element.classList.add("corClara");
                        var element = document.getElementById("container6");
                        element.classList.remove("corEscura");
                        element.classList.add("corClara");
                    }
                    if(document.getElementById("guardaPagina").value == "escala_daf"){
                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                    }
                }else{  // corEscura
                    document.getElementsByTagName("body")[0].style.background = "#101418";
                    var element = document.getElementById("containerCabec");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");
                    var element = document.getElementById("container3");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");
                    var element = document.getElementById("tricoluna0");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");
                    var element = document.getElementById("tricoluna1");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");
                    var element = document.getElementById("tricoluna2");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");
                    var element = document.getElementById("tricoluna3");
                    element.classList.remove("corClara");
                    element.classList.add("corEscura");

                    if(document.getElementById("guardaPagina").value == "tarefas"){
                        var element = document.getElementById("faixaCentral");
                        element.classList.remove("corClara");
                        element.classList.add("corEscura");
                        var element = document.getElementById("menuTop1");
                        element.classList.remove("corClara");
                        element.classList.add("corEscura");
                        $("#faixaCentral").load("modulos/conteudo/relTarefas.php?selec="+document.getElementById("guardaSelecSit").value+"&area="+document.getElementById("guardaSelecSetor").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                    }
                    if(document.getElementById("guardaPagina").value == "viaturas"){
                        var element = document.getElementById("container5");
                        element.classList.remove("corClara");
                        element.classList.add("corEscura");
                        var element = document.getElementById("container6");
                        element.classList.remove("corClara");
                        element.classList.add("corEscura");
                    }
                    if(document.getElementById("guardaPagina").value == "escala_daf"){
                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                    }
                }
                ajaxIni();
                if(ajax){ // guardar o valor individual
                    ajax.open("POST", "modulos/config/registr.php?acao=salvaTema&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro ao salvar.")
                                }else{
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
            $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

            if(isset($_REQUEST["carpag"])){ // vem da indexb.php ao clicar em tem mensagem nas Tarefas
                $Pag = $_REQUEST["carpag"];
            }else{
                $Pag = "";
            }
        ?>
        <input type="hidden" id="guardaTema" value="<?php echo $Tema; ?>" />
        <input type="hidden" id="guardaPagina" value="<?php echo $Pag; ?>" />
    </body>
</html>