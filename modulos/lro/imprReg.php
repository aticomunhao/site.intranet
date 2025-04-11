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
    $Dom = "Logo2.png";

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
    $Cabec3 = $tblCabec[2];

    class PDF extends FPDF{
        function Footer(){
            $this->SetY(-15); // Vai para 1.5 cm da parte inferior
            $this->SetFont('Arial','I',8);// Seleciona a fonte Arial itálico 8
            $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R'); // data/hora + nº página / total de páginas
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
//    $pdf->SetX(40); 
    $pdf->SetFont('Arial','' , 14); 
    $pdf->Cell(0, 5, $Cabec1, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 12); 
    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "impr" ){
        $pdf->MultiCell(0, 3, "Registro de Ocorrência", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
   
    $rs = pg_query($Conec, "SELECT ".$xProj.".livroreg.id, to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), turno, descturno, numrelato, nomecompl, usuant, relato, ocor, nomeusual, relsubstit, usuprox 
    FROM ".$xProj.".livroreg INNER JOIN ".$xProj.".poslog ON ".$xProj.".livroreg.codusu = ".$xProj.".poslog.pessoas_id
    WHERE ".$xProj.".livroreg.ativo = 1 And ".$xProj.".livroreg.id =  $Num");
    $row = pg_num_rows($rs);

    $tbl = pg_fetch_row($rs);
    $CodAnt = $tbl[0];
    $UsuAnt = $tbl[6];
    $UsuProx = $tbl[11];

    $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuAnt ");
    $tbl0 = pg_fetch_row($rs0);
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $NomeAnt = $tbl0[0];
    }else{
        $NomeAnt = "";
    }
    $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuProx ");
    $tbl1 = pg_fetch_row($rs1);
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $NomeProx = $tbl1[0];
    }else{
        $NomeProx = "";
    }

    $DescTurno = $tbl[3];

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->ln(7);
    $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
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
    $pdf->Cell(60, 4, " - Turno: ".$DescTurno, 0, 1, 'L');

    $pdf->ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetDrawColor(200); // cinza claro

    $pdf->SetX(20); 
    $pdf->Cell(60, 4, "I - Recebí o serviço de: ".$NomeAnt, 0, 1, 'L');
    $pdf->ln(2);
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);

    $pdf->ln(5);
    $pdf->SetX(20); 
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 4, "II - Relato: ", 0, 1, 'L');

    $pdf->SetFont('Arial', '', 10);
    $pdf->ln(3);
    $pdf->SetX(15); 
    $RelSubst = $tbl[10];

    if($tbl[8] == 0){
        $pdf->SetX(25); 
        $pdf->Cell(0, 4, "Não houve ocorrências", 0, 1, 'L');
        if($RelSubst != ""){
            $pdf->ln(2);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetX(25);
            $pdf->MultiCell(0, 3, "Observações: ", 0, 'L', false); //relato
            $pdf->SetX(35);
            $pdf->MultiCell(0, 4, $RelSubst, 0, 'J', false); //relato
            $pdf->SetFont('Arial', '', 8);
        }
    }else{
        $pdf->SetX(25); 
        $pdf->MultiCell(0, 5, $tbl[7], 0, 'J', false); //relato
        if($RelSubst != ""){
            $pdf->ln(2);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetX(25);
            $pdf->MultiCell(0, 3, "Observações: ", 0, 'L', false); //relato
            $pdf->SetX(35);
            $pdf->MultiCell(0, 4, $RelSubst, 0, 'J', false); //relato
            $pdf->SetFont('Arial', '', 8);
        }
    }
    $pdf->ln(2);
    $pdf->SetFont('Arial', '', 10);
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->ln(5);
    $pdf->SetX(20); 
    $pdf->Cell(60, 4, "III - Passei o serviço para: ".$NomeProx, 0, 1, 'L');

    $pdf->ln(3);
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->ln(10);
    $pdf->MultiCell(0, 5, "(a) ".$tbl[5], 0, 'C', false); //assinatura
 }
 $pdf->Output();