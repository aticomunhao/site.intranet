<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style type="text/css">
            .etiqCab{
               text-align: center; color: #808080; font-size: 80%; font-weight: bold; padding-right: 1px; padding-bottom: 1px; border-bottom: 1px solid;
            }
            .etiqDados{
               font-size: 90%; padding-left: 6px; padding-right: 6px; border-bottom: 1px solid;
            }
        </style>

        <script>
            function MarcaEV(CodTar){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=marcaTransf&codigo="+CodTar, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
        </script>

    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            ?>
            <table style="margin: 0 auto;">
                <tr>
                    <td colspan="6" style="text-align: center; font-weight: bold;">- Tarefas Expedidas -</td>
                </tr>
                <tr>
                    <td style="display: none;"></td>
                    <td></td>
                    <td class="etiqCab">Executante</td>
                    <td class="etiqCab">Tarefa</td>
                    <td class="etiqCab">Prioridade</td>
                    <td class="etiqCab">Situação</td>
                </tr>

                <?php
                $rs = pg_query($Conec, "SELECT idtar, tittarefa, usuexec, prio, sit, marca FROM ".$xProj.".tarefas WHERE ativo = 1 And usuins = ".$_SESSION["usuarioID"]." and sit < 4 ");
                $row = pg_num_rows($rs);
                if($row > 0){
                    While ($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0];  // idtar
                        $Tarefa = nl2br($tbl[1]); 
                        $UsuExec = $tbl[2];
                        $Prio = $tbl[3];
                        $Sit = $tbl[4];
                        $Marca = $tbl[5];
                        $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $UsuExec "); 
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $NomeExec = $tbl1[1];
                            if(is_null($tbl1[1] || $tbl1[1] == "")){
                                $NomeExec = $tbl1[0];
                            }
                        }else{$NomeExec = "";}

                        if($Prio == 0){
                            $DescPrio = "Urgente";
                        }
                        if($Prio == 1){
                            $DescPrio = "Muito Importante";
                        }
                        if($Prio == 2){
                            $DescPrio = "Importante";
                        }
                        if($Prio == 3){
                            $DescPrio = "Normal";
                        }
                        if($Sit == 1){
                            $DescSit = "Designada";
                        }
                        if($Sit == 2){
                            $DescSit = "Recebida";
                        }
                        if($Sit == 3){
                            $DescSit = "Andamento";
                        }

                        ?>
                        <tr>
                            <td class="etiqDados" style="text-align: center;">
                                <input type="checkbox" name="ev" value="ev" id="ev" title="marca para transferir." onClick="MarcaEV(<?php echo $Cod ?>);" <?php if($Marca == 1) {echo "checked";} ?> >
                            </td>
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td class="etiqDados"><?php echo $NomeExec; ?></td>
                            <td class="etiqDados" style="min-width: 300px;"><?php echo $Tarefa; ?></td>
                            <td class="etiqDados" style="text-align: center;"><?php echo $DescPrio; ?></td>
                            <td class="etiqDados" style="text-align: center;"><?php echo $DescSit; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php
                }else{
                    ?>
                    <tr>
                        <td colspan="6" style="text-align: center; font-weight: bold;">- Nenhuma tarefa expedida. -</td>
                    </tr>
                    <?php
                }
                ?>
        </table>
    </body>
</html>