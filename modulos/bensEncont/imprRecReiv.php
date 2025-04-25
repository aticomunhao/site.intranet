<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
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
           $this->SetY(-15);// 1.5 cm da parte inferior
           $this->SetFont('Arial','I',8);
           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    $pdf->SetLeftMargin(20);
    $pdf->SetTitle('Reivindic', $isUTF8=TRUE);      
    $pdf->SetFont('Arial', '' , 12); //Monta o arquivo pdf 

    $pdf->Cell(25, 26, "", 1, 0, 'L'); 
    if($Dom != "" && $Dom != "NULL"){
        if(file_exists('../../imagens/'.$Dom)){
            if(getimagesize('../../imagens/'.$Dom)!=0){
                $pdf->Image('../../imagens/'.$Dom,25,12,16,20);
            }
        }
    }

    $pdf->SetFillColor(232, 232, 232); // fundo cinza
    $pdf->SetFont('Arial', 'B' , 12); 

    if($Acao == "imprReciboReiv"){
        $Cod = $_REQUEST["codigo"];
        $rs1 = pg_query($Conec, "SELECT to_char(datareiv, 'DD/MM/YYYY'), TO_CHAR(dataperdeu, 'DD/MM/YYYY'), nome, email, telef, localperdeu, descdobemperdeu, observ, processoreiv, encontrado, entregue 
        FROM ".$xProj.".bensreivind WHERE id = $Cod ");
        $tbl1 = pg_fetch_row($rs1);

        $pdf->SetX(45); 
        $pdf->Cell(130, 14, "RECLAMAÇÃO DE BENS PERDIDOS", 1, 0, 'C', true);
        $pdf->SetY(10); 
        $pdf->SetX(170);
        $pdf->Cell(27, 26, "", 1, 0, 'C', true); 
        $pdf->SetFont('Arial', 'B' , 18); 
        $pdf->SetY(5); 
        $pdf->SetX(170);
        $pdf->Cell(27, 26, "DAF", 0, 0, 'C'); 

        $pdf->SetFont('Arial','' , 8); 
        $pdf->SetY(25); 
        $pdf->SetX(170);
        $pdf->MultiCell(26, 4, "Dir. Administrativa e Financeira", 0, 'C', true);

        $pdf->SetY(24); 
        $pdf->SetX(45); 
        $pdf->Cell(65, 12, "Data: ", 1, 0, 'L');
        $pdf->Cell(60, 12, "Controle nº: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 11); 
        $pdf->SetX(55); 
        $pdf->Cell(10, 12, $tbl1[0], 0, 0, 'L'); // Data
        $pdf->SetX(130); 
        $pdf->Cell(10, 12, $tbl1[8], 0, 1, 'L'); // Processo

        $pdf->SetFont('Arial','' , 8); 
        $pdf->Cell(177, 10, "Nome: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 11); 
        $pdf->SetX(35); 
        $pdf->Cell(10, 10, $tbl1[2], 0, 1, 'L'); // Nome

        $pdf->SetFont('Arial','' , 8); 
        $pdf->Cell(100, 10, "E-Mail: ", 1, 0, 'L');
        $pdf->Cell(77, 10, "Telefone: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->SetX(35); 
        $pdf->Cell(10, 10, $tbl1[3], 0, 0, 'L'); // E-Mail
        $pdf->SetX(135); 
        $pdf->Cell(10, 10, $tbl1[4], 0, 1, 'L'); // Telefone

        $pdf->SetY(54); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 10, "Local e data da Ocorrência: ", 0, 0, 'L');
        $pdf->SetY(56); 
        $pdf->Cell(177, 15, "", 1, 0, 'L');
        
        $pdf->SetY(61); 
        $pdf->SetX(35); 
        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->MultiCell(0, 4, $tbl1[5], 0, 'J');  // Local

        $pdf->SetY(69); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 10, "Descrição do bem perdido: ", 0, 0, 'L');

        $pdf->SetY(71); 
        $pdf->Cell(177, 30, "", 1, 0, 'L');
        $pdf->SetY(76); 
        $pdf->SetX(35); 
        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->MultiCell(0, 4, $tbl1[6], 0, 'J'); // Descrição

        $pdf->SetY(101); 
        $pdf->Cell(88, 10, "", 1, 0, 'L');
        $pdf->Cell(89, 10, "", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->SetX(25); 

        if($tbl1[9] == 0){
            $Encontr = "OBJETO NÃO ENCONTRADO";
        }else{
            $Encontr = "Objeto Econtrado";
        }

        $Entregue = "";
        if($tbl1[9] == 1){
            if($tbl1[10] == 0){
                $Entregue = "- Ainda não Entregue";
            }else{
                $Entregue = "- Objeto Entregue";
            }
        }
        $pdf->Cell(50, 10, $Encontr." ".$Entregue, 0, 0, 'L');

        $pdf->SetY(99); 
        $pdf->SetX(110); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 10, "Assinatura: ", 0, 1, 'L');
        $pdf->SetY(111); 
        $pdf->SetFont('Arial','' , 7);
        $pdf->Cell(50, 4, "Form-018-C/DAF/2016", 0, 0, 'L');

//------

//Segunda via 
$lin = 160;
        $pdf->SetY($lin); 
        $pdf->Cell(25, 26, "", 1, 0, 'L'); 
        if($Dom != "" && $Dom != "NULL"){
            if(file_exists('../../imagens/'.$Dom)){
                if(getimagesize('../../imagens/'.$Dom)!=0){
                    $pdf->Image('../../imagens/'.$Dom,25,$lin+2,16,20);
                }
            }
        }
        $pdf->SetX(45); 
        $pdf->Cell(130, 14, "RECLAMAÇÃO DE BENS PERDIDOS", 1, 0, 'C', true);

        $pdf->SetX(170);
        $pdf->Cell(27, 26, "", 1, 0, 'C', true); 
        $pdf->SetFont('Arial', 'B' , 18); 
        $pdf->SetY($lin-5); 
        $pdf->SetX(170);
        $pdf->Cell(27, 26, "DAF", 0, 0, 'C'); 

        $pdf->SetFont('Arial','' , 8); 
        $pdf->SetY($lin+15); 
        $pdf->SetX(170);
        $pdf->MultiCell(26, 4, "Dir. Administrativa e Financeira", 0, 'C', true);

        $pdf->SetY($lin+14); // 159
        $pdf->SetX(45); 
        $pdf->Cell(65, 12, "Data: ", 1, 0, 'L');
        $pdf->Cell(60, 12, "Controle nº: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 11); 
        $pdf->SetX(55); 
        $pdf->Cell(10, 12, $tbl1[0], 0, 0, 'L'); // Data
        $pdf->SetX(130); 
        $pdf->Cell(10, 12, $tbl1[8], 0, 1, 'L'); // Processo

        $pdf->SetFont('Arial','' , 8); 
        $pdf->Cell(177, 10, "Nome: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 11); 
        $pdf->SetX(35); 
        $pdf->Cell(10, 10, $tbl1[2], 0, 1, 'L'); // Nome

        $pdf->SetFont('Arial','' , 8); 
        $pdf->Cell(100, 10, "E-Mail: ", 1, 0, 'L');
        $pdf->Cell(77, 10, "Telefone: ", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->SetX(35); 
        $pdf->Cell(10, 10, $tbl1[3], 0, 0, 'L'); // E-Mail
        $pdf->SetX(135); 
        $pdf->Cell(10, 10, $tbl1[4], 0, 1, 'L'); // Telefone

        $pdf->SetY($lin+47); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 4, "Local e data da Ocorrência: ", 0, 0, 'L');
        $pdf->SetY($lin+46); 
        $pdf->Cell(177, 15, "", 1, 0, 'L');
        
        $pdf->SetY($lin+51); 
        $pdf->SetX(35); 
        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->MultiCell(0, 4, $tbl1[5], 0, 'J');  // Local

        $pdf->SetY($lin+62); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 4, "Descrição do bem perdido: ", 0, 0, 'L');

        $pdf->SetY($lin+61); 
        $pdf->Cell(177, 30, "", 1, 0, 'L');
        $pdf->SetY($lin+66); 
        $pdf->SetX(35); 
        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->MultiCell(0, 4, $tbl1[6], 0, 'J'); // Descrição

        $pdf->SetY($lin+91); 
        $pdf->Cell(88, 10, "", 1, 0, 'L');
        $pdf->Cell(89, 10, "", 1, 0, 'L');

        $pdf->SetFont('Arial', 'B' , 10); 
        $pdf->SetX(35); 

        if($tbl1[9] == 0){
            $Encontr = "OBJETO NÃO ENCONTRADO";
        }else{
            $Encontr = "Objeto Econtrado";
        }

        $Entregue = "";
        if($tbl1[9] == 1){
            if($tbl1[10] == 0){
                $Entregue = "- Ainda não Entregue";
            }else{
                $Entregue = "- Objeto Entregue";
            }
        }
        $pdf->Cell(50, 10, $Encontr." ".$Entregue, 0, 0, 'L');

        $pdf->SetY($lin+89); 
        $pdf->SetX(110); 
        $pdf->SetFont('Arial','' , 8);
        $pdf->Cell(50, 10, "Assinatura: ", 0, 1, 'L');

        $pdf->SetY($lin+101); 
        $pdf->SetFont('Arial','' , 7);
        $pdf->Cell(50, 4, "Form-018-C/DAF/2016", 0, 0, 'L');
    }
 }
 $pdf->Output();