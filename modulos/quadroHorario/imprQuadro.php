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

    if($Acao == "imprQuadro"){
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
        $pdf->MultiCell(150, 3, "Quadro Horário ".$SiglaGrupo." - ".$DescMes."/".$Ano, 0, 'C', false);
        $pdf->ln();
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);

        $pdf->SetDrawColor(200);
        $pdf->ln(2);

        $rs = pg_query($Conec, "SELECT ".$xProj.".quadrohor.id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI'),  
        date_part('dow', dataescala), TO_CHAR(horafim1 - horaini1, 'HH24:MI'), quadrohor_id  
        FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
        WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
        $row = pg_num_rows($rs);
        $Cont = 1;
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
                if(!is_null($tbl[8]) || $tbl[8] != ""){
                    $CodQuadro = $tbl[8];
                }else{
                    $CodQuadro = 0;
                    $Cont = 1;
                }


                //Para não repetir data e dia da semana
                $rsCont = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".quadroins WHERE quadrohor_id = $CodQuadro;");
                $tblCont = pg_fetch_row($rsCont);
                $Num = $tblCont[0];
                if($CodQuadro == 0){
                    $Num = 0;
                    $Cont = 0;
                }
                if($Cont > $Num && $CodQuadro > 0){
                    $Cont = 1;
                }

                $CodPartic1 = $tbl[3]; // pessoas_id de poslog - salvo em salvaEsc.php
                if(is_null($tbl[3]) || $tbl[3] == ""){
                    $CodPartic1 = 0;
                }



                if($Cont == 1 || $CodQuadro == 0){
                    $pdf->Cell(25, 5, $tbl[2], 0, 0, 'C');
                }else{
                    $pdf->Cell(25, 5, '', 0, 0, 'C');
                }
                if($Cont == 1 || $CodQuadro == 0){
                    $pdf->Cell(10, 5, $Semana_Extract[$tbl[6]], 0, 0, 'L');
                }else{
                    $pdf->Cell(10, 5, '', 0, 0, 'L');
                }

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

                $lin = $pdf->GetY();
                $pdf->Line(10, $lin, 200, $lin);

                $Cont++;

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
                    $CargaMin = 0;
                    $Carga1Min = 0;

                    $rs1 = pg_query($Conec, "SELECT TO_CHAR(AGE(horafim1, horaini1), 'HH24'), TO_CHAR(AGE(horafim1, horaini1), 'MI'), TO_CHAR(horafim1 - horaini1, 'HH24:MI')
                    FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
                    WHERE turno1_id = $Cod And grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        while($tbl1 = pg_fetch_row($rs1)){
//                            $Carga1 = $Carga1+$tbl1[0];
//                            $Carga1Min = $Carga1Min+$tbl1[1];

                            $CargaCor =  $tbl1[0]; 
                            if($tbl1[2] >= "08:00"){
                                $CargaCor = ($tbl1[0]-1); // carga corrigida
                            }
                            $CargaMinCor = $tbl1[1];
                            if($tbl1[2] > "06:00" && $tbl1[2] < "08:00"){
                                $CargaMinCor = ($tbl1[1]-15);
                            }

                            $Carga1 = $Carga1+$CargaCor;
                            $Carga1Min = $Carga1Min+$CargaMinCor;
                        }
                    }

                    $Carga = $Carga+$Carga1;
                    $CargaMin = $CargaMin+$Carga1Min;
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