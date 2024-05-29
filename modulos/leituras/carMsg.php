<!DOCTYPE html>
<html lang="pt-br">
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
            $Texto = "Usuário não cadastrado. <br>O acesso é proporcionado pela ATI.";
         }
         if($Msg == 2){
            $Texto = "Usuário sem acesso administrativo. <br>O acesso é proporcionado pela ATI.";
         }
        ?>
        <div style="text-align: center;">
        <table style="margin: 0 auto;">
            <tr>
                <td style="text-align: center; font-size: 80%;">
                <div style="margins: 10px; padding: 20px; text-align: center; border: 1px solid blue; border-radius: 15px;"><?php echo $Texto; ?> </div>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>