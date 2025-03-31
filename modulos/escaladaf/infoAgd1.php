<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <?php
            if(isset($_REQUEST["mesano"])){
                $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
                $Texto = "Trabalhando na escala ".$Busca;
            }else{
                $Texto = "Nada foi encontrado";
            }   
        ?>
        <div style="text-align: center;">
        <table style="margin: 0 auto;">
            <tr>
                <td style="text-align: center; font-size: 120%;">
                <div style="margin: 10px; padding: 20px; text-align: center; border: 1px solid blue; border-radius: 15px;"><?php echo $Texto; ?> </div>
                </td>
            </tr>
        </table>
        </div>
    </body>
</html>