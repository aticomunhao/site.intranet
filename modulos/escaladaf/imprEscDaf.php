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
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
    $Semana_Extract = array(
        '0' => 'D',
        '1' => '2ª',
        '2' => '3ª',
        '3' => '4ª',
        '4' => '5ª',
        '5' => '6ª',
        '6' => 'S',
        'xª'=> ''
    );

    function SomaCarga($Hora, $Min){
        if($Min < 0){
            $Min = ($Min+60); // $Min será negativo
            $Hora = ($Hora-1);
        }
        $M = $Min%60;
        if($M == 0){
            $M = "00";
        }
        $H = floor($Min/60);
        $Hora = $Hora+$H;
        return $Hora."h ".$M."min";
    };

    function buscaDia($Conec, $xProj, $Mes, $Ano, $Dia){
        $rsSis = pg_query($Conec, "SELECT id FROM ".$xProj.".quadrohor WHERE TO_CHAR(dataescala, 'DD') = '$Dia' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $escSis = 1; 
        }else{
           $escSis = 0;
        }
        return $escSis;
    }


    class PDF extends FPDF{
        function Footer(){
           // Vai para 1.5 cm da parte inferior
           $this->SetY(-10);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage("L", "A4");
    $pdf->SetLeftMargin(20);

    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 

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
    $pdf->SetFillColor(255, 255, 255); // branco
    $pdf->SetTextColor(0, 0, 0);

    if($Acao == "imprPlan"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        $Ano = $Proc[1];
        $Data = date('01/'.$Mes.'/'.$Ano);
        $DescMes = $mes_extenso[$Mes];

        $pdf->SetTitle('Escala DAF '.$Busca, $isUTF8=TRUE);
        $pdf->SetFont('Arial', '' , 10);
        $pdf->SetTextColor(25, 25, 112);
        $pdf->MultiCell(0, 3, "Escala ".$DescMes."/".$Ano, 0, 'C', false);
        $pdf->ln();
        $pdf->SetDrawColor(200);
        $pdf->ln(2);

        $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala)  
        FROM ".$xProj.".escaladaf 
        WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");

        $row = pg_num_rows($rs);
        $Cont = 1;
        if($row > 0){
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(5);
            $pdf->SetFont('Arial', '' , 10);

            //Dia
            $pdf->SetX(50); 
            while($tbl = pg_fetch_row($rs)){
                if($tbl[2] == 6 || $tbl[2] == 0){
                    $pdf->SetFillColor(232, 232, 232); // fundo cinza
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->Cell(7, 5, $tbl[1], 1, 0, 'C', true);
            }
            $pdf->Cell(7, 5, "", 0, 1, 'L');

            //Semana
            $pdf->SetX(50); 
            $rs1 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
            FROM ".$xProj.".escaladaf 
            WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
            while($tbl1 = pg_fetch_row($rs1)){
                if($tbl1[1] == 6 || $tbl1[1] == 0){
                    $pdf->SetFillColor(232, 232, 232); // fundo cinza
                }else{
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->Cell(7, 5, $Semana_Extract[$tbl1[1]], 1, 0, 'C', true);
            }
            $pdf->Cell(7, 5, "", 0, 1, 'L');

            
            $rs1 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 ORDER BY nomeusual, nomecompl ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1)){
                    $Cod = $tbl1[0];
                    $pdf->Cell(20, 5, $tbl1[2], 0, 0, 'L');

//                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque, marcadaf 
//                    FROM ".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".escaladaf ON ".$xProj.".escaladaf_ins.escaladaf_id = ".$xProj.".escaladaf.id
//                    WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '01' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' 
//                    ORDER BY dataescala");

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '01' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(50); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '01' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);


//                        if($tbl3[2] == 1){
//                            $pdf->SetFillColor(255, 255, 0);
//                        }


                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }
                    
                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '02' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(57); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra  
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '02' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '03' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(64); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '03' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '04' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(71); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '04' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '05' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(78); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '05' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '06' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(85); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '06' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '07' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(92); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '07' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '08' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(99); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '08' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '09' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(106); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '09' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '10' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(113); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '10' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '11' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(120); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '11' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '12' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(127); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '12' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque 
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '13' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(134); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '13' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }
   
                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque 
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '14' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(141); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '14' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }
   
                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '15' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(148); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '15' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '16' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(155); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '16' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '17' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(162); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '17' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '18' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(169); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '18' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '19' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(176); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '19' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '20' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(183); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '20' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '21' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(190); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '21' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '22' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(197); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '22' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '23' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(204); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '23' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '24' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(211); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '24' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '25' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(218); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '25' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '26' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(225); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '26' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '27' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(232); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '27' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                    FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '28' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX(239); 
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        if($tbl2[1] == 6 || $tbl2[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                        }else{
                            $pdf->SetFillColor(255, 255, 255);
                        }
                        if($tbl2[2] == 1){
                            $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                        }
                        $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                    }else{
                        $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                        FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '28' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $tbl3 = pg_fetch_row($rs3);
                        if($tbl3[1] == 6 || $tbl3[1] == 0){
                            $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                            $pdf->SetFillColor(255, 255, 255);
                        }else{
                            $pdf->Cell(7, 5, "", 1, 0, 'C');
                        }
                    }

                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 29) == 1){ //Ver se o dia existe neste mes
                        $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                        FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '29' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                        $row2 = pg_num_rows($rs2);
                        $pdf->SetX(246); 
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            if($tbl2[1] == 6 || $tbl2[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            }else{
                                $pdf->SetFillColor(255, 255, 255);
                            }
                            if($tbl2[2] == 1){
                                $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                            }
                            $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                        }else{
                            $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                            FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '29' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                            $tbl3 = pg_fetch_row($rs3);
                            if($tbl3[1] == 6 || $tbl3[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                                $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                                $pdf->SetFillColor(255, 255, 255);
                            }else{
                                $pdf->Cell(7, 5, "", 1, 0, 'C');
                            }
                        }
                    }else{ // se o dia não existir 
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }

                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 30) == 1){ //Ver se o dia existe neste mes
                        $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                        FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '30' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                        $row2 = pg_num_rows($rs2);
                        $pdf->SetX(253); 
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            if($tbl2[1] == 6 || $tbl2[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            }else{
                                $pdf->SetFillColor(255, 255, 255);
                            }
                            if($tbl2[2] == 1){
                                $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                            }
                            $pdf->Cell(7, 5, $tbl2[0], 1, 0, 'C', true); // letra
                        }else{
                            $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                            FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '30' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                            $tbl3 = pg_fetch_row($rs3);
                            if($tbl3[1] == 6 || $tbl3[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                                $pdf->Cell(7, 5, "", 1, 0, 'C', true);
                                $pdf->SetFillColor(255, 255, 255);
                            }else{
                                $pdf->Cell(7, 5, "", 1, 0, 'C');
                            }
                        }
                    }else{
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }

                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 31) == 1){ //Ver se o dia existe neste mes
                        $rs2 = pg_query($Conec, "SELECT letraturno, date_part('dow', dataescalains), destaque  
                        FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '31' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
                        $row2 = pg_num_rows($rs2);
                        $pdf->SetX(260); 
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            if($tbl2[1] == 6 || $tbl2[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                            }else{
                                $pdf->SetFillColor(255, 255, 255);
                            }
                            if($tbl2[2] == 1){
                                $pdf->SetFillColor(255, 255, 0); // fundo amarelo
                            }
                            $pdf->Cell(7, 5, $tbl2[0], 1, 1, 'C', true); // letra
                        }else{
                            $rs3 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
                            FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'DD') = '31' And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                            $tbl3 = pg_fetch_row($rs3);
                            if($tbl3[1] == 6 || $tbl3[1] == 0){
                                $pdf->SetFillColor(232, 232, 232); // fundo cinza
                                $pdf->Cell(7, 5, "", 1, 1, 'C', true);
                                $pdf->SetFillColor(255, 255, 255);
                            }else{
                                $pdf->Cell(7, 5, "", 1, 1, 'C');
                            }
                        }
                    }else{
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }

                }
            }


//Quadro horário
            $pdf->ln(3);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(5);
            $pdf->SetFont('Arial', '' , 10);
            $pdf->SetX(15); 
            $pdf->Cell(20, 7, "Horários de Trabalho:", 0, 1, 'L');

//Primeira linha
            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 1 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(20); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 6 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(75); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 11 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(130); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 16 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(185); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 21 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(240); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 1, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 1, 'C');
            }


//Segunda linha
            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 2 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 7 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(75); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 12 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(130); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 17 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(185); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 22 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(240); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 1, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 1, 'C');
            }


//Terceira linha
            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 3 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 8 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(75); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 13 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(130); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 18 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(185); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 23 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(240); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 1, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 1, 'C');
            }


//Quarta linha
            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 4 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 9 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(75); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 14 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(130); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 19 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(185); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 24 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(240); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 1, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 1, 'C');
            }


//Quinta linha
            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 5 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 10 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(75); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 15 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(130); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 20 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(185); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 0, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 0, 'C');
            }

            $rs1 = pg_query($Conec, "SELECT letra, horaturno FROM ".$xProj.".escaladaf_turnos WHERE ordemletra = 25 And ativo = 1");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(240); 
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(10, 5, $tbl1[0], 1, 0, 'C');
                $pdf->Cell(35, 5, $tbl1[1], 1, 1, 'C');
            }else{
                $pdf->Cell(10, 5, "", 0, 0, 'C');
                $pdf->Cell(35, 5, "", 0, 1, 'C');
            }


            
// Encarregado - order by datamodif DESC para pegar sempre o último chefe marcado  
            $pdf->ln(7);
            $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE enc_escdaf = 1 And ativo = 1 ORDER BY datamodif DESC");
            $row2 = pg_num_rows($rs2);
            $pdf->SetX(140); 
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $pdf->Cell(100, 5, $tbl2[0], 0, 0, 'C');
            }
            $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE chefe_escdaf = 1 And ativo = 1 ORDER BY datamodif DESC");
            $row3 = pg_num_rows($rs3);
            $pdf->SetX(200); 
            if($row3 > 0){
                $tbl3 = pg_fetch_row($rs3);
                $pdf->Cell(100, 5, $tbl3[0], 0, 1, 'C');
            }else{
                $pdf->Cell(100, 5, "", 0, 1, 'C');
            }


//Notas
            $pdf->ln(3);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 290, $lin);
            $pdf->ln(5);
            $pdf->SetFont('Arial', '' , 10);
            $pdf->SetX(15); 
            $pdf->Cell(20, 7, "Notas:", 0, 1, 'L');
            $pdf->SetFont('Arial', '' , 9);
            $rs1 = pg_query($Conec, "SELECT numnota, textonota FROM ".$xProj.".escaladaf_notas WHERE ativo = 1 ORDER BY numnota");
            $row1 = pg_num_rows($rs1);
            $pdf->SetX(20); 
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1)){
                    $pdf->Cell(10, 4, $tbl1[0], 1, 0, 'C');
                    $pdf->MultiCell(0, 4, $tbl1[1], 0, 'J', false);
                    $pdf->ln(1);
                }
            }

        }else{
            $pdf->SetFont('Arial', '' , 10);
            $pdf->MultiCell(0, 5, "Nada foi Encontrado.", 0, 'C', false);
        }
    }
}
$pdf->Output();