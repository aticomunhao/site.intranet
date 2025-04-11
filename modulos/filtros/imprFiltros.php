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

    class PDF extends FPDF{
        function Footer(){
           $this->SetY(-15);  // Vai para 1.5 cm da parte inferior
           $this->SetFont('Arial','I',8);
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage("L", "A4");
//    $pdf->AddPage();
    $pdf->SetLeftMargin(20);
    $pdf->SetTitle('Filtros', $isUTF8=TRUE);
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Filtros', $isUTF8=TRUE);
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
//    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->MultiCell(0, 3, "Relação de Filtros e Purificadores", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 290, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "listaFiltros"){
        $rs0 = pg_query($Conec, "SELECT ".$xProj.".filtros.id, numapar, descmarca, desctipo, TO_CHAR(datatroca, 'DD/MM/YYYY'), TO_CHAR(datatroca, 'YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'YYYY'), localinst, modelo, ".$xProj.".filtros.observ, 
            CASE WHEN dataaviso <= CURRENT_DATE AND notific = 1 THEN 'aviso' END
            FROM ".$xProj.".filtros_tipos INNER JOIN (".$xProj.".filtros INNER JOIN ".$xProj.".filtros_marcas ON ".$xProj.".filtros.codmarca = ".$xProj.".filtros_marcas.id) ON ".$xProj.".filtros.tipofiltro =  ".$xProj.".filtros_tipos.id 
            WHERE ".$xProj.".filtros.ativo = 1 ORDER BY numapar ");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->Cell(10, 3, "Núm", 0, 0, 'C');
            $pdf->Cell(25, 3, "Marca", 0, 0, 'L');
            $pdf->Cell(35, 3, "Modelo", 0, 0, 'L');
            $pdf->Cell(28, 3, "Tipo", 0, 0, 'L');
            $pdf->Cell(20, 3, "Data Troca", 0, 0, 'L');
            $pdf->Cell(20, 3, "Data Venc", 0, 0, 'L');
            $pdf->Cell(65, 3, "Local", 0, 0, 'L');
            $pdf->Cell(35, 3, "Observações", 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(1);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                if($tbl0[5] == "3000"){
                    $DataTroca = "";
                }else{
                    $DataTroca = $tbl0[4];
                }
                if($tbl0[7] == "3000"){
                    $DataVenc = "";
                }else{
                    $DataVenc = $tbl0[6];
                }
                $pdf->SetX(20); 
                $pdf->Cell(10, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->Cell(25, 5, substr($tbl0[2], 0, 12), 0, 0, 'L');
                $pdf->Cell(35, 5, substr($tbl0[11], 0, 20), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[3], 0, 15), 0, 0, 'L');
                $pdf->Cell(20, 5, $DataTroca, 0, 0, 'L');

                if($tbl0[13] == 'aviso'){
                    $pdf->SetTextColor(255, 0, 0);
                }

                $pdf->Cell(20, 5, $DataVenc, 0, 0, 'L');
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(65, 5, substr($tbl0[10], 0, 38), 0, 0, 'L');
                $pdf->MultiCell(65, 4, $tbl0[12], 0, 'L', false);

                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);
                $pdf->ln(1);
            }
            $pdf->SetX(10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
        }
    }
 }
 $pdf->Output();