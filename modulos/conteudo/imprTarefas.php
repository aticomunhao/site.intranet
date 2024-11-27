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
    $Dom = "logo_comunhao_completa_cor_pos_150px.png";
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
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'       Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
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
//    $pdf->SetX(40); 
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
//    $CodSetorUsu = $_SESSION["CodSetorUsu"]; //para a visualização das tarefas por setores
    $CodSetorUsu = parEsc("grupotarefa", $Conec, $xProj, $_SESSION["usuarioID"]);
    $UsuLogadoId = $_SESSION["usuarioID"];
    $MeuOrg = parEsc("orgtarefa", $Conec, $xProj, $_SESSION["usuarioID"]); // nível no organograma


$VerTarefas = 4;

    if($Acao == "listamesTarefa" || $Acao == "listaanoTarefa" || $Acao == "listaMandante" || $Acao == "listaExecutante" || $Acao == "listaSitTarefa" || $Acao == "listaCombo"){
        $pdf->ln();
        $pdf->SetFont('Arial', 'I', 14);
        if($Acao == "listamesTarefa"){
            $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
            $Proc = explode("/", $Busca);
            $Mes = $Proc[0];
            if(strLen($Mes) < 2){
                $Mes = "0".$Mes;
            }
            $Ano = $Proc[1];
            $pdf->SetTitle('Relação Mensal Tarefas', $isUTF8=TRUE);

            $pdf->MultiCell(0, 5, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
            //Conta tarefas no mês
            if($VerTarefas == 1){ // 1 = Todos 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('MONTH', datains) = '$Mes' And DATE_PART('YEAR', datains) = '$Ano'");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 2){ // 2 = só mandante e executante 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('MONTH', datains) = '$Mes' And DATE_PART('YEAR', datains) = '$Ano' And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('MONTH', datains) = '$Mes' And DATE_PART('YEAR', datains) = '$Ano' And usuexec = $UsuLogadoId");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.usuins = $UsuLogadoId Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.usuexec = $UsuLogadoId
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 3){ // 3 = visualização por setor 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('MONTH', datains) = '$Mes' And DATE_PART('YEAR', datains) = '$Ano' And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('MONTH', datains) = '$Mes' And DATE_PART('YEAR', datains) = '$Ano' And setorexec = $CodSetorUsu");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And setorins = $CodSetorUsu Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And setorexec = $CodSetorUsu 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 4){ // 3 = visualização por organograma 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgexec >= $MeuOrg ");
                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgexec >= $MeuOrg 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            $tblCont = pg_fetch_row($rsCont);
            $pdf->SetTextColor(120, 120, 120);  
            $pdf->SetFont('Arial', 'I', 9);
            if($tblCont[0] == 1){
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefa expedida", 0, 'C', false);
            }else{
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefas expedidas", 0, 'C', false);
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->ln(3);
            $pdf->SetDrawColor(0);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
        }
        if($Acao == "listaanoTarefa"){ 
            $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
            $pdf->SetTitle('Relação Anual Tarefas', $isUTF8=TRUE);
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->MultiCell(0, 5, $Ano, 0, 'C', false);
            //Conta tarefas no ano
            if($VerTarefas == 1){ // 1 = Todos 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$Ano'");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0  And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 2){ // 2 = só mandante e executante 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$Ano' And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$Ano' And usuexec = $UsuLogadoId");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.usuins = $UsuLogadoId Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.usuexec = $UsuLogadoId
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 3){ // 3 = visualização por setor 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$Ano' And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$Ano' And setorexec = $CodSetorUsu");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And setorins = $CodSetorUsu Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And setorexec = $CodSetorUsu 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            if($VerTarefas == 4){ // 4 = visualização por organograma 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' And ".$xProj.".tarefas.orgexec >= $MeuOrg 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl"); 
            }
            $tblCont = pg_fetch_row($rsCont);
            $pdf->SetTextColor(120, 120, 120);  
            $pdf->SetFont('Arial', 'I', 9);
            if($tblCont[0] == 1){
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefa expedida", 0, 'C', false);
            }else{
                $pdf->MultiCell(0, 3, $tblCont[0]." tarefas expedidas", 0, 'C', false);
            }
            $pdf->SetTextColor(0, 0, 0);

            $pdf->ln(3);
            $pdf->SetDrawColor(0);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
        }

        if($Acao == "listaMandante"){ 
            $CodUsu = addslashes(filter_input(INPUT_GET, 'codigo')); 
            $pdf->SetTitle('Relação Individual', $isUTF8=TRUE);
            $rs1 = pg_query($Conec, "SELECT DISTINCT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
            FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".poslog.pessoas_id = $CodUsu "); 
        }

        if($Acao == "listaExecutante"){ 
            $CodUsu = addslashes(filter_input(INPUT_GET, 'codigo')); 
            $pdf->SetTitle('Relação Individual', $isUTF8=TRUE);
            $rs1 = pg_query($Conec, "SELECT DISTINCT ".$xProj.".tarefas.usuexec, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual  
            FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".poslog.pessoas_id = $CodUsu "); 
        }


        if($Acao == "listaCombo"){ 
            $CodMand = (int) filter_input(INPUT_GET, 'mandante'); 
            $CodExec = (int) filter_input(INPUT_GET, 'executante'); 
            $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
            $Sit = (int) filter_input(INPUT_GET, 'sit');
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

            $pdf->SetTitle('Relação Combo', $isUTF8=TRUE);
            $rs1 = pg_query($Conec, "SELECT DISTINCT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
            FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
            WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuins = $CodMand ");


            $rs8 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodMand");
            $tbl8 = pg_fetch_row($rs8);
            $NomeMand = $tbl8[0];
            $rs9 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodExec");
            $tbl9 = pg_fetch_row($rs9);
            $NomeExec = $tbl9[0];
        }


        if($Acao == "listaSitTarefa"){ 
            $Sit = (int) filter_input(INPUT_GET, 'numero');
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

            $pdf->SetTitle('Relação de Tarefas', $isUTF8=TRUE);
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->MultiCell(0, 5, "Tarefas na fase: ".$Desc, 0, 'C', false);
            if($VerTarefas == 1){ // 1 = Todos 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And sit = $Sit");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And sit = $Sit 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");        
            }
            if($VerTarefas == 2){ // 2 = só mandante e executante 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And usuins = $UsuLogadoId And sit = $Sit Or ativo != 0 And usuexec = $UsuLogadoId And sit = $Sit");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuins = $UsuLogadoId And sit = $Sit Or ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $UsuLogadoId And sit = $Sit 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");     
            }
            if($VerTarefas == 3){ // 3 = visualização por setor 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ativo != 0 And setorins = $CodSetorUsu And sit = $Sit Or ativo != 0 And setorexec = $CodSetorUsu And sit = $Sit");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                WHERE ".$xProj.".tarefas.ativo != 0 And setorins = $CodSetorUsu And sit = $Sit Or ".$xProj.".tarefas.ativo != 0 And setorexec = $CodSetorUsu And sit = $Sit 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");
            }
            if($VerTarefas == 4){ // 4 = visualização por organograma 
                $rsCont = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgins >= $MeuOrg And ".$xProj.".tarefas.sit = $Sit Or ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgexec >= $MeuOrg And ".$xProj.".tarefas.sit = $Sit ");

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id 
                WHERE ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgins >= $MeuOrg And ".$xProj.".tarefas.sit = $Sit Or ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgexec >= $MeuOrg And ".$xProj.".tarefas.sit = $Sit 
                GROUP BY ".$xProj.".tarefas.usuins, ".$xProj.".poslog.nomecompl, ".$xProj.".poslog.nomeusual 
                ORDER BY ".$xProj.".poslog.nomecompl");
            }
            $tblCont = pg_fetch_row($rsCont);
            $pdf->SetTextColor(120, 120, 120);  
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetX(30);
            $pdf->MultiCell(0, 3, $tblCont[0]." tarefas", 0, 'C', false);
            $pdf->SetTextColor(0, 0, 0);

            $pdf->ln(3);
            $pdf->SetDrawColor(0);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
        }

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

                if($Acao == "listaCombo"){
                    $pdf->MultiCell(0, 5, "Tarefas na fase: ".$Desc."/".$Ano, 0, 'C', false);
                    $pdf->Cell(20, 5, "Expedição: ", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'B' , 10);
                    $pdf->Cell(0, 5, $NomeMand, 0, 1, 'L');
                    $pdf->SetFont('Arial', '' , 10);
                    $pdf->Cell(20, 5, "Execução:  ", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'B' , 10);
                    $pdf->Cell(0, 5, $NomeExec, 0, 1, 'L');

                    $pdf->ln(2);
                }else{
                    if($Acao == "listaExecutante"){
                        $pdf->SetFont('Arial', 'B' , 10); 
                        $pdf->Cell(23, 5, "Executadas por: ".$Nome, 0, 1, 'L');
                    }else{
                        $pdf->Cell(23, 5, "Expedidas por: ".$Nome, 0, 1, 'L');
                    }
                }
                if($Acao == "listamesTarefa"){ 
                    $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                    WHERE ".$xProj.".tarefas.usuins = $Cod And ".$xProj.".tarefas.ativo != 0 And DATE_PART('MONTH', ".$xProj.".tarefas.datains) = '$Mes' And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' 
                    ORDER BY ".$xProj.".tarefas.datains DESC");
                }
                if($Acao == "listaanoTarefa"){ 
                    $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                    WHERE ".$xProj.".tarefas.usuins = $Cod And ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano' 
                    ORDER BY ".$xProj.".tarefas.datains DESC");
                }
                if($Acao == "listaMandante"){ 
                    $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                    WHERE ".$xProj.".tarefas.usuins = $CodUsu And ".$xProj.".tarefas.ativo != 0 
                    ORDER BY ".$xProj.".tarefas.datains DESC");
                }

                if($Acao == "listaExecutante"){ 
                    $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id
                    WHERE ".$xProj.".tarefas.usuexec = $CodUsu And ".$xProj.".tarefas.ativo != 0 
                    ORDER BY ".$xProj.".tarefas.datains DESC");
                }

                if($Acao == "listaSitTarefa"){ 
                    $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                    EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                    WHERE ".$xProj.".tarefas.usuins = $Cod And sit = $Sit and ".$xProj.".tarefas.ativo != 0 
                    ORDER BY ".$xProj.".tarefas.datains DESC");
                }
                if($Acao == "listaCombo"){
                    if($Sit == 0){
                        $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                        FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                        WHERE ".$xProj.".tarefas.usuins = $CodMand And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $CodExec And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano'
                        ORDER BY ".$xProj.".tarefas.datains DESC");
                    }else{
                        $rs2 = pg_query($Conec, "SELECT usuexec, tittarefa, nomecompl, TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit1, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit2, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit3, 'DD/MM/YYYY HH24:MI'), TO_CHAR(".$xProj.".tarefas.datasit4, 'DD/MM/YYYY HH24:MI'), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit1, ".$xProj.".tarefas.datains)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit2, ".$xProj.".tarefas.datasit1)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit3, ".$xProj.".tarefas.datasit2)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datasit3)), 
                        EXTRACT('years' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('month' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('days' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('hours' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), EXTRACT('min' FROM AGE(".$xProj.".tarefas.datasit4, ".$xProj.".tarefas.datains)), prio, nomeusual 
                        FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id
                        WHERE ".$xProj.".tarefas.usuins = $CodMand And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $CodExec And sit = $Sit And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$Ano'
                        ORDER BY ".$xProj.".tarefas.datains DESC");
                    }
                    $row2 = pg_num_rows($rs2);
                }

                while($tbl2 = pg_fetch_row($rs2)){
                    $pdf->SetX(24);
                    $pdf->SetFont('Arial', '' , 10); 

                    if($Acao != "listaCombo"){
                        if($Acao == "listaExecutante"){
                            $pdf->Cell(9, 5, "de: ", 0, 0, 'L');
                            $pdf->SetFont('Arial', '' , 10); 
                        }else{
                            $pdf->Cell(9, 5, "para: ", 0, 0, 'L');
                            $pdf->SetFont('Arial', 'B' , 10); 
                        }
                        if(!is_null($tbl2[34]) && $tbl2[34] != ""){
                            $Nome = $tbl2[34]." - ".$tbl2[2]; // nomeusual
                        }else{
                            $Nome = $tbl2[2]; // Nomecompleto
                        }
                        $pdf->Cell(0, 5, $Nome, 0, 1, 'L');
                    }
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
                    $pdf->SetX(35); 
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->MultiCell(0, 3, "Prioridade: ".$Prio, 0, 'L', false);
                    $pdf->SetX(35); 
                    $pdf->MultiCell(0, 3, "Tarefa: ".$tbl2[1], 0, 'L', false);

                    $pdf->SetFont('Arial', '' , 7); 
                    $pdf->SetX(35); 
                    $pdf->Cell(30, 4, "Expedida", 0, 0, 'L');
                    $pdf->Cell(30, 4, "Ciência", 0, 0, 'L');
                    $pdf->Cell(30, 4, "Aceita", 0, 0, 'L');
                    $pdf->Cell(30, 4, "Andamento", 0, 0, 'L');
                    $pdf->Cell(25, 4, "Terminada", 0, 0, 'L');
                    $pdf->Cell(30, 4, "Tempo Total", 0, 1, 'L');
                    //primeira diferença de data hora - 8

                    $pdf->SetX(35); 
                    if($tbl2[3] != "31/12/3000 00:00"){
                        $pdf->Cell(30, 4, $tbl2[3], 0, 0, 'L');  // Expedida
                    }

                    if($tbl2[4] != "31/12/3000 00:00"){
                        $pdf->SetX(65);
                        $pdf->Cell(30, 4, $tbl2[4], 0, 0, 'L'); //Ciência
                    }
                    if($tbl2[5] != "31/12/3000 00:00"){
                        $pdf->SetX(95);
                        $pdf->Cell(30, 4, $tbl2[5], 0, 0, 'L'); //Aceita
                    }
                    if($tbl2[6] != "31/12/3000 00:00"){
                        $pdf->SetX(125);
                        $pdf->Cell(30, 4, $tbl2[6], 0, 0, 'L'); //Andamento
                    }
                    if($tbl2[7] != "31/12/3000 00:00"){
                        $pdf->SetX(155);
                        $pdf->Cell(30, 4, $tbl2[7], 0, 0, 'L'); //Terminada
                    }

                    $pdf->SetX(180);
                    if($tbl2[7] != "31/12/3000 00:00"){  // Terminada
                        $pdf->Cell(10, 4, calcData($tbl2[28], $tbl2[29], $tbl2[30], $tbl2[31], $tbl2[32]), 0, 1, 'L');
                    }else{
                        $pdf->Cell(10, 4, "", 0, 1, 'L');
                    }

                    $pdf->SetTextColor(120, 120, 120);   //  $pdf->SetTextColor(200, 200, 200);
                    $pdf->SetX(35);
                    $pdf->Cell(24, 4, "tempos: ", 0, 0, 'R');

                    $pdf->SetX(65);
                    if($tbl2[4] != "31/12/3000 00:00"){  // Ciência
                        $pdf->Cell(10, 4, calcData($tbl2[8], $tbl2[9], $tbl2[10], $tbl2[11], $tbl2[12]), 0, 0, 'L');
                    }
                    $pdf->SetX(95);
                    if($tbl2[5] != "31/12/3000 00:00"){  // Aceita
                        $pdf->Cell(10, 4, calcData($tbl2[13], $tbl2[14], $tbl2[15], $tbl2[16], $tbl2[17]), 0, 0, 'L');
                    }
                    $pdf->SetX(125);
                    if($tbl2[6] != "31/12/3000 00:00"){  // Andamento
                        $pdf->Cell(10, 4, calcData($tbl2[18], $tbl2[19], $tbl2[20], $tbl2[21], $tbl2[22]), 0, 0, 'L');
                    }
                    $pdf->SetX(155);
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
                if($Acao == "listaCombo"){
                    $pdf->SetFont('Arial', '' , 10); 
                    $pdf->SetX(35);
                    if($row2 == 1){
                        $pdf->Cell(25, 4, "Total: ".$row2." tarefa", 0, 1, 'L');
                    }else{
                        $pdf->Cell(25, 4, "Total: ".$row2." tarefas", 0, 1, 'L');
                    }
                }
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "Nenhum registro encontrado.", 0, 1, 'L');
        }
    }
    if($Acao == "estatTarefas"){
        $pdf->SetTitle('Resumo Anual', $isUTF8=TRUE);
        $pdf->ln();
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(0, 5, "Resumo Anual de Tarefas Expedidas", 0, 'C', false);

        $rs0 = pg_query($Conec, "SELECT DATE_PART('YEAR', datains) FROM ".$xProj.".tarefas WHERE ativo != 0 
        GROUP BY DATE_PART('YEAR', datains) ORDER BY DATE_PART('YEAR', datains) DESC");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $pdf->ln(5);
                $AnoTar = $tbl0[0];

                $pdf->SetX(40);
                $pdf->SetFont('Arial', 'BU' , 10); 
                $pdf->Cell(0, 5, $tbl0[0], 0, 1, 'L'); //Ano

//$VerTarefas = 2;                

                if($VerTarefas == 1){ // 1 = Todos 
                    $rs1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar'");
                    $rsP1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0");
                    $rsP3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2");
                    $rsP4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3");

                    $rs2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1");
                    $rs3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2");
                    $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3");
                    $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4");
                    $rsCalc = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas WHERE DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL");
                    $rs6 = pg_query($Conec, "SELECT TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR((CURRENT_DATE - ".$xProj.".tarefas.datains), 'DD'), tittarefa, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.sit 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id 
                    WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 ORDER BY (CURRENT_DATE - ".$xProj.".tarefas.datains) DESC");
                }

                if($VerTarefas == 2){ // 2 = só mandante e executante 
                    $rs1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And usuexec = $UsuLogadoId");

                    $rsP1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0 And usuexec = $UsuLogadoId");
                    
                    $rsP3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2 And usuexec = $UsuLogadoId");

                    $rsP4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3 And usuexec = $UsuLogadoId");

                    $rs2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1 And usuexec = $UsuLogadoId ");

                    $rs3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2 And usuexec = $UsuLogadoId ");

                    $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3 And usuexec = $UsuLogadoId ");

                    $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And usuins = $UsuLogadoId Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And usuexec = $UsuLogadoId ");

                    $rsCalc = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas 
                    WHERE DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL And usuins = $UsuLogadoId Or DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL And usuexec = $UsuLogadoId");
                    
                    $rs6 = pg_query($Conec, "SELECT TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR((CURRENT_DATE - ".$xProj.".tarefas.datains), 'DD'), tittarefa, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.sit 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id 
                    WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuins = $UsuLogadoId Or DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.usuexec = $UsuLogadoId 
                    ORDER BY (CURRENT_DATE - ".$xProj.".tarefas.datains) DESC");
                }
 
                if($VerTarefas == 3){ // 3 = visualização por setor 
                    $rs1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And setorins = $CodSetorUsu or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And setorexec = $CodSetorUsu");

                    $rsP1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0 And setorexec = $CodSetorUsu");

                    $rsP3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2 And setorexec = $CodSetorUsu");

                    $rsP4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3 And setorexec = $CodSetorUsu");

                    $rs2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1 And setorexec = $CodSetorUsu");

                    $rs3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2 And setorexec = $CodSetorUsu");

                    $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3 And setorexec = $CodSetorUsu");

                    $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And setorins = $CodSetorUsu Or ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And setorexec = $CodSetorUsu");

                    $rsCalc = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas 
                    WHERE DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL And setorins = $CodSetorUsu Or DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL And setorexec = $CodSetorUsu ");

                    $rs6 = pg_query($Conec, "SELECT TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR((CURRENT_DATE - ".$xProj.".tarefas.datains), 'DD'), tittarefa, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.sit 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id 
                    WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And setorins = $CodSetorUsu Or DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And setorexec = $CodSetorUsu 
                    ORDER BY (CURRENT_DATE - ".$xProj.".tarefas.datains) DESC");
                }
                if($VerTarefas == 4){ // 3 = visualização por organograma 
                    $rs1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rsP1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.prio = 0 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.prio = 0 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rsP3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And prio = 2 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And prio = 2 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rsP4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.prio = 3 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.prio = 3 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rs2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 1 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR',".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 1 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rs3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 2 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 2 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 3 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 3 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas 
                    WHERE ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 4 And ".$xProj.".tarefas.orgins >= $MeuOrg Or ".$xProj.".tarefas.ativo != 0 And DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 4 And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rsCalc = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas 
                    WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.tempototal IS NOT NULL And ".$xProj.".tarefas.orgins >= $MeuOrg Or DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And ".$xProj.".tarefas.sit = 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.tempototal IS NOT NULL And ".$xProj.".tarefas.orgexec >= $MeuOrg");

                    $rs6 = pg_query($Conec, "SELECT TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR((CURRENT_DATE - ".$xProj.".tarefas.datains), 'DD'), tittarefa, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.sit 
                    FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id 
                    WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgins >= $MeuOrg Or DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 And ".$xProj.".tarefas.orgexec >= $MeuOrg 
                    ORDER BY (CURRENT_DATE - ".$xProj.".tarefas.datains) DESC");
                }

                $tbl1 = pg_fetch_row($rs1);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(50);
                $pdf->Cell(55, 5, "Tarefas expedidas: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 5, number_format($tbl1[0], 0, ",","."), 0, 1, 'R');

                $pdf->ln(2);
                $pdf->SetX(50);
                $pdf->SetFont('Arial', '' , 8); 
                $pdf->Cell(50, 4, "Prioridades: ", 0, 1, 'L');

//                $rsP1 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 0");
                $tblP1 = pg_fetch_row($rsP1);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Urgentes: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP1[0], 0, ",","."), 0, 1, 'R');
//
//                $rsP2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 1");
//                $tblP2 = pg_fetch_row($rsP2);
//                $pdf->SetFont('Arial', '' , 10); 
//                $pdf->SetX(55);
//                $pdf->Cell(50, 4, "Muito Importantes: ", 0, 0, 'L');
//                $pdf->SetFont('Arial', 'B' , 10); 
//                $pdf->Cell(10, 4, number_format($tblP2[0], 0, ",","."), 0, 1, 'R');
//
//                $rsP3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 2");
                $tblP3 = pg_fetch_row($rsP3);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Importantes: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP3[0], 0, ",","."), 0, 1, 'R');

//                $rsP4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And prio = 3");
                $tblP4 = pg_fetch_row($rsP4);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Normais: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP4[0], 0, ",","."), 0, 1, 'R');

                $pdf->ln(2);
                $pdf->SetX(50);
                $pdf->SetFont('Arial', '' , 8); 
                $pdf->Cell(50, 4, "Situação: ", 0, 1, 'L');

//                $rs2 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 1");
                $tbl2 = pg_fetch_row($rs2);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Tarefas Designadas: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tbl2[0], 0, ",","."), 0, 1, 'R');

//                $rs3 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 2");
                $tbl3 = pg_fetch_row($rs3);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Tarefas Aceitas: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tbl3[0], 0, ",","."), 0, 1, 'R');

//                $rs4 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 3");
                $tbl4 = pg_fetch_row($rs4);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Tarefas em Andamento: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tbl4[0], 0, ",","."), 0, 1, 'R');

//                $rs5 = pg_query($Conec, "SELECT COUNT(idtar) FROM ".$xProj.".tarefas WHERE ativo != 0 And DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4");
                $tbl5 = pg_fetch_row($rs5);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Tarefas Terminadas: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tbl5[0], 0, ",","."), 0, 0, 'R');
                $pdf->SetFont('Arial', '' , 10); 

                $Ano = 0;
                $Mes = 0;
                $Dia = 0;
                $Hor = 0;
                $Min = 0;
//                $rsCalc = pg_query($Conec, "SELECT tempototal FROM ".$xProj.".tarefas WHERE DATE_PART('YEAR', datains) = '$AnoTar' And sit = 4 And ativo != 0 And tempototal IS NOT NULL");
                $rowCalc = pg_num_rows($rsCalc);

                while($tblCalc = pg_fetch_row($rsCalc)){
                    if(!is_null($tblCalc[0])){
                        $e = explode(';', $tblCalc[0]);
                        $Ano = $Ano+$e[0];
                        $Mes = $Mes+$e[1];
                        $Dia = $Dia+$e[2];
                        $Hor = $Hor+$e[3];
                        $Min = $Min+$e[4];
                    }
                }
                if($rowCalc > 0){
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->Cell(25, 4, "  Tempo médio: ".CalcMedia($Ano, $Mes, $Dia, $Hor, $Min, $rowCalc), 0, 0, 'L');
                }
                $pdf->Cell(25, 4, "", 0, 1, 'L');
                $pdf->SetFont('Arial', '' , 8); 

                $pdf->ln(7);
//                $rs6 = pg_query($Conec, "SELECT TO_CHAR(".$xProj.".tarefas.datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR((CURRENT_DATE - ".$xProj.".tarefas.datains), 'DD'), tittarefa, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, ".$xProj.".tarefas.sit 
//                FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuexec = ".$xProj.".poslog.pessoas_id 
//                WHERE DATE_PART('YEAR', ".$xProj.".tarefas.datains) = '$AnoTar' And sit < 4 And ".$xProj.".tarefas.ativo != 0 ORDER BY (CURRENT_DATE - ".$xProj.".tarefas.datains) DESC");
                $row6 = pg_num_rows($rs6);
                if($row6 > 0){
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->Cell(50, 4, "Tarefas em aberto a: ", 0, 1, 'L');
                    $pdf->ln(1);
                    while($tbl6 = pg_fetch_row($rs6)){
                        $UsuIns = $tbl6[4];
                        $rs7 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuIns");
                        $tbl7 = pg_fetch_row($rs7);
                        $NomeUsuIns = $tbl7[0];
                        if($tbl6[5] == 1){
                            $DescSit = "Designada";
                        }
                        if($tbl6[5] == 2){
                            $DescSit = "Aceita";
                        }
                        if($tbl6[5] == 3){
                            $DescSit = "em Andamento";
                        }

                        $pdf->SetX(41);
                        $pdf->Cell(30, 4, number_format($tbl6[1], 0, ",",".")." dias", 0, 0, 'R');

                        $pdf->Cell(0, 4, "para ".$tbl6[3], 0, 1, 'L');

                        $pdf->SetX(71);
                        $pdf->MultiCell(0, 3, "Expedida em ".$tbl6[0]." por ".$NomeUsuIns, 0, 'L', false);
                        $pdf->SetX(71);
                        $pdf->MultiCell(0, 3, "Situação: ".$DescSit, 0, 'L', false);
                        $pdf->SetX(71);
                        $pdf->MultiCell(0, 3, "Tarefa: ".$tbl6[2], 0, 'L', false);
                        $pdf->ln(1);
                    }
                }else{
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->Cell(50, 4, "Tarefas em aberto:", 0, 1, 'L');
                    $pdf->SetX(60);
                    $pdf->Cell(50, 4, "Nenhum registro.", 0, 1, 'L');
                    $pdf->ln(1);
                }
                $pdf->ln(5);
                $pdf->SetDrawColor(0);
                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->MultiCell(0, 5, 'Nenhum registro encontrado', 0, 'C', false);
        }
    }
    $pdf->Output();
}