<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
}

 date_default_timezone_set('America/Sao_Paulo');

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

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
    $Cabec3 = $tblCabec[2];

    class PDF extends FPDF{
        function Footer(){
            $this->SetY(-15); // Vai para 1.5cm da parte inferior
            $this->SetFont('Arial','I',8);
            $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R'); // data/hora + nº página / total de páginas
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
       
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Reclamações', $isUTF8=TRUE);
    $pdf->SetDrawColor(200); // cinza claro

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
//    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);

    $pdf->MultiCell(0, 3, "Relação de Reclamações de Bens Perdidos", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetFont('Arial', 'B', 9);

    if($Acao == "listaReivindic"){
        if(isset($_REQUEST["selec"])){
            $Selec = $_REQUEST["selec"];
        }else{
            $Selec = "todos";
        }
        $Condic = "ativo = 1";
        if($Selec == "encontrado"){
            $Condic = "ativo = 1 And encontrado = 1";
        }
        if($Selec == "entregue"){
            $Condic = "ativo = 1 And entregue = 1";
        }

        $rs = pg_query($Conec, "SELECT to_char(datareiv, 'DD/MM/YYYY'), TO_CHAR(dataperdeu, 'DD/MM/YYYY'), nome, email, telef, localperdeu, descdobemperdeu, observ, processoreiv, encontrado, entregue 
        FROM ".$xProj.".bensreivind WHERE $Condic ORDER BY datareiv DESC, processoreiv DESC ");

        $row = pg_num_rows($rs);
        $pdf->SetFont('Arial', 'I', 14);
        $pdf->ln(5);
        if($row > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(15);
            $pdf->Cell(17, 4, "Número", 0, 0, 'L');
            $pdf->Cell(20, 4, "Data", 0, 0, 'L');
            $pdf->Cell(50, 4, "Nome", 0, 0, 'L');
            $pdf->Cell(53, 4, "Telefone", 0, 0, 'L');
            $pdf->Cell(50, 4, "E-Mail", 0, 1, 'L');

            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
            $pdf->ln(1);
            $pdf->SetFont('Arial', '', 9);
            while($tbl = pg_fetch_row($rs)){
                $NumRelat = $tbl[8];
                $DataIns =  $tbl[0];
                $Nome = $tbl[2];
                $Telef = $tbl[4];
                $EMail = $tbl[3];
                $Local = $tbl[5];
            
                $Relato = $tbl[6];
            
                $pdf->SetX(15);
                $pdf->Cell(17, 5, $NumRelat, 0, 0, 'L');
                $pdf->Cell(20, 5, $DataIns, 0, 0, 'L');
                $pdf->Cell(50, 5, substr($Nome, 0, 34), 0, 0, 'L');
                $pdf->Cell(53, 5, substr($Telef, 0, 35), 0, 0, 'L');
                $pdf->MultiCell(0, 4, $EMail, 0, 'L', false);

                $pdf->SetX(33);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(20, 4, "-Local da Perda: ", 0, 0, 'L');
                $pdf->SetX(55);
                $pdf->MultiCell(0, 4, $Local, 0, 'J', false); //Local
                $pdf->SetFont('Arial', '', 9);

                $pdf->SetX(33);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(20, 4, "-Descrição Bem: ", 0, 0, 'L');
                $pdf->SetX(55);
                $pdf->MultiCell(0, 4, $Relato, 0, 'J', false); //descr
                $pdf->SetFont('Arial', '', 9);

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
                $pdf->ln(1);
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $Info = "Nada foi encontrado";
            if($Selec == "encontrado"){
                $Info = "Nenhum registro de Bem Encontrado";
            }
            if($Selec == "entregue"){
                $Info = "Nenhum registro de Bem Entregue";
            }
            $pdf->Cell(0, 4, $Info, 0, 0, 'C');
        }
    }
    $pdf->Output();
 }