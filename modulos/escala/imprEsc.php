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
           $this->SetY(-10);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
         }
    }
        
    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    $pdf->SetLeftMargin(25);
    
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    
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
    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
//    $pdf->Cell(150, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(150, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->SetTextColor(0, 0, 0);

    if($Acao == "imprEscala"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        $Ano = $Proc[1];
        $NumGrupo = filter_input(INPUT_GET, 'numgrupo');
        $rs0 = pg_query($Conec, "SELECT siglagrupo, qtd_turno FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $tbl0 = pg_fetch_row($rs0);
            $SiglaGrupo = $tbl0[0];
            $Turnos = $tbl0[1];
        }else{
            $SiglaGrupo = "";
            $Turnos = 1;
        }


        $Data = date('01/'.$Mes.'/'.$Ano);
        $DescMes = $mes_extenso[$Mes];

        $pdf->SetTitle('Escala '.$Busca, $isUTF8=TRUE);
        $pdf->SetFont('Arial', '' , 10);
        $pdf->SetTextColor(25, 25, 112);
        $pdf->MultiCell(150, 3, "Escala ".$SiglaGrupo." - ".$DescMes."/".$Ano, 0, 'C', false);
        $pdf->ln();
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);

        $pdf->SetDrawColor(200);
        $pdf->ln(2);

        $rs = pg_query($Conec, "SELECT id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, horaini1, horafim1, turno2_id, horaini2, horafim2, turno3_id, horaini3, 
        horafim3, turno4_id, horaini4, horafim4, turno5_id, horaini5, horafim5, turno6_id, horaini6, horafim6
        FROM ".$xProj.".escalas WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
        $row = pg_num_rows($rs);
        if($row > 0){
            $pdf->SetFont('Arial', 'I' , 7);
            $pdf->Cell(25, 4, "Data", 0, 0, 'C');
            $pdf->Cell(15, 4, "Início", 0, 0, 'C');
            $pdf->Cell(15, 4, "Fim", 0, 0, 'C');
            $pdf->Cell(15, 4, "Escalado", 0, 1, 'L');

            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
            $pdf->SetFont('Arial', '' , 10);
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0]; // id de escalas
                $CodPartic1 = $tbl[3]; // pessoas_id de poslog - salvo em salvaEsc.php
                $pdf->Cell(25, 5, $tbl[2], 0, 0, 'C');
                if($tbl[4] == 0 && $tbl[5] == 0){
                    $Ini = "";
                }else{
                    if($tbl[4] < 10){
                        $Ini = "0".$tbl[4].":00";
                    }else{
                        $Ini = $tbl[4].":00";
                    }
                }
                if($tbl[5] == 0){
                    $Fim = "";
                }else{
                    if($tbl[5] < 10){
                        $Fim = "0".$tbl[5].":00";
                    }else{
                        $Fim = $tbl[5].":00";
                    }
                }
                $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic1;");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $Nome1 = $tbl1[0];
                }else{
                    $Nome1 = "";
                }

                $pdf->Cell(150, 5, $Nome1, 0, 1, 'L');

                if($tbl[6] != 0){
                    if($Turnos >= 2){
                        $pdf->SetX(50); 
                        $CodPartic2 = $tbl[6]; // pessoas_id de poslog - salvo em salvaEsc.php
                        if($tbl[7] == 0 && $tbl[8] == 0){
                            $Ini = "";
                        }else{
                            if($tbl[7] < 10){
                                $Ini = "0".$tbl[7].":00";
                            }else{
                                $Ini = $tbl[7].":00";
                            }
                        }

                        if($tbl[8] == 0){
                            $Fim = "";
                        }else{
                            if($tbl[8] < 10){
                                $Fim = "0".$tbl[8].":00";
                            }else{
                                $Fim = $tbl[8].":00";
                            }
                        }
                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic2;");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            $Nome2 = $tbl2[0];
                        }else{
                            $Nome2 = "";
                        }
                        $pdf->Cell(150, 5, $Nome2, 0, 1, 'L');
                    }
                }

                if($tbl[9] != 0){
                    if($Turnos >= 3){
                        $pdf->SetX(50); 
                        $CodPartic3 = $tbl[9];
                        if($tbl[10] == 0 && $tbl[11] == 0){
                            $Ini = "";
                        }else{
                            if($tbl[10] < 10){
                                $Ini = "0".$tbl[10].":00";
                            }else{
                                $Ini = $tbl[10].":00";
                            }
                        }
                        if($tbl[11] == 0){
                            $Fim = "";
                        }else{
                            if($tbl[11] < 10){
                                $Fim = "0".$tbl[11].":00";
                            }else{
                                $Fim = $tbl[11].":00";
                            }
                        }
                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic3;");
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            $tbl3 = pg_fetch_row($rs3);
                            $Nome3 = $tbl3[0];
                        }else{
                            $Nome3 = "";
                        }
                        $pdf->Cell(150, 5, $Nome3, 0, 1, 'L');
                    }
                }
                if($tbl[12] != 0){
                    if($Turnos >= 4){
                        $pdf->SetX(50); 
                        $CodPartic4 = $tbl[12];
                        if($tbl[13] == 0 && $tbl[14] == 0){
                            $Ini = "";
                        }else{
                            if($tbl[13] < 10){
                                $Ini = "0".$tbl[13].":00";
                            }else{
                                $Ini = $tbl[13].":00";
                            }
                        }
                        if($tbl[14] == 0){
                            $Fim = "";
                        }else{
                            if($tbl[14] < 10){
                                $Fim = "0".$tbl[14].":00";
                            }else{
                                $Fim = $tbl[14].":00";
                            }
                        }
                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs4 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic4;");
                        $row4 = pg_num_rows($rs4);
                        if($row4 > 0){
                            $tbl4 = pg_fetch_row($rs4);
                            $Nome4 = $tbl4[0];
                        }else{
                            $Nome4 = "";
                        }
                        $pdf->Cell(150, 5, $Nome4, 0, 1, 'L');
                    }
                }
                
                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
            }
        }else{
            $pdf->SetFont('Arial', '' , 10);
            $pdf->MultiCell(0, 5, "Nada foi Encontrado.", 0, 'C', false);
        }
    }
}
$pdf->Output();
