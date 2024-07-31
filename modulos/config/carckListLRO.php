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
            new DataTable('#idTabelaLRO', { // zero configuration
                info: false,
                ordering: false,
                paging: false
            });

            tableck = new DataTable('#idTabelaLRO');
            tableck.on('click', 'tbody tr', function () {
                data = tableck.row(this).data();
                $id = data[0];
                document.getElementById("guardacod").value = $id;
                if($id !== 0){
                    carregaCheckList($id);
                }
            });

        </script>
    </head>
    <body> 
        <input type="hidden" id="guardacodsetor" value="0" /> <!-- quando carrega o modal -->
        <div style="border: 2px solid; border-radius: 15px; padding: 10px; background: linear-gradient(180deg, white, #9eecb9); ">
            <div style="text-align: center;"><h4>Lista de Verificação</h4></div>
            <div style="text-align: center;">Passagem de Serviço na Portaria<br></div>
            <?php
                $rs0 = pg_query($Conec, "SELECT id, setor, itemverif, ativo FROM ".$xProj.".livrocheck WHERE setor = 1 ORDER BY itemverif ");
            ?>
            <table id="idTabelaLRO" class="display" style="width:85%">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="text-align: left; font-size: 80%;"></th>
                        <th style="text-align: left; font-size: 80%;">Item</th>
                        <th style="text-align: center; font-size: 80%;">Ativo</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $Item = 1;
                while ($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0]; // id
                    if($Item < 10){
                        $Item = "0".$Item;
                    }
                    ?>
                    <tr>
                        <td style="display: none;"><?php echo $Cod; ?></td>
                        <td style="padding-left: 5px;"><?php echo $Item; ?></td>
                        <td><?php echo $tbl0[2]; ?></td>
                        <td style="text-align: center; font-size: 80%;">
                        <?php 
                        if($tbl0[3] == 1){
                            echo "<img src='imagens/ok.png' height='15px;' title='Ativa'>";
                        }else{
                            echo "<img src='imagens/oknao.png' height='15px;' title='Inativa'>";
                        }
                        ?>
                        </td>
                    </tr>
                    <?php
                    $Item++;
                }
                ?>
                </tbody>
            </table>
            <br><br>
            <div style="text-align: center;"><button class="botpadrblue" onclick="insModalCkList();">Inserir</button></div>
        </div>
        <br><br>


       <!-- div modal para editar cklist  -->
        <div id="relacmodalCkList" class="relacmodal">
            <div class="modal-content-ckListLRO">
                <span class="close" onclick="fechaModalckList();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Edição do Checklist - LRO</h5>
                <div style="margin-top: 10px; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width: 100%;">
                        <tr>
                            <td class="etiqAzul">Item:</td>
                            <td><input type="text" id="descitem" style="width: 100%;" value="" onchange="modif();"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"></td>
                            <td style="text-align: right:">
                                <label style="font-size: 12px;" title="Ativo ou inativo">Situação: </label>
                                <input type="radio" name="atividadecklist" id="atividadecklist1" value="1" title="Ativo no sistema" onchange="salvaAtivCkList(value);"><label for="atividadecklist1" style="font-size: 12px; padding-left: 3px;"> Ativo</label>
                                <input type="radio" name="atividadecklist" id="atividadecklist2" value="0" title="Bloqueado" onchange="salvaAtivCkList(value);"><label for="atividadecklist2" style="font-size: 12px; padding-left: 3px;"> Inativo</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><div id="mensagemCkList" style="color: red; font-weight: bold; text-align: center;"></div></td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin: 5px;">
                        <button class="botpadrblue" onclick="salvaModalCkList();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>