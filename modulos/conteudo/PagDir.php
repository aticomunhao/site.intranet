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
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#relPagina").load("modulos/conteudo/relPag.php");
                $("#container6").load("modulos/conteudo/relArq.php");
            })
        </script>
    </head>
    <body>
        <?php
            $Dir = (int) filter_input(INPUT_GET, 'Diretoria');// para selecionar o setor e só os arquivos do setor - atravessando para PagDir carrega RelArq
            $SubDir = (int) filter_input(INPUT_GET, 'Subdiretoria');
            $SiglaDir = filter_input(INPUT_GET, 'SiglaDir');// para selecionar o setor e só os arquivos do setor - atravessando para PagDir carrega RelArq


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
            $_SESSION["SiglaPagDir"] = $SiglaDir;

            $admEdit = parAdm("editPagina", $Conec, $xProj); // nível para editar
        
        ?>
        <div id="container5">
            <div class="cContainer corFundo"><?php echo $Descr." - ".$Sigla; ?></div>
            <?php
                if($_SESSION["CodSubSetorUsu"] == 1){ // está na diretoria
                    if($_SESSION["CodSetorUsu"] == $_SESSION["PagDir"] && $_SESSION["AdmUsu"] >= $admEdit){ // botão editar página
//                    if($_SESSION["SiglaSetor"] == $_SESSION["SiglaPagDir"] && $_SESSION["AdmUsu"] >= $admEdit){ // botão editar página
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
    </body>
</html>