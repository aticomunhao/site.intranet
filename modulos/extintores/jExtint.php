<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script>
            new DataTable('#idTabela', {
                lengthMenu: [
                    [100, 200, 300],
                    [100, 200, 300]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                document.getElementById("guardaid").value = $id;
                carregaExtintor($id);
            });

        </script>
    </head>
    <body>
        <?php
        $TempoAviso  = parAdm("aviso_extint", $Conec, $xProj); // dias de antecedência para aviso
        $Condic = $xProj.".extintores.ativo = 1";
        $CompDesc = "";
         if(isset($_REQUEST["acao"])){
            $Acao = $_REQUEST["acao"];
        }else{
            $Acao = "ext_todos";
        }
        if($Acao == "ext_todos"){
            $Condic = $xProj.".extintores.ativo = 1";
            $CompDesc = "";
        }
        if($Acao == "ext_vencidos"){
            $Condic = $xProj.".extintores.ativo = 1 And datavalid <= CURRENT_DATE";
            $CompDesc = " vencido(s)";
        }
        if($Acao == "ext_vencer"){
            $Condic = $xProj.".extintores.ativo = 1 And datavalid BETWEEN CURRENT_DATE AND CURRENT_DATE+$TempoAviso";
            $CompDesc = " a vencer";
        }
        if($Acao == "ext_vencervencidos"){
            $Condic = $xProj.".extintores.ativo = 1 And datavalid <= CURRENT_DATE+$TempoAviso";
            $CompDesc = " vencido(s)";
        }

        $rs0 = pg_query($Conec, "SELECT ".$xProj.".extintores.id, ext_num, ext_local, desc_tipo, ext_capac, ext_reg, ext_serie, TO_CHAR(datacarga, 'DD/MM/YYYY'), TO_CHAR(datavalid, 'DD/MM/YYYY'), TO_CHAR(datacasco, 'DD/MM/YYYY'), ext_compl, 
        CASE WHEN datavalid BETWEEN CURRENT_DATE AND CURRENT_DATE+$TempoAviso THEN 'aviso' WHEN datavalid < CURRENT_DATE THEN 'vencido' END, CASE WHEN datavalid <= CURRENT_DATE THEN 'vencido' END 
        FROM ".$xProj.".extintores INNER JOIN ".$xProj.".extintores_tipo ON ".$xProj.".extintores.ext_tipo = ".$xProj.".extintores_tipo.id
        WHERE $Condic ORDER BY ext_num, ext_compl");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="margin-top: 70px; padding: 5px; border-top: 2px solid blue; border-radius: 10px;">
            <div style="text-align: center;">
                <label style="font-size: 80%;">
                    <?php
                    if($row0 == 0){
                        echo "Nenhum extintor".$CompDesc;
                    } 
                    if($row0 == 1){
                        echo "1 extintor".$CompDesc;
                    }
                    if($row0 > 1){
                        echo $row0." extintores".$CompDesc; 
                    }
                    ?>
                </label>
            </div>
            <table id="idTabela" class="display" style="width:99%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th style="display: none;"></th>
                        <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;">Número</th>
                        <th class="etiq" style="border-bottom: 1px solid gray; text-align: left;">Compl</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Tipo</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Capacidade</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;">Local</th>
                        <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;">Revisado</th>
                        <th class="etiq" style="border-bottom: 1px solid gray; text-align: center;">Vencimento</th>
                        <th class="etiq" style="border-bottom: 1px solid gray;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($tbl = pg_fetch_row($rs0)) {
                        $Cod = $tbl[0];
                        $DataRevis = $tbl[7];
                        if($DataRevis == "31/12/3000"){
                            $DataRevis = "";
                        }
                        $DataValid = $tbl[8];
                        if($DataValid == "31/12/3000"){
                            $DataValid = "";
                        }
                        $DataCasco = $tbl[9];
                        if($DataCasco == "31/12/3000"){
                            $DataCasco = "";
                        }
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $tbl[0]; ?></td>
                        <td style="border-bottom: 1px solid gray; text-align: center;"><?php echo str_pad($tbl[1], 3, 0, STR_PAD_LEFT); ?></td>
                        <td style="border-bottom: 1px solid gray; text-align: left; color: blue;"><?php echo $tbl[10]; ?></td>
                        <td style="border-bottom: 1px solid gray;"><?php echo $tbl[3]; ?></td>
                        <td style="border-bottom: 1px solid gray;"><?php echo $tbl[4]; ?></td>
                        <td style="border-bottom: 1px solid gray;"><?php echo $tbl[2]; ?></td>
                        <td style="border-bottom: 1px solid gray; text-align: center;"><?php echo $DataRevis; ?></td>
                        <td style="border-bottom: 1px solid gray; text-align: center;<?php if($tbl[11] == 'aviso'){echo 'color: #CD00CD; font-weight: bold;';}else if($tbl[11] == 'vencido'){echo 'color: red; font-weight: bold;';}else{echo 'font-weight: normal;';} ?>"><?php echo $DataValid; ?></td>
                        <td style="border-bottom: 1px solid gray; text-align: center;"><?php if($tbl[12] == 'vencido'){echo "<img src='imagens/oknao.png' title='Vencido'>";} ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>