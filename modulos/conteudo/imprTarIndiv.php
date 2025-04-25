<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }
 $mes_extenso = array(
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
); 

 if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once('../../class/fpdf/fpdf.php'); // adaptado ao PHP 7.2 - 8.2
    define('FPDF_FONTPATH', '../../class/fpdf/font/');  
    $Dom = "Logo2.png";
    date_default_timezone_set('America/Sao_Paulo'); 
    function CalcData($Ano, $Mes, $Dia, $Hora, $Min){
        if($Ano == 0 ){
            $Ano = ""; 
        }elseif($Ano == 1){
            $Ano = $Ano."ano "; 
        }else{
            $Ano = $Ano."anos "; 
        }

        if($Mes == 0 ){
            $Mes = ""; 
        }elseif($Mes == 1){
            $Mes = $Mes."mês "; // acentuam-se monossílabos tônicos terminados em a/as, e/es, o/os:- dá, pás, mês, só, pós, fé, trás.
        }else{
            $Mes = $Mes."meses "; 
        }

        if($Dia == 0 ){
            $Dia = ""; 
        }elseif($Dia == 1){
            $Dia = $Dia."dia "; 
        }else{
            $Dia = $Dia."dias "; 
        }

        if(strlen($Hora) == 1){
            $Hora = "0".$Hora;
        }
        $Hora = $Hora."h ";

        if(strlen($Min) == 1){
            $Min = "0".$Min;
        }
        $Min = $Min."m"; 

        $Valor = $Ano.$Mes.$Dia.$Hora.$Min;
        return $Valor;
    }
    function CalcTotalHoras($Ano, $Mes, $Dia, $Hor, $Min){
        if($Min > 60){
            $I = $Min%60;
            $H = floor($Min/60);
        }else{
            $I = $Min;
            $H = 0;
        }
        if($Hor > 24){
            $H = $H + $Hor%24;
            $D = floor($Hor/24);
        }else{
            $H = $Hor;
            $D = 0;
        }
        if($Dia > 30){
            $D = $D = $Dia%30;
            $M = floor($Dia/30);
        }else{
            $D = $Dia;
            $M = 0;
        }
        if($Mes > 12){
            $M = $M + $Mes%12;
            $A = floor($Mes/12);
        }else{
            $M = $Mes;
            $A = 0;
        }
        $A = $A+$Ano;

        return $A." ".$M." ". $D." ". $H.":".$I; 
    }
    function CalcMedia($Ano, $Mes, $Dia, $Hor, $Min, $Num){
        $TotMin = $Min+($Hor*60)+($Dia*1440)+($Mes*43200)+($Ano*518499);
        $Med = floor(($TotMin)/$Num);

        $Min = $Med%60;
        $Horas = floor($Med/60);
        $Hor = ($Horas%24);
        $Dias = floor($Horas/24);
        $Dia = $Dias%30;
        $Meses = floor($Dias/30);
        $Mes = $Meses%12;
        $Ano = floor($Meses/12);

        if($Ano == 0){
            $Ano = "";
        }else{
            if($Ano == 1){
                $Ano = "01ano";
            }else{
                $Ano = str_pad($Ano, 2,0, STR_PAD_LEFT)."anos";
            }
        }
        if($Mes == 0){
            $Mes = "";
        }else{
            if($Mes == 1){
                $Mes = "01mês";
            }else{
                $Mes = str_pad($Mes, 2,0, STR_PAD_LEFT)."meses";
            }
        }
        if($Dia == 0){
            $Dia = "";
        }else{
            if($Dia == 1){
                $Dia = "01dia";
            }else{
                $Dia = str_pad($Dia, 2,0, STR_PAD_LEFT)."dias";
            }
        }
        if($Hor == 0){
            $Hor = "00h";
        }else{
            $Hor = str_pad($Hor, 2,0, STR_PAD_LEFT)."h";
        }
        if($Min == 0){
            $Min = "00min";
        }else{
            $Min = str_pad($Min, 2,0, STR_PAD_LEFT)."min";
        }

        $Tempo = $Ano." ".$Mes." ".$Dia." ".$Hor." ".$Min;

        return $Tempo;
    }

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
    $Cabec3 = $tblCabec[2];

    class PDF extends FPDF{
        function Footer(){
           // Vai para 1.5 cm da parte inferior
           $this->SetY(-15);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
//           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }
        
    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Relação Tarefas', $isUTF8=TRUE);
    if($Dom != "" && $Dom != "NULL"){
        if(file_exists('../../imagens/'.$Dom)){
            if(getimagesize('../../imagens/'.$Dom)!=0){
                $pdf->Image('../../imagens/'.$Dom,12,8,16,20);
            }
        }
    }
    $pdf->SetFont('Arial','' , 14); 
    $pdf->Cell(0, 5, $Cabec1, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 12); 
    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
//    $pdf->Cell(150, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');

    if($Acao == "listaMandante"){
        $pdf->MultiCell(0, 3, "Relação Individual de Tarefas Expedidas", 0, 'C', false);
    }else if($Acao == "listaExecutante"){
        $pdf->MultiCell(0, 3, "Relação Individual de Tarefas Recebidas", 0, 'C', false);
    }else{
        $pdf->MultiCell(0, 3, "Tarefas", 0, 'C', false);
    }
    $pdf->SetDrawColor(0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $VerTarefas = parAdm("vertarefa", $Conec, $xProj); // ver tarefas   1: todos - 2: só mandante e executante - 3: visualização por setor 
    $CodSetorUsu = parEsc("grupotarefa", $Conec, $xProj, $_SESSION["usuarioID"]);
    $UsuLogadoId = $_SESSION["usuarioID"];

    if($Acao == "imprIndiv"){
        $pdf->ln();
        $pdf->SetTitle('Relação de Tarefas', $isUTF8=TRUE);

        $CodUsu = addslashes(filter_input(INPUT_GET, 'codigo')); // usuário
//        $MesTar = addslashes(filter_input(INPUT_GET, 'mes')); 
        $AnoTar = addslashes(filter_input(INPUT_GET, 'ano')); 
        $Sit = addslashes(filter_input(INPUT_GET, 'sit')); 
            if($Sit == 0){
                $Desc = "Todas";
            }
            if($Sit == 1){
                $Desc = "Designada";
            }
            if($Sit == 2){
                $Desc = "Aceita";
            }
            if($Sit == 3){
                $Desc = "em Andamento";
            }
            if($Sit == 4){
                $Desc = "Terminada";
            }

            $rsNome = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
            $tblNome = pg_fetch_row($rsNome);
            $NomeCompl = $tblNome[0];
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->MultiCell(0, 5, $NomeCompl, 0, 'C', false);

            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetTitle('Relação de Tarefas', $isUTF8=TRUE);
            $pdf->MultiCell(0, 5, "Tarefas na fase: ".$Desc, 0, 'C', false);

            if($Sit == 0){
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And usuexec = $CodUsu And DATE_PART('YEAR', datains) = $AnoTar ");
                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $CodUsu And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = $AnoTar 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");        
            }else{
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And sit = $Sit And usuexec = $CodUsu And DATE_PART('YEAR', datains) = $AnoTar ");
                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.sit = $Sit And ".$xProj.".tarefas.usuexec = $CodUsu And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = $AnoTar 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");        
            }
            $tblCont = pg_fetch_row($rsCont);
            $pdf->SetTextColor(120, 120, 120);  
            $pdf->SetFont('Arial', 'I', 9);

            if($tblCont[0] > 1){
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefas", 0, 'C', false);
            }else{
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefa", 0, 'C', false);
            }
            $pdf->SetTextColor(0, 0, 0);

            $pdf->ln(3);
            $pdf->SetDrawColor(0);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);

            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $pdf->SetFont('Arial','' , 10); 
                while($tbl1 = pg_fetch_row($rs1) ){
                    $Cod = $tbl1[0];
                    $pdf->ln(3);
                    $pdf->SetFont('Arial', '' , 10); 
                    if(!is_null($tbl1[2]) && $tbl1[2] != ""){
                        $Nome = $tbl1[2]." - ".$tbl1[1]; // nomeusual
                    }else{
                        $Nome = $tbl1[1]; // Nomecompleto
                    }

        if($Acao == "listaExecutante"){
            $pdf->SetFont('Arial', 'B' , 9); 
//                    $pdf->Cell(23, 5, "Executadas por: ".$tbl1[1], 0, 1, 'L');
            $pdf->Cell(23, 5, "Executadas por: ".$Nome, 0, 1, 'L');
        }else{
//                    $pdf->Cell(23, 5, "Expedidas por: ".$tbl1[1], 0, 1, 'L');
            $pdf->Cell(23, 5, "Expedidas por: ".$Nome, 0, 1, 'L');
        }
 
        if($Sit == 0){
            $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
            FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".tarefas.usuins = $Cod And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $CodUsu And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = $AnoTar 
            ORDER BY ".$xProj.".tarefas.datains DESC");
        }else{
            $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
            EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
            FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".tarefas.usuins = $Cod And sit = $Sit And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $CodUsu And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = $AnoTar 
            ORDER BY ".$xProj.".tarefas.datains DESC");
        }

        while($tbl2 = pg_fetch_row($rs2)){
            $pdf->SetX(24);
            $pdf->SetFont('Arial', '' , 10); 
            if($Acao == "listaExecutante"){
                $pdf->Cell(9, 5, "de: ", 0, 0, 'L');
                $pdf->SetFont('Arial', '' , 10); 
            }else{
//                $pdf->Cell(9, 5, "para: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
            }
            if(!is_null($tbl2[34]) && $tbl2[34] != ""){
                $Nome = $tbl2[34]." - ".$tbl2[2]; // nomeusual
            }else{
                $Nome = $tbl2[2]; // Nomecompleto
            }
//                    $pdf->Cell(0, 5, $tbl2[2], 0, 1, 'L');
//            $pdf->Cell(0, 5, $Nome, 0, 1, 'L');
            switch ($tbl2[33]){
                case 0:
                    $Prio = "Urgente";
                    break;
                case 1:
                    $Prio = "Muito Importante";
                    break;
                case 2:
                    $Prio = "Importante";
                    break;
                case 3:
                    $Prio = "Normal";
                    break;
            }
            $pdf->SetX(25); 
            $pdf->SetFont('Arial', '' , 8); 
            $pdf->MultiCell(0, 3, "Prioridade: ".$Prio, 0, 'L', false);
            $pdf->SetX(25); 
            $pdf->MultiCell(0, 3, "Tarefa: ".$tbl2[1], 0, 'L', false);

            $pdf->SetFont('Arial', '' , 7); 
            $pdf->SetX(25); 
            $pdf->Cell(30, 4, "Expedida", 0, 0, 'L');
            $pdf->Cell(30, 4, "Ciência", 0, 0, 'L');
            $pdf->Cell(30, 4, "Aceita", 0, 0, 'L');
            $pdf->Cell(30, 4, "Andamento", 0, 0, 'L');
            $pdf->Cell(30, 4, "Terminada", 0, 0, 'L');
            $pdf->Cell(30, 4, "Tempo Total", 0, 1, 'L');
            //primeira diferença de data hora - 8

            $pdf->SetX(25); 
            if($tbl2[3] != "31/12/3000 00:00"){
                $pdf->Cell(30, 4, $tbl2[3], 0, 0, 'L');  // Expedida
            }

            if($tbl2[4] != "31/12/3000 00:00"){
                $pdf->SetX(55);
                $pdf->Cell(30, 4, $tbl2[4], 0, 0, 'L'); //Ciência
            }
            if($tbl2[5] != "31/12/3000 00:00"){
                $pdf->SetX(85);
                $pdf->Cell(30, 4, $tbl2[5], 0, 0, 'L'); //Aceita
            }
            if($tbl2[6] != "31/12/3000 00:00"){
                $pdf->SetX(115);
                $pdf->Cell(30, 4, $tbl2[6], 0, 0, 'L'); //Andamento
            }
            if($tbl2[7] != "31/12/3000 00:00"){
                $pdf->SetX(145);
                $pdf->Cell(30, 4, $tbl2[7], 0, 0, 'L'); //Terminada
            }

            $pdf->SetX(175);
            if($tbl2[7] != "31/12/3000 00:00"){  // Terminada
                $pdf->Cell(10, 4, calcData($tbl2[28], $tbl2[29], $tbl2[30], $tbl2[31], $tbl2[32]), 0, 1, 'L');
            }else{
                $pdf->Cell(10, 4, "", 0, 1, 'L');
            }

            $pdf->SetTextColor(120, 120, 120);   //  $pdf->SetTextColor(200, 200, 200);
            $pdf->SetX(25);
            $pdf->Cell(24, 4, "tempos: ", 0, 0, 'R');

            $pdf->SetX(55);
            if($tbl2[4] != "31/12/3000 00:00"){  // Ciência
                $pdf->Cell(10, 4, calcData($tbl2[8], $tbl2[9], $tbl2[10], $tbl2[11], $tbl2[12]), 0, 0, 'L');
            }
            $pdf->SetX(85);
            if($tbl2[5] != "31/12/3000 00:00"){  // Aceita
                $pdf->Cell(10, 4, calcData($tbl2[13], $tbl2[14], $tbl2[15], $tbl2[16], $tbl2[17]), 0, 0, 'L');
            }
            $pdf->SetX(115);
            if($tbl2[6] != "31/12/3000 00:00"){  // Andamento
                $pdf->Cell(10, 4, calcData($tbl2[18], $tbl2[19], $tbl2[20], $tbl2[21], $tbl2[22]), 0, 0, 'L');
            }
            $pdf->SetX(145);
            if($tbl2[7] != "31/12/3000 00:00"){  // Terminada
                $pdf->Cell(10, 4, calcData($tbl2[23], $tbl2[24], $tbl2[25], $tbl2[26], $tbl2[27]), 0, 0, 'L');
            }
            $pdf->SetTextColor(0, 0, 0);

            $pdf->Cell(10, 4, "", 0, 1, 'L');

            $pdf->SetDrawColor(200); // cinza claro
            $lin = $pdf->GetY();
            $pdf->Line(35, $lin, 200, $lin);
            $pdf->ln(3);
        }
        $pdf->SetDrawColor(0);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(10);
        if($Acao == "listaExecutante"){ // calcular o tempo médio de execução das tarefas
            $Ano = 0;
            $Mes = 0;
            $Dia = 0;
            $Hor = 0;
            $Min = 0;

            $rs3 = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas WHERE usuexec = $CodUsu And ativo != 0");
            while($tbl3 = pg_fetch_row($rs3)){
                if(!is_null($tbl3[0])){
                    $e = explode(';', $tbl3[0]);
                    $Ano = $Ano+$e[0];
                    $Mes = $Mes+$e[1];
                    $Dia = $Dia+$e[2];
                    $Hor = $Hor+$e[3];
                    $Min = $Min+$e[4];
                }
            }
            $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE usuexec = $CodUsu And ativo != 0 And datasit4 != '3000-12-31' And datasit4 IS NOT NULL");
            $tbl4 = pg_fetch_row($rs4);
            $NumTar = $tbl4[0];

            $rs5 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu ");
            $tbl5 = pg_fetch_row($rs5);
            $pdf->SetX(40);
            $pdf->SetFont('Arial', 'B' , 10); 
            $pdf->Cell(0, 5, $tbl5[0], 0, 1, 'L'); //nome

            $pdf->SetFont('Arial', '' , 10); 
            $pdf->SetX(40);
            $pdf->Cell(25, 4, "Quantidade de Tarefas terminadas:  ".$NumTar, 0, 1, 'L');

            if($NumTar > 0){
                $pdf->SetX(40);
                $pdf->Cell(25, 4, "Tempo médio de execução: ".CalcMedia($Ano, $Mes, $Dia, $Hor, $Min, $NumTar), 0, 1, 'L');
            }
        }
        if($Acao == "listaMandante"){ 
            $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE usuins = $CodUsu And ativo != 0");
            $tbl4 = pg_fetch_row($rs4);
            $NumTar = $tbl4[0];

            $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE usuins = $CodUsu And ativo != 0 And datasit4 != '3000-12-31' And datasit4 IS NOT NULL");
            $tbl5 = pg_fetch_row($rs5);
            $NumTarTerm = $tbl5[0];

            $rs6 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu ");
            $tbl6 = pg_fetch_row($rs6);
            $pdf->SetX(40);
            $pdf->SetFont('Arial', 'B' , 10); 
            $pdf->Cell(0, 5, $tbl6[0], 0, 1, 'L'); //nome
            $pdf->SetFont('Arial', '' , 10); 
            $pdf->SetX(40);
            $pdf->Cell(85, 4, "Quantidade de Tarefas expedidas:", 0, 0, 'L');
            $pdf->Cell(15, 4, number_format($NumTar, 0, ",",".")."    (".$NumTarTerm." terminadas)", 0, 1, 'R');
        }
    }
}else{
    $pdf->SetFont('Arial', '', 10);
    $pdf->ln(10);
    $pdf->Cell(20, 4, "Nenhum registro encontrado.", 0, 1, 'L');
}

    }
    $pdf->Output();
}