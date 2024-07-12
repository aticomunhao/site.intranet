
<?php
session_start();
require_once("abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <?php
        $Menu1 = escMenu($Conec, $xProj, 1); // Comunhão
        $Menu2 = escMenu($Conec, $xProj, 2); // Claro
        $Menu3 = escMenu($Conec, $xProj, 3); // SBA
        $Menu4 = escMenu($Conec, $xProj, 4); // Controle Ar Cond 1
        $Menu5 = escMenu($Conec, $xProj, 5); // Controle Ar Cond 2
        $Menu6 = escMenu($Conec, $xProj, 6); // Controle Ar Cond 3
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td>...</td>
                <td><div class="fundoMenu">Controles</div></td>
                <td>...</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="padding-bottom: 10px;"><div class="fundoMenu">Água</div></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><div class="fundoMenu">Ar Condicionado</div></td>
                <td><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(4)"><?php echo $Menu4; ?></div></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(5)"><?php echo $Menu5; ?></div></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-bottom: 10px;"><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(6)"><?php echo $Menu6; ?></div></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><div class="fundoMenu">Eletricidade</div></td>
                <td><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(1)"><?php echo $Menu1; ?></div></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(2)"><?php echo $Menu2; ?></div></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><div class="fundoMenu bordaRed" title="Clique para editar" onclick="abreEditMenu(3)"><?php echo $Menu3; ?></div></td>
                <td></td>
            </tr>
        </table>
    </body>
</html>