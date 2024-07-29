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
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
    $Semana_Extract = array(
        '0' => 'Dom',
        '1' => '2ª',
        '2' => '3ª',
        '3' => '4ª',
        '4' => '5ª',
        '5' => '6ª',
        '6' => 'Sab',
        'xª'=> ''
    );

    function SomaCarga($Hora, $Min){
        $M = $Min%60;
        if($M == 0){
            $M = "00";
        }
        $H = floor($Min/60);
        $Hora = $Hora+$H;
        return $Hora."h ".$M."min";
    };
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

        $rs = pg_query($Conec, "SELECT id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI'), turno2_id, TO_CHAR(horaini2, 'HH24:MI'), TO_CHAR(horafim2, 'HH24:MI'), turno3_id, TO_CHAR(horaini3, 'HH24:MI'), 
        TO_CHAR(horafim3, 'HH24:MI'), turno4_id, TO_CHAR(horaini4, 'HH24:MI'), TO_CHAR(horafim4, 'HH24:MI'), date_part('dow', dataescala), TO_CHAR(horafim1 - horaini1, 'HH24:MI'), TO_CHAR(horafim2 - horaini2, 'HH24:MI'), TO_CHAR(horafim3 - horaini3, 'HH24:MI'), TO_CHAR(horafim4 - horaini4, 'HH24:MI') 
        FROM ".$xProj.".escalas WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
        $row = pg_num_rows($rs);
        if($row > 0){
            $pdf->SetFont('Arial', 'I' , 7);
            $pdf->Cell(25, 4, "Data", 0, 0, 'C');
            $pdf->Cell(10, 4, "Sem", 0, 0, 'L');
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
                $pdf->Cell(10, 5, $Semana_Extract[$tbl[15]], 0, 0, 'L');
                if(is_null($tbl[4]) && is_null($tbl[5])){
                    $Ini = "";
                }else{
                    $Ini = $tbl[4];
                }
                if(is_null($tbl[5])){
                    $Fim = "";
                }else{
                    $Fim = $tbl[5];
                }
                $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic1;");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    if(is_null($tbl1[0]) || $tbl1[0] == ""){
                        $NomeCompl = "";
                    }else{
                        $NomeCompl = $tbl1[0];
                    }
                    if(is_null($tbl1[1]) || $tbl1[1] == ""){
                        $NomeUsual = "";
                    }else{
                        $NomeUsual = $tbl1[1];
                    }

                }else{
                    $NomeCompl = "";
                    $NomeUsual = "";
                }
                $pdf->Cell(50, 5, substr($NomeUsual, 0, 30), 0, 0, 'L');
                $pdf->SetFont('Arial', 'I' , 8);
                $pdf->Cell(150, 5, $NomeCompl, 0, 1, 'L');
                $pdf->SetFont('Arial', '' , 10);




                if($tbl[6] != 0){
                    if($Turnos >= 2){
                        $pdf->SetX(60); 
                        $CodPartic2 = $tbl[6]; // pessoas_id de poslog - salvo em salvaEsc.php
                        if(is_null($tbl[7]) && is_null($tbl[8])){
                            $Ini = "";
                        }else{
                            $Ini = $tbl[7];
                        }
                        if(is_null($tbl[8])){
                            $Fim = "";
                        }else{
                            $Fim = $tbl[8];
                        }

                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic2;");
                        $row2 = pg_num_rows($rs2);
                        if($row2 > 0){
                            $tbl2 = pg_fetch_row($rs2);
                            if(is_null($tbl2[0]) || $tbl2[0] == ""){
                                $NomeCompl = "";
                            }else{
                                $NomeCompl = $tbl2[0];
                            }
                            if(is_null($tbl2[1]) || $tbl2[1] == ""){
                                $NomeUsual = "";
                            }else{
                                $NomeUsual = $tbl2[1];
                            }
                        }else{
                            $NomeCompl = "";
                            $NomeUsual = "";
                        }
                        $pdf->Cell(50, 5, $NomeUsual, 0, 0, 'L');
                        $pdf->SetFont('Arial', 'I' , 8);
                        $pdf->Cell(150, 5, $NomeCompl, 0, 1, 'L');
                        $pdf->SetFont('Arial', '' , 10);
                    }

                }

                if($tbl[9] != 0){
                    if($Turnos >= 3){
                        $pdf->SetX(60); 
                        $CodPartic3 = $tbl[9];
                        if(is_null($tbl[10]) && is_null($tbl[11])){
                            $Ini = "";
                        }else{
                            $Ini = $tbl[10];
                        }
                        if(is_null($tbl[11])){
                            $Fim = "";
                        }else{
                            $Fim = $tbl[11];
                        }
                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs3 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic3;");
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            $tbl3 = pg_fetch_row($rs3);
                            if(is_null($tbl3[0]) || $tbl3[0] == ""){
                                $NomeCompl = "";
                            }else{
                                $NomeCompl = $tbl3[0];
                            }
                            if(is_null($tbl3[1]) || $tbl3[1] == ""){
                                $NomeUsual = "";
                            }else{
                                $NomeUsual = $tbl3[1];
                            }
                        }else{
                            $NomeCompl = "";
                            $NomeUsual = "";
                        }
                        $pdf->Cell(50, 5, $NomeUsual, 0, 0, 'L');
                        $pdf->SetFont('Arial', 'I' , 8);
                        $pdf->Cell(150, 5, $NomeCompl, 0, 1, 'L');
                        $pdf->SetFont('Arial', '' , 10);
                    }
                }
                if($tbl[12] != 0){
                    if($Turnos >= 4){
                        $pdf->SetX(60); 
                        $CodPartic4 = $tbl[12];
                        if(is_null($tbl[13]) && is_null($tbl[14])){
                            $Ini = "";
                        }else{
                            $Ini = $tbl[13];
                        }
                        if(is_null($tbl[14])){
                            $Fim = "";
                        }else{
                            $Fim = $tbl[14];
                        }
                        $pdf->Cell(15, 5, $Ini, 0, 0, 'C');
                        $pdf->Cell(15, 5, $Fim, 0, 0, 'C');

                        $rs4 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic4;");
                        $row4 = pg_num_rows($rs4);
                        if($row4 > 0){
                            $tbl4 = pg_fetch_row($rs4);
                            if(is_null($tbl4[0]) || $tbl4[0] == ""){
                                $NomeCompl = "";
                            }else{
                                $NomeCompl = $tbl4[0];
                            }
                            if(is_null($tbl4[1]) || $tbl4[1] == ""){
                                $NomeUsual = "";
                            }else{
                                $NomeUsual = $tbl4[1];
                            }
        
                        }else{
                            $NomeCompl = "";
                            $NomeUsual = "";
                        }
                        $pdf->Cell(50, 5, $NomeUsual, 0, 0, 'L');
                        $pdf->SetFont('Arial', 'I' , 8);
                        $pdf->Cell(150, 5, $NomeCompl, 0, 1, 'L');
                        $pdf->SetFont('Arial', '' , 10);
                    }
                }
                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);
            } // fim while

            $pdf->ln(10);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
            $pdf->SetFont('Arial', 'I' , 10);
            $pdf->Cell(150, 5, 'Carga Horária Mensal', 0, 1, 'L');

            $rs = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl"); 
            $row = pg_num_rows($rs);
            if($row > 0){
                while($tbl = pg_fetch_row($rs)){
                    $Cod = $tbl[0];
                    $Nome = $tbl[2];
                    if(is_null($tbl[2]) || $tbl[2] == ""){
                        $Nome = $tbl[1];
                    }
                    $Carga = 0;
                    $Carga1 = 0;
                    $Carga2 = 0;
                    $Carga3 = 0;
                    $Carga4 = 0;
                    $CargaMin = 0;
                    $Carga1Min = 0;
                    $Carga2Min = 0;
                    $Carga3Min = 0;
                    $Carga4Min = 0;
                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno1_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        while($tbl1 = pg_fetch_row($rs1)){
                            $Carga1 = $Carga1+$tbl1[0];
                            $Carga1Min = $Carga1Min+$tbl1[1];
                        }
                    }
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim2, horaini2), 'HH24'), TO_CHAR(AGE(horafim2, horaini2), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno2_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        while($tbl2 = pg_fetch_row($rs2)){
                            $Carga2 = $Carga2+$tbl2[0];
                            $Carga2Min = $Carga2Min+$tbl2[1];
                        }
                    }
                    $rs3 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim3, horaini3), 'HH24'), TO_CHAR(AGE(horafim3, horaini3), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno3_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        while($tbl3 = pg_fetch_row($rs3)){
                            $Carga3 = $Carga3+$tbl3[0];
                            $Carga3Min = $Carga3Min+$tbl3[1];
                        }
                    }
                    $rs4 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim4, horaini4), 'HH24'), TO_CHAR(AGE(horafim4, horaini4), 'MI') FROM ".$xProj.".escalas 
                    WHERE turno4_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row4 = pg_num_rows($rs4);
                    if($row4 > 0){
                        while($tbl4 = pg_fetch_row($rs4)){
                            $Carga4 = $Carga4+$tbl4[0];
                            $Carga4Min = $Carga4Min+$tbl4[1];
                        }
                    }

                    $Carga = $Carga+$Carga1+$Carga2+$Carga3+$Carga4;
                    $CargaMin = $CargaMin+$Carga1Min+$Carga2Min+$Carga3Min+$Carga4Min;
                    $CargaHoraria = SomaCarga($Carga, $CargaMin);

                    $pdf->SetX(40); 
                    $pdf->Cell(40, 5, $Nome, 0, 0, 'L');
                    $pdf->Cell(50, 5, $CargaHoraria, 0, 1, 'R');


                }
            }
        }else{
            $pdf->SetFont('Arial', '' , 10);
            $pdf->MultiCell(0, 5, "Nada foi Encontrado.", 0, 'C', false);
        }
    }
}
$pdf->Output();
