<?php
session_start();
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
            <div id="container3" class="container-fluid corFundo"> <!-- corpo da página -->
                <!-- Carrosel  -->
                <div id="CorouselPagIni" class="carousel slide carousel-fade" data-bs-ride="carousel" style="text-align: center;">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#CorouselPagIni" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#CorouselPagIni" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#CorouselPagIni" data-bs-slide-to="2"></button>
                        <button type="button" data-bs-target="#CorouselPagIni" data-bs-slide-to="3"></button>
                    </div>

                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="5000">
                            <img src="imagens/slides/imgfundo0.jpg" height=250px; alt="Animado" class="d-block w-100">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="mb-0" style="color: black;">Comunhão Espírita de Brasília</h5>
                                <p class="mb-0" style="color: black;">Fora da caridade não há salvação.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="imagens/slides/imgfundo1.jpg" height=250px; alt="" class="d-block w-100">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="mb-0">Comunhão Espírita de Brasília</h5>
                                <p class="mb-0">Fora da caridade não há salvação.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="imagens/slides/imgfundo2.jpg" height=250px; alt="" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img src="imagens/slides/imgfundo3.jpg" height=250px; alt="" class="d-block w-100">
                        </div>
                    </div>

                    <!-- Left and right controls/icons -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#CorouselPagIni" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#CorouselPagIni" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>

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
                    <br><br>
                    <div style="text-align: justify;">
                        <p>&nbsp; &nbsp; &nbsp; A Casa Espírita de excelência na sua organização, na geração de conhecimento, na educação, na difusão doutrinária, na assistência espiritual e social, com estímulo à vivência cristã.</p>
                    </div>
                    <div style="text-align: center;">
                        <h4>Fora da caridade não há salvação.</h4>
                    </div>
                </div>
            </div>
            <!-- Rodapé -->
            <div id="container4" class="container-fluid corFundoMenu-dia<?php echo $diaSemana; ?>"></div>
        </div>
    </body>
</html>