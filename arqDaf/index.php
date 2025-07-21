<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Arquivos DAF</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="imagens/Logo1.png" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" media="screen" href="../class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../class/superfish/css/superfish.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" /> <!-- depois do css do superfish porque a mudança de cores está aqui  -->
        <script src="../comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="../class/superfish/js/hoverIntent.js"></script>
        <script src="../class/superfish/js/superfish.js"></script>
        <script src="../class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/eventos.js"></script>
        <style>
            .tricol0{
                margin: 10px; padding: 20px; min-height: 300px;
            }
			@media (max-width: 742px){
                .divAniver{
                    border: 0;
                    margin-top: 90px;
                }
                #tricoluna3{
                    margin-top: 90px;
                }
			}
        </style>
        <script>
            $(document).ready(function(){
                //Carga inicial
                $('#container1').load('modulos/cabec.php');
                $('#container2').load('modulos/menuin.php?diasemana='+document.getElementById('guardadiasemana').value);
                $('#container4').load('modulos/rodape.php');
            });
        </script>
    </head>
    <body>
        <?php
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
            require_once("modulos/config/logform.php");
//            $diaSemana = 1;
        ?>
        <input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>
        <div id="container0" class="container-fluid"> <!-- página toda -->
            <div id="container1" class="container-fluid corFundo" style="margin-top: 3px;"></div> <!-- cabec.php banner superior dividido em 3 -->
            <div id="container2" class="container-fluid fontSiteFamily corFundoMenu-dia<?php echo $diaSemana; ?>"></div> <!-- Menu -->
            <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->

                <div id="tricoluna0" class="tricol0" style="height: 55vh;">
                    <div id="tricoluna1" class="box" style="position: relative; float: left; width: 30%;;"></div>
                    <div id="tricoluna2" class="box" style="position: relative; float: left; width: 2%; text-align: center;"></div> <!-- Separador -->
                    <div id="tricoluna3" class="box" style="position: relative; float: left; width: 68%; text-align: left;"></div>  <!-- Texto pág inicial -->
                </div>

                <!-- Rodapé -->
                <div>
                    <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>
                </div>
            </div>
        </div>
    </body>
</html>