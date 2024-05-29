<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <style type="text/css">
            .relacmsg{
                display: none; /* oculto default */
                position: fixed;
                z-index: 210;
                left: 0;
                top: 0;
                width: 100%; /* largura total */
                height: 100%; /* altura total */
                overflow: auto; /* autoriza scroll se necessário */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }
            .modal-content-Msg{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 20% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 45%; /* acertar de acordo com a tela */
            }
            /* Botão fechar */
            .close{
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                text-align: right;
            }
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>
        <script type="text/javascript">
//            $(document).ready(function(){
//            })
            function fechaMensagem(Cod){
                if(parseInt(Cod) === 1){
                    document.getElementById("relacmensagem").style.display = "none";
                }
                if(parseInt(Cod) === 2){
                    document.getElementById("infomensagem").style.display = "none";
                }
            }
        </script>
    </head>
    <body>
        <div id="relacmensagem" class="relacmsg">
            <div class="modal-content-Msg">
                <span class="close" onclick="fechaMensagem(1);">&times;</span>
                <br />
                <h2><img src="imagens/LogoComunhao.png" height="40px;">Sucesso</h2>
                <div id="textoMsg" style="text-align: center; font-weight: bold; padding: 10px;"></div>
            </div>
        </div>
        <div id="infomensagem" class="relacmsg">
            <div class="modal-content-Msg">
                <span class="close" onclick="fechaMensagem(2);">&times;</span>
                <br />
                <h5><img src="imagens/LogoComunhao.png" height="30px;"><label id="textoTitulo" style="padding-left: 5px;">Informação</label></h5>
                <div id="textoInfo" style="text-align: center; font-weight: bold; padding: 10px;"></div>
            </div>
        </div>
    </body>
</html>