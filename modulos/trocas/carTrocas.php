<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
    </head>
    <body> 
        <div style='margin: 20px; padding-top: 5px; border: 3px solid green; border-radius: 15px;'>
            <table style="margin: 0 auto; border: 0; width: 80%;" >
                <?php
                $_SESSION["itrArq"] = "";// guarda o nome da imagem icorporada em postAceptor.php
                require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
                $admEdit = (int) filter_input(INPUT_GET, 'admEdit'); // vem de relTrocas.php

                $rs = pg_query($Conec, "SELECT idtr, iduser, idsetor, textotroca, to_char(".$xProj.".trocas.datains, 'DD/MM/YYYY'), siglasetor, descsetor FROM ".$xProj.".trocas INNER JOIN ".$xProj.".setores ON ".$xProj.".trocas.idsetor = ".$xProj.".setores.codset WHERE trocaativa = 1 ORDER BY ".$xProj.".trocas.datains DESC");
                echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                echo "</tr>";         
                $row = pg_num_rows($rs);
                if($row > 0){
                    While ($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0];
                        $idSetor = $tbl[2];
                        $LadoEsq = $tbl[4]."<br>".$tbl[5];
                        echo "<tr>";
                            echo "<td style='width: 30px; texto-align: center;'><div style='margin: 10px; text-alig: center; padding: 10px; border: 2px solid blue; border-radius: 20px;'>$LadoEsq</div>";
                            if($idSetor == $_SESSION["CodSetorUsu"] && $_SESSION["AdmUsu"] >= $admEdit || $_SESSION["AdmUsu"] > 6){
                                echo"<div style='padding-left: 20px; padding-right: 20px;'>&nbsp;<div class='iContainer' style='width: 70%; font-size: 1.1rem;' onclick='abreEdit($Cod)' title='Editar anúncio'> Editar </div><div class='modalConfirm' data-bs-toggle='modal' data-bs-target='#deletaModal' onclick='guardaCod($Cod)' title='Apagar anúncio'> &#10008; </div></div>";
                            }
                            echo "</td>";
                            echo "<td style='width: 80%; '><div style='margin: 10px; padding: 5px; border: 2px solid blue; border-radius: 15px;'>".$tbl[3]."</div></td>";
                        echo "</tr>";
                        
         
                    }
                }
                ?>
            </table>
        </div>
    </body>
</html>