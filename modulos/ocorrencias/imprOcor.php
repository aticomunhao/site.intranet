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

    $rs = pg_query($Conec, "SELECT usuins, TO_CHAR(datains, 'DD/MM/YYYY'), TO_CHAR(dataocor, 'DD/MM/YYYY'), codsetor, ocorrencia, numocor FROM ".$xProj.".ocorrencias WHERE codocor = $Num ");
    $row = pg_num_rows($rs);
    $tbl = pg_fetch_row($rs);
    $CodUsu = $tbl[0];
    
    $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu ");
    $tbl0 = pg_fetch_row($rs0);
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $NomeUsu = $tbl0[0];
    }else{
        $NomeUsu = "";
    }

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->ln(7);

    $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
    $pdf->Cell(0, 4, "- Ocorrência: ".$tbl[5]." de ".$tbl[2].", registrada em: ".$tbl[1] , 0, 1, 'L');
    $pdf->ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetDrawColor(200); // cinza claro

    $rs1 = pg_query($Conec, "SELECT descideo FROM ".$xProj.".ocorrideogr WHERE coddaocor = $Num ");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $col = 20;
        $FimImg = $pdf->GetY()+22;
        //impressão das imagens
        while($tbl1 = pg_fetch_row($rs1)){
            if($tbl1[0] != "" && $tbl1[0] != "NULL"){
                if(file_exists('imagens/'.$tbl1[0])){
                    if(getimagesize('imagens/'.$tbl1[0])!=0){
                        $pdf->Image('imagens/'.$tbl1[0], $col, $lin, 16, 16);
                    }
                }
            }
            $col = $col+20;
            if($col == 180){
                $lin = $lin+20;
                $col = 20;
                $FimImg = $FimImg+20;
            }
        }
    }else{
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 8, "- Nenhuma imagem.", 0, 2, 'L');
        $FimImg = $pdf->GetY();
    }
        $pdf->ln(3);
        $pdf->Line(10, $FimImg, 200, $FimImg);
        $pdf->SetY($FimImg); 
        $pdf->ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 4, "- Relato: ", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->ln(3);
        $pdf->SetX(15); 
        $pdf->MultiCell(0, 5, $tbl[4], 0, 'J', false);

        $pdf->ln(3);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(3);
        //assinatura
        $pdf->MultiCell(0, 5, "(a) ".$NomeUsu, 0, 'C', false);
    
 }
 $pdf->Output();