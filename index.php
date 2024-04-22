<?php
//session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>CEsB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="imagens/LogoComunhao.png" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/superfish/css/superfish.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" /> <!-- depois do css do superfish porque a mudança de cores está aqui  -->
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/superfish/js/hoverIntent.js"></script>
        <script src="class/superfish/js/superfish.js"></script>
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/eventos.js"></script>
        <script>
            $(document).ready(function(){
                //Carga inicial
                $('#container1').load('modulos/cabec.php');
                $('#container2').load('modulos/menuin.php?diasemana='+document.getElementById('guardadiasemana').value);
                $('#container4').load('modulos/rodape.php');
                $('#CorouselPagIni').load('modulos/carousel.php');
                
            });
        </script>
    </head>
    <body>
        <?php
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
            require_once("modulos/config/logform.php");
            $diaSemana = 1;
        ?>
        <input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>
        <div id="container0" class="container-fluid"> <!-- página toda -->
            <div id="container1" class="container-fluid corFundo"></div> <!-- cabec.php banner superior dividido em 3 -->
                <div id="container2" class="container-fluid fontSiteFamily corFundoMenu-dia<?php echo $diaSemana; ?>"></div> <!-- Menu -->
                <section style="height: 95vh;">
                    <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->
                        <!-- Carrosel  -->
                        <div id="CorouselPagIni" class="carousel slide carousel-fade" data-bs-ride="carousel" style="text-align: center;"></div>
                        <div id="container5" style="width: 25%;"> <!--  containers 5 e 6 dentro do container 3 -->
                            <div style="text-align: center; border: 2px solid blue; border-radius: 10px; padding: 10px; font-family: tahoma, arial, cursive, sans-serif;">
                            <span style="font-weight: bold;">Aniversariantes</span>
                            <?php
                                require_once("modulos/aniverIni.php");
                            ?>
                        </div>
                    </div>
                    <div id="container6" style="width: 70%; padding-left: 80px; padding-right: 100px;">
                        <div style="padding-left: 20px;">
                            <h3>Comunhão Espírita de Brasília</h3>
                        </div>
                        <br>
                        <div style="text-align: justify;">
                            <p>&nbsp; &nbsp; &nbsp; Casa Espírita de excelência na sua organização, na geração de conhecimento, na educação, na difusão doutrinária, na assistência espiritual e social, com estímulo à vivência cristã.</p>
                        </div>
                        <div style="text-align: center;">
                            <h5>Fora da caridade não há salvação.</h5>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Rodapé -->
            <div class="footer">
                <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>
            </div>
        </div>
    </body>
</html>