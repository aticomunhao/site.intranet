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

    function buscaSemana($Conec, $xProj, $Data, $Dias){
        $Data = implode("-", array_reverse(explode("/", $Data))); 
        $rsSis = pg_query($Conec, "SELECT EXTRACT(DOW FROM dataescala + $Dias) FROM ".$xProj.".escala_adm WHERE dataescala = '$Data'");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $ProcSis = pg_fetch_row($rsSis);
           $escSis = $ProcSis[0]; 
        }else{
           $escSis = "xª";
        }
        return $escSis;
    }
    function buscaDia($Conec, $xProj, $Data, $Dias){
        $Data = implode("-", array_reverse(explode("/", $Data))); 
        $rsSis = pg_query($Conec, "SELECT TO_CHAR(dataescala + $Dias, 'DD') FROM ".$xProj.".escala_adm WHERE dataescala = '$Data'");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $ProcSis = pg_fetch_row($rsSis);
           $escSis = $ProcSis[0];
        }else{
           $escSis = "dia";
        }
        return $escSis;
    }
    function buscaOcup($Conec, $xProj, $Data, $Dias, $Quadro){
        $Data = implode("-", array_reverse(explode("/", $Data)));
        $rsSis = pg_query($Conec, "SELECT $Quadro FROM ".$xProj.".escala_adm WHERE dataescala = date '$Data' + interval '$Dias day' ");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $ProcSis = pg_fetch_row($rsSis);
           $escSis = $ProcSis[0];
        }else{
           $escSis = "00";
        }
        return $escSis;
    }
    
    function buscaCor($Conec, $xProj, $Mes, $Ano, $Cod){
        $rsSis = pg_query($Conec, "SELECT oprcor FROM ".$xProj.".escala_eft WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $ProcSis = pg_fetch_row($rsSis);
           $escSis = $ProcSis[0]; 
        }else{
           $escSis = "#FFFFFF";
        }
        return $escSis;
    }
    
    function buscaTrigr($Conec, $xProj, $Mes, $Ano, $Cod){
        $rsSis = pg_query($Conec, "SELECT trigr FROM ".$xProj.".escala_eft WHERE poslog_id = $Cod And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rsSis);
        if($row > 0){
           $ProcSis = pg_fetch_row($rsSis);
           $escSis = $ProcSis[0];
        }else{
           $escSis = "";
        }
        return $escSis;
    }

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

    if($Acao == "listamesEscala"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if($Mes < 10){
//            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $Data = date('01/'.$Mes.'/'.$Ano);

        $pdf->SetTitle('Escala '.$Busca, $isUTF8=TRUE);
        $pdf->SetFont('Arial', '' , 10);
        $pdf->SetTextColor(25, 25, 112);
        $pdf->MultiCell(150, 3, "Escala Mês: ".$Busca, 0, 'C', false);
        $pdf->ln();
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);

        $pdf->SetDrawColor(200);
        $pdf->ln(7);

        $pdf->SetFont('Arial', '' , 6);
        $pdf->SetX(32); 
        //Dias da semana
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 0)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 1)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 2)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 3)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 4)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 5)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 6)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 7)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 8)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 9)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 10)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 11)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 12)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 13)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 14)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 15)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 16)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 17)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 18)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 19)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 20)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 21)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 22)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 23)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 24)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 25)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 26)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 27)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 28)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 29)], 0, 0, 'C');
         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 30)], 0, 1, 'C');
//         $pdf->Cell(5, 3, $Semana_Extract[buscaSemana($Conec, $xProj, $Data, 31)], 0, 1, 'C');

         $pdf->SetX(32); 
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 0), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 1), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 2), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 3), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 4), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 5), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 6), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 7), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 8), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 9), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 10), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 11), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 12), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 13), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 14), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 15), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 16), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 17), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 18), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 19), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 20), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 21), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 22), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 23), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 24), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 25), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 26), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 27), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 28), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 29), 0, 0, 'C');
         $pdf->Cell(5, 3, buscaDia($Conec, $xProj, $Data, 30), 0, 1, 'C');


        $pdf->Cell(12, 3, '0600/0630', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0600_0630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0600_0630')), 1, 1, 'C');
        
        $pdf->Cell(12, 3, '0630/0700', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0630_0700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0630_0700')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0700/0730', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0700_0730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0700_0730')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0730/0800', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0730_0800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0730_0800')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0800/0830', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0800_0830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0800_0830')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0830/0900', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0830_0900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0830_0900')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0900/0930', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0900_0930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0900_0930')), 1, 1, 'C');

        $pdf->Cell(12, 3, '0930/1000', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora0930_1000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0930_1000')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1000/1030', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1000_1030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1000_1030')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1030/1100', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1030_1100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1030_1100')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1100/1130', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1100_1130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1100_1130')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1130/1200', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1130_1200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1130_1200')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1200/1230', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1200_1230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1200_1230')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1230/1300', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1230_1300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1230_1300')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1300/1330', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1300_1330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1300_1330')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1330/1400', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1330_1400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1330_1400')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1400/1430', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1400_1430')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1400_1430')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1430/1500', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1430_1500')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1430_1500')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1500/1530', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1500_1530')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1500_1530')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1530/1600', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1530_1600')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1530_1600')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1600/1630', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1600_1630')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1600_1630')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1630/1700', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1630_1700')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1630_1700')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1700/1730', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1700_1730')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1700_1730')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1730/1800', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1730_1800')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1730_1800')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1800/1830', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1800_1830')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1800_1830')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1830/1900', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1830_1900')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1830_1900')), 1, 1, 'C');

        $pdf->Cell(12, 3, '1900/1930', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1900_1930')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1900_1930')), 1, 1, 'C');


        $pdf->Cell(12, 3, '1930/2000', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora1930_2000')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora1930_2000')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2000/2030', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2000_2030')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2000_2030')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2030/2100', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2030_2100')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2030_2100')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2100/2130', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2100_2130')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2100_2130')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2130/2200', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2130_2200')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2130_2200')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2200/2230', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2200_2230')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2200_2230')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2230/2300', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2230_2300')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2230_2300')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2300/2330', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2300_2330')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2300_2330')), 1, 1, 'C');

        $pdf->Cell(12, 3, '2330/2400', 0, 0, 'L');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 1, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 2, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 3, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 4, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 5, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 6, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 7, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 8, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 9, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 10, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 11, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 12, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 13, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 14, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 15, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 16, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 17, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 18, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 19, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 20, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 21, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 22, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 23, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 24, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 25, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 26, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 27, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 28, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 29, 'hora2330_2400')), 1, 0, 'C');
        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora2330_2400')), 1, 1, 'C');


        $pdf->ln(10);

        $pdf->SetFont('Arial', 'I' , 9);
        $pdf->SetX(32); 
        $pdf->Cell(5, 6, "Carga Horária - Legenda:", 0, 1, 'L');
        $rs = pg_query($Conec, "SELECT trigr, nomecompl, tempomensal 
        FROM ".$xProj.".escala_eft INNER JOIN ".$xProj.".poslog ON ".$xProj.".escala_eft.poslog_id = ".$xProj.".poslog.pessoas_id 
        WHERE mes = '$Mes' And ano = '$Ano' ORDER BY trigr");
        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $pdf->SetX(32); 
                $Trigr = $tbl[0];
                $Nome = $tbl[1];
                $Tempo = $tbl[2];

                $pdf->Cell(18, 4, $Tempo, 0, 0, 'L');
                $pdf->Cell(5, 4, "-", 0, 0, 'L');

                $pdf->Cell(10, 4, $Trigr, 0, 0, 'L');
//                $pdf->Cell(5, 4, "-", 0, 0, 'L');
                $pdf->Cell(120, 4, $Nome, 0, 1, 'L');

                
                

            }
        }






//        $pdf->SetFillColor(255, 0, 0);
//        $pdf->Cell(5, 3, buscaTrigr($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 30, 'hora0600_0630')), 1, 1, 'C', true);
//        $pdf->SetDrawColor(200);
        

//buscaCor($Conec, $xProj, $Mes, $Ano, buscaOcup($Conec, $xProj, $Data, 0, 'hora0630_0700'))

    }



    




}
$pdf->Output();
