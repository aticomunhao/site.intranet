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
    $Num = $_REQUEST["codigo"];
    require_once('../../class/fpdf/fpdf.php'); // adaptado ao PHP 7.2 - 8.2
    define('FPDF_FONTPATH', '../../class/fpdf/font/');  
    $Dom = "logo_comunhao_completa_cor_pos_150px.png";

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
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Ocorrência', $isUTF8=TRUE);
    if($Dom != "" && $Dom != "NULL"){
        if(file_exists('../../imagens/'.$Dom)){
            if(getimagesize('../../imagens/'.$Dom)!=0){
                $pdf->Image('../../imagens/'.$Dom,12,8,16,20);
            }
        }
    }
    $pdf->SetX(40); 
    $pdf->SetFont('Arial','' , 14); 
    $pdf->Cell(150, 5, $Cabec1, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 12); 
    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(150, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "impr" ){
        $pdf->MultiCell(150, 3, "Registro de Ocorrência", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
   
    $rs = pg_query($Conec, "SELECT ".$xProj.".livroreg.id, to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), turno, descturno, numrelato, nomecompl, usuant, relato, ocor, nomeusual, relsubstit 
    FROM ".$xProj.".livroreg INNER JOIN ".$xProj.".poslog ON ".$xProj.".livroreg.codusu = ".$xProj.".poslog.pessoas_id
    WHERE ".$xProj.".livroreg.ativo = 1 And ".$xProj.".livroreg.id =  $Num");

    $row = pg_num_rows($rs);
    $tbl = pg_fetch_row($rs);
    $CodAnt = $tbl[0];
    
    $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodAnt ");
    $tbl0 = pg_fetch_row($rs0);
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $NomeAnt = $tbl0[0];
    }else{
        $NomeAnt = "";
    }
    $DescTurno = $tbl[3];
//    $rs1 = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $tbl[2] ");
//    $tbl1 = pg_fetch_row($rs1);
//    $row1 = pg_num_rows($rs1);
//    if($row1 > 0){
//        $DescTurno = $tbl1[0];
//    }else{
//        $DescTurno = "";
//    }

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->ln(7);

    $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
    //$pdf->Cell(0, 4, "- Registro: ".$tbl[4]." de ".$tbl[1]." - Turno: ".$DescTurno, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(16, 4, "- Registro: ", 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 9);

    $Tam = $pdf->GetStringWidth($tbl[4]); // calcula o tamanho ocupado pq varia com o complemento
    $pdf->Cell($Tam, 4, $tbl[4], 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(6, 4, " de ", 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(14, 4, $tbl[1], 0, 0, 'L');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(60, 4, " - Turno: ".$DescTurno, 0, 0, 'L');

    $pdf->ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetDrawColor(200); // cinza claro

    $pdf->ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 4, "- Relato: ", 0, 1, 'L');

    $pdf->SetFont('Arial', '', 10);
    $pdf->ln(3);
    $pdf->SetX(15); 
    $RelSubst = $tbl[10];

    if($tbl[8] == 0){
        $pdf->Cell(0, 4, "Não houve ocorrências", 0, 1, 'L');
        if($RelSubst != ""){
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetX(15);
            $pdf->MultiCell(0, 4, "Observações: ".$RelSubst, 0, 'J', false); //relato
            $pdf->SetFont('Arial', '', 8);
        }
    }else{
        $pdf->MultiCell(0, 5, $tbl[7], 0, 'J', false); //relato
        if($RelSubst != ""){
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetX(15);
            $pdf->MultiCell(0, 4, "Observações: ".$RelSubst, 0, 'J', false); //relato
            $pdf->SetFont('Arial', '', 8);
        }
    }

    $pdf->ln(3);
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->ln(3);
    $pdf->MultiCell(0, 5, "(a) ".$tbl[5], 0, 'C', false); //assinatura
 }
 $pdf->Output();