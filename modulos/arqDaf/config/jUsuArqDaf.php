<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start();
require_once("abrealasArqDaf.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
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
                    if(parseInt(document.getElementById("UsuAdm").value) > 1){ // administrador 
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
            $rsT = pg_query($Conec, "SELECT pessoas_id FROM ".$xProj.".daf_poslog WHERE ativo = 1 And adm != 7"); 
            $Total = pg_num_rows($rsT);

            $Condic = "id != 0 And adm != 3 And nomecompl IS NOT NULL ";
            if($Acao == "online"){
                $Condic = "ativo = 1 And EXTRACT(EPOCH FROM (NOW() - logfim)) <= 60 And adm != 3";
            }
            if($Acao == "dehoje"){
                $Condic = "ativo = 1 And TO_CHAR(logini, 'YYYY/MM/DD') = TO_CHAR(CURRENT_DATE, 'YYYY/MM/DD') And adm != 3 ";
            }
            if($Acao == "inativos"){
                $Condic = "ativo = 0 And adm != 3 ";
            }
            $rs0 = pg_query($Conec, "SELECT pessoas_id, cpf, nomeusual, nomecompl FROM ".$xProj.".daf_poslog WHERE $Condic ORDER BY nomecompl"); 
            $row0 = pg_num_rows($rs0);

            ?>
            <table id="idTabelaUsu" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th>Login</th>
                        <th style="display: none;"></th>
                        <th>Nome Usual</th>
                        <th>Nome Completo</th>
                        <th style="text-align: center;">Último Login</th>
                        <th style="text-align: center;">Ativo</th>
                        <th style="text-align: center;">Diretório</th>
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
                        FROM ".$xProj.".daf_poslog  
                        WHERE cpf = '$Cpf' ");  
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
                            <td style="text-align: center; font-size: 85%;"><?php echo $DataLog; ?></td>  <!-- ultimologin formatado -->
                            <td style="text-align: center; font-size: 85%;"><?php echo $DescAtiv; ?></td>
                            <td style="text-align: center; font-size: 85%;"><?php if($CodSetor == 1){echo "Todos";}else{echo "Diretório ".($CodSetor-1);} ?></td>
                            <td style="text-align: center;">
                                <?php 
                                 if($Tempo <= 60){
                                    echo "<img src='../../imagens/ok.png' title='On Line'>";
                                 }else{
                                    echo "<img src='../../imagens/oknao.png' title='Off Line'>";
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