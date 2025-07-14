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
    $Chave = parEsc("chave", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
	$FiscClav = parEsc("fisc_clav", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves

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
    <div style="text-align: center; margin: 10px;">
        <h5>Chaves Ausentes</h5>
        <?php 
        $Hoje = date('Y/m/d');
        $rs = pg_query($Conec, "SELECT ".$xProj.".chaves_ctl.id, chaves_id, chavenum, chavenumcompl, chavesala, TO_CHAR(datasaida, 'DD/MM/YYYY HH24:MI'), funcentrega, usuretira, cpfretira, telef, chavelocal, 
        datasaida, chavecompl 
        FROM ".$xProj.".chaves_ctl INNER JOIN ".$xProj.".chaves ON ".$xProj.".chaves_ctl.chaves_id = ".$xProj.".chaves.id 
        WHERE ".$xProj.".chaves.ativo = 1 And ".$xProj.".chaves_ctl.ativo = 1 And usudevolve = 0 ORDER BY datasaida ");
        // And TO_CHAR(datavolta, 'YYYY') = '3000'

        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0]; // id de chaves_ctl
                $IdChave = $tbl[1];
                ?>
                <div style="border: 2px solid red; border-radius: 8px; padding: 5px;">
                    <table style="margin: 0 auto; width:95%">
                        <tr>          
                            <td colspan="2"><div class="quadrlista" style="border-color: #E90074; font-size: 130%;"> <?php echo str_pad($tbl[2], 3, 0, STR_PAD_LEFT)." ".$tbl[12]; ?></div>
                            <div class="quadrlista"><label class="etiq">Sala: </label> <?php echo $tbl[4]; ?></div>
                        </td>
                        </tr>
                        <tr>              
                            <td colspan="2">
                                <?php
                                if(strtotime($tbl[11]) < strtotime($Hoje)){
                                    echo "<div class='quadrlista' style='text-align: left; color: red; border-color: red;'><label class='etiq' style='color: red;'>Retirada em </label><label style='font-weight: bold;'> $tbl[5] </label></div>";
                                }else{
                                    echo "<div class='quadrlista' style='text-align: left;'><label class='etiq'>Retirada em </label><label style='font-weight: bold;'> $tbl[5] </label></div>";
                                }
                                ?>
                                <div><input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Retorno" onclick="retornoChave(<?php echo $tbl[0]; ?>);">
                                <?php
                                   if($_SESSION["AdmUsu"] > 6){
                                        echo "<img src='imagens/lixeiraPreta.png' height='20px;' style='cursor: pointer;' onclick='apagaSaidaChave($Cod, $IdChave);' title='Apagar este lançamento.'>";
                                    }
                                ?>
                                </div>
                            </td>
                        </tr>
                        <tr>              
                            <td>
                                <?php
                                $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual, siglasetor 
                                FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset
                                WHERE pessoas_id = $tbl[7]; ");
                                $row1 = pg_num_rows($rs1);
                                if($row1 > 0){
                                    $tbl1 = pg_fetch_row($rs1);
//                                    $Nome = $tbl1[1];
//                                    if(is_null($tbl1[1]) || $tbl1[1] == ""){
//                                        $Nome = substr($tbl1[0], 0, 30);
//                                    }
                                    $Nome = $tbl1[0];
                                    $SiglaSetor = $tbl1[2];
                                }else{
                                    $Nome = "";
                                    $SiglaSetor = "";
                                }
                                ?>
                                <div class="quadrlista" style="text-align: left;"><label class="etiq">por: </label> <?php echo $Nome; ?></div>
                            </td>
                            <td>
                                <?php
                                    if($SiglaSetor != ""){
                                        ?>
                                            <div class="quadrlista" title="Sigla setor"><?php echo $SiglaSetor; ?></div>
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><div class="quadrlista"><label class="etiq">CPF: </label><?php echo Mask("###.###.###-##",$tbl[8]); ?></div><div class="quadrlista"><label class="etiq">Telef: </label><?php echo $tbl[9]; ?></div></td>
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
            <div style="text-align: center; padding-left: 5px; padding-rigth: 5px;"><label class="etiq">Tudo em ordem</label> </div>
            <?php
        }
        ?>
        <br><br>
    </div>
<br><br>