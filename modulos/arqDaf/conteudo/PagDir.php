<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start();
if(!isset($_SESSION["usuarioCPF"])){
    header("Location: ../indexc.php");
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
                background: linear-gradient(180deg, white, #86c1eb);
                width: 15%;
                min-height: 500px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid blue; 
                border-radius: 10px; 
                padding: 10px;
            }
            #container6{
                width: 80%;
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
                width: 60%; /* acertar de acordo com a tela */
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
                $("#container6").load("conteudo/relArq.php");
            })
        </script>
    </head>
    <body>
        <?php
//            require_once(dirname(dirname(__FILE__))."/config/abrealasArqDaf.php");
            require_once("../config/abrealasArqDaf.php");
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            $Dir = (int) filter_input(INPUT_GET, 'Diretoria');// número para selecionar o setor e só os arquivos do setor - atravessando para PagDir carrega RelArq

            $rs0 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".daf_setores WHERE codset = $Dir");
            $Proc0 = pg_fetch_row($rs0);
            $Sigla = $Proc0[0];  // SiglaSetor
            $Descr = $Proc0[1];  // DescSetor
           $_SESSION["PagDirDaf"] = $Dir;
        ?>
        <div id="container5">
            <div class="cContainer corFundo"><img src="imagens/folder0.jpg" height="20px;" style="padding-right: 10px;" ><?php echo $Sigla; ?></div>
            <div style="text-align: center; margin-top: 140px;">
                <img src="imagens/folder0Tr<?php echo substr($Sigla, -2);?>.png" height="100px;">
            </div>
        </div>
        <div id="container6"></div> <!--  containers 6 na página relArq.php -->  
    </body>
</html>