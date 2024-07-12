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
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .modal-content-Mes{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }

            .quadrodia {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .quadrinho {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .quadroEscolha {
                position: relative; float: left; 
                min-height: 35px;
                border: 1px solid; border-radius: 5px; 
                text-align: center; 
                padding: 5px; width: 40px;
                cursor: pointer;
            }
            .modalEngr{
                display: none; /* Hidden por default */
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
            .modalEngr-content{
                background-color: transparent;
                margin: 20% auto; /* 15% do topo e centrado */
                text-align: center;
                width: 10%; /* acertar de acordo com a tela */
            }
        </style>
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
                $("#selecGrupo").change(function(){
                    document.getElementById("selectMes").style.display = "block";
                });

                
   

            }); // fim do ready



            
        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg

        $Ini = strtotime(date('Y/m/01')); // número - para começar com o dia 1
        $DiaIni = strtotime("-1 day", $Ini); // para começar com o dia 1 no loop for


        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="mudou" value = "0" />
        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">

        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
            <div class="row" style="text-align: center;">
                <div class="col" style="text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;">
                    <label>Selecione o grupo: </label>
                    <select id="selecGrupo" style="font-size: 1rem; width: 90px;" title="Selecione o grupo.">
                        <option value="0"></option>
                        <option value="1">Grupo 1</option>
                        <option value="2">Grupo 2</option>
                    </select>
                </div>
                <div class="col" style="text-align: center;"><h4>Escalas</h4></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col" style="text-align: right; margin: 5px; width: 95%; padding: 2px;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpBens();" title="Guia rápido"></div>
            </div>
      
            <br>
            <div id="selectMes" class="container" style="margin: 0 auto; display: none;">
                <label>Selecione o mês: </label>
                <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
                    <option value="1"></option>
                    <option value="2">07/2024</option>
                    <option value="3">08/2024</option>
                    <option value="4">09/2024</option>
                </select>
            </div>




        </div>


        <div id="engrenagem" class="modalEngr" style="display: none;">
         <div class="modalEngr-content">
            <img src="imagens/Engrenagens.gif" width="70" height="50" draggable="false" style="padding-top: 2px; padding-left: 2px;"/>
         </div>
      </div>

    </body>
</html>