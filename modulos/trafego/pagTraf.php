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
    </head>
    <body>
        <?php
            date_default_timezone_set('America/Sao_Paulo'); //  echo date("l, d/m/Y");
            $data = date('Y-m-d');
            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona - começa no domingo, dia 0
//$diaSemana = 7;
        ?>
            <div style="border-radius: 20px; text-align: center; width: 100%; height: 500px; background-image: url('modulos/trafego/imagens/folder<?php echo $diaSemana; ?>.jpg'); background-repeat: no-repeat; background-position-x: center; background-position-y: center;">
                <h1>Arquivos</h1>
                <h4>Espaço para troca de arquivos</h4>
                <h5>entre usuários.</h5>
            </div>

    </body>
</html>