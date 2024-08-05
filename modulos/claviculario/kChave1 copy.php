<?php
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }

    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 

    $Clav = parEsc("clav", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
    $Chave = parEsc("chave", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
	$FiscClav = parEsc("fisc_clav", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves

?>
    <div style="text-align: center; margin: 10px;">
        <h5>Chaves Ausentes</h5>
        <?php 
        $rs = pg_query($Conec, "SELECT ".$xProj.".chaves_ctl.id, chaves_id, chavenum, chavenumcompl, chavesala, TO_CHAR(datasaida, 'DD/MM/YYYY HH24:MI'), funcentrega, usuretira  
        FROM ".$xProj.".chaves_ctl INNER JOIN ".$xProj.".chaves ON ".$xProj.".chaves_ctl.chaves_id = ".$xProj.".chaves.id 
        WHERE ".$xProj.".chaves.ativo = 1 And usudevolve = 0 And TO_CHAR(datavolta, 'YYYY') = '3000' ORDER BY datasaida ");
        $row = pg_num_rows($rs);

        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td style="display: none;"></td>
                <td class="etiq aCentro">Chave</td>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0]; // id de chaves_ctl
                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $Cod; ?></td>                          
                            <td>
                                <div style="border: 2px solid red; border-radius: 8px; padding: 5px; min-width: 300px;">
                                    <div class="quadrlista" style="border-color: #E90074; font-size: 130%;"> <?php echo str_pad($tbl[2], 3, 0, STR_PAD_LEFT)." ".$tbl[3]; ?></div>
                                    <div class="quadrlista"><label class="etiq" style="padding-bottom: 1px;">Sala: </label> <?php echo $tbl[4]; ?></div>

                                    <?php
                                    $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $tbl[7]; ");
                                    $row1 = pg_num_rows($rs1);
                                    if($row1 > 0){
                                        $tbl1 = pg_fetch_row($rs1);
                                        $Nome = $tbl1[1];
                                        if(is_null($tbl1[1]) || $tbl1[1] == ""){
                                            $Nome = $tbl1[0];
                                        }
                                    }else{
                                        $Nome = "";
                                    }
                                    ?>
                                    <br><br>
                                    <div class="quadrlista" style="text-align: left;"><label class="etiq">Retirada em </label> <?php echo $tbl[5]; ?></div>
                                    <div><input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Retorno" onclick="retornoChave(<?php echo $tbl[0]; ?>);"></div>
                                    <div class="quadrnomelista" style="text-align: left;"><label class="etiq">por: </label> <?php echo $Nome; ?></div>

                                </div>
                                <br>

                            </td> 
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 5px;"></td>   
                        </tr>
                        <?php
                    }
                }
            ?>
        </table>
        <br><br>
    </div>
    <br><br>