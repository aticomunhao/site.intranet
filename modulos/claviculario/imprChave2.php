<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }
 date_default_timezone_set('America/Sao_Paulo');
 
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once('../../class/fpdf/fpdf.php'); // adaptado ao PHP 7.2 - 8.2
    define('FPDF_FONTPATH', '../../class/fpdf/font/');  
    $Dom = "logo_comunhao_completa_cor_pos_150px.png";

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
    $Cabec3 = $tblCabec[2];

    $semana = array(
        '0' => 'DOM', 
        '1' => 'SEG',
        '2' => 'TER',
        '3' => 'QUA',
        '4' => 'QUI',
        '5' => 'SEX',
        '6' => 'SAB'
    );
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

    class PDF extends FPDF{
        function Footer(){
           $this->SetY(-15);
           $this->SetFont('Arial','I',8);
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage("L", "A4");
    $pdf->SetLeftMargin(30);
    $pdf->SetTitle('Resumo Clavic DAF', $isUTF8=TRUE);
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Claviculário DAF', $isUTF8=TRUE);
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
//    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->MultiCell(0, 3, "Claviculário da DAF", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 290, $lin);
    $pdf->SetDrawColor(200); // cinza claro
    $pdf->ln(7);  

    if($Acao == "listamesChaves"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
    }
    if($Acao == "listaanoChaves"){
        $Ano = filter_input(INPUT_GET, 'ano'); 
    }


    if($Acao == "listamesChaves" || $Acao == "listaanoChaves"){
        $rs0 = pg_query($Conec, "SELECT ".$xProj.".chaves2.id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente 
        FROM ".$xProj.".chaves2  
        WHERE ativo = 1 And chavenum != 0 ORDER BY chavenum");
        $row0 = pg_num_rows($rs0);
        $pdf->SetFont('Arial', 'I', 14);
        if($Acao == "listamesChaves"){
            $pdf->MultiCell(0, 5, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
        }
        if($Acao == "listaanoChaves"){
            $pdf->MultiCell(0, 5, $Ano, 0, 'C', false);
        }

        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(25);
            $pdf->Cell(20, 3, "Chave", 0, 0, 'L');
            $pdf->Cell(80, 3, "Local", 0, 0, 'L');
            $pdf->Cell(20, 3, "Sala", 0, 0, 'R');
            $pdf->Cell(150, 3, "Obs", 0, 0, 'L');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(25); 
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT).$tbl0[2], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(80, 5, $tbl0[3], 0, 0, 'L');
                $pdf->Cell(20, 5, $tbl0[4], 0, 0, 'R');
                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(0, 4, $tbl0[5], 0, 'L', false);
                $pdf->SetFont('Arial', '', 10);
                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);

                if($Acao == "listamesChaves"){
                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(datasaida, 'DD/MM/YYYY HH24:MI'), TO_CHAR(datavolta, 'DD/MM/YYYY HH24:MI'), TO_CHAR(datasaida, 'YYYY'), TO_CHAR(datavolta, 'YYYY'), usuretira, usudevolve, TO_CHAR(datavolta - datasaida, 'DD HH24:MI'), TO_CHAR(datavolta - datasaida, 'DD'), TO_CHAR(CURRENT_DATE - datasaida, 'DD'), telef FROM ".$xProj.".chaves2_ctl 
                    WHERE chaves_id = $Cod And DATE_PART('MONTH', datasaida) = '$Mes' And DATE_PART('YEAR', datasaida) = '$Ano' ORDER BY datasaida DESC");
                }
                if($Acao == "listaanoChaves"){
                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(datasaida, 'DD/MM/YYYY HH24:MI'), TO_CHAR(datavolta, 'DD/MM/YYYY HH24:MI'), TO_CHAR(datasaida, 'YYYY'), TO_CHAR(datavolta, 'YYYY'), usuretira, usudevolve, TO_CHAR(datavolta - datasaida, 'DD HH24:MI'), TO_CHAR(datavolta - datasaida, 'DD'), TO_CHAR(CURRENT_DATE - datasaida, 'DD'), telef FROM ".$xProj.".chaves2_ctl 
                    WHERE chaves_id = $Cod And DATE_PART('YEAR', datasaida) = '$Ano' ORDER BY datasaida DESC ");
                }

                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $pdf->ln(1);
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(30, 3, "Retirada", 0, 0, 'C');
                    $pdf->Cell(30, 3, "Devolução", 0, 0, 'C');
                    $pdf->Cell(70, 3, "Retirada por", 0, 0, 'L');
                    $pdf->Cell(70, 3, "Devolvida por", 0, 0, 'L');
                    $pdf->Cell(30, 3, "Tempo de uso (Dia h:min)", 0, 1, 'R');

                    $pdf->SetFont('Arial', '', 10);

                    while($tbl1=pg_fetch_row($rs1)){
                        $pdf->SetX(50);
                        if($tbl1[2] != '3000'){
                            $pdf->Cell(30, 5, $tbl1[0], 0, 0, 'L');
                        }else{
                            $pdf->Cell(30, 5, "", 0, 0, 'L');
                        }
                        if($tbl1[3] != '3000'){
                            $pdf->Cell(30, 5, $tbl1[1], 0, 0, 'L');
                        }else{
                            $pdf->Cell(30, 5, "", 0, 0, 'L');
                        }

                        $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $tbl1[4] ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            $pdf->Cell(70, 5, $tbl2[0], 0, 0, 'L');
                        }else{
                            $pdf->Cell(70, 5, "", 0, 0, 'L');
                        }

                        $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $tbl1[5] ");
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            $tbl3 = pg_fetch_row($rs3);
                            $pdf->Cell(70, 5, $tbl3[0], 0, 0, 'L');
                        }else{
                            $pdf->Cell(70, 5, "", 0, 0, 'L');
                        }
                        if($tbl1[3] != '3000'){
                            if($tbl1[7] > 0){
                                $pdf->SetTextColor(255, 0, 0); // vermelho
                            }
                            $pdf->Cell(30, 5, $tbl1[6], 0, 1, 'R');
                            $pdf->SetTextColor(0, 0, 0);
                        }else{
                            if($tbl1[8] > 0){
                                $pdf->SetTextColor(255, 0, 0); // vermelho
                            }
                            $pdf->Cell(30, 5, "Ausente", 0, 1, 'R');
                            $pdf->SetTextColor(0, 0, 0);
                        }

                        $lin = $pdf->GetY();
                        $pdf->Line(50, $lin, 282, $lin);
                    }
                }
                $pdf->ln(5);
            }
            
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum usuário encontrado.', 0, 1, 'L');
        }
    }


    $pdf->AddPage();
    $pdf->ln(10);
    if($Acao == "listamesChaves"){
        $rs0 = pg_query($Conec, "SELECT id, chavenum, chavenumcompl, chavelocal, chavesala FROM ".$xProj.".chaves2  
        WHERE ativo = 1 And chavenum != 0 ORDER BY chavenum");
        $row0 = pg_num_rows($rs0);

        $pdf->SetFont('Arial', 'I', 14);
        if($Acao == "listamesChaves"){
            $pdf->MultiCell(0, 5, "Uso das chaves em ".$mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
        }
        $pdf->ln(4);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(40);
            $pdf->Cell(20, 3, "Chave", 0, 0, 'L');
            $pdf->Cell(20, 3, "nº de retiradas", 0, 0, 'R');
            $pdf->Cell(20, 3, "Local da Chave", 0, 0, 'L');
            $pdf->ln(4);

            $lin = $pdf->GetY();
            $pdf->Line(40, $lin, 180, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(40); 
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT).$tbl0[2], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);

                $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves2_ctl WHERE ativo = 1 And chaves_id = $Cod And DATE_PART('MONTH', datasaida) = '$Mes' And DATE_PART('YEAR', datasaida) = '$Ano'");
                $row1 = pg_num_rows($rs1);
                $pdf->Cell(20, 5, $row1, 0, 0, 'R');
                $pdf->Cell(80, 5, $tbl0[3], 0, 1, 'L');
            }
        }       
    }

    $pdf->ln(10);
    if($Acao == "listaanoChaves"){
        $rs0 = pg_query($Conec, "SELECT id, chavenum, chavenumcompl, chavelocal, chavesala FROM ".$xProj.".chaves2  
        WHERE ativo = 1 And chavenum != 0 ORDER BY chavenum");
        $row0 = pg_num_rows($rs0);

        $pdf->SetFont('Arial', 'I', 14);
        $pdf->MultiCell(0, 5, "Uso das chaves em ".$Ano, 0, 'C', false);
        
        $pdf->ln(4);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(40);
            $pdf->Cell(20, 3, "Chave", 0, 0, 'L');
            $pdf->Cell(20, 3, "nº de retiradas", 0, 0, 'R');
            $pdf->Cell(20, 3, "Local da Chave", 0, 0, 'L');
            $pdf->ln(4);

            $lin = $pdf->GetY();
            $pdf->Line(40, $lin, 180, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(40); 
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(20, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT).$tbl0[2], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);

                $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves2_ctl WHERE ativo = 1 And chaves_id = $Cod And DATE_PART('YEAR', datasaida) = '$Ano'");
                $row1 = pg_num_rows($rs1);
                $pdf->Cell(20, 5, $row1, 0, 0, 'R');
                $pdf->Cell(80, 5, $tbl0[3], 0, 1, 'L');
            }
        }       
    }


 }
 $pdf->Output();