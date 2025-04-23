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
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    $pdf->SetLeftMargin(30);
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Viaturas', $isUTF8=TRUE);
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

    $pdf->MultiCell(0, 3, "Demonstrativo de Despesas com Viaturas", 0, 'C', false);
    $pdf->ln();

    $pdf->SetFont('Arial', 'I', 10);
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetDrawColor(200); // cinza claro  
    $pdf->ln(5);
    $pdf->SetTextColor(0, 0, 0);

    if($Acao == "listamesViat"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $pdf->MultiCell(0, 3, $mes_extenso[$Mes]."/".$Ano, 0, 'C', false);
    }
    if($Acao == "listaanoViat"){
        $Ano = filter_input(INPUT_GET, 'ano'); 
        $pdf->MultiCell(0, 3, "Ano ".$Ano, 0, 'C', false);
    }


    if($Acao == "listamesViat"){
        $rs0 = pg_query($Conec, "SELECT id, desc_viatura FROM ".$xProj.".viaturas_tipo WHERE ativo = 1 ORDER BY desc_viatura");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->ln(2);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $CodViat = $tbl0[0];
                $pdf->SetX(20); 
                $pdf->SetFont('Arial', 'IU', 12);
                $pdf->Cell(40, 5, $tbl0[1], 0, 1, 'L');
                $pdf->SetFont('Arial', '', 8);

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".viaturas.id, TO_CHAR(datacompra, 'DD/MM/YYYY'), volume, custo, desc_viatura, coddespesa, tipocomb, tipomanut, odometro 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_tipo ON ".$xProj.".viaturas.codveiculo = ".$xProj.".viaturas_tipo.id 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes ORDER BY datacompra DESC ");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1)){
                        $CodDesp = $tbl1[5]; // 1=Combust, 2=Manutenção
                        $CodComb = $tbl1[6];
                        $CodManut = $tbl1[7];
                        $Volume = $tbl1[2];
                        $Custo = $tbl1[3];
                        $Odomet = $tbl1[8];
                        $DescComb = "";
                        $DescManut = "";
                        if($CodDesp == 1){
                            $rs2 = pg_query($Conec, "SELECT desc_combust FROM ".$xProj.".viaturas_comb WHERE id = $CodComb ");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescComb = $tbl2[0];
                            }
                        }
                        if($CodDesp == 2){
                            $rs2 = pg_query($Conec, "SELECT desc_manut FROM ".$xProj.".viaturas_manut WHERE id = $CodManut ");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescManut = $tbl2[0];
                            }
                        }

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetX(40); 
                        $pdf->Cell(20, 4, $tbl1[1], 0, 0, 'L');
                        if($CodDesp == 1){
                            $pdf->Cell(20, 5, number_format(($Volume/100), 2, ",",".")." litros ", 0, 0, 'R');
                        }else{
                            $pdf->Cell(20, 5, "Manutenção", 0, 0, 'R');
                        }
                        $pdf->Cell(20, 4, $DescComb.$DescManut, 0, 0, 'L');
                        $pdf->Cell(30, 4, "R$ ".number_format(($Custo/100), 2, ",","."), 0, 0, 'R');
                        $pdf->Cell(40, 4, number_format($Odomet, 0, ",",".")." Km", 0, 1, 'R');

                    }
                }
                
                $lin = $pdf->GetY();
                $pdf->Line(40, $lin, 180, $lin);
                $pdf->ln(1);


                $rs4 = pg_query($Conec, "SELECT MIN(odometro) 
                FROM ".$xProj.".viaturas 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ativo = 1 And odometro > 0 ");
                $tbl4 = pg_fetch_row($rs4);
                $MinOdometro = $tbl4[0];

                $rs4 = pg_query($Conec, "SELECT MAX(odometro) 
                FROM ".$xProj.".viaturas 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ativo = 1 ");
                $tbl4 = pg_fetch_row($rs4);
                $MaxOdometro = $tbl4[0];

                //Combustível
                $rs3 = pg_query($Conec, "SELECT tipocomb, desc_combust 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_comb ON ".$xProj.".viaturas.tipocomb = ".$xProj.".viaturas_comb.id
                WHERE codveiculo = $CodViat And coddespesa = 1 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 GROUP BY tipocomb, desc_combust");
                $row3 = pg_num_rows($rs3);
                if($row3 > 1){
                    while($tbl3 = pg_fetch_row($rs3)){
                        $CodComb = $tbl3[0];
                        $DescComb = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And tipocomb = $CodComb And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Vol = $tbl4[0];
                        $Cust = $tbl4[1];

                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Subtotais: ", 0, 0, 'L');
                        $pdf->SetX(64); 
                        $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros ".$DescComb, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                    
                    $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                    $tbl4 = pg_fetch_row($rs4);
                    $Vol = $tbl4[0];
                    $Cust = $tbl4[1];
                    $pdf->SetX(45); 
                    $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                    $pdf->SetX(63); 
                    $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros", 0, 0, 'L');
                    $pdf->SetX(90); 
                    $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 0, 'R');
                    $pdf->SetX(130); 
                    $pdf->Cell(40, 5, number_format(($MaxOdometro-$MinOdometro), 0, ",",".")." Km", 0, 0, 'R');
                    $pdf->SetX(169);
                    $pdf->Cell(10, 5, "rodados", 0, 1, 'L');
                }else{
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $CodComb = $tbl3[0];
                        $DescComb = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And tipocomb = $CodComb And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Vol = $tbl4[0];
                        $Cust = $tbl4[1];

                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                        $pdf->SetX(63); 
                        $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros ".$DescComb, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 0, 'R');
                        $pdf->SetX(130); 
                        $pdf->Cell(40, 5, number_format(($MaxOdometro-$MinOdometro), 0, ",",".")." Km", 0, 0, 'R');
                        $pdf->SetX(169);
                        $pdf->Cell(10, 5, "rodados", 0, 1, 'L');
                    }
                }

                //Manutenção
                $rs3 = pg_query($Conec, "SELECT tipomanut, desc_manut 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_manut ON ".$xProj.".viaturas.tipomanut = ".$xProj.".viaturas_manut.id
                WHERE codveiculo = $CodViat And coddespesa = 2 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 GROUP BY tipomanut, desc_manut");
                $row3 = pg_num_rows($rs3);
                if($row3 > 1){
                    while($tbl3 = pg_fetch_row($rs3)){
                        $CodManut = $tbl3[0];
                        $DescManut = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And tipomanut = $CodManut And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Cust = $tbl4[0];
                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Subtotais: ", 0, 0, 'L');
                        $pdf->SetX(64); 
                        $pdf->Cell(40, 4, "Manutenção ".$DescManut, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                    
                    $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                    $tbl4 = pg_fetch_row($rs4);
                    $Cust = $tbl4[0];
                    $pdf->SetX(45); 
                    $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                    $pdf->SetX(90); 
                    $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                }else{
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $CodManut = $tbl3[0];
                        $DescManut = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And tipomanut = $CodManut And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Cust = $tbl4[0];
                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                        $pdf->SetX(63); 
                        $pdf->Cell(40, 4, "Manutenção ".$DescManut, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                }

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
                $pdf->ln();
            }
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }
    }


    if($Acao == "listaanoViat"){
        $rs0 = pg_query($Conec, "SELECT id, desc_viatura FROM ".$xProj.".viaturas_tipo WHERE ativo = 1 ORDER BY desc_viatura");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->ln(2);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $CodViat = $tbl0[0];
                $pdf->SetX(20); 
                $pdf->SetFont('Arial', 'IU', 12);
                $pdf->Cell(40, 5, $tbl0[1], 0, 1, 'L');
                $pdf->SetFont('Arial', '', 8);

                $rs1 = pg_query($Conec, "SELECT ".$xProj.".viaturas.id, TO_CHAR(datacompra, 'DD/MM/YYYY'), volume, custo, desc_viatura, coddespesa, tipocomb, tipomanut, odometro 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_tipo ON ".$xProj.".viaturas.codveiculo = ".$xProj.".viaturas_tipo.id 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano ORDER BY datacompra DESC ");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1)){
                        $CodDesp = $tbl1[5]; // 1=Combust, 2=Manutenção
                        $CodComb = $tbl1[6];
                        $CodManut = $tbl1[7];
                        $Volume = $tbl1[2];
                        $Custo = $tbl1[3];
                        $Odomet = $tbl1[8];
                        $DescComb = "";
                        $DescManut = "";
                        if($CodDesp == 1){
                            $rs2 = pg_query($Conec, "SELECT desc_combust FROM ".$xProj.".viaturas_comb WHERE id = $CodComb ");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescComb = $tbl2[0];
                            }
                        }
                        if($CodDesp == 2){
                            $rs2 = pg_query($Conec, "SELECT desc_manut FROM ".$xProj.".viaturas_manut WHERE id = $CodManut ");
                            $row2 = pg_num_rows($rs2);
                            if($row2 > 0){
                                $tbl2 = pg_fetch_row($rs2);
                                $DescManut = $tbl2[0];
                            }
                        }

                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetX(40); 
                        $pdf->Cell(20, 4, $tbl1[1], 0, 0, 'L');
                        if($CodDesp == 1){
                            $pdf->Cell(20, 5, number_format(($Volume/100), 2, ",",".")." litros ", 0, 0, 'R');
                        }else{
                            $pdf->Cell(20, 5, "Manutenção", 0, 0, 'R');
                        }
                        $pdf->Cell(20, 4, $DescComb.$DescManut, 0, 0, 'L');
                        $pdf->Cell(30, 4, "R$ ".number_format(($Custo/100), 2, ",","."), 0, 0, 'R');
                        $pdf->Cell(40, 4, number_format($Odomet, 0, ",",".")." Km", 0, 1, 'R');

                    }
                }
                
                $lin = $pdf->GetY();
                $pdf->Line(40, $lin, 180, $lin);
                $pdf->ln(1);

                $rs4 = pg_query($Conec, "SELECT MIN(odometro) 
                FROM ".$xProj.".viaturas 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And ativo = 1 And odometro > 0 ");
                $tbl4 = pg_fetch_row($rs4);
                $MinOdometro = $tbl4[0];

                $rs4 = pg_query($Conec, "SELECT MAX(odometro) 
                FROM ".$xProj.".viaturas 
                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0  ");
                $tbl4 = pg_fetch_row($rs4);
                $MaxOdometro = $tbl4[0];

                //Combustível
                $rs3 = pg_query($Conec, "SELECT tipocomb, desc_combust 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_comb ON ".$xProj.".viaturas.tipocomb = ".$xProj.".viaturas_comb.id
                WHERE codveiculo = $CodViat And coddespesa = 1 And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And volume != 0 GROUP BY tipocomb, desc_combust");
                $row3 = pg_num_rows($rs3);
                if($row3 > 1){
                    while($tbl3 = pg_fetch_row($rs3)){
                        $CodComb = $tbl3[0];
                        $DescComb = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And tipocomb = $CodComb And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Vol = $tbl4[0];
                        $Cust = $tbl4[1];

                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Subtotais: ", 0, 0, 'L');
                        $pdf->SetX(64); 
                        $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros ".$DescComb, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                    
                    $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                    $tbl4 = pg_fetch_row($rs4);
                    $Vol = $tbl4[0];
                    $Cust = $tbl4[1];
                    $pdf->SetX(45); 
                    $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                    $pdf->SetX(63); 
                    $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros", 0, 0, 'L');
                    $pdf->SetX(90); 
                    $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 0, 'R');
                    $pdf->SetX(130); 
                    $pdf->Cell(40, 5, number_format(($MaxOdometro-$MinOdometro), 0, ",",".")." Km", 0, 0, 'R');
                    $pdf->SetX(169);
                    $pdf->Cell(10, 5, "rodados", 0, 1, 'L');
                }else{
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $CodComb = $tbl3[0];
                        $DescComb = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(volume), SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 1 And tipocomb = $CodComb And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And volume != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Vol = $tbl4[0];
                        $Cust = $tbl4[1];

                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                        $pdf->SetX(63); 
                        $pdf->Cell(40, 4, number_format(($Vol/100), 2, ",",".")." litros ".$DescComb, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 0, 'R');
                        $pdf->SetX(130); 
                        $pdf->Cell(40, 5, number_format(($MaxOdometro-$MinOdometro), 0, ",",".")." Km", 0, 0, 'R');
                        $pdf->SetX(169);
                        $pdf->Cell(10, 5, "rodados", 0, 1, 'L');
                    }
                }

                //Manutenção
                $rs3 = pg_query($Conec, "SELECT tipomanut, desc_manut 
                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_manut ON ".$xProj.".viaturas.tipomanut = ".$xProj.".viaturas_manut.id
                WHERE codveiculo = $CodViat And coddespesa = 2 And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 GROUP BY tipomanut, desc_manut");
                $row3 = pg_num_rows($rs3);
                if($row3 > 1){
                    while($tbl3 = pg_fetch_row($rs3)){
                        $CodManut = $tbl3[0];
                        $DescManut = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And tipomanut = $CodManut And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Cust = $tbl4[0];
                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Subtotais: ", 0, 0, 'L');
                        $pdf->SetX(64); 
                        $pdf->Cell(40, 4, "Manutenção ".$DescManut, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                    
                    $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                    $tbl4 = pg_fetch_row($rs4);
                    $Cust = $tbl4[0];
                    $pdf->SetX(45); 
                    $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                    $pdf->SetX(90); 
                    $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                }else{
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $CodManut = $tbl3[0];
                        $DescManut = $tbl3[1];
                        $rs4 = pg_query($Conec, "SELECT SUM(custo) FROM ".$xProj.".viaturas WHERE codveiculo = $CodViat And coddespesa = 2 And tipomanut = $CodManut And DATE_PART('YEAR', datacompra) = $Ano And ".$xProj.".viaturas.ativo = 1 And tipomanut != 0 ");
                        $tbl4 = pg_fetch_row($rs4);
                        $Cust = $tbl4[0];
                        $pdf->SetX(45); 
                        $pdf->Cell(40, 4, "Total: ", 0, 0, 'L');
                        $pdf->SetX(63); 
                        $pdf->Cell(40, 4, "Manutenção ".$DescManut, 0, 0, 'L');
                        $pdf->SetX(90); 
                        $pdf->Cell(40, 4, "R$ ".number_format(($Cust/100), 2, ",","."), 0, 1, 'R');
                    }
                }

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
                $pdf->ln();
            }
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }
    }
}
$pdf->Output();