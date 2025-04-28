<?php
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }

    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 

    $ClavEdit = parEsc("clav_edit2", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
    $Clav = parEsc("clav2", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
    $Chave = parEsc("chave2", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
	$FiscClav = parEsc("fisc_clav2", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves

    //formata CNPJ e CPF em máscaras.
    function Mask($mask,$str){
        $str = str_replace(" ","",$str);
        for($i=0;$i<strlen($str);$i++){
            $mask[strpos($mask,"#")] = $str[$i];
        }
        //Chamada Mask("###.###.###-##",$Var) cpf
        //Chamada Mask("##.###.###/####-##",$Var) cnpj
        return $mask;
    }

    ?>
    <div style="text-align: center; margin: 5px;">
        <h5>Agenda</h5>
        <?php 
        $rs = pg_query($Conec, "SELECT ".$xProj.".chaves2_agd.id, chaves_id, chavenum, chavenumcompl, chavesala, TO_CHAR(datasaida, 'DD/MM/YYYY'), usuretira, cpfretira, telef, chavelocal, datasaida, chavecompl 
        FROM ".$xProj.".chaves2_agd INNER JOIN ".$xProj.".chaves2 ON ".$xProj.".chaves2_agd.chaves_id = ".$xProj.".chaves2.id 
        WHERE ".$xProj.".chaves2.ativo = 1 And ".$xProj.".chaves2_agd.ativo = 1 ORDER BY datasaida ");
        $row = pg_num_rows($rs);
        $Hoje = date('Y/m/d');
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $CodAg = $tbl[0];
                $CodChaves = $tbl[1]; // chaves_id de chaves2_ctl
                ?>
                <div style="border: 2px solid #CFB53B; border-radius: 8px; padding: 5px;">
                    <table style="margin: 0 auto; width:95%">
                        <tr>          
                            <td colspan="2"><div class="quadrlista" style="border-color: #E90074; font-size: 120%;"> <?php echo str_pad($tbl[2], 3, 0, STR_PAD_LEFT)." ".$tbl[11]; ?></div>
                            <div class="quadrlista"><label class="etiq">Sala: </label> <?php echo $tbl[4]; ?></div>
                            <div class="quadrlista" style="border: 0px;"></div>
                        </td>
                        </tr>
                        <tr>              
                            <td colspan="2"> 
                                <?php
                                if(strtotime($tbl[10]) < strtotime($Hoje)){
                                    echo "<div class='quadrlista' style='text-align: left; color: red; border-color: red;'><label class='etiq' style='color: red;' title='Data expirada.'>Agendada: </label> $tbl[5] </div>";
                                }else{
                                    echo "<div class='quadrlista' style='text-align: left;'><label class='etiq'>Agendada: </label> $tbl[5] </div>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>              
                            <td colspan="2">
                                <?php
                                $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual, siglasetor 
                                FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset
                                WHERE pessoas_id = $tbl[6]; ");
                                $row1 = pg_num_rows($rs1);
                                if($row1 > 0){
                                    $tbl1 = pg_fetch_row($rs1);
//                                    $Nome = $tbl1[1];
//                                    if(is_null($tbl1[1]) || $tbl1[1] == ""){
//                                        $Nome = $tbl1[0];
//                                    }
                                    $Nome = $tbl1[0];
                                    $SiglaSetor = $tbl1[2];
                                }else{
                                    $Nome = "";
                                }
                                ?>
                                <div class="quadrlista" style="text-align: left;"><label class="etiq">para: </label> <?php echo $Nome; ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><div class="quadrlista"><label class="etiq">CPF: </label><?php echo Mask("###.###.###-##",$tbl[7]); ?></div></td>
                        </tr>
                        <tr>
                            <td colspan="2"><div class="quadrlista"><label class="etiq">Telef: </label><?php echo $tbl[8]; ?></div></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <?php
                                    if($ClavEdit == 1){
                                        echo "<img src='imagens/lixeiraPreta.png' height='20px;' style='cursor: pointer; padding-right: 3px;' onclick='apagaAgendaChaves($CodAg);' title='Apagar este agendamento.'>";
                                    }
                                    if(strtotime($tbl[10]) == strtotime($Hoje)){
                                        ?>
                                        <input type="button" id="botinserir" class="resetbot fundoAmareloCl" style="font-size: 80%;" value="Registrar Entrega" onclick="saidaChaveAgenda(<?php echo $CodAg; ?>, <?php echo $CodChaves; ?>, <?php echo $tbl[6]; ?>, '<?php echo $tbl[5]; ?>');">
                                        <?php
                                    }else{
                                        ?>
                                        <input disabled type="button" id="botinserir" class="resetbot fundoAmareloCl" style="font-size: 80%;" value="Registrar Entrega" >
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 5px;"></td>   
                        </tr>
                    </table>
                </div>
                <br>
                <?php
            }
        }else{
            ?>
            <div style="text-align: center; padding-left: 5px; padding-rigth: 5px;"><label class="etiq">Nenhum Agendamento </label> </div>
            <?php
        }
        ?>
    </div>