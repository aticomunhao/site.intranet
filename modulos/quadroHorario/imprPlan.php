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
    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
//    $pdf->Cell(150, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->SetTextColor(0, 0, 0);

    if($Acao == "imprPlan"){
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

        $pdf->SetTitle('Quadro Horário'.$Busca, $isUTF8=TRUE);
        $pdf->SetFont('Arial', '' , 10);
        $pdf->SetTextColor(25, 25, 112);
        $pdf->MultiCell(0, 3, "Quadro Horário ".$SiglaGrupo." - ".$DescMes."/".$Ano, 0, 'C', false);
        $pdf->ln();
//        $lin = $pdf->GetY();
//        $pdf->Line(10, $lin, 200, $lin);

        $pdf->SetDrawColor(200);
        $pdf->ln(2);

        $rs_ = pg_query($Conec, "SELECT ".$xProj.".quadrohor.id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI'),  
        date_part('dow', dataescala), TO_CHAR(horafim1 - horaini1, 'HH24:MI'), quadrohor_id, TO_CHAR(dataescala, 'DD')  
        FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
        WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");

        $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), grupo_id  
        FROM ".$xProj.".quadrohor 
        WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");


        $row = pg_num_rows($rs);
        $Cont = 1;
        if($row > 0){
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 280, $lin);
            $pdf->ln(5);
            $pdf->SetFont('Arial', '' , 10);

            //Dia
            $pdf->SetX(50); 
            while($tbl = pg_fetch_row($rs)){
                $pdf->Cell(7, 5, $tbl[1], 1, 0, 'C');
            }
            $pdf->Cell(7, 5, "", 0, 1, 'L');

            //Semana
            $pdf->SetX(50); 
            $rs1 = pg_query($Conec, "SELECT id, date_part('dow', dataescala) 
            FROM ".$xProj.".quadrohor 
            WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
    
            while($tbl1 = pg_fetch_row($rs1)){
                $pdf->Cell(7, 5, $Semana_Extract[$tbl1[1]], 1, 0, 'C');
            }
            $pdf->Cell(7, 5, "", 0, 1, 'L');


//            $rs1 = pg_query($Conec, "SELECT DISTINCT turno1_id, nomeusual 
//            FROM ".$xProj.".quadrohor INNER JOIN (".$xProj.".poslog INNER JOIN ".$xProj.".quadroins ON ".$xProj.".poslog.pessoas_id = ".$xProj.".quadroins.turno1_id) ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
//            WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY nomeusual");
            $rs1 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1)){
                    $Cod = $tbl1[0];
                    $pdf->Cell(20, 5, $tbl1[2], 0, 0, 'L');

                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '01' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");

                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(50); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }
                    


                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '02' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(57); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }


                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '03' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(64); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '04' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(71); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '05' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(78); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '06' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(85); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '07' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(92); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '08' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(99); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }


                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '09' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(106); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '10' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(113); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '11' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(120); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '12' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }

                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(127); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }


                    

                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '13' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(134); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }


                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '14' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(141); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '15' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(148); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }


                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '16' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(155); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '17' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(162); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '18' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(169); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '19' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(176); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }





                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '20' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(183); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '21' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(190); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '22' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(197); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '23' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(204); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '24' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(211); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }



                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '25' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(218); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '26' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(225); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '27' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(232); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }





                    
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '28' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(239); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }




                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 29) == 1){ //Ver se o dia existe neste mes
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '29' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(246); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }
                    }else{
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }



                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 30) == 1){ //Ver se o dia existe neste mes
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '30' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(253); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 0, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 0, 'C');
                    }
                    }else{
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }


                    

                    if(buscaDia($Conec, $xProj, $Mes, $Ano, 31) == 1){ //Ver se o dia existe neste mes
                    $rs2 = pg_query($Conec, "SELECT TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI') 
                    FROM ".$xProj.".quadroins WHERE turno1_id = $Cod And TO_CHAR(horaini1, 'DD') = '31' And TO_CHAR(horaini1, 'MM') = '$Mes' And TO_CHAR(horaini1, 'YYYY') = '$Ano' ");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $Ini = $tbl2[0];
                        $Fim = $tbl2[1];
                    }else{
                        $Ini = "";
                        $Fim = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$Ini' And horafim = '$Fim' ");
                    $row3 = pg_num_rows($rs3);
                    $pdf->SetX(260); 
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $IdTurno = $tbl3[0];
                        $pdf->Cell(7, 5, $IdTurno, 1, 1, 'C');
                    }else{
                        $pdf->Cell(7, 5, "", 1, 1, 'C');
                    }
                    }else{
                        $pdf->Cell(7, 5, "", 0, 1, 'C');
                    }



                }
            }
            $pdf->ln(3);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 280, $lin);
            $pdf->ln(10);
            $pdf->SetFont('Arial', '' , 10);
            $pdf->SetX(30); 
            $pdf->Cell(20, 7, "Horários de Trabalho:", 0, 1, 'R');
//Quadro
            $rs1 = pg_query($Conec, "SELECT id, horaini, horafim FROM ".$xProj.".quadroturnos WHERE ativo = 1 ORDER BY id ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1)){
                    $pdf->Cell(15, 5, $tbl1[0], 1, 0, 'R');
                    $pdf->Cell(35, 5, $tbl1[1]."  às  ".$tbl1[2], 1, 1, 'R');
                }

            }
        }else{
            $pdf->SetFont('Arial', '' , 10);
            $pdf->MultiCell(0, 5, "Nada foi Encontrado.", 0, 'C', false);
        }
    }
}
$pdf->Output();