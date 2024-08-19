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
           $this->SetY(-15);  // Vai para 1.5 cm da parte inferior
           $this->SetFont('Arial','I',8);
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage("L", "A4");
    $pdf->SetLeftMargin(30);
    $pdf->SetTitle('Relação de Contratos', $isUTF8=TRUE);
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Claviculário Portaria', $isUTF8=TRUE);
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
    $pdf->MultiCell(0, 3, "Contratos", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 290, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    $Mes = "08";

    if($Acao == "listaContratos"){
//Contratadas
        $pdf->ln(5);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 5, "Empresas Contratadas pela Comunhão", 0, 'C', false);
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr,
        CASE WHEN dataaviso <= CURRENT_DATE THEN true And datavencim >= CURRENT_DATE ELSE false END 
        FROM ".$xProj.".contratos1 WHERE ativo = 1 ORDER BY dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(25);
            $pdf->Cell(20, 3, "Assinatura", 0, 0, 'C');
            $pdf->Cell(30, 3, "Número", 0, 0, 'C');
            $pdf->Cell(20, 3, "Vencimento", 0, 0, 'C');
            $pdf->Cell(65, 3, "Empresa", 0, 0, 'L');
            
            $pdf->Cell(20, 3, "Setor", 0, 0, 'L');
            $pdf->Cell(20, 3, "Vigência", 0, 0, 'L');
            $pdf->Cell(90, 3, "Objeto", 0, 0, 'L');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(25); 
                $pdf->Cell(20, 5, $tbl0[2], 0, 0, 'C');
                $pdf->Cell(30, 5, $tbl0[1], 0, 0, 'C');
                $pdf->Cell(20, 5, $tbl0[3], 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $tbl0[6]");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $DescEmpr = $tbl1[0];
                }else{
                    $DescEmpr = "";
                }

                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 5, substr($DescEmpr, 0, 65), 0, 0, 'L');

                $rs2 = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $tbl0[5]");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $DescSetor = $tbl2[0];
                }else{
                    $DescSetor = "";
                }
                $pdf->Cell(20, 5, $DescSetor, 0, 0, 'L');
                $pdf->Cell(20, 5, $tbl0[7], 0, 0, 'L');
                $pdf->MultiCell(100, 4, $tbl0[9], 0, 'L', false);

                $pdf->SetFont('Arial', '', 10);
                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);
            }
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum contrato encontrado.', 0, 1, 'L');
        }

//Contratantes
        $pdf->ln(10);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 5, "Empresas Contratantes", 0, 'C', false);
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr,
        CASE WHEN dataaviso <= CURRENT_DATE THEN true And datavencim >= CURRENT_DATE ELSE false END 
        FROM ".$xProj.".contratos2 WHERE ativo = 1 ORDER BY dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(25);
            $pdf->Cell(20, 3, "Assinatura", 0, 0, 'C');
            $pdf->Cell(30, 3, "Número", 0, 0, 'C');
            $pdf->Cell(20, 3, "Vencimento", 0, 0, 'C');
            $pdf->Cell(65, 3, "Empresa", 0, 0, 'L');
            
            $pdf->Cell(20, 3, "Setor", 0, 0, 'L');
            $pdf->Cell(20, 3, "Vigência", 0, 0, 'L');
            $pdf->Cell(90, 3, "Objeto", 0, 0, 'L');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(25); 
                $pdf->Cell(20, 5, $tbl0[2], 0, 0, 'C');
                $pdf->Cell(30, 5, $tbl0[1], 0, 0, 'C');
                $pdf->Cell(20, 5, $tbl0[3], 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $tbl0[6]");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $DescEmpr = $tbl1[0];
                }else{
                    $DescEmpr = "";
                }

                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 5, substr($DescEmpr, 0, 65), 0, 0, 'L');

                $rs2 = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $tbl0[5]");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $DescSetor = $tbl2[0];
                }else{
                    $DescSetor = "";
                }
                $pdf->Cell(20, 5, $DescSetor, 0, 0, 'L');
                $pdf->Cell(20, 5, $tbl0[7], 0, 0, 'L');
                $pdf->MultiCell(100, 4, $tbl0[9], 0, 'L', false);

                $pdf->SetFont('Arial', '', 10);
                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);
            }
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum contrato encontrado.', 0, 1, 'L');
        }
    }


    if($Acao == "listaContratadas"){
//Contratadas
        $pdf->ln(5);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 5, "Empresas Contratadas pela Comunhão", 0, 'C', false);
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr,
        CASE WHEN dataaviso <= CURRENT_DATE THEN true And datavencim >= CURRENT_DATE ELSE false END 
        FROM ".$xProj.".contratos1 WHERE ativo = 1 ORDER BY dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(25);
            $pdf->Cell(20, 3, "Assinatura", 0, 0, 'C');
            $pdf->Cell(30, 3, "Número", 0, 0, 'C');
            $pdf->Cell(20, 3, "Vencimento", 0, 0, 'C');
            $pdf->Cell(65, 3, "Empresa", 0, 0, 'L');
            
            $pdf->Cell(20, 3, "Setor", 0, 0, 'L');
            $pdf->Cell(20, 3, "Vigência", 0, 0, 'L');
            $pdf->Cell(90, 3, "Objeto", 0, 0, 'L');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(25); 
                $pdf->Cell(20, 5, $tbl0[2], 0, 0, 'C');
                $pdf->Cell(30, 5, $tbl0[1], 0, 0, 'C');
                $pdf->Cell(20, 5, $tbl0[3], 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $tbl0[6]");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $DescEmpr = $tbl1[0];
                }else{
                    $DescEmpr = "";
                }

                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 5, substr($DescEmpr, 0, 65), 0, 0, 'L');

                $rs2 = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $tbl0[5]");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $DescSetor = $tbl2[0];
                }else{
                    $DescSetor = "";
                }
                $pdf->Cell(20, 5, $DescSetor, 0, 0, 'L');
                $pdf->Cell(20, 5, $tbl0[7], 0, 0, 'L');
                $pdf->MultiCell(100, 4, $tbl0[9], 0, 'L', false);

                $pdf->SetFont('Arial', '', 10);
                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);
            }
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum contrato encontrado.', 0, 1, 'L');
        }
    }

    if($Acao == "listaContratantes"){
        //Contratantes
        $pdf->ln(10);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 5, "Empresas Contratantes", 0, 'C', false);
        $rs0 = pg_query($Conec, "SELECT id, numcontrato, TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), codsetor, codempresa, vigencia, notific, objetocontr,
        CASE WHEN dataaviso <= CURRENT_DATE THEN true And datavencim >= CURRENT_DATE ELSE false END 
        FROM ".$xProj.".contratos2 WHERE ativo = 1 ORDER BY dataassinat DESC");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(25);
            $pdf->Cell(20, 3, "Assinatura", 0, 0, 'C');
            $pdf->Cell(30, 3, "Número", 0, 0, 'C');
            $pdf->Cell(20, 3, "Vencimento", 0, 0, 'C');
            $pdf->Cell(65, 3, "Empresa", 0, 0, 'L');
            
            $pdf->Cell(20, 3, "Setor", 0, 0, 'L');
            $pdf->Cell(20, 3, "Vigência", 0, 0, 'L');
            $pdf->Cell(90, 3, "Objeto", 0, 0, 'L');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(25, $lin, 282, $lin);
            $pdf->SetFont('Arial', '', 10);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(25); 
                $pdf->Cell(20, 5, $tbl0[2], 0, 0, 'C');
                $pdf->Cell(30, 5, $tbl0[1], 0, 0, 'C');
                $pdf->Cell(20, 5, $tbl0[3], 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $tbl0[6]");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $DescEmpr = $tbl1[0];
                }else{
                    $DescEmpr = "";
                }

                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(65, 5, substr($DescEmpr, 0, 65), 0, 0, 'L');

                $rs2 = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $tbl0[5]");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $DescSetor = $tbl2[0];
                }else{
                    $DescSetor = "";
                }
                $pdf->Cell(20, 5, $DescSetor, 0, 0, 'L');
                $pdf->Cell(20, 5, $tbl0[7], 0, 0, 'L');
                $pdf->MultiCell(100, 4, $tbl0[9], 0, 'L', false);

                $pdf->SetFont('Arial', '', 10);
                $lin = $pdf->GetY();
                $pdf->Line(25, $lin, 282, $lin);
            }
            $pdf->SetX(50);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum contrato encontrado.', 0, 1, 'L');
        }
    }



 }
 $pdf->Output();