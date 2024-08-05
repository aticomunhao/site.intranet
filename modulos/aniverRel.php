<?php
   session_start(); 
   if(file_exists("config/abrealas.php")){
    include_once("config/abrealas.php");
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../index.php");
    }
 }else{
    echo "404 - Not Found";
 }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="class/superfish/js/jquery.js"></script> <!-- versão 1.12.1 veio com o superfish - tem que usar esta, a versão 3.6 não recarrega a página-->
        <style>
            .resetbot{
                border-radius: 5px;
            }
        </style>
        <script>
            new DataTable('#idTabela', {
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

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
//                data = table.row(this).data();
//                $id = data[1];
            });

            $(document).ready(function(){
            });

            function foco(id){
                document.getElementById(id).focus();
            }
        </script>
    </head>
    <body>
        <?php
        require_once("config/abrealas.php");
        require_once("config/gUtils.php");
//        $rs0 = pg_query($ConecPes, "SELECT id, nome_completo, TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM'), nome_resumido FROM ".$xPes.".pessoas WHERE nome_completo != '' ORDER BY nome_completo ");
        $rs0 = pg_query($Conec, "SELECT id, nomecompl, TO_CHAR(datanasc, 'DD'), TO_CHAR(datanasc , 'MM'), nomeusual FROM ".$xProj.".poslog WHERE nomecompl != '' ORDER BY nomecompl ");
        $row0 = pg_num_rows($rs0);
        ?>
        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px;">
            <div style="text-align: center;">
                <h3>Aniversariantes</h3>
            </div>
            <table id="idTabela" class="display" style="width:75%;">
                <thead>
                    <tr>
                        <th style="display: none;"></th>
                        <th>Nome</th>
                        <th>Nome Completo</th>
                        <th style="text-align: center;">Aniversário</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    while ($tbl = pg_fetch_row($rs0)) {
                        $Cod = $tbl[0];
                        if(!is_null($tbl[1])){
                            $NomeCompl = GUtils::normalizarNome($tbl[1]); // não suporta null
                        }else{
                            $NomeCompl = "";
                        }
                        $DiaAniv = $tbl[2];
                        $MesAniv = $tbl[3];
                        if(!is_null($tbl[4])){
                            $NomeUsual = GUtils::normalizarNome($tbl[4]);
                        }else{
                            $NomeUsual = "";
                        }
                        
                    ?>
                    <tr>
                        <td style="display: none;"></td> <!-- para não indexar pela primeira coluna (nome usual). Evita configurações no datatable -->
                        <td><?php echo $NomeUsual; ?></td>
                        <td><?php echo $NomeCompl; ?></td>
                        <td style="text-align: center;"><?php echo $DiaAniv."/".$MesAniv; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>