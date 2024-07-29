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
        <script type="text/javascript">
            new DataTable('#idTabelaGr', { // zero configuration
                info: false,
                ordering: false,
                paging: false
            });

            table2 = new DataTable('#idTabelaGr');
            table2.on('click', 'tbody tr', function () {
                data = table2.row(this).data();
                $Cod = data[0];
                document.getElementById("guardacod").value = $Cod;
                if($Cod !== 0){
                    carregaModalGrupos($Cod);
                }
            });
            $(document).ready(function(){
                
            });

        </script>
    </head>
    <body> 
        <input type="hidden" id="guardacodsetor" value="0" /> <!-- quando carrega o modal -->
        <div style="border: 2px solid; border-radius: 15px; padding: 10px;">
            <div style="text-align: center;"><h4>Grupos que usam Escalas</h4></div>
            <div style="text-align: center;">Clique para editar<br>
            <label class="etiqAzul">As modificações feitas aqui são passadas para o módulo Escalas</label><br> 
        </div>
            <?php
                $rs0 = pg_query($Conec, "SELECT id, siglagrupo, descgrupo, qtd_turno, ativo FROM ".$xProj.".escalas_gr WHERE ativo = 1 ORDER BY siglagrupo");
            ?>
            <table id="idTabelaGr" class="display" style="width:85%">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="text-align: center; font-size: 80%;">Sigla</th>
                        <th style="text-align: center; font-size: 80%;">Descrição</th>
                        <th style="text-align: center; font-size: 80%;" title="Quantidade de turnos na escala">Turnos</th>
                        <th style="text-align: center; font-size: 80%;">Usuários</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                while ($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0]; // codocor
                    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $Cod");
                    $row1 = pg_num_rows($rs1);
                    ?>
                    <tr>
                        <td style="display: none;"><?php echo $Cod; ?></td>
                        <td><?php echo $tbl0[1]; ?></td>
                        <td><?php echo $tbl0[2]; ?></td>
                        <td style="text-align: center; font-size: 80%;"><?php echo $tbl0[3]; ?></td>
                        <td style="text-align: center; font-size: 80%;"><?php echo $row1; ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <br><br>
            <div style="text-align: center;"><button class="botpadrblue" onclick="inserirGrupo();">Inserir</button></div>
        </div>
        <br><br>
    </body>
</html>