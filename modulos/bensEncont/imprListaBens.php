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
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
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
    $pdf->AddPage("L", "A4");
    $pdf->SetLeftMargin(20);
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Relação Mensal Bens', $isUTF8=TRUE);
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
//    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "listamesBens" || $Acao == "listaanoBens"){
        $pdf->MultiCell(0, 3, "Registros de Bens Encontrados", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 290, $lin);

    if($Acao == "listamesBens"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(datareceb, 'DD/MM/YYYY'), date_part('dow', datareceb), numprocesso, descdobem, codusuins, usuguarda, usurestit, usucsg, usudestino, usuarquivou, CURRENT_DATE-datareceb, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, localachou, nomeachou, telefachou FROM ".$xProj.".bensachados WHERE ativo = 1 And DATE_PART('MONTH', datareceb) = '$Mes' And DATE_PART('YEAR', datareceb) = '$Ano' ORDER BY datareceb DESC, id DESC ");
    }

    if($Acao == "listaanoBens"){
        $Ano = filter_input(INPUT_GET, 'ano'); 
        $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(datareceb, 'DD/MM/YYYY'), date_part('dow', datareceb), numprocesso, descdobem, codusuins, usuguarda, usurestit, usucsg, usudestino, usuarquivou, CURRENT_DATE-datareceb, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, localachou, nomeachou, telefachou FROM ".$xProj.".bensachados WHERE ativo = 1 And DATE_PART('YEAR', datareceb) = '$Ano' ORDER BY datareceb DESC, id DESC ");
    }

    if($Acao == "listamesBens" || $Acao == "listaanoBens"){
        $row0 = pg_num_rows($rs0);
        $Cont = 0;
        $pdf->SetFont('Arial', '', 10);
        $pdf->ln(7);
        if($row0 > 0){
            $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
            $pdf->SetFont('Arial', 'I', 14);
            if($Acao == "listamesBens"){
                $pdf->MultiCell(0, 5, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
            }
            if($Acao == "listaanoBens"){
                $pdf->MultiCell(0, 5, $Ano, 0, 'C', false);
            }
            $pdf->ln(5);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(20, 4, "Data", 0, 0, 'C');
            $pdf->Cell(10, 4, "Sem", 0, 0, 'L');
            $pdf->Cell(20, 4, "Processo", 0, 0, 'C');
            $pdf->Cell(110, 4, "Descrição do bem encontrado", 0, 0, 'L');
            $pdf->Cell(14, 4, "Registro", 0, 0, 'C');
            $pdf->Cell(17, 4, "Guarda", 0, 0, 'C');
            $pdf->Cell(18, 4, "Restituido", 0, 0, 'C');
            $pdf->Cell(20, 4, "Destinado", 0, 0, 'C');
            $pdf->Cell(18, 4, "Arquivado", 0, 0, 'C');
            $pdf->Cell(20, 4, "Dias", 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0);
            $lin = $pdf->GetY();
            $pdf->SetDrawColor(200); // cinza claro
            $pdf->Line(10, $lin, 290, $lin);

            $pdf->SetFont('Arial', '', 10);
            while ($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->ln(2);
                $UsuIns = $tbl0[5]; // usuário que inseriu só para dar o status do processo 
                $SobGuarda = $tbl0[6];
                $Restit = $tbl0[7];
                $GuardaCSG = $tbl0[8];
                $Destino = $tbl0[9];
                $Arquivado = $tbl0[10];
                $Intervalo = (int) $tbl0[12];
                $Dias = str_pad(($tbl0[11]), 2, "0", STR_PAD_LEFT);

                $pdf->Cell(20, 5, $tbl0[1], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(10, 5, $semana[$tbl0[2]], 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(20, 5, $tbl0[3], 1, 0, 'L'); // Processo
                $lin = $pdf->GetY();
                    
                //GetStringWidth dividido pelo tamanho do campo adotado (150) dá o número de linhas que ocupará
                // https://stackoverflow.com/questions/71867059/fpdf-getstringheight-equivalent-to-getstringwidth
                $Tam = $pdf->GetStringWidth($tbl0[4]);
                $Linhas = ceil($Tam/140);

                $pdf->SetFont('Arial', '', 8);
                $pdf->MultiCell(110, 5, $tbl0[4], 0, 'L', false); // descdobem

                $pdf->SetY($lin);
                $pdf->SetX(180);
                if($UsuIns > 0){
                    $pdf->Cell(14, 5, "Registro", 1, 0, 'C'); 
                }

                $pdf->SetX(195);
                if($GuardaCSG > 0){
                    $pdf->Cell(17, 5, "Guarda SSV", 1, 0, 'C'); 
                }else{
                    $pdf->Cell(17, 5, "", 1, 0, 'C'); 
                }

                $pdf->SetX(213);
                if($Restit > 0){
                    $pdf->Cell(17, 5, "Restituido", 1, 0, 'C'); 
                }else{
                    $pdf->Cell(17, 5, "", 1, 0, 'C'); 
                }

                $pdf->SetX(231);
                if($Destino > 0){
                        $pdf->Cell(17, 5, "Destinado", 1, 0, 'C'); 
                }else{
                    $pdf->Cell(17, 5, "", 1, 0, 'C'); 
                }

                $pdf->SetX(250);
                if($Arquivado > 0){
                    $pdf->Cell(17, 5, "Arquivado", 1, 0, 'C'); 
                }else{
                    $pdf->Cell(17, 5, "", 1, 0, 'C'); 
                }
                $pdf->SetX(270);
                if($Dias <= 90){
                    $pdf->Cell(17, 5, $Dias." dias", 1, 0, 'C'); 
                }

                $pdf->SetX(290);
                $pdf->Cell(17, 5, "", 0, 1, 'C'); 

                $pdf->SetFont('Arial', '', 10);
                $pdf->ln(5*$Linhas); // avança o número de linhas determinado pelo multicel acima

                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 290, $lin);
            }
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0." registros", 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
        }else{
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(10);
            $pdf->Cell(20, 4, "Nenhum registro encontrado. Informe à ATI,", 0, 1, 'L');
        }
    }
}
$pdf->Output();