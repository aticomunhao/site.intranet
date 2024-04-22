<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
function cleanString($text) {
    $utf8 = array(
      '/[áàâãä]/u'    =>   'a',
      '/[ÁÀÂÃÄ]/u'    =>   'A',
      '/[ÍÌÎÏ]/u'     =>   'I',
      '/[íìîï]/u'     =>   'i',
      '/[éèêë]/u'     =>   'e',
      '/[ÉÈÊË]/u'     =>   'E',
      '/[óòôõºö]/u'   =>   'o',
      '/[ÓÒÔÕÖ]/u'    =>   'O',
      '/[úùûü]/u'     =>   'u',
      '/[ÚÙÛÜ]/u'     =>   'U',
      '/ç/'           =>   'c',
      '/Ç/'           =>   'C',
      '/ª/'           =>   'a',
      '/ñ/'           =>   'n',
      '/Ñ/'           =>   'N',
      '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
      '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
      '/[“”«»„]/u'    =>   ' ', // Double quote
      '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
 }
 
    $arquivo = $_FILES['arquivo'];  //recebe o arquivo do formulário

    //Verificar as extensões outra vez - já foi feito no custom.js - O js não está entendendo o MIME type pptx e ppsx - está sendo filtrado só aqui
    if($arquivo['type'] == 'application/pdf' || $arquivo['type'] == 'application/msword' || $arquivo['type'] == 'application/vnd.openxmlformats' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $arquivo['type'] == 'text/plain' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.slideshow'){
        // MIME docx = application/vnd.openxmlformats-officedocument.wordprocessingml.document
        // MIME pptx = application/vnd.openxmlformats-officedocument.presentationml.presentation 

        $DescSetor = "Sistema";
        // Seleciona o setor do usuário
//        $rs0 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".usuarios INNER JOIN ".$xProj.".setores ON ".$xProj.".usuarios.codsetor = ".$xProj.".setores.codset WHERE ".$xProj.".usuarios.id = ".$_SESSION["usuarioID"]." And codsubsetor = 1 ");

//Verificar onde estará o código do setor
        $rs0 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset 
        WHERE ".$xProj.".poslog.pessoas_id = ".$_SESSION["usuarioID"]." ");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $Proc0 = pg_fetch_row($rs0);
            $DescSetor = $Proc0[0];
        }else{   // Seleciona o subsetor do usuário
            $rs0 = pg_query($Conec, "SELECT siglasubsetor FROM ".$xProj.".usuarios INNER JOIN ".$xProj.".subsetores ON ".$xProj.".usuarios.codsubsetor = ".$xProj.".subsetores.codsubset WHERE ".$xProj.".usuarios.id = ".$_SESSION["usuarioID"]." And codsubsetor > 1 ");
            $Proc0 = mysqli_fetch_array($rs0);
            $DescSetor = $Proc0[0];               
        }

        $NomeArq = $_FILES['arquivo']['name']; // salvar no bd - pode ser usado para indexação
        $DescArq = uniqid()."-".$DescSetor."-".$_FILES['arquivo']['name'];
        $DescArq = cleanString($DescArq);
        $DescArq = str_replace("ã", "a", $DescArq);

        $Caminho = "arquivos/".$DescArq;
        if(move_uploaded_file($arquivo['tmp_name'], $Caminho)){  // realiza o uploud
            $rsCod = pg_query($Conec, "SELECT MAX(codarq) FROM ".$xProj.".arqsetor");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".arqsetor (codarq, codsetor, codsubsetor, descarq, nomearq, usuins) 
            VALUES ($CodigoNovo, ".$_SESSION["CodSetorUsu"].", ".$_SESSION["CodSubSetorUsu"].", '$DescArq', '$NomeArq', ".$_SESSION["usuarioID"].")"); // Salva no bd
            $_SESSION['msgarq'] = "Arquivo carregado com sucesso";
        }else{
            $_SESSION['msgarq'] = "O arquivo NÃO foi carregado";
        }
    }else{
        $_SESSION["msgarq"] = "Tipo de arquivo não permitido - Updload suspenso.";
    }