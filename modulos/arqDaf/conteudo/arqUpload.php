<?php
session_name("arqAdm");
session_start();
date_default_timezone_set('America/Sao_Paulo'); 
require_once("../config/abrealasArqDaf.php");
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

    //Verificar as extensões pelo tipo MIME
    if($arquivo['type'] == 'application/pdf' || $arquivo['type'] == 'application/msword' || $arquivo['type'] == 'application/vnd.openxmlformats' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $arquivo['type'] == 'text/plain' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $arquivo['type'] == 'image/jpeg' || $arquivo['type'] == 'image/png' || $arquivo['type'] == 'image/webp' || $arquivo['type'] == 'application/x-zip-compressed'){

        $NomeArq = $_FILES['arquivo']['name']; // salvar no bd - pode ser usado para indexação
        $DescArq = uniqid()."-".$_FILES['arquivo']['name'];
        $DescArq = cleanString($DescArq);
        $DescArq = str_replace("ã", "a", $DescArq);

        $Caminho = dirname(dirname(__FILE__))."/arquivos/".$DescArq;
        if(move_uploaded_file($arquivo['tmp_name'], $Caminho)){  // realiza o uplouad
            $rsCod = pg_query($Conec, "SELECT MAX(codarq) FROM ".$xProj.".daf_arqsetor");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 

            $usuarioID = arqDafAdm("pessoas_id", $Conec, $xProj, $_SESSION["usuarioCPF"]);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".daf_arqsetor (codarq, codsetor, descarq, nomearq, ativo, usuins, datains) 
            VALUES ($CodigoNovo, ".$_SESSION["PagDirDaf"].", '$DescArq', '$NomeArq', 1, $usuarioID, NOW() )"); // Salva no bd
            $_SESSION['msgarqDaf'] = "Arquivo carregado com sucesso";
        }else{
            $_SESSION['msgarqDaf'] = "O arquivo NÃO foi carregado";
        }
    }else{
        $_SESSION["msgarqDaf"] = "Tipo de arquivo não permitido - Updload suspenso.";
    }