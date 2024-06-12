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
        '09' => 'Novembro',
        '10' => 'Setembro',
        '11' => 'Outubro',
        '12' => 'Dezembro'
    ); 
    $mesNum_extenso = array(
        '1' => 'Janeiro',
        '2' => 'Fevereiro',
        '3' => 'Março',
        '4' => 'Abril',
        '5' => 'Maio',
        '6' => 'Junho',
        '7' => 'Julho',
        '8' => 'Agosto',
        '9' => 'Novembro',
        '10' => 'Setembro',
        '11' => 'Outubro',
        '12' => 'Dezembro'
    ); 

    class PDF extends FPDF{
        function Footer(){
           // Vai para 1.5 cm da parte inferior
           $this->SetY(-15);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
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
    $pdf->SetX(40); 
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
        $pdf->MultiCell(0, 3, "Controle de Manutenção nos Aparelhos de Ar Condicionado", 0, 'C', false);
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
                $Cod = $tbl0[0];
                $pdf->SetX(15); 
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 5, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->SetFont('Arial', '', 8); 
                $pdf->Cell(55, 5, $tbl0[2], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 10);

                $rs1 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '01'");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $pdf->Cell(17, 5, $tbl1[0]."/".$tbl1[1], 0, 0, 'C');
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs2 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '02'");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    if($tbl2[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl2[0]."/".$tbl2[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }
                
                $rs3 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '03'");
                $row3 = pg_num_rows($rs3);
                if($row3 > 0){
                    $tbl3 = pg_fetch_row($rs3);
                    if($tbl3[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl3[0]."/".$tbl3[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs4 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '04'");
                $row4 = pg_num_rows($rs4);
                if($row4 > 0){
                    $tbl4 = pg_fetch_row($rs4);
                    if($tbl4[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl4[0]."/".$tbl4[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs5 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '05'");
                $row5 = pg_num_rows($rs5);
                if($row5 > 0){
                    $tbl5 = pg_fetch_row($rs5);
                    if($tbl5[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl5[0]."/".$tbl5[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs6 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '06'");
                $row6 = pg_num_rows($rs6);
                if($row6 > 0){
                    $tbl6 = pg_fetch_row($rs6);
                    if($tbl6[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl6[0]."/".$tbl6[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs7 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '07'");
                $row7 = pg_num_rows($rs7);
                if($row7 > 0){
                    $tbl7 = pg_fetch_row($rs7);
                    if($tbl7[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl7[0]."/".$tbl7[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs8 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '08'");
                $row8 = pg_num_rows($rs8);
                if($row8 > 0){
                    $tbl8 = pg_fetch_row($rs8);
                    if($tbl8[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl8[0]."/".$tbl8[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs9 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '09'");
                $row9 = pg_num_rows($rs9);
                if($row9 > 0){
                    $tbl9 = pg_fetch_row($rs9);
                    if($tbl9[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl9[0]."/".$tbl9[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs10 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '10'");
                $row10 = pg_num_rows($rs10);
                if($row10 > 0){
                    $tbl10 = pg_fetch_row($rs10);
                    if($tbl10[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl10[0]."/".$tbl10[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs11 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '11'");
                $row11 = pg_num_rows($rs11);
                if($row11 > 0){
                    $tbl11 = pg_fetch_row($rs11);
                    if($tbl11[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl11[0]."/".$tbl11[1], 0, 0, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 0, 'C');
                }

                $rs12 = pg_query($Conec, "SELECT to_char(datavis, 'DD'), to_char(datavis, 'MM'), tipovis FROM ".$xProj.".visitas_ar WHERE controle_id = $Cod And DATE_PART('YEAR', datavis) = '$Ano' And DATE_PART('MONTH', datavis) = '12'");
                $row12 = pg_num_rows($rs12);
                if($row12 > 0){
                    $tbl12 = pg_fetch_row($rs12);
                    if($tbl12[2] == 2){
                        $pdf->SetTextColor(255, 0, 0); // vermelho
                    }
                    $pdf->Cell(17, 5, $tbl12[0]."/".$tbl12[1], 0, 1, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }else{
                    $pdf->Cell(17, 5, '', 0, 1, 'C');
                }

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 290, $lin);
            }
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);


            $pdf->ln(10);
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 14);
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