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
    date_default_timezone_set('America/Sao_Paulo'); 
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
    $pdf->SetTitle('Resumo Achados e Perdidos', $isUTF8=TRUE);
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
    $pdf->MultiCell(0, 3, "Achados e Perdidos", 0, 'C', false);

    $pdf->SetDrawColor(0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);

    if($Acao == "resumo"){
        $pdf->SetTitle('Resumo Anual', $isUTF8=TRUE);
        $pdf->ln();
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(0, 5, "Resumo Anual de Achados e Perdidos", 0, 'C', false);

        $rs0 = pg_query($Conec, "SELECT DATE_PART('YEAR', datareceb) FROM ".$xProj.".bensachados WHERE ativo != 0 
        GROUP BY DATE_PART('YEAR', datareceb) ORDER BY DATE_PART('YEAR', datareceb) DESC");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $pdf->ln(5);
                $AnoTar = $tbl0[0];

                $pdf->SetX(40);
                $pdf->SetFont('Arial', 'BU' , 10); 
                $pdf->Cell(0, 5, $tbl0[0], 0, 1, 'L'); //Ano
                
                $rs1 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar'");
                $tbl1 = pg_fetch_row($rs1);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(50);
                $pdf->Cell(55, 5, "Achados e Perdidos: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 5, number_format($tbl1[0], 0, ",","."), 0, 1, 'R');

                $pdf->ln(2);
                $pdf->SetX(50);
                $pdf->SetFont('Arial', '' , 8); 
                $pdf->Cell(50, 4, "Situação: ", 0, 1, 'L');

                $rsP1 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usurestit != 0");
                $tblP1 = pg_fetch_row($rsP1);

                $rsPR = pg_query($Conec, "SELECT TO_CHAR(datarestit - datareceb, 'DD') FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usurestit != 0");
                $rowPR = pg_num_rows($rsPR);
                if($rowPR == 0){
                    $rowPR = 1; // para evitar divisão por zero
                }
                $Dias = 0;
                while($tblPR = pg_fetch_row($rsPR)){
                    $Dias = $Dias+$tblPR[0];
                }
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Restituídos: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP1[0], 0, ",","."), 0, 0, 'R');
                if($Dias > 0){
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->Cell(25, 4, "Tempo médio: ".number_format(($Dias/$rowPR), 1, ",",".")." dias", 0, 0, 'L');
                }
                $pdf->Cell(25, 4, "", 0, 1, 'L');

                $rsP4 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usucsg != 0 And usuarquivou = 0 And usurestit = 0 And usudestino = 0");
                $tblP4 = pg_fetch_row($rsP4);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Sob Guarda SSV: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP4[0], 0, ",","."), 0, 1, 'R');

                $rsP3 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And codencdestino != 0");
                $tblP3 = pg_fetch_row($rsP3);
                $pdf->SetFont('Arial', '' , 10); 
                $pdf->SetX(55);
                $pdf->Cell(50, 4, "Destinados: ", 0, 0, 'L');
                $pdf->SetFont('Arial', 'B' , 10); 
                $pdf->Cell(10, 4, number_format($tblP3[0], 0, ",","."), 0, 0, 'R');

                $rsPR = pg_query($Conec, "SELECT (dataarquivou - datareceb) FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usudestino != 0");
                $rowPR = pg_num_rows($rsPR);
                $Dias = 0;
                while($tblPR = pg_fetch_row($rsPR)){
                    $Dias = $Dias+$tblPR[0];
                }

                if($Dias > 0){
                    $pdf->SetFont('Arial', '' , 8); 
                    $pdf->Cell(25, 4, "Tempo médio: ".number_format(($Dias/$rowPR), 1, ",",".")." dias", 0, 0, 'L');
                }
                $pdf->Cell(25, 4, "", 0, 1, 'L');

                $rsDest = pg_query($Conec, "SELECT numdest, descdest FROM ".$xProj.".bensdestinos WHERE numdest > 0 And descdest != '' ORDER BY descdest");
                while($tblDest = pg_fetch_row($rsDest)){
                    $CodDest = $tblDest[0];
                    $DescDest = $tblDest[1];
                    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usudestino != 0 And codencdestino = $CodDest ");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        $pdf->SetFont('Arial', '' , 8); 
                        $pdf->SetX(65);
                        $pdf->Cell(50, 4, $DescDest." (".$row1.")", 0, 1, 'L');
                        $rsProc = pg_query($Conec, "SELECT id, processo FROM ".$xProj.".bensprocessos WHERE processo != '' ORDER BY processo");
                        while($tblProc = pg_fetch_row($rsProc)){
                            $CodProc = $tblProc[0];
                            $DescProc = $tblProc[1];
                            $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE ativo != 0 And DATE_PART('YEAR', datareceb) = '$AnoTar' And usudestino != 0 And codencdestino = $CodDest And codencprocesso = $CodProc ");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $pdf->SetFont('Arial', '' , 7); 
                                $pdf->SetX(75);
                                $pdf->Cell(50, 4, $DescProc." (".$row2.")", 0, 1, 'L');
                            }
                        }
                    }
                }
                $pdf->ln(10);
                $rsP5 = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE ativo = 1 And DATE_PART('YEAR', datareceb) = '$AnoTar' And codusuins != 0 And usucsg = 0 And usurestit = 0 And usudestino = 0");
                $rowP5 = pg_num_rows($rsP5);
                if($rowP5 > 0){
                    $tblP5 = pg_fetch_row($rsP5);
                    $pdf->SetFont('Arial', '' , 10); 
                    $pdf->SetX(55);
                    $pdf->Cell(50, 4, "Não entregues ao SSV: ", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'B' , 10); 
                    $pdf->Cell(10, 4, number_format($rowP5, 0, ",","."), 0, 1, 'R');
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