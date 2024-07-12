<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style type="text/css">
        </style>
        <script type="text/javascript">
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT id, empresa, valorvisita FROM ".$xProj.".empresas_el WHERE ativo = 1 ORDER BY empresa");
            $row0 = pg_num_rows($rs0);
            ?>
            <table style="margin: 0 auto; width: 70%">
                <tr>
                    <td></td>
                    <td class="etiq aCentro">Relação de Empresas</td>
                    <td class="etiq aCentro">Preço Visita</td>
                </tr>
                <?php
                if($row0 > 0){
                    while($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0];
                        ?>
                        <tr>
                            <td></td>
                            <td class="aEsq" onclick="editaEmpresa(<?php echo $tbl0[0]; ?>)" title="Clique para editar"><?php echo "<div style='border: 1px solid; border-radius: 5px; padding-left: 2px;'>".$tbl0[1]."</div>"; ?></td>
                            <td class="aDir" onclick="editaEmpresa(<?php echo $tbl0[0]; ?>)" title="Clique para editar"><?php echo "<div style='border: 1px solid; border-radius: 5px; padding-right: 2px;'>".number_format($tbl0[2], 2, ",",".")."</div>"; ?></td>
                        </tr>
                        <?php
                    }
                }else{
                    ?>
                    <tr>
                        <td></td>
                        <td>Nada foi encontrado</td>
                        <td></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
    </body>
</html>