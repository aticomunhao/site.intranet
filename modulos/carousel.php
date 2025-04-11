<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body>
        <?php
            require_once("config/abrealas.php");
            if(!$Conec){
                echo "Sem contato com o PostGresql";
            }
            $rs = pg_query($Conec, "SELECT * FROM information_schema.tables WHERE table_schema = 'cesb';");
            $row = pg_num_rows($rs);
            if($row == 0){
                die("<br>Faltam tabelas. Informe à ATI");
                return false;
            }
            //  o nome da imagem é modificado a cada mudança para contornar o cache de imagem
            $rs1 = pg_query($Conec, "SELECT descarq FROM ".$xProj.".carousel WHERE codcar = 1");
            $tbl1 = pg_fetch_row($rs1);
//            $Slide1 = $tbl1[0];
            if(file_exists("../imagens/slides/".$tbl1[0])){
                $Slide1 = $tbl1[0];
            }else{
                $Slide1 = "imgfundo0.jpg";
            }

            $rs2 = pg_query($Conec, "SELECT descarq FROM ".$xProj.".carousel WHERE codcar = 2");
            $tbl2 = pg_fetch_row($rs2);
            if(file_exists("../imagens/slides/".$tbl2[0])){
                $Slide2 = $tbl2[0];
            }else{
                $Slide2 = "imgfundo1.jpg";
            }

            $rs3 = pg_query($Conec, "SELECT descarq FROM ".$xProj.".carousel WHERE codcar = 3");
            $tbl3 = pg_fetch_row($rs3);
            if(file_exists("../imagens/slides/".$tbl3[0])){
                $Slide3 = $tbl3[0];
            }else{
                $Slide3 = "imgfundo2.jpg";
            }

            $rs4 = pg_query($Conec, "SELECT descarq FROM ".$xProj.".carousel WHERE codcar = 4");
            $tbl4 = pg_fetch_row($rs4);
            if(file_exists("../imagens/slides/".$tbl4[0])){
                $Slide4 = $tbl4[0];
            }else{
                $Slide4 = "imgfundo3.jpg";
            }
        ?>

        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
                <img src="imagens/slides/<?php echo $Slide1; ?>" height=200px; alt="" class="d-block w-100">
            </div>
            <div class="carousel-item">
                <img src="imagens/slides/<?php echo $Slide2; ?>" height=200px; alt="" class="d-block w-100">
            </div>
            <div class="carousel-item">
                <img src="imagens/slides/<?php echo $Slide3; ?>" height=200px; alt="" class="d-block w-100">
            </div>
            <div class="carousel-item">
                <img src="imagens/slides/<?php echo $Slide4; ?>" height=200px; alt="" class="d-block w-100">
            </div>
        </div>

        <!-- Controles à direita e esquerda -->
        <button class="carousel-control-prev" type="button" data-bs-target="#CorouselPagIni" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#CorouselPagIni" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </body>
</html>