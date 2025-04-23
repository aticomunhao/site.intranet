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
        $Cumpr = "";
        if(isset($_REQUEST["cumpr"])){
            $Cumpr = $_REQUEST["cumpr"];
        }

        if($Msg == 1){
            $Texto = $Cumpr."<br>Usuário não cadastrado. <br>O acesso é proporcionado pela DAF/ATI.";
        }
        if($Msg == 2){
            $Texto = $Cumpr."<br>Usuário sem acesso administrativo. <br>O acesso é proporcionado pela DAF/ATI.";
        }
        ?>
        <div style="text-align: center;">
        <table style="margin: 0 auto;">
            <tr>
                <td style="text-align: center; font-size: 80%;">
                <div style="margin: 10px; padding: 20px; text-align: center; border: 1px solid blue; border-radius: 15px;"><?php echo $Texto; ?> </div>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>