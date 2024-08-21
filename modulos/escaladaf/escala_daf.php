<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-ui.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style>
             .quadrodia {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }

            .etiq{
                text-align: right; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }

        </style>
        <script>
            $(document).ready(function(){
                document.getElementById("selecMesAno").value = document.getElementById("guardamesano").value;
//                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));



                $("#selecMesAno").change(function(){
                    if(parseInt(document.getElementById("selecMesAno").value) > 0){
                        
                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));

                    }
                });


            });
            function ajaxIni(){
                try{
                ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                catch(e){
                try{
                   ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                   catch(ex) {
                   try{
                       ajax = new XMLHttpRequest();}
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }

            function abreEdit(PesId, DiaId){
                alert(PesId);
                alert(DiaId);

            }

        </script>

</head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_adm");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_daf (
        id SERIAL PRIMARY KEY, 
        dataescala date DEFAULT '3000-12-31',
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");




//------------

    $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escala_daf GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
    $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
    $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for
    $ParamIni = date("m/Y");

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

    //Mantem a tabela meses à frente
    for($i = 0; $i < 180; $i++){
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_daf WHERE dataescala = '$Data' ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escala_daf (dataescala) VALUES ('$Data')");
        }
    }
            
    ?>

        <input type="hidden" id="guardamesano" value="<?php echo $ParamIni; ?>" />
        <input type="hidden" id="guardanumgrupo" value = "<?php echo $NumGrupo; ?>" />

        <label>Selecione o mês: </label>
        <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
            <option value=""></option>
                <?php 
                    if($OpcoesEscMes){
                        while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                            <?php 
                        }
                    }
                ?>
        </select>
        <div style="position: relative; float: right; padding-right: 20px;">
            <button class="botpadrblue" onclick="abreModal();">Participantes</button>
        </div>


        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                <td>
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro"></div>
                        <div class="col quadro" style="text-align: center;"></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="position: relative; float: rigth; text-align: right;"></div> 
                    </div>
                </div>
                    </td>
                </tr>
            </table>
            <div id="faixacentral"></div>
        </div>
    </body>
</html>