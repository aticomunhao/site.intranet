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
        <style type="text/css">
            #container5{
                width: 55%;
                min-height: 500px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid blue; 
                border-radius: 10px; 
                padding: 10px;
            }
            #container6{
                width: 40%;
                min-height: 500px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid blue; 
                border-radius: 10px; 
                padding: 10px;
            }
            .cContainer{ /* encapsula uma frase no topo de uma div em reArq.php e pagArq.php */
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
            .modalPagDir{
                display: none; /* oculto default */
                position: fixed;
                z-index: 200;
                left: 0;
                top: 0;
                width: 100%; /* largura total */
                height: 100%; /* altura total */
                overflow: auto; /* autoriza scroll se necessário */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }
            .modalMsg-content-PagDir{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%; /* acertar de acordo com a tela */
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
            $(document).ready(function(){
                $("#relPagina").load("modulos/conteudo/relPag.php");
                $("#container6").load("modulos/conteudo/relArq.php");

                //Fecha caixa ao clicar na página
                modalHelp = document.getElementById('relacHelpPagDir'); //span[0]
                spanHelp = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalHelp){
                        modalHelp.style.display = "none";
                    }
                };    
            })
            function carregaHelpPagDir(){
                document.getElementById("relacHelpPagDir").style.display = "block";
            }
            function fechaModalHelp(){
                document.getElementById("relacHelpPagDir").style.display = "none";
            }
        </script>
    </head>
    <body>
        <?php
            $Dir = (int) filter_input(INPUT_GET, 'Diretoria');// número para selecionar o setor e só os arquivos do setor - atravessando para PagDir carrega RelArq
            $SubDir = (int) filter_input(INPUT_GET, 'Subdiretoria');

            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = $Dir");
            $Proc0 = pg_fetch_row($rs0);
            $Sigla = $Proc0[0];  // SiglaSetor
            $Descr = $Proc0[1];  // DescSetor

            if($SubDir > 1){
                $rs1 = mysqli_query($xVai, "SELECT SiglaSubSetor, DescSubSetor FROM cesb_subsetores WHERE CodSubSet = $SubDir");
                $Proc1 = mysqli_fetch_array($rs1);
                $Sigla = $Proc1["SiglaSubSetor"];
                $Descr = $Proc1["DescSubSetor"];
            }
            $_SESSION["PagDir"] = $Dir;
            $_SESSION["PagSubDir"] = $SubDir;
            $admEdit = parAdm("editPagina", $Conec, $xProj); // nível para editar
        
        ?>
        <div id="container5">
            <div class="cContainer corFundo"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer; padding-right: 10px;" onclick="carregaHelpPagDir();" title="Guia rápido"><?php echo $Descr." - ".$Sigla; ?></div>
            <?php
                if($_SESSION["CodSubSetorUsu"] == 1){ // está na diretoria
                    if($_SESSION["CodSetorUsu"] == $_SESSION["PagDir"] && $_SESSION["AdmUsu"] >= $admEdit){ // botão editar página
                        echo "<div class='bContainer corFundo' onclick='abreEdit()'> Editar </div>";
                    }
                }else{ // está em uma subdiretoria (sem uso)
                    if($_SESSION["CodSubSetorUsu"] == $_SESSION["PagSubDir"] && $_SESSION["AdmUsu"] >= $admEdit){ // retiradas as subdiretorias em 02/Out/23
                        echo "<div class='bContainer corFundo' onclick='abreEdit()'> Editar </div>";
                    }
                }
            ?>
            <div id="relPagina"></div>  <!-- div para mostrar a página do setor -->
        </div>
        <div id="container6"></div> <!--  containers 6 na página relArq.php -->  

        <!-- div modal para leitura instruções -->
        <div id="relacHelpPagDir" class="modalPagDir">
            <div class="modalMsg-content-PagDir">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h3 style="text-align: center; color: #666;">Informações</h3>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - As páginas das Diretorias e das Assessorias estão assim divididas em duas partes.</li>
                        <li>2 - A primeira parte foi concebida para informar o público interno sobre as atividades do setor.</li>
                        <li>3 - Esta primeira parte pode ser modificada a qualquer tempo por um usuário do setor com nível administrativo adequado.</li>
                        <li>4 - A segunda parte se destina a guardar arquivos de interesse da Diretoria ou Assessoria, que estarão sempre disponíveis para leitura ou download.</li>
                        <li>5 - Os arquivos de um setor só serão vistos por outros usuários do mesmo setor.</li>
                        <li>6 - O upload e a deleção dos arquivos só podem ser efetuados por usuários do setor com nível administrativo adequado.</li>
                        <li>7 - Se houver necessidade de enviar arquivos para outro setor ou outro usuário, utilize a opção disponível em Ferramentas -> Tráfego de Arquivos.</li>
                        <li>8 - Os arquivos colocados no Tráfego de Arquivos podem ser vistos por todos os usuários.</li>
                        <li>9 - A primeira parte destas páginas podem ser vistas por todos os usuários da intranet. O conteúdo da segunda parte só pode ser visto pelos usuários cadastrados no setor.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>