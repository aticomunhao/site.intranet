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
            new DataTable('#idTabela', { // zero configuration
                info: false,
                ordering: false,
                paging: false
            });

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[0];
                document.getElementById("guardacod").value = $id;
                if($id !== 0){
                    carregaModal($id);
                }
            });
        </script>
    </head>
    <body> 
        <input type="hidden" id="guardacodsetor" value="0" /> <!-- quando carrega o modal -->
        <div style="border: 2px solid; border-radius: 15px; padding: 10px; background: linear-gradient(180deg, white, #FFF8DC)">
            <div style="text-align: center;"><h4>Diretorias e Assessorias</h4></div>
            <div style="text-align: center;">Clique para editar<br>
            <label class="etiqAzul">As modificações feitas aqui são passadas para o menu das Diretorias e Assessorias</label><br> 
            <label class="etiqAzul">Os nomes dos usuários de cada setor são mostrados na edição</label>
        </div>
            <?php
                $rs0 = pg_query($Conec, "SELECT codset, siglasetor, descsetor, ativo FROM ".$xProj.".setores WHERE codset > 1 ORDER BY codset");
            ?>
            <table id="idTabela" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="text-align: center; font-size: 80%;">Sigla</th>
                        <th style="text-align: center; font-size: 80%;">Descrição</th>
                        <th style="text-align: center; font-size: 80%;" title="Não inclui administradores">Usuários</th>
                        <th style="text-align: center; font-size: 80%;" title="Não inclui usuários">Adm+</th>
                        <th style="text-align: center; font-size: 80%;">Ativo</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                while ($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0]; // codocor
                    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE ativo = 1 And codsetor = $Cod And adm < 4");
                    $row1 = pg_num_rows($rs1);
                    $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE ativo = 1 And codsetor = $Cod And adm >= 4 ");
                    $row2 = pg_num_rows($rs2);
                    ?>
                    <tr>
                        <td style="display: none;"><?php echo $Cod; ?></td>
                        <td><?php echo $tbl0[1]; ?></td>
                        <td><?php echo $tbl0[2]; ?></td>
                        <td style="text-align: center; font-size: 80%;"><?php echo $row1; ?></td>
                        <td style="text-align: center; font-size: 80%;"><?php echo $row2; ?></td>
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
                }
                ?>
                </tbody>
            </table>
            <br><br>
            <div style="text-align: center;"><button class="botpadrblue" onclick="insModalDir();">Inserir</button></div>
        </div>
        <br><br>

       <!-- div modal para editar diretorias  -->
        <div id="relacmodalDir" class="relacmodal">
            <div class="modal-content-Diretorias">
                <span class="close" onclick="fechaModalDir();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Edição de Diretorias e Assessorias</h5>
                <div style="margin-top: 10px; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width: 100%;">
                        <tr>
                            <td class="etiqAzul">Sigla:</td>
                            <td><input type="text" id="sigladir" style="width: 20%;" value="" onchange="modif();"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição:</td>
                            <td><input type="text" id="descdir" style="width: 100%;" value="" onchange="modif();"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"></td>
                            <td style="text-align: right:">
                                <label style="font-size: 12px;" title="Ativo ou inativo">Situação: </label>
                                <input type="radio" name="atividade" id="atividade1" value="1" title="Ativo no sistema" onclick="salvaAtivDir(value);"><label for="atividade1" style="font-size: 12px; padding-left: 3px;"> Ativa</label>
                                <input type="radio" name="atividade" id="atividade2" value="0" title="Bloqueado" onclick="salvaAtivDir(value);"><label for="atividade2" style="font-size: 12px; padding-left: 3px;"> Inativa</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><div id="mensagemDir" style="color: red; font-weight: bold; text-align: center;"></div></td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin: 5px;">
                        <button class="botpadrblue" onclick="salvaModalDir();">Salvar</button>
                    </div>
                </div>
                <div id="relausuarios" style="padding-left: 20px;"></div> <!-- Apresenta os usuários do setor com o nível administrativo -->
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>