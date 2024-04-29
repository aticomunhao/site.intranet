<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <style type="text/css">
            .etiq{
                text-align: right; color: #036; font-size: 80%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
        </style>
        <script type="text/javascript">
            function PegaSrc(Arq){
                document.getElementById("guardasrc").value = Arq;
            }
        </script>
    </head>
    <body>
        <div class="cContainer corFundo">Ideogramas</div>
        <div style="margin-top: 10px;">
            <?php
            $folder = "imagens/";
            $files = scandir($folder); // ordem alfabética normal  // $files = scandir($folder, 1); // ordem alfabética descendente
            $file_array = array();
            foreach ($files as $file) {
               $data = date("Y/m/d H:i:s", filectime($folder.$file)); // Data em que foi inserido no servidor
               $file_array[] = array($data, $file);
            }
            sort($file_array); // ordena pela data em que a imagem foi carregada
            unset($file_array[0]); // eliminando o '.' e '..' 
            unset($file_array[1]);

            foreach ($file_array as $file){
                $NomeArq = $file[1]; 
                ?>
                <div draggable="true" class="ideogr">
                    <img src="modulos/ocorrencias/imagens/<?php echo $NomeArq; ?>" width="50px" height="50px" ondrag="PegaSrc('<?php echo $NomeArq; ?>')">
                </div>
                <?php
            }
            ?>
        </div>
        <input type="hidden" id="guardasrc" value="0" />
    </body>
</html>