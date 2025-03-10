<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }
    //formata CNPJ e CPF em máscaras.
function Mask($mask,$str){
    $str = str_replace(" ","",$str);
    for($i=0;$i<strlen($str);$i++){
        $mask[strpos($mask,"#")] = $str[$i];
    }
    //Chamada Mask("###.###.###-##",$Var) cpf
    //Chamada Mask("##.###.###/####-##",$Var) cnpj
    return $mask;
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
    $pdf->AddPage();
    $pdf->SetLeftMargin(20);
    $pdf->SetTitle('Extintores', $isUTF8=TRUE);      
    $pdf->SetFont('Arial', '' , 12); //Monta o arquivo pdf  
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
    $pdf->MultiCell(0, 4, "Controle de Extintores de Incêndio", 0, 'C', false);

    if($Acao == "imprExtint"){
        $TempoAviso  = parAdm("aviso_extint", $Conec, $xProj); // dias de antecedência para aviso
        $Condic = "ativo = 1";
        if(isset($_REQUEST["valor"])){
            $Cond = $_REQUEST["valor"];
            if($Cond == "vencer"){
                $Condic = "ativo = 1 And datavalid BETWEEN CURRENT_DATE AND CURRENT_DATE+$TempoAviso";
                $pdf->SetTextColor(205, 0, 205);
                $pdf->MultiCell(0, 3, "Próximos ao Vencimento", 0, 'C', false);
            }
            if($Cond == "vencidos"){
                $Condic = "ativo = 1 And datavalid < CURRENT_DATE";
                $pdf->SetTextColor(255, 0, 0);
                $pdf->MultiCell(0, 3, "Extintores Vencidos", 0, 'C', false);
            }
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->ln();
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->SetDrawColor(200); // cinza claro  
        $pdf->SetFont('Arial', '', 10);

        $rs0 = pg_query($Conec, "SELECT id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, TO_CHAR(datacarga, 'DD/MM/YYYY'), TO_CHAR(datavalid, 'DD/MM/YYYY'), TO_CHAR(datacasco, 
        'DD/MM/YYYY'), TO_CHAR(datacasco, 'YYYY'), CASE WHEN datavalid BETWEEN CURRENT_DATE AND CURRENT_DATE+$TempoAviso THEN 'aviso' WHEN datavalid < CURRENT_DATE THEN 'vencido' END, CASE WHEN datavalid <= CURRENT_DATE THEN 'vencido' END
        FROM ".$xProj.".extintores WHERE $Condic ORDER BY ext_num");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 7);
            $pdf->SetX(15);
            $pdf->Cell(10, 5, "Núm", 0, 0, 'C');
            $pdf->Cell(45, 5, "Tipo", 0, 0, 'L');
            $pdf->Cell(20, 5, "Capacidade", 0, 0, 'L'); // capacidade
            $pdf->Cell(22, 5, "Inspeção", 0, 0, 'L'); // datacarga
            $pdf->Cell(22, 5, "Vencimento", 0, 0, 'L'); // vencimento
            $pdf->Cell(22, 5, "Local", 0, 1, 'L'); // casco
            
            $lin = $pdf->GetY();
            $pdf->Line(15, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(1);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $Num = $tbl0[1];
                $Local = $tbl0[2];
                $Empr = $tbl0[3];
                $Tipo = $tbl0[4];

                if($tbl0[11] == "3000"){
                    $DataCasco = "";
                }else{
                    $DataCasco = $tbl0[10];
                }

                $rs1 = pg_query($Conec, "SELECT empresa FROM ".$xProj.".extintores_empr WHERE id = $Empr");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $DescEmpr = $tbl1[0];
                }else{
                    $DescEmpr = "";
                }

                $pdf->ln(1);
                $rs2 = pg_query($Conec, "SELECT desc_tipo FROM ".$xProj.".extintores_tipo WHERE id = $Tipo");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $DescTipo = $tbl2[0];
                }else{
                    $DescTipo = "";
                }

                $pdf->ln(1);
                $pdf->SetX(15);
                $pdf->Cell(10, 5, str_pad($Num, 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->Cell(45, 5, substr($DescTipo,0,25), 0, 0, 'L');
                $pdf->Cell(20, 5, substr($tbl0[5], 0, 13), 0, 0, 'L'); // capacidade
                $pdf->Cell(22, 5, $tbl0[8], 0, 0, 'L'); // datacarga

                //Vencimento
                if($tbl0[12] == 'aviso'){
                    $pdf->SetTextColor(205, 0, 205); // magenta3
                }
                if($tbl0[12] == 'vencido'){
                    $pdf->SetTextColor(255, 0, 0); // vermelho
                }
                $pdf->Cell(22, 5, $tbl0[9], 0, 0, 'L'); // vencimento
                $pdf->SetTextColor(0, 0, 0); // preto

                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(0, 4, $tbl0[2], 0, 'L', false); // local

                //Segunda linha
                $pdf->SetX(25);
                $pdf->Cell(40, 4, "Reg: ".substr($tbl0[6], 0, 22), 0, 0, 'L'); // registro
                $pdf->Cell(40, 4, "Série: ".substr($tbl0[7],0,22), 0, 0, 'L'); // série
                $pdf->Cell(29, 4, "Casco: ".$DataCasco, 0, 0, 'L'); // casco
                $pdf->MultiCell(0, 4, $DescEmpr, 0, 'L', false);// empresa
                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(2);
                $lin = $pdf->GetY();
                $pdf->Line(15, $lin, 200, $lin);
            }
        }else{
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetX(20);
            if($Cond == "vencidos"){
                $pdf->Cell(0, 10, 'Nenhum extintor vencido.', 0, 1, 'C');
            }
            if($Cond == "vencer"){
                $pdf->Cell(0, 10, 'Nenhum extintor próximo ao vencimento.', 0, 1, 'C');
            }
            if($Cond == "todos"){
                $pdf->Cell(0, 10, 'Nada foi encontrado.', 0, 1, 'C');
            }
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 200, $lin);
            $pdf->ln(10);
        }
    }
 }
 $pdf->Output();