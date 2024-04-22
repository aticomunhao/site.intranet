<?php
//Monta a grade 
	session_start();
    if(!isset($_SESSION['AdmUsu'])){
        header("Location: index.php");
     }
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    $admIns = parAdm("insevento", $Conec, $xProj);   // nível para inserir evento no calendário
    $admEdit = parAdm("editevento", $Conec, $xProj); // nível para editar evento no calendário

    date_default_timezone_set('America/Sao_Paulo'); 
    $monthTime = $_REQUEST["monthTime"];
    $startDate = strtotime("last sunday", $monthTime);

echo "<table style='margin: 0 auto; text-align: center;'>";
echo "<thead>
    <tr>
        <th style='width: 1%;'>Dom</th>
        <th>Seg</th>
        <th>Ter</th>
        <th>Qua</th>
        <th>Qui</th>
        <th>Sex</th>
        <th>Sab</th>
    </tr>
</thead>";

echo "<tbody>";
for($row = 0; $row < 6; $row++){
    echo "<tr>";
    for($column = 0; $column < 7; $column++){
        if(date("Y-m", $startDate) != date("Y-m", $monthTime)){
            echo "<td class='other-month' style='vertical-align: top; text-align: left; height: 100px; font-size: 1.2em; font-weight: bold;'>";
        }else{
            echo "<td style='vertical-align: top; text-align: left; height: 100px; font-size: 1.2em; font-weight: bold;'>";
        }

        $HojeUnix = strtotime(date('Y/m/d'));
        if($HojeUnix == $startDate){
            if($_SESSION["AdmUsu"] >= $admIns){ // nível adm para inserir
                echo "<div style='cursor: pointer; background-color: #FFFF00 ; text-align: center; border: 1px solid; width: 30px; border-radius: 15px;' onclick='pegaData($startDate);' title='Inserir evento'>".date("j", $startDate)."</div>";
            }else{
                echo "<div style='background-color: #FFFF00 ; text-align: center; border: 1px solid; width: 30px; border-radius: 15px;' title='Inserir evento'>".date("j", $startDate)."</div>";
            }
        }else{
            if($_SESSION["AdmUsu"] >= $admIns){
                echo "<div style='background-color: #E6E8FA; cursor: pointer;' onclick='pegaData($startDate);' title='Inserir evento'>".date('j', $startDate)."</div>";
            }else{
                echo "<div style='background-color: #E6E8FA;'>".date('j', $startDate)."</div>";
            }
        }

        //Coluna Repet = 0 -> sem repetição, =1 -> repetição mensal, =2 -> repetição anual
        $date = new DateTime("@$startDate"); // timestamp
        $hojeQuadrinho = $date->format('Y-m-d');
        $Dia = $date->format('d');
        $Mes = $date->format('m');

        $rs0 = pg_query($Conec, "SELECT idev, evnum, titulo, cor, fixo FROM ".$xProj.".calendev WHERE '$hojeQuadrinho' = dataini And ativo = 1 
        or to_char(dataini, 'DD') = '$Dia' And repet = 1 And ativo = 1 
        or to_char(dataini, 'DD') = '$Dia' And to_char(dataini, 'MM') = '$Mes' And repet = 2 And ativo = 1 
        ORDER BY evnum");

        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            while ($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0]; // idev
                $evNum = $tbl0[1];  //evNum
                $Tit = substr($tbl0[2], 0, 20);
                $Cor = $tbl0[3];
                $Fixo = $tbl0[4];
                if($Fixo == 0){ // não é evento fixo
                    if($_SESSION["AdmUsu"] >= $admEdit){ // nível adm para editar
                        echo "<div style='background-color: ".$Cor."; font-size: .6em; margin: 0; padding: 0px; padding-left: 2px; border: 1px solid; border-radius: 3px; cursor: pointer;' onclick='pegaEvento($Cod, $evNum);' title='Editar evento'>$Tit</div>";
                    }else{
                        echo "<div style='background-color: ".$Cor."; font-size: .6em; margin: 0; padding: 0px; padding-left: 2px; border: 1px solid; border-radius: 3px;'>$Tit</div>";
                    }
                }else{
                    if($_SESSION["AdmUsu"] > 6){  // se for evento fixo, super usuário pode editar
                        echo "<div style='background-color: ".$Cor."; font-size: .6em; margin: 0; padding: 0px; padding-left: 3px; border: 1px solid; border-radius: 5px; cursor: pointer;' onclick='pegaEvento($Cod, $evNum);' title='Editar evento'>$Tit</div>";
                    }else{
                        echo "<div style='background-color: ".$Cor."; font-size: .6em; margin: 0; padding: 0px; padding-left: 3px; border: 1px solid; border-radius: 5px;'>$Tit</div>";
                    }
                }
            }
        }
        echo "</td>";
        $startDate = strtotime("+1 day", $startDate);
    }
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";