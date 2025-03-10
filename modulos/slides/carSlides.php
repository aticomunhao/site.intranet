<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            //  o nome da imagem é modificado a cada mudança para contornar o cache de imagem
            $rs1 = pg_query($Conec, "SELECT codcar, descarq FROM ".$xProj.".carousel WHERE codcar = 1");
            $tbl1 = pg_fetch_row($rs1);
            $Slide1 = $tbl1[1]; //descarq

            $rs2 = pg_query($Conec, "SELECT CodCar, descArq FROM ".$xProj.".carousel WHERE CodCar = 2");
            $tbl2 = pg_fetch_row($rs2);
            $Slide2 = $tbl2[1];

            $rs3 = pg_query($Conec, "SELECT CodCar, descArq FROM ".$xProj.".carousel WHERE CodCar = 3");
            $tbl3 = pg_fetch_row($rs3);
            $Slide3 = $tbl3[1];

            $rs4 = pg_query($Conec, "SELECT CodCar, descArq FROM ".$xProj.".carousel WHERE CodCar = 4");
            $tbl4 = pg_fetch_row($rs4);
            $Slide4 = $tbl4[1];
        ?>

        <div class="mostraslide">
            <img src="imagens/slides/<?php echo $Slide1; ?>" width=250px; height=125px; alt="slide1">
            <div>
                <input type="radio" name="trocaslide" id="trocaslide0" value="1" title="Substituir Slide" onclick="Subst(value);">
                <label for="trocaslide0" class="etiq">Substituir Slide 1</label>
            </div>
        </div>
        <div class="mostraslide">
            <img src="imagens/slides/<?php echo $Slide2; ?>" width=250px; height=125px; alt="slide2">
            <div>
                <input type="radio" name="trocaslide" id="trocaslide1" value="2" title="Substituir Slide" onclick="Subst(value);">
                <label for="trocaslide1" class="etiq">Substituir Slide 2</label>
            </div>
        </div>
        <div class="mostraslide">
            <img src="imagens/slides/<?php echo $Slide3; ?>" width=250px; height=125px; alt="slide3">
            <div>
                <input type="radio" name="trocaslide" id="trocaslide2" value="3" title="Substituir Slide" onclick="Subst(value);">
                <label for="trocaslide2" class="etiq">Substituir Slide 3</label>
            </div>
        </div>
        <div class="mostraslide">
            <img src="imagens/slides/<?php echo $Slide4; ?>" width=250px; height=125px; alt="slide4">
            <div>
                <input type="radio" name="trocaslide" id="trocaslide3" value="4" title="Substituir Slide" onclick="Subst(value);">
                <label for="trocaslide3" class="etiq">Substituir Slide 4</label>
            </div>
        </div>

    </body>
</html>