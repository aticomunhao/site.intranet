<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
   header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

   $Acao = filter_input(INPUT_GET, "acao");

   if($Acao == "ressetasession"){
      $Arq = filter_input(INPUT_GET, "arquivo"); // fechou o modal sem salvar
      if(file_exists("imagens/".$Arq)){
         $var = unlink("imagens/".$Arq) or die ("SemPerm");
      }
      $_SESSION['geremsg'] = 0;
      $_SESSION['arquivo'] = "";
      $responseText = json_encode($_SESSION['geremsg']);
      echo $responseText;
   }
   if($Acao == "guardaslide"){
      $Slide = filter_input(INPUT_GET, "slide"); // fechou o modal sem salvar
      $_SESSION['gerenum'] = $Slide;
      $responseText = json_encode($_SESSION['gerenum']);
      echo $responseText;
   }
   if($Acao == "acertaslide"){ // vem de gereSlide.php
      $Valor = (int) filter_input(INPUT_GET, "valor");
      $Slide = filter_input(INPUT_GET, "numslide");
      $Arq = filter_input(INPUT_GET, "arquivo");
      $Erro = 0;
      if($Valor == 0){ // apagar
         if(file_exists("imagens/".$Arq)){
            $var = unlink("imagens/".$Arq) or die ("SemPerm");
         }
      }
      if($Valor == 1){ //substituir
         $NovoArquivo = "imgfundo".uniqid().".jpg"; //mudar o nome da imagem sempre para contornar o cache
         pg_query($Conec, "UPDATE ".$xProj.".carousel SET descarqant = descarq, descarq = '$NovoArquivo' WHERE codcar = $Slide");
         if(copy("imagens/".$Arq, dirname(dirname(dirname(__FILE__)))."/imagens/slides/".$NovoArquivo)){
            unlink("imagens/".$Arq) or die ("SemPerm");

            //Apagar a imagem velha
            $rs0 = pg_query($Conec, "SELECT descarqant FROM ".$xProj.".carousel WHERE codcar = $Slide");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
               $tbl0 = pg_fetch_row($rs0);
               $ArqAnt = $tbl0[0];
               if(file_exists(dirname(dirname(dirname(__FILE__)))."/imagens/slides/".$ArqAnt)){
                  $var = unlink(dirname(dirname(dirname(__FILE__)))."/imagens/slides/".$ArqAnt) or die ("SemPerm");
               }
            }
         }else{
            $Erro = 1;
         }
      }
      $var = array("coderro"=>$Erro);
      $responseText = json_encode($var);
      echo $responseText;
   }