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
    $pdf->AddPage();
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Relação Mensal Água', $isUTF8=TRUE);
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
//    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(150, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(150, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "listamesAgua" || $Acao == "listaanoAgua"){
        $pdf->MultiCell(150, 3, "Controle do Consumo de Água", 0, 'C', false);
    }
    if($Acao == "listamesEletric" || $Acao == "listaanoEletric"){
        $Colec = (int) filter_input(INPUT_GET, 'colec'); 
        if($Colec == 1){
            $pdf->MultiCell(150, 3, "Controle do Consumo de Energia Elétrica - Comunhão", 0, 'C', false);
        }
        if($Colec == 2){
            $pdf->MultiCell(150, 3, "Controle do Consumo de Energia Elétrica - Operadora Claro", 0, 'C', false);
        }
        if($Colec == 3){
            $pdf->MultiCell(150, 3, "Controle do Consumo de Energia Elétrica - Operadora Oi", 0, 'C', false);
        }
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);


    $rs = pg_query($Conec, "SELECT valoriniagua, TO_CHAR(datainiagua, 'YYYY/MM/DD'), valorinieletric, TO_CHAR(datainieletric, 'YYYY/MM/DD'), valorinieletric2, TO_CHAR(datainieletric2, 'YYYY/MM/DD'), valorinieletric3, TO_CHAR(datainieletric3, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $ValorIniAgua = $tbl[0];
        $DataIniAgua = $tbl[1];
        $ValorIniEletric = $tbl[2];
        $DataIniEletric = $tbl[3];
        $ValorIniEletric2 = $tbl[4];
        $DataIniEletric2 = $tbl[5];
        $ValorIniEletric3 = $tbl[6];
        $DataIniEletric3 = $tbl[7];
    }

    if($Acao == "listamesAgua"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if($Mes < 10){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];

        if($ValorIniAgua > 0 && !is_null($DataIniAgua)){
            $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3, dataleitura FROM ".$xProj.".leitura_agua WHERE ativo = 1 And DATE_PART('MONTH', dataleitura) = '$Mes' And DATE_PART('YEAR', dataleitura) = '$Ano' ORDER BY dataleitura ");
            $row0 = pg_num_rows($rs0);
            $Cont = 0;
            $Leit24Ant = 0;
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(7);
            if($row0 > 0){
                $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
                $pdf->SetFont('Arial', 'I', 14);
                $pdf->MultiCell(0, 3, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
                $pdf->ln(5);

                $pdf->SetFont('Arial', 'I', 8);
                $pdf->SetX(45); 
                $pdf->Cell(18, 3, "Leitura", 0, 0, 'C');
                $pdf->SetX(85); 
                $pdf->Cell(20, 3, "Leitura", 0, 0, 'C');
                $pdf->SetX(129); 
                $pdf->Cell(20, 3, "Leitura", 0, 1, 'C');

                $pdf->Cell(18, 4, "Data", 0, 0, 'C');
                $pdf->Cell(15, 4, "Sem", 0, 0, 'L');
                $pdf->Cell(22, 4, "07h30", 0, 0, 'C');
                $pdf->Cell(25, 4, "Cons", 0, 0, 'L');
                $pdf->Cell(20, 4, "16h30", 0, 0, 'L');
                $pdf->Cell(24, 4, "Cons", 0, 0, 'L');
                $pdf->Cell(20, 4, "24h00", 0, 0, 'L');
                $pdf->Cell(24, 4, "Cons", 0, 0, 'L');
                $pdf->Cell(20, 4, "Cons Diário", 0, 1, 'L');
                $pdf->SetTextColor(0, 0, 0);
                $lin = $pdf->GetY();
                $pdf->SetDrawColor(200); // cinza claro
                $pdf->Line(10, $lin, 200, $lin);

                while ($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0];
                    $DataLinha = $tbl0[6];
                    $pdf->Cell(35, 5, $tbl0[1]."     ".$semana[$tbl0[2]], 0, 0, 'L');

                    if(strtotime($DataLinha) == strtotime($DataIniAgua)){ // "2024-03-01"
                        $Leit24Ant = $ValorIniAgua;  //1696.485;
                    }else{
                        $rs1 = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_agua WHERE dataleitura = (date '$DataLinha' - 1) And ativo = 1");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $Leit24Ant = $tbl1[0];
                        }
                    }
                    $Leit07 = $tbl0[3];
                    $Leit16 = $tbl0[4];
                    $Leit24 = $tbl0[5];
                    if($Leit07 == 0){
                        $Cons1 = 0;
                    }else{
                        $Cons1 = ($Leit07-$Leit24Ant);
                    }
                    if($Leit16 == 0){
                        $Cons2 = 0;
                    }else{
                        $Cons2 = ($Leit16-$Leit07);
                    }
                    if($Leit24 == 0){
                        $Cons3 = 0;    
                    }else{
                        $Cons3 = ($Leit24-$Leit16);
                    }

                    if($Leit07 == 0){
                        $Cons1 = 0;
                        $Cons2 = 0;
                        $Cons3 = 0;
                    }
                    if($Leit16 == 0){
                        $Cons2 = 0;
                        $Cons3 = 0;
                    }
                    $ConsDia = $Cons1+$Cons2+$Cons3;

                    $pdf->Cell(20, 5, number_format($tbl0[3], 3, ",","."), 0, 0, 'L');
                    $pdf->Cell(22, 5, number_format($Cons1, 3, ",","."), 0, 0, 'L');

                    $pdf->Cell(22, 5, number_format($tbl0[4], 3, ",","."), 0, 0, 'L');
                    $pdf->Cell(22, 5, number_format($Cons2, 3, ",","."), 0, 0, 'L');

                    $pdf->Cell(22, 5, number_format($tbl0[5], 3, ",","."), 0, 0, 'L');
                    $pdf->Cell(20, 5, number_format($Cons3, 3, ",","."), 0, 0, 'L');

                    $pdf->Cell(20, 5, number_format($ConsDia, 3, ",","."), 0, 1, 'R');

                    $lin = $pdf->GetY();
                    $pdf->Line(10, $lin, 200, $lin);
                }

                //Estatística
                $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura), COUNT(id), SUM(leitura1), SUM(leitura2), SUM(leitura3) 
                FROM ".$xProj.".leitura_agua 
                WHERE dataleitura IS NOT NULL And leitura1 != 0  And DATE_PART('MONTH', dataleitura) = '$Mes' And DATE_PART('YEAR', dataleitura) = '$Ano'
                GROUP BY DATE_PART('MONTH', dataleitura) ORDER BY DATE_PART('MONTH', dataleitura) DESC ");

                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1) ){
                        $Mes = $tbl1[0];
                        $QuantDias = $tbl1[1];
                        $SomaLeit1 = 0;
                        $SomaLeit2 = 0;
                        $SomaLeit3 = 0;
                        $SomaLeitAnt = 0;
                        $rs2 = pg_query($Conec, "SELECT dataleitura, leitura1, leitura2, leitura3 FROM ".$xProj.".leitura_agua 
                        WHERE DATE_PART('MONTH', dataleitura) = $Mes And DATE_PART('YEAR', dataleitura) = '$Ano' And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0 ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            while($tbl2 = pg_fetch_row($rs2) ){
                                $DataLinha = $tbl2[0]; // dataleitura
                                $SomaLeit1 = $SomaLeit1+$tbl2[1];
                                $SomaLeit2 = $SomaLeit2+$tbl2[2];
                                $SomaLeit3 = $SomaLeit3+$tbl2[3];
        
                                if(strtotime($DataLinha) == strtotime($DataIniAgua)){ // "2024-03-01"
                                    $SomaLeitAnt = ($SomaLeitAnt+$ValorIniAgua);  //1696.485
                                }
        
                                if($DataLinha != $DataIniAgua){
                                    $rs3 = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_agua 
                                    WHERE dataleitura = (date '$DataLinha' - 1) And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0");
                                    $tbl3 = pg_fetch_row($rs3);
                                    $row3 = pg_num_rows($rs3);
                                    if($row3 > 0){
                                        $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                                    }
                                }
                                $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                                $Cons2 = ($SomaLeit2-$SomaLeit1);
                                $Cons3 = ($SomaLeit3-$SomaLeit2);
                                $MediaDiaria = ($Cons1+$Cons2+$Cons3)/$QuantDias;
                            }
                            $pdf->SetFont('Arial', 'I', 10);
                            $pdf->Cell(163, 5, "Consumo Mensal:", 0, 0, 'R');
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                            $pdf->Cell(26, 5, number_format(($Cons1+$Cons2+$Cons3), 3, ",",".")." m3", 0, 1, 'R');
                            $pdf->SetTextColor(0, 0, 0);

                            $pdf->SetFont('Arial', 'I', 8);
                            $pdf->ln(10);
                            $pdf->SetX(130); 
                            $pdf->Cell(22, 4, "Períodos", 0, 1, 'L');

                            $pdf->SetTextColor(125, 125, 125); //cinza

                            $pdf->SetX(100); 
                            $pdf->Cell(24, 4, "00h00 / 07h30", 0, 0, 'C');
                            $pdf->Cell(24, 4, "07h30 / 16h30", 0, 0, 'C');
                            $pdf->Cell(24, 4, "16h30 / 24h00", 0, 1, 'C');

                            $pdf->SetTextColor(0, 0, 0);
                            $lin = $pdf->GetY();
                            $pdf->SetDrawColor(200); // cinza claro                
                            $pdf->Line(30, $lin, 180, $lin);
                            $pdf->SetX(40); 
                            $pdf->Cell(20, 5, "Consumo Mensal por Período", 0, 0, 'L');
                            $pdf->SetX(95); 
                            $pdf->Cell(24, 5, number_format($Cons1, 3, ",","."), 0, 0, 'R');
                            $pdf->Cell(24, 5, number_format($Cons2, 3, ",","."), 0, 0, 'R');
                            $pdf->Cell(24, 5, number_format($Cons3, 3, ",","."), 0, 1, 'R');

                            $pdf->SetX(40); 
                            $pdf->Cell(20, 5, "Consumo Mensal Diário por Período", 0, 0, 'L');
                            $pdf->SetX(95); 
                            $pdf->Cell(24, 5, number_format($Cons1/$QuantDias, 3, ",","."), 0, 0, 'R');
                            $pdf->Cell(24, 5, number_format($Cons2/$QuantDias, 3, ",","."), 0, 0, 'R');
                            $pdf->Cell(24, 5, number_format($Cons3/$QuantDias, 3, ",","."), 0, 1, 'R');
                            $lin = $pdf->GetY();
                            $pdf->Line(40, $lin, 167, $lin);

                            $pdf->SetX(112); 
                            if($QuantDias == 1){
                                $pdf->Cell(20, 5, "Consumo Médio Diário: ( 1 dia)", 0, 0, 'L');
                            }else{
                                $pdf->Cell(20, 5, "Consumo Médio Diário: "."(".$QuantDias." dias)", 0, 0, 'L');
                            }
                            $pdf->SetX(145); 
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                            $pdf->Cell(27, 5, number_format($MediaDiaria, 3, ",",".")." m3", 0, 1, 'R');
                            $pdf->SetTextColor(0, 0, 0);

                            $lin = $pdf->GetY();
                            $pdf->SetDrawColor(200); // cinza claro                
                            $pdf->Line(30, $lin, 180, $lin);
                        }
                        $pdf->ln(20);
                    }
                }
            }else{
                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(10);
                $pdf->Cell(20, 4, "Ndenhum registro encontrado. Informe à ATI,", 0, 1, 'L');
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "É necessário inserir os valores iniciais da medição nos parâmetros do sistema. Informe à ATI,", 0, 1, 'L');
        }
    }

    if($Acao == "listaanoAgua"){
        $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
		$pdf->SetTitle('Relação Anual Água', $isUTF8=TRUE);
        if($ValorIniAgua > 0 && !is_null($DataIniAgua)){
            $pdf->ln(7);
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->MultiCell(0, 3, $Ano, 0, 'C', false);
            $pdf->ln(5);

            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50); 
            $pdf->Cell(14, 3, "Período", 0, 0, 'C');
            $pdf->SetX(86); 
            $pdf->Cell(20, 3, "Período", 0, 0, 'C');
            $pdf->SetX(126); 
            $pdf->Cell(20, 3, "Período", 0, 0, 'C');
            $pdf->SetX(165); 
            $pdf->Cell(20, 3, "Consumo", 0, 1, 'C');

            $pdf->SetX(20); 
            $pdf->Cell(20, 4, "Mês", 0, 0, 'C');
            $pdf->SetX(45); 
            $pdf->Cell(23, 4, "07h30", 0, 0, 'C');
            $pdf->SetX(86); 
            $pdf->Cell(20, 4, "16h30", 0, 0, 'C');
            $pdf->SetX(126); 
            $pdf->Cell(20, 4, "24h00", 0, 0, 'C');

            $pdf->SetX(165); 
            $pdf->Cell(20, 4, "Mensal", 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0);
            $lin = $pdf->GetY();
            $pdf->SetDrawColor(200); // cinza claro                
            $pdf->Line(10, $lin, 200, $lin);

                $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura), COUNT(id), SUM(leitura1), SUM(leitura2), SUM(leitura3) 
                FROM ".$xProj.".leitura_agua 
                WHERE dataleitura IS NOT NULL And leitura1 != 0  And DATE_PART('YEAR', dataleitura) = '$Ano'
                GROUP BY DATE_PART('MONTH', dataleitura) ORDER BY DATE_PART('MONTH', dataleitura) ");

                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1) ){
                        $Mes = $tbl1[0];
                        $QuantDias = $tbl1[1];
                        $SomaLeit1 = 0;
                        $SomaLeit2 = 0;
                        $SomaLeit3 = 0;
                        $SomaLeitAnt = 0;
                        $rs2 = pg_query($Conec, "SELECT dataleitura, leitura1, leitura2, leitura3 FROM ".$xProj.".leitura_agua 
                        WHERE DATE_PART('MONTH', dataleitura) = $Mes And DATE_PART('YEAR', dataleitura) = '$Ano' And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0 ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            while($tbl2 = pg_fetch_row($rs2) ){
                                $DataLinha = $tbl2[0]; // dataleitura
                                $SomaLeit1 = $SomaLeit1+$tbl2[1];
                                $SomaLeit2 = $SomaLeit2+$tbl2[2];
                                $SomaLeit3 = $SomaLeit3+$tbl2[3];
        
                                if(strtotime($DataLinha) == strtotime($DataIniAgua)){ // "2024-03-01"
                                    $SomaLeitAnt = ($SomaLeitAnt+$ValorIniAgua);  //1696.485
                                }
        
                                if($DataLinha != $DataIniAgua){
                                    $rs3 = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_agua 
                                    WHERE dataleitura = (date '$DataLinha' - 1) And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0");
                                    $tbl3 = pg_fetch_row($rs3);
                                    $row3 = pg_num_rows($rs3);
                                    if($row3 > 0){
                                        $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                                    }
                                }
                                $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                                $Cons2 = ($SomaLeit2-$SomaLeit1);
                                $Cons3 = ($SomaLeit3-$SomaLeit2);
                                $MediaDiaria = ($Cons1+$Cons2+$Cons3)/$QuantDias;
                            }
                            $pdf->SetX(22); 
                            $pdf->Cell(14, 5, $mesNum_extenso[$Mes], 0, 0, 'C');
                            $pdf->SetX(41); 
                            $pdf->Cell(20, 5, number_format($Cons1, 3, ",","."), 0, 0, 'R');
                            $pdf->SetX(81); 
                            $pdf->Cell(20, 5, number_format($Cons2, 3, ",","."), 0, 0, 'R');
                            $pdf->SetX(121); 
                            $pdf->Cell(20, 5, number_format($Cons3, 3, ",","."), 0, 0, 'R');
                            $pdf->SetX(160); 
                            $pdf->Cell(26, 5, number_format(($Cons1+$Cons2+$Cons3), 3, ",",".")." m3", 0, 1, 'R');

                            $lin = $pdf->GetY();
                            $pdf->SetDrawColor(200); // cinza claro                
                            $pdf->Line(10, $lin, 200, $lin);
                        }
                    }
            }else{
                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(10);
                $pdf->Cell(20, 4, "Ndenhum registro encontrado. Informe à ATI,", 0, 1, 'L');
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "É necessário inserir os valores iniciais da medição nos parâmetros do sistema. Informe à ATI,", 0, 1, 'L');
        }

    }

    if($Acao == "listamesEletric"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Colec = (int) filter_input(INPUT_GET, 'colec'); 
		$pdf->SetTitle('Relação Mensal Eletricidade', $isUTF8=TRUE);
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if($Mes < 10){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        if($Colec == 2){
            $ValorIniEletric = $ValorIniEletric2; 
            $DataIniEletric = $DataIniEletric2;
        }
        if($Colec == 3){
            $ValorIniEletric = $ValorIniEletric3; 
            $DataIniEletric = $DataIniEletric3;
        }

        if($ValorIniEletric > 0 && !is_null($DataIniEletric)){
            $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura".$Colec.", 'DD/MM/YYYY'), date_part('dow', dataleitura".$Colec."), leitura".$Colec.", dataleitura".$Colec." 
            FROM ".$xProj.".leitura_eletric 
            WHERE colec = $Colec And ativo = 1 And DATE_PART('MONTH', dataleitura".$Colec.") = '$Mes' And DATE_PART('YEAR', dataleitura".$Colec.") = '$Ano' 
            ORDER BY dataleitura".$Colec." ");
            $row0 = pg_num_rows($rs0);
            $Cont = 0;
            $Leit24Ant = 0;
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(7);
            if($row0 > 0){
                $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
                $pdf->SetFont('Arial', 'I', 14);
                $pdf->MultiCell(0, 3, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
                $pdf->ln(5);

                $pdf->SetFont('Arial', 'I', 8);
                $pdf->SetX(20); 
                $pdf->Cell(20, 4, "Data", 0, 0, 'C');
                $pdf->Cell(15, 4, "Sem", 0, 0, 'C');
                
                $pdf->SetX(78); 
                $pdf->Cell(20, 4, "Leitura", 0, 0, 'R');
                $pdf->SetX(140); 
                $pdf->Cell(20, 4, "Consumo", 0, 1, 'R');
                $pdf->SetTextColor(0, 0, 0);
                $lin = $pdf->GetY();
                $pdf->SetDrawColor(200); // cinza claro                
                $pdf->Line(10, $lin, 200, $lin);

                while ($tbl0 = pg_fetch_row($rs0)){
                    $Cod = $tbl0[0];
                    $DataLinha = $tbl0[4];
                    $pdf->SetX(20); 
                    $pdf->Cell(20, 5, $tbl0[1], 0, 0, 'C');
                    $pdf->Cell(15, 5, $semana[$tbl0[2]], 0, 0, 'C');

                    if(strtotime($DataLinha) == strtotime($DataIniEletric)){ // "2024-03-01"
                        $Leit24Ant = $ValorIniEletric;  //1696.485;
                    }else{
                        $rs1 = pg_query($Conec, "SELECT leitura".$Colec." FROM ".$xProj.".leitura_eletric WHERE dataleitura".$Colec." = (date '$DataLinha' - 1) And colec = $Colec And ativo = 1");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            $tbl1 = pg_fetch_row($rs1);
                            $Leit24Ant = $tbl1[0];
                        }
                    }
                    $Leit07 = $tbl0[3];
                     if($Leit07 == 0){
                        $Cons1 = 0;
                    }else{
                        $Cons1 = ($Leit07-$Leit24Ant);
                    }
                    if($Leit07 == 0){
                        $Cons1 = 0;
                    }
                    $pdf->SetX(80); 
                    $pdf->Cell(20, 5, number_format($tbl0[3], 3, ",","."), 0, 0, 'R');
                    $pdf->SetX(140); 
                    $pdf->Cell(20, 5, number_format($Cons1, 3, ",","."), 0, 1, 'R');

                    $lin = $pdf->GetY();
                    $pdf->Line(10, $lin, 200, $lin);
                }

                //Estatística
                $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura".$Colec."), COUNT(id), SUM(leitura".$Colec.") 
                FROM ".$xProj.".leitura_eletric 
                WHERE dataleitura".$Colec." IS NOT NULL And leitura".$Colec." != 0  And DATE_PART('MONTH', dataleitura".$Colec.") = '$Mes' And DATE_PART('YEAR', dataleitura".$Colec.") = '$Ano'
                GROUP BY DATE_PART('MONTH', dataleitura".$Colec.") ORDER BY DATE_PART('MONTH', dataleitura".$Colec.") DESC ");

                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1) ){
                        $Mes = $tbl1[0];
                        $QuantDias = $tbl1[1];
                        $SomaLeitAnt = 0;
                        $SomaLeit1 = 0;
                        $rs2 = pg_query($Conec, "SELECT dataleitura".$Colec.", leitura".$Colec." FROM ".$xProj.".leitura_eletric 
                        WHERE DATE_PART('MONTH', dataleitura".$Colec.") = $Mes And DATE_PART('YEAR', dataleitura".$Colec.") = '$Ano' And colec = $Colec And ativo = 1 And leitura".$Colec." != 0 ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            while($tbl2 = pg_fetch_row($rs2) ){
                                $DataLinha = $tbl2[0]; // dataleitura
                                $SomaLeit1 = $SomaLeit1+$tbl2[1];
        
                                if(strtotime($DataLinha) == strtotime($DataIniEletric)){ // "2024-03-01"
                                    $SomaLeitAnt = ($SomaLeitAnt+$ValorIniEletric);  //1696.485
                                }
        
                                if($DataLinha != $DataIniEletric){
                                    $rs3 = pg_query($Conec, "SELECT leitura".$Colec." FROM ".$xProj.".leitura_eletric WHERE dataleitura".$Colec." = (date '$DataLinha' - 1) And colec = $Colec And ativo = 1 And leitura".$Colec." != 0");
                                    $tbl3 = pg_fetch_row($rs3);
                                    $row3 = pg_num_rows($rs3);
                                    if($row3 > 0){
                                        $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                                    }
                                }
                                $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                                $MediaDiaria = ($Cons1/$QuantDias);
                            }
                            $pdf->SetFont('Arial', 'I', 10);
                            $pdf->SetX(132); 
                            $pdf->Cell(25, 5, "Consumo Mensal:", 0, 0, 'R');
                            $pdf->SetX(155); 
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                            $pdf->Cell(27, 5, number_format($Cons1, 3, ",",".")." kWh", 0, 1, 'R');
                            $pdf->SetTextColor(0, 0, 0);

                            $pdf->SetX(132); 
                            $pdf->Cell(25, 5, "Consumo Médio Diário: ", 0, 0, 'R');

                            $pdf->SetX(155); 
                            $pdf->SetTextColor(255, 0, 0); // vermelho
                            $pdf->Cell(27, 5, number_format($MediaDiaria, 3, ",",".")." kWh", 0, 0, 'R');
                            $pdf->SetTextColor(0, 0, 0);

                            $pdf->SetX(185); 
                            $pdf->SetFont('Arial', 'I', 8);
                            if($QuantDias == 1){
                                $pdf->Cell(20, 5, "(".$QuantDias." dia)", 0, 1, 'L');
                            }else{
                                $pdf->Cell(20, 5, "(".$QuantDias." dias)", 0, 1, 'L');
                            }
                            
                        }
                        $pdf->ln(20);
                    }
                }
            }else{
                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(10);
                $pdf->Cell(20, 4, "Ndenhum registro encontrado. Informe à ATI,", 0, 1, 'L');
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "É necessário inserir os valores iniciais da medição nos parâmetros do sistema. Informe à ATI,", 0, 1, 'L');
        }
    }

    if($Acao == "listaanoEletric"){
        $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
        $Colec = (int) filter_input(INPUT_GET, 'colec'); 
		$pdf->SetTitle('Relação Anual Eletricidade', $isUTF8=TRUE);
        if($Colec == 2){
            $ValorIniEletric = $ValorIniEletric2; 
            $DataIniEletric = $DataIniEletric2;
        }
        if($Colec == 3){
            $ValorIniEletric = $ValorIniEletric3; 
            $DataIniEletric = $DataIniEletric3;
        }

        if($ValorIniEletric > 0 && !is_null($DataIniEletric)){
            $pdf->ln(7);
            $pdf->SetFont('Arial', 'I', 14);
            $pdf->MultiCell(0, 3, $Ano, 0, 'C', false);
            $pdf->ln(5);

            $pdf->SetFont('Arial', 'I', 8);

            $pdf->SetX(50); 
            $pdf->Cell(20, 4, "Mês", 0, 0, 'C');

            $pdf->SetX(120); 
            $pdf->Cell(20, 4, "Consumo", 0, 1, 'R');
            $pdf->SetTextColor(0, 0, 0);
            $lin = $pdf->GetY();
            $pdf->SetDrawColor(200); // cinza claro                
            $pdf->Line(10, $lin, 200, $lin);

                $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura".$Colec."), COUNT(id), SUM(leitura".$Colec.") 
                FROM ".$xProj.".leitura_eletric 
                WHERE dataleitura".$Colec." IS NOT NULL And leitura".$Colec." != 0  And DATE_PART('YEAR', dataleitura".$Colec.") = '$Ano'
                GROUP BY DATE_PART('MONTH', dataleitura".$Colec.") ORDER BY DATE_PART('MONTH', dataleitura".$Colec.") ");

                $row1 = pg_num_rows($rs1);
                $SomaAno = 0;
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1) ){
                        $Mes = $tbl1[0];
                        $QuantDias = $tbl1[1];
                        $SomaLeit1 = 0;
                        $SomaLeitAnt = 0;

                        $rs2 = pg_query($Conec, "SELECT dataleitura".$Colec.", leitura".$Colec." FROM ".$xProj.".leitura_eletric 
                        WHERE DATE_PART('MONTH', dataleitura".$Colec.") = $Mes And DATE_PART('YEAR', dataleitura".$Colec.") = '$Ano' And colec = $Colec And ativo = 1 And leitura".$Colec." != 0 ");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            while($tbl2 = pg_fetch_row($rs2) ){
                                $DataLinha = $tbl2[0]; // dataleitura
                                $SomaLeit1 = $SomaLeit1+$tbl2[1];
                                
        
                                if(strtotime($DataLinha) == strtotime($DataIniEletric)){ // "2024-03-01"
                                    $SomaLeitAnt = ($SomaLeitAnt+$ValorIniEletric);  //1696.485
                                }
        
                                if($DataLinha != $DataIniEletric){
                                    $rs3 = pg_query($Conec, "SELECT leitura".$Colec." FROM ".$xProj.".leitura_eletric WHERE dataleitura".$Colec." = (date '$DataLinha' - 1) And colec = $Colec And ativo = 1 And leitura".$Colec." != 0");
                                    $tbl3 = pg_fetch_row($rs3);
                                    $row3 = pg_num_rows($rs3);
                                    if($row3 > 0){
                                        $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                                    }
                                }
                                $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                                $MediaDiaria = ($Cons1/$QuantDias);
                                
                            }
                            $pdf->SetX(50); 
                            $pdf->Cell(20, 5, $mesNum_extenso[$Mes], 0, 0, 'C');
                            $pdf->SetX(120); 
                            $pdf->Cell(26, 5, number_format($Cons1, 3, ",",".")." kWh", 0, 1, 'R');
                            $SomaAno = $SomaAno+$Cons1;

                            $lin = $pdf->GetY();
                            $pdf->SetDrawColor(200); // cinza claro                
                            $pdf->Line(10, $lin, 200, $lin);
                        }

                        $pdf->ln(5);
                        $pdf->SetFont('Arial', 'I', 10);
                        $pdf->SetX(95); 
                        $pdf->Cell(26, 5, "Consumo Anual: ", 0, 0, 'R');
                        $pdf->SetX(122); 
                        $pdf->Cell(26, 5, number_format($SomaAno, 3, ",",".")." kWh", 0, 1, 'R');
                    }
            }else{
                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(10);
                $pdf->Cell(20, 4, "Nenhum registro encontrado. Informe à ATI,", 0, 1, 'L');
            }
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "É necessário inserir os valores iniciais da medição nos parâmetros do sistema. Informe à ATI,", 0, 1, 'L');
        }
    }
 }
 $pdf->Output();