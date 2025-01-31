<?php
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 

    $Clav = parEsc("clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
    $Chave = parEsc("chave3", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
	$FiscClav = parEsc("fisc_clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves
?>
    <div style="text-align: center; margin: 10px;">
        <h5>Claviculário de Chaves Lacradas</h5>
        <?php 
        $rs = pg_query($Conec, "SELECT id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente 
        FROM ".$xProj.".chaves3 WHERE ativo = 1 ORDER BY chavenum, chavenumcompl ");
        $row = pg_num_rows($rs);
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td style="display: none;"></td>
                <td class="etiq aCentro">Chave</td>
                <td class="etiq aCentro">Sala</td>
                <td class="etiq aCentro">Nome</td>
                <td class="etiq aCentro">Local</td>
                <td class="etiq aCentro">Obs</td>
                <td class="etiq aCentro"></td>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0]; // id de quadrohor
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td>
                                <?php
                                if($FiscClav == 1 || $_SESSION["AdmUsu"]  > 6){
                                    ?>
                                    <div class="quadrinhoClick" onclick="editaChave(<?php echo $Cod; ?>);" title="Clique para editar."> <?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></div>
                                    <?php
                                }else{
                                    ?>
                                    <div class="quadrinho"> <?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></div>
                                    <?php
                                }
                                ?>
                            </td>
                            <td><div class="quadrinho"> <?php echo $tbl[4]; ?></div></td>
                            <td><div class="quadrinho"> <?php echo $tbl[2]; ?></div></td>
                            <td><div class="quadrinho"> <?php echo $tbl[3]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%;"> <?php echo $tbl[5]; ?></div></td>
                            <td class="etiq aCentro">
                                <?php
                                if($tbl[6] == 1){
                                    echo "<img src='imagens/ChaveAzul.png' height='20px;' style='cursor: pointer;' onclick='saidaChave($Cod);' title='Chave presente'>";
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