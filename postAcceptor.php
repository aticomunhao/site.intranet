<?php
session_start();
// filtrar imagens 

   function redimencionarImagemGIF($imagem){
      $imagem_original = imagecreatefromgif($imagem);
      //Salva o tamanho antigo da imagem
      list($largura_antiga, $altura_antiga) = getimagesize($imagem);
      $Taxa = ($largura_antiga/$altura_antiga);
      $Largura = 400;
      $Altura = round(($Largura/$Taxa));
      //Cria uma nova imagem com o tamanho indicado
      $imagem_tmp = imagecreatetruecolor($Largura, $Altura);
      //Faz a interpolação da imagem base com a imagem original
      imagecopyresampled($imagem_tmp, $imagem_original, 0, 0, 0, 0, $Largura, $Altura, $largura_antiga, $altura_antiga);
      //Salva a nova imagem
      $resultado = imagejpeg($imagem_tmp, $imagem);
      // Libera memoria
      imagedestroy($imagem_original);
      imagedestroy($imagem_tmp);
//      return $resultado;
   }
   function redimencionarImagemJPG($imagem){
      // Cria um identificador para nova imagem
      $imagem_original = imagecreatefromjpeg($imagem);
      list($largura_antiga, $altura_antiga) = getimagesize($imagem);
      $Taxa = ($largura_antiga/$altura_antiga);
      $Largura = 400;
      $Altura = round(($Largura/$Taxa));
      // Cria uma nova imagem com o tamanho indicado
      $imagem_tmp = imagecreatetruecolor($Largura, $Altura);
      // Faz a interpolação da imagem base com a imagem original
      imagecopyresampled($imagem_tmp, $imagem_original, 0, 0, 0, 0, $Largura, $Altura, $largura_antiga, $altura_antiga);
      // Salva a nova imagem
      $resultado = imagejpeg($imagem_tmp, $imagem);
      // Libera memoria
      imagedestroy($imagem_original);
      imagedestroy($imagem_tmp);
//      return $resultado;
   }
   function redimencionarImagemPNG($imagem){
      // Cria um identificador para nova imagem
      $imagem_original = imagecreatefrompng($imagem);
      // Salva o tamanho antigo da imagem
      list($largura_antiga, $altura_antiga) = getimagesize($imagem);
      $Taxa = ($largura_antiga/$altura_antiga);
      $Largura = 400;
      $Altura = round(($Largura/$Taxa));
      // Cria uma nova imagem com o tamanho indicado - Esta imagem servirá de base para a imagem a ser reduzida
      $imagem_tmp = imagecreatetruecolor($Largura, $Altura);
      $background = imagecolorallocate($imagem_tmp , 0, 0, 0);  // removing the black from the placeholder
      imagecolortransparent($imagem_tmp, $background);
      imageAlphaBlending($imagem_tmp, false);
      imageSaveAlpha($imagem_tmp, true);
      // Faz a interpolação da imagem base com a imagem original
      imagecopyresampled($imagem_tmp, $imagem_original, 0, 0, 0, 0, $Largura, $Altura, $largura_antiga, $altura_antiga);
      // Salva a nova imagem
      $resultado = imagepng($imagem_tmp, $imagem);
      // Libera memoria
      imagedestroy($imagem_original);
      imagedestroy($imagem_tmp);
//      return $resultado;
   }

   $imageFolder =  "itr/";
   $Sigla = $_SESSION["SiglaSetor"];  // OM do usuário
   reset ($_FILES);
   $temp = current($_FILES);
   if (is_uploaded_file($temp['tmp_name'])){
//      if (isset($_SERVER['HTTP_ORIGIN'])) {
//         if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
//            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
//         }else{
//            header("HTTP/1.1 403 Origin Denied");
//            return;
//         }
//      }
//      if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
//         header("HTTP/1.1 400 Nome do arquivo inválido. Não use caracteres especiais.");
//         return;
//      }
//      if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
//         header("HTTP/1.1 400 Extensão do arquivo inválida. Use arquivos png, jpg ou gif");
//         return;
//      }

 
   $Num = uniqid();
   $_SESSION["itrArq"] = $Sigla."-".$Num."-".$temp['name'];

   $filetowrite = $imageFolder.$Sigla."-".$Num."-".$temp['name'];
      move_uploaded_file($temp['tmp_name'], $filetowrite);
      $Extensao = pathinfo($filetowrite, PATHINFO_EXTENSION);
      list($width, $height) = getimagesize($filetowrite);
         if($width > 400){ // diminuir o tamanho da imagem
            if($Extensao == "png"){
                redimencionarImagemPNG($filetowrite);
            }
            if($Extensao == "jpg"){
               redimencionarImagemJPG($filetowrite);
            }
            if($Extensao == "gif"){
               redimencionarImagemGIF($filetowrite);
            }
         }

//      echo json_encode(array('location'=>$filetowrite, 'httporig'=>$_SERVER['HTTP_ORIGIN']));
      echo json_encode(array('location'=>$filetowrite));
   }else{
      header("HTTP/1.1 500 Erro no servidor");
   }