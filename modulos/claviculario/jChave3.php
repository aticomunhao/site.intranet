<?php
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 

    $ClavEdit = parEsc("clav_edit3", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
    $Clav = parEsc("clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução

    $rs = pg_query($Conec, "SELECT id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente, chavecompl 
    FROM ".$xProj.".chaves3 WHERE ativo = 1 ORDER BY chavenum, chavenumcompl ");
    $row = pg_num_rows($rs);

?>
    <div style="text-align: center; margin: 10px;">
        <div style="position: relative; float: right;"><label style="font-size: 80%;"><?php echo $row." chaves"; ?></label></div>
        <h5>Claviculário de Chaves Lacradas</h5>
        <table style="margin: 0 auto;">
            <tr>
                <td style="display: none;"></td>
                <td class="etiq aCentro" title="Número da chave">Chave</td>
                <td class="etiq aCentro">Sala</td>
                <td class="etiq aCentro">Nome Sala</td>
                <td class="etiq aCentro">Local</td>
                <td class="etiq aCentro">Obs</td>
                <td class="etiq aCentro" title="Número lacre">Codigo</td>
                <td class="etiq aCentro"></td>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0];
                        $Presente = $tbl[6];
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td>
                                <?php
                                if($ClavEdit == 1 || $_SESSION["AdmUsu"]  > 6){
                                    ?>
                                    <div class="quadrinhoClick" onclick="editaChave(<?php echo $Cod; ?>);" title="Clique para editar."> <?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></div>
                                    <?php
                                }else{
                                    ?>
                                    <div class="quadrinho" title="Número da chave"> <?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td><div class="quadrinho" title="Sala"> <?php echo $tbl[4]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%; text-align: left;" title="Nome da Sala"> <?php echo $tbl[2]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%; text-align: left;" title="Local"> <?php echo $tbl[3]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%; text-align: left;" title="Observações"> <?php echo $tbl[5]; ?></div></td>
                            <td><div class="quadrinho" title="Complemento ao número da chave"> <?php echo $tbl[7]; ?></div></td>
                            <td class="etiq aCentro">
                                <?php
                                if($Presente == 1){
                                    if($Clav == 1 || $ClavEdit == 1){
                                        echo "<img src='imagens/ChaveAzul.png' height='20px;' style='cursor: pointer;' onclick='saidaChave($Cod);' title='Chave presente'>";
                                    }else{
                                        echo "<img src='imagens/ChaveAzul.png' height='20px;' title='Chave presente'>";
                                    }
                                }else{
                                    echo "<img src='imagens/ChaveVerm.png' height='20px;' style='cursor: pointer;' onclick='retornoChave1($Cod);' title='Chave ausente'>";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            ?>
        </table>
        <br><br>
    </div>