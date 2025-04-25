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
    $Menu4 = escMenu($Conec, $xProj, 4);
    date_default_timezone_set('America/Sao_Paulo'); 
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
           // Vai para 1.5 cm da parte inferior
           $this->SetY(-15);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
//           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'      Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage("L", "A4");
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Condicionadores de Ar', $isUTF8=TRUE);
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
    if($Acao == "listamesManut"){
        $pdf->MultiCell(0, 3, "Controle de Manutenção nos Aparelhos de Ar Condicionado - ".$Menu4, 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 290, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "listamesManut"){
        $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
		$pdf->SetTitle('Relação Anual Manutenção', $isUTF8=TRUE);
        $rs0 = pg_query($Conec, "SELECT id, num_ap, localap, empresa_id FROM ".$xProj.".controle_ar WHERE num_ap IS NOT NULL And ativo = 1 ORDER BY num_ap");
        $row0 = pg_num_rows($rs0);

        if($row0 > 0){
            $pdf->ln(5);
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->MultiCell(0, 3, $Ano, 0, 'C', false);
            $pdf->ln(5);

            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(15);

            $pdf->Cell(10, 3, "Apar", 0, 0, 'C');
            $pdf->Cell(55, 3, "Local", 0, 0, 'L');
            $pdf->Cell(17, 3, "Jan", 0, 0, 'C');
            $pdf->Cell(17, 3, "Fev", 0, 0, 'C');
            $pdf->Cell(17, 3, "Mar", 0, 0, 'C');
            $pdf->Cell(17, 3, "Abr", 0, 0, 'C');
            $pdf->Cell(17, 3, "Mai", 0, 0, 'C');
            $pdf->Cell(17, 3, "Jun", 0, 0, 'C');
            $pdf->Cell(17, 3, "Jul", 0, 0, 'C');
            $pdf->Cell(17, 3, "Ago", 0, 0, 'C');
            $pdf->Cell(17, 3, "Set", 0, 0, 'C');
            $pdf->Cell(17, 3, "Out", 0, 0, 'C');
            $pdf->Cell(17, 3, "Nov", 0, 0, 'C');
            $pdf->Cell(17, 3, "Dez", 0, 1, 'C');
            $pdf->ln(1);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 290, $lin);

            while($tbl0 = pg_fetch_row($rs0)){
                $MaiorRow = 1; // quantos lançamentos 
                $Cod = $tbl0[0];
                $pdf->SetX(15); 
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->SetFont('Arial', '', 8); 
                $pdf->Cell(55, 5, $tbl0[2], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);

                $rs1 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '01' ORDER BY datavis DESC");
                $row1 = pg_num_rows($rs1);
                if($row1 > $MaiorRow){$MaiorRow = $row1;}
                $lin = $pdf->GetY();
                $pdf->SetX(80);
                if($row1 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row1 == 1){
                    $tbl1 = pg_fetch_row($rs1);
                    if($tbl1[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl1[0]."/".$tbl1[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row1 > 1){
                    $Cont=1;
                    while($tbl1 = pg_fetch_row($rs1)){
                        if($tbl1[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(80);
                        if($Cont < $row1){
                            $pdf->Cell(17, 5, $tbl1[0]."/".$tbl1[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(80);
                            $pdf->Cell(17, 5, $tbl1[0]."/".$tbl1[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs2 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '02' ORDER BY datavis DESC");
                $row2 = pg_num_rows($rs2);
                if($row2 > $MaiorRow){$MaiorRow = $row2;}
                $lin = $pdf->GetY();
                $pdf->SetX(97);
                if($row2 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row2 == 1){
                    $tbl2 = pg_fetch_row($rs2);
                    if($tbl2[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl2[0]."/".$tbl2[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row2 > 1){
                    $Cont=1;
                    while($tbl2 = pg_fetch_row($rs2)){
                        if($tbl2[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(97);
                        if($Cont < $row2){
                            $pdf->Cell(17, 5, $tbl2[0]."/".$tbl2[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(97);
                            $pdf->Cell(17, 5, $tbl2[0]."/".$tbl2[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }
                
                $rs3 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '03' ORDER BY datavis DESC");
                $row3 = pg_num_rows($rs3);
                if($row3 > $MaiorRow){$MaiorRow = $row3;}
                $lin = $pdf->GetY();
                $pdf->SetX(114);
                if($row3 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row3 == 1){
                    $tbl3 = pg_fetch_row($rs3);
                    if($tbl3[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl3[0]."/".$tbl3[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row3 > 1){
                    $Cont=1;
                    while($tbl3 = pg_fetch_row($rs3)){
                        if($tbl3[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(114);
                        if($Cont < $row3){
                            $pdf->Cell(17, 5, $tbl3[0]."/".$tbl3[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(114);
                            $pdf->Cell(17, 5, $tbl3[0]."/".$tbl3[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs4 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '04' ORDER BY datavis DESC");
                $row4 = pg_num_rows($rs4);
                if($row4 > $MaiorRow){$MaiorRow = $row4;}
                $lin = $pdf->GetY();
                $pdf->SetX(131);
                if($row4 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row4 == 1){
                    $tbl4 = pg_fetch_row($rs4);
                    if($tbl4[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl4[0]."/".$tbl4[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row4 > 1){
                    $Cont=1;
                    while($tbl4 = pg_fetch_row($rs4)){
                        if($tbl4[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(131);
                        if($Cont < $row4){
                            $pdf->Cell(17, 5, $tbl4[0]."/".$tbl4[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(131);
                            $pdf->Cell(17, 5, $tbl4[0]."/".$tbl4[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs5 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '05' ORDER BY datavis DESC");
                $row5 = pg_num_rows($rs5);
                if($row5 > $MaiorRow){$MaiorRow = $row5;}
                $lin = $pdf->GetY();
                $pdf->SetX(148);
                if($row5 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row5 == 1){
                    $tbl5 = pg_fetch_row($rs5);
                    if($tbl5[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl5[0]."/".$tbl5[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row5 > 1){
                    $Cont=1;
                    while($tbl5 = pg_fetch_row($rs5)){
                        if($tbl5[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(148);
                        if($Cont < $row5){
                            $pdf->Cell(17, 5, $tbl5[0]."/".$tbl5[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(148);
                            $pdf->Cell(17, 5, $tbl5[0]."/".$tbl5[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs6 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '06' ORDER BY datavis DESC");
                $row6 = pg_num_rows($rs6);
                if($row6 > $MaiorRow){$MaiorRow = $row6;}
                $lin = $pdf->GetY();
                $pdf->SetX(165);
                if($row6 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row6 == 1){
                    $tbl6 = pg_fetch_row($rs6);
                    if($tbl6[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl6[0]."/".$tbl6[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row6 > 1){
                    $Cont=1;
                    while($tbl6 = pg_fetch_row($rs6)){
                        if($tbl6[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(165);
                        if($Cont < $row6){
                            $pdf->Cell(17, 5, $tbl6[0]."/".$tbl6[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(165);
                            $pdf->Cell(17, 5, $tbl6[0]."/".$tbl6[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $linFinal = $pdf->GetY();
                    $pdf->SetY($lin);
                }

                $rs7 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '07' ORDER BY datavis DESC");
                $row7 = pg_num_rows($rs7);
                if($row7 > $MaiorRow){$MaiorRow = $row7;}
                $lin = $pdf->GetY();
                $pdf->SetX(182);
                if($row7 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row7 == 1){
                    $tbl7 = pg_fetch_row($rs7);
                    if($tbl7[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl7[0]."/".$tbl7[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row7 > 1){
                    $Cont=1;
                    while($tbl7 = pg_fetch_row($rs7)){
                        if($tbl7[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(182);
                        if($Cont < $row7){
                            $pdf->Cell(17, 5, $tbl7[0]."/".$tbl7[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(182);
                            $pdf->Cell(17, 5, $tbl7[0]."/".$tbl7[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs8 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '08' ORDER BY datavis DESC");
                $row8 = pg_num_rows($rs8);
                if($row8 > $MaiorRow){$MaiorRow = $row8;}
                $lin = $pdf->GetY();
                $pdf->SetX(199);
                if($row8 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row8 == 1){
                    $tbl8 = pg_fetch_row($rs8);
                    if($tbl8[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl8[0]."/".$tbl8[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row8 > 1){
                    $Cont=1;
                    while($tbl8 = pg_fetch_row($rs8)){
                        if($tbl8[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(199);
                        if($Cont < $row8){
                            $pdf->Cell(17, 5, $tbl8[0]."/".$tbl8[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(199);
                            $pdf->Cell(17, 5, $tbl8[0]."/".$tbl8[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs9 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '09' ORDER BY datavis DESC");
                $row9 = pg_num_rows($rs9);
                if($row9 > $MaiorRow){$MaiorRow = $row9;}
                $lin = $pdf->GetY();
                $pdf->SetX(216);
                if($row9 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row9 == 1){
                    $tbl9 = pg_fetch_row($rs9);
                    if($tbl9[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl9[0]."/".$tbl9[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row9 > 1){
                    $Cont=1;
                    while($tbl9 = pg_fetch_row($rs9)){
                        if($tbl9[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(216);
                        if($Cont < $row9){
                            $pdf->Cell(17, 5, $tbl9[0]."/".$tbl9[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(216);
                            $pdf->Cell(17, 5, $tbl9[0]."/".$tbl9[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs10 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '10' ORDER BY datavis DESC");
                $row10 = pg_num_rows($rs10);
                if($row10 > $MaiorRow){$MaiorRow = $row10;}
                $lin = $pdf->GetY();
                $pdf->SetX(233);
                if($row10 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row10 == 1){
                    $tbl10 = pg_fetch_row($rs10);
                    if($tbl10[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl10[0]."/".$tbl10[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row10 > 1){
                    $Cont=1;
                    while($tbl10 = pg_fetch_row($rs10)){
                        if($tbl10[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(233);
                        if($Cont < $row10){
                            $pdf->Cell(17, 5, $tbl10[0]."/".$tbl10[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(233);
                            $pdf->Cell(17, 5, $tbl10[0]."/".$tbl10[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs11 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '11' ORDER BY datavis DESC");
                $row11 = pg_num_rows($rs11);
                if($row11 > $MaiorRow){$MaiorRow = $row11;}
                $lin = $pdf->GetY();
                $pdf->SetX(250);
                if($row11 == 0){
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                if($row11 == 1){
                    $tbl11 = pg_fetch_row($rs11);
                    if($tbl11[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl11[0]."/".$tbl11[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row11 > 1){
                    $Cont=1;
                    while($tbl11 = pg_fetch_row($rs11)){
                        if($tbl11[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(250);
                        if($Cont < $row11){
                            $pdf->Cell(17, 5, $tbl11[0]."/".$tbl11[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(250);
                            $pdf->Cell(17, 5, $tbl11[0]."/".$tbl11[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }

                $rs12 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '12' ORDER BY datavis DESC");
                $row12 = pg_num_rows($rs12);
                if($row12 > $MaiorRow){$MaiorRow = $row12;}
                $lin = $pdf->GetY();
                $pdf->SetX(267);
                if($row12 == 0){
                    $pdf->Cell(17, 5, '', 0, 1, 'C');
                }
                if($row12 == 1){
                    $tbl12 = pg_fetch_row($rs12);
                    if($tbl12[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->SetX(267);
                    $pdf->Cell(17, 5, $tbl12[0]."/".$tbl12[1], 0, 1, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
                if($row12 > 1){
                    $Cont=1;
                    while($tbl12 = pg_fetch_row($rs12)){
                        if($tbl12[2] == 2){
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                        }
                        $pdf->SetX(267);
                        if($Cont < $row12){
                            $pdf->Cell(17, 5, $tbl12[0]."/".$tbl12[1], 0, 1, 'C');
                        }else{
                            $pdf->SetX(267);
                            $pdf->Cell(17, 5, $tbl12[0]."/".$tbl12[1], 0, 0, 'C');
                        }
                        $Cont++;
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetY($lin);
                }
                
                $i = 1;
                while($i < $MaiorRow){
                    $pdf->Cell(17, 5, '', 0, 1, 'C');
                    $i++;
                }

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 290, $lin);
            }
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);

            $pdf->ln(10);
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(17, 5, "Ano: ".$Ano, 0, 1, 'C');

            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".visitas_ar WHERE ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And tipovis = 1");
            $row0 = pg_num_rows($rs0);
            $pdf->SetX(50);
            $pdf->Cell(60, 5, "Manutenção Preventiva: ", 0, 0, 'L');
            $pdf->Cell(10, 5, $row0, 0, 1, 'R');

            $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".visitas_ar WHERE ativo = 1 And DATE_PART('YEAR', datavis) = '$Ano' And tipovis = 2");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(50);
            $pdf->Cell(60, 5, "Manutenção Corretiva: ", 0, 0, 'L');
            $pdf->Cell(10, 5, $row1, 0, 1, 'R');
        }else{
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->ln(5);
            $pdf->SetX(50);
            $pdf->Cell(17, 5, 'Nenhum aparelho encontrado.', 0, 1, 'C');
        }
    }
 }
 $pdf->Output();