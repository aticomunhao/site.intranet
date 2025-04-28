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
            new DataTable('#idTabelaUsu', {
                lengthMenu: [
                    [100, 200, 500],
                    [100, 200, 500]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            tableUsu = new DataTable('#idTabelaUsu');
            tableUsu.on('click', 'tbody tr', function () {
                let data = tableUsu.row(this).data();
                $id = data[2];//
                document.getElementById("guardaid_click").value = $id;

                let PegaCpf = data[1]; // erro no linux atravessando cpf formatado
                Cpf1 = PegaCpf.replace(".", ""); // replaceAll ainda não funciona no internet explorer
                Cpf2 = Cpf1.replace(".", "");
                Cpf3 = Cpf2.replace("-", "");
                document.getElementById("guardaid_cpf").value = Cpf3;

                if($id !== ""){
                    if(parseInt(document.getElementById("UsuAdm").value) < 7){  // superusuário
                        if(parseInt(document.getElementById("UsuAdm").value) > 3 && parseInt(document.getElementById("admEditUsu").value) === 1){ // adminisetrador 
                            if(document.getElementById("guardaSiglaSetor").value === data[4]){ // sigla do usuário = sigla do administrador logado
                                document.getElementById("setor").disabled = true; // congela a escolha do setor
                                carregaModal($id);
                            }else{
                                document.getElementById("textoMsg").innerHTML = "Não pertence ao setor.";
                                document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                setTimeout(function(){
                                    document.getElementById("relacmensagem").style.display = "none";
                                }, 2000);
                            }
                        }
                    }else{
                        carregaModal($id);
                    }
                }
            });

        </script>
    </head>
    <body>
        <?php
            function formatCnpjCpf($value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                $CPF_LENGTH = 11;
                $cnpj_cpf = preg_replace("/\D/", '', $value);
                if (strlen($cnpj_cpf) === $CPF_LENGTH) {
                    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
                } 
                return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
            }

            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "todos";
            }
            $rsT = pg_query($Conec, "SELECT pessoas_id FROM ".$xProj.".poslog WHERE ativo = 1"); 
            $Total = pg_num_rows($rsT);

            $Condic = "id != 0 And nomecompl IS NOT NULL ";
            if($Acao == "online"){
                $Condic = "ativo = 1 And EXTRACT(EPOCH FROM (NOW() - logfim)) <= 60 ";
            }
            if($Acao == "dehoje"){
                $Condic = "ativo = 1 And TO_CHAR(logini, 'YYYY/MM/DD') = TO_CHAR(CURRENT_DATE, 'YYYY/MM/DD') ";
            }
            if($Acao == "inativos"){
                $Condic = "ativo = 0 ";
            }
            $rs0 = pg_query($Conec, "SELECT pessoas_id, cpf, nomeusual, nomecompl FROM ".$xProj.".poslog WHERE $Condic ORDER BY nomecompl"); 
            $row0 = pg_num_rows($rs0);

            $rs1 = pg_query($Conec, "SELECT pico_dia, to_char(data_pico_dia, 'DD/MM/YYYY'), pico_online, to_char(data_pico_online, 'DD/MM/YYYY HH24:MI') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $PicoDia = $tbl1[0];
                $DataPicoDia = $tbl1[1];
                $PicoOnLine = $tbl1[2];
                $DataPicoOnLine = $tbl1[3];
            }else{
                $PicoDia = "";
                $DataPicoDia = "";
                $PicoOnLine = "";
                $DataPicoOnLine = "";
            }
            $rs2 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".poslog WHERE ativo = 1 And TO_CHAR(logini, 'YYYY/MM/DD') = TO_CHAR(CURRENT_DATE, 'YYYY/MM/DD')");
            $tbl2 = pg_fetch_row($rs2);
            $TotDia = $tbl2[0];

            $rs3 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".poslog WHERE ativo = 1 And EXTRACT(EPOCH FROM (NOW() - logfim)) <= 60");
            $tbl3 = pg_fetch_row($rs3);
            $TotOnLine = $tbl3[0];

            ?>
              <div class="box" style="position: relative; float: left; width: 33%; text-align: left;">
                <table>
                    <tr>
                        <td style="font-size: 80%; color: green;">Total de Usuários:</td>
                        <td style="font-size: 80%; color: green; text-align: right; padding-left: 3px; min-width: 15px;"><?php echo $Total; ?></td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; padding-left: 10px;"></td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; min-width: 15px;"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-size: 80%; color: green;">Usuários do Dia:</td>
                        <td style="font-size: 80%; color: green; text-align: right; min-width: 15px;"><?php echo $TotDia; ?></td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; padding-left: 10px;">Pico:</td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; min-width: 15px;"><?php echo $PicoDia; ?></td>
                        <td></td>
                        <td style="font-size: 80%; color: #4682B4;">em <?php echo $DataPicoDia; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 80%; color: green;">Usuários on Line:</td>
                        <td style="font-size: 80%; color: green; text-align: right; min-width: 15px;"><?php echo $TotOnLine; ?></td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; padding-left: 10px;">Pico:</td>
                        <td style="font-size: 80%; color: #4682B4; text-align: right; min-width: 15px;"><?php echo $PicoOnLine; ?></td>
                        <td></td>
                        <td style="font-size: 80%; color: #4682B4;">em <?php echo $DataPicoOnLine; ?></td>
                    </tr>
                </table>
            </div>
            <table id="idTabelaUsu" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th>Login</th>
                        <th style="display: none;"></th>
                        <th>Nome Usual</th>
                        <th>Nome Completo</th>
                        <th style="text-align: center;">Setor</th>
                        <th style="text-align: center;">Último Login</th>
                        <th style="text-align: center;">Ativo</th>
                        <th style="text-align: center;">On Line</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($row0 > 0){
                    while ($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0]; // id
                        $Cpf = $tbl0[1];
                        $rs1 = pg_query($Conec, "SELECT ativo, to_char(logini, 'DD/MM/YYYY HH24:MI'), codsetor, EXTRACT(EPOCH FROM (NOW() - logfim)) AS difference, to_char(logini, 'YYYY/MM/DD') 
                        FROM ".$xProj.".poslog  
                        WHERE cpf = '$Cpf' ");   //  WHERE ".$xProj.".poslog.pessoas_id = $Cod");
                        $row1 = pg_num_rows($rs1);

                        // se constar da tabela poslog
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $Ativ = $tbl1[0]; // ativo
                            $DataLog = $tbl1[1];
                            if($tbl1[4] == "1500/01/01" || $tbl1[4] == "3000/12/31"){
                                $DataLog = "";
                            }
                            $CodSetor = $tbl1[2];
                            $Tempo = $tbl1[3];
                            $rs2 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $CodSetor");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescSetor = $tbl2[0];
                            }else{
                                $DescSetor = "n/d";
                            }
                            if($Ativ == 1){
                                $DescAtiv = "Ativo";
                            }else{
                                $DescAtiv = "Inativo";
                            }
                        ?>
                        <tr>
                            <td style="display: none;"></td>
                            <td style="text-align: center; font-size: 85%;"><?php echo formatCnpjCpf($tbl0[1]); ?></td> <!-- cpf -->
                            <td style="display: none;"><?php echo $Cod; ?></td>
                            <td><?php echo $tbl0[2]; ?></td> <!-- nome usual -->
                            <td><?php echo $tbl0[3]; ?></td> <!-- nome completo -->
                            <td style="text-align: center;"><?php echo $DescSetor; ?></td> <!-- siglasetor -->
                            <td style="text-align: center; font-size: 85%;"><?php echo $DataLog; ?></td>  <!-- ultimologin formatado -->
                            <td style="text-align: center; font-size: 85%;"><?php echo $DescAtiv; ?></td>
                            <td style="text-align: center;">
                                <?php 
                                 if($Tempo <= 60){
                                    echo "<img src='imagens/ok.png' title='On Line'>";
                                 }else{
                                    echo "<img src='imagens/oknao.png' title='Off Line'>";
                                 }
                                ?>
                            </td>
                        </tr>
                        <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <br><br>
    </body>
</html>