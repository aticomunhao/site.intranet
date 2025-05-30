<?php
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 

    $ClavEdit = parEsc("clav_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
    $Clav = parEsc("clav", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução

    if(isset($_REQUEST["usuario"])){
        $Usu = $_REQUEST["usuario"];
    }else{
        $Usu = 0;
    }

    $rsMarc = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves_aut WHERE pessoas_id = $Usu And ativo = 1");
    $rowMarc = pg_num_rows($rsMarc); // conta chaves marcadas

    $rs = pg_query($Conec, "SELECT id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente, chavecompl 
    FROM ".$xProj.".chaves WHERE ativo = 1 ORDER BY chavenum, chavenumcompl ");
    $row = pg_num_rows($rs);

    $Todas = 0;
    if($rowMarc == $row){
        $Todas = 1; // todas as chaves marcadas
    }
    ?>
    <div style="text-align: center; margin: 10px;">
        <div id="chavesmarcadas" style="position: relative; float: left; width: 45%; text-align: left; font-size: 80%;"><?php echo "Marcadas: ".$rowMarc; ?></div>
        <div style="position: relative; float: right; width: 45%; text-align: right; font-size: 80%;"><label style="font-size: 80%;"><?php echo "Claviculário: ".$row." chaves"; ?></label></div>
        <table style="margin: 0 auto;">
            <tr>
                <td style="display: none;"></td>
                <td class="etiq aCentro;" style="min-width: 50px;">
                    <div style="text-align: center; border: 1px solid; border-radius: 5px;">Marcar<br><input type="checkbox" id="checkGeral" <?php if($Todas == 1){echo "CHECKED";} ?> title="Marcar todas as chaves" onclick="marcaChaveTodas(this);" ><br>Todas</div>
                </td>
                <td class="etiq aCentro bordaInf" title="Número da chave">Chave</td>
                <td class="etiq aCentro bordaInf">Sala</td>
                <td class="etiq aCentro bordaInf">Nome Sala</td>
                <td class="etiq aCentro bordaInf">Local</td>
                <td class="etiq aCentro bordaInf">Obs</td>
                <td class="etiq aCentro bordaInf">Seg</td>
                <td class="etiq aCentro bordaInf">Ter</td>
                <td class="etiq aCentro bordaInf" style="color: blue;">Qua</td>
                <td class="etiq aCentro bordaInf">Qui</td>
                <td class="etiq aCentro bordaInf">Sex</td>
                <td class="etiq aCentro bordaInf" style="color: red;">Sab</td>
                <td class="etiq aCentro bordaInf" style="color: red;">Dom</td>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0];
                        $rs1 = pg_query($Conec, "SELECT id, seg, ter, qua, qui, sex, sab, dom 
                        FROM ".$xProj.".chaves_aut WHERE ativo = 1 And chaves_id = $Cod And pessoas_id = $Usu ");
                        $row1 = pg_num_rows($rs1);
                        $tbl1 = pg_fetch_row($rs1);
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td>
                                <input type="checkbox" <?php if($row1 > 0){echo "CHECKED";} ?> title="Marcar chave para retirar na portaria" onchange="marcaChaveInd(this, <?php echo $Cod; ?>);" >
                            </td>
                            <td><div class="quadrinho"> <?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT)." ".$tbl[7]; ?></div></td>
                            <td><div class="quadrinho"> <?php echo $tbl[4]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%; text-align: left;"> <?php echo $tbl[2]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 80%; text-align: left;"> <?php echo $tbl[3]; ?></div></td>
                            <td><div class="quadrinho" style="font-size: 70%; text-align: left;"> <?php echo $tbl[5]; ?></div></td>

                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[1] == 1){echo "CHECKED";} ?> title="2ª Feira" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'seg', 1);" ></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[2] == 1){echo "CHECKED";} ?> title="3ª Feira" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'ter', 2);" ></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[3] == 1){echo "CHECKED";} ?> title="4ª Feira" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'qua', 3);" style="outline: 1px solid blue;"></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[4] == 1){echo "CHECKED";} ?> title="5ª Feira" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'qui', 4);" ></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[5] == 1){echo "CHECKED";} ?> title="6ª Feira" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'sex', 5);" ></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[6] == 1){echo "CHECKED";} ?> title="Sábado" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'sab', 6);" style="outline: 1px solid red;"></td>
                            <td><input type="checkbox" <?php if($row1 > 0 && $tbl1[7] == 1){echo "CHECKED";} ?> title="Domingo" onchange="marcaChaveSemana(this, <?php echo $Cod; ?>, <?php echo $row1; ?>, 'dom', 0);" style="outline: 1px solid red;"></td>
                        </tr>
                        <tr>
                            <td colspan="14"><hr style="margin: 0; padding: 0;"></td>
                        </tr>
                        <?php
                    }
                }
            ?>
        </table>
        <br><br>
    </div>