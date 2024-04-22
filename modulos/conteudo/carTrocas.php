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
        <div style='margin: 20px; border: 3px solid green; border-radius: 10px;'>
            <table style="margin: 0 auto; border: 0; width: 80%;" >
                <?php
                $_SESSION["itrArq"] = "";// guarda o nome da imagem icorporada em postAceptor.php
                require_once(dirname(dirname(__FILE__))."/dbclass.php");
                $admEdit = (int) filter_input(INPUT_GET, 'admEdit'); // vem de relTrocas.php

                $rs = mysqli_query($xVai, "SELECT idTr, idUser, idSetor, textoTroca, date_format(cesb_trocas.DataIns, '%d/%m/%Y') AS DataInsert, SiglaSetor, DescSetor FROM cesb_trocas INNER JOIN cesb_setores ON cesb_trocas.idSetor = cesb_setores.CodSet WHERE trocaAtiva = 1 ORDER BY cesb_trocas.DataIns DESC");
                echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                echo "</tr>";         
                $row = mysqli_num_rows($rs);
                if($row > 0){
                    While ($tbl = mysqli_fetch_array($rs)){
                        $Cod = $tbl["idTr"];
                        $idSetor = $tbl["idSetor"];
                        $LadoEsq = $tbl["DataInsert"]."<br>".$tbl["SiglaSetor"];
                        echo "<tr>";
                            echo "<td style='width: 30px; texto-align: center;'><div style='margin: 10px; text-alig: center; padding: 10px; border: 1px solid blue; border-radius: 20px;'>$LadoEsq</div>";
                            if($idSetor == $_SESSION["CodSetorUsu"] && $_SESSION["AdmUsu"] >= $admEdit){
                                echo"<div style='padding-left: 20px; padding-right: 20px;'>&nbsp;<div class='iContainer' style='width: 70%; font-size: .9rem;' onclick='abreEdit($Cod)'> Editar </div><div class='delContainer' data-bs-toggle='modal' data-bs-target='#deletaModal' onclick='guardaCod($Cod)'> &#10008; </div></div>";
                            }
                            echo "</td>";
                            echo "<td style='width: 80%;'>".$tbl["textoTroca"]."</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td colspan='2'><hr></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
    </body>
</html>