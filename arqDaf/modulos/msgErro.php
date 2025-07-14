<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <?php
        if(isset($_REQUEST["msgtipo"])){
            $Msg = (int) $_REQUEST["msgtipo"];
        }else{
            $Msg = 1;
        }
         if($Msg == 1){
            $Texto = "Sem contato com os arquivos do sistema. <br>Informe à ATI.";
         }
        ?>

        <div style="margin: 100px; text-align: center;">
            <table style="margin: 0 auto;">
                <tr>
                    <td>
                       <div style="margin: 10px; padding: 20px; text-align: center; border: 2px solid blue; border-radius: 15px;"><img src="./imagens/Logo1.png" height="40px;"></div>
                    </td>                     
                    <td style="text-align: center; font-size: 80%;">
                        <div style="margin: 10px; padding: 20px; text-align: center; border: 2px solid blue; border-radius: 15px;"><?php echo $Texto; ?> </div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>