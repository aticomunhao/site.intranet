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
           $this->SetY(-15);  // Vai para 1.5 cm da parte inferior
           $this->SetFont('Arial','I',8);
           $this->Cell(0, 10, 'Impresso: '.date("d/m/Y H:i").'                   Pag '.$this->PageNo().'/{nb}', 0, 0, 'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    $pdf->SetLeftMargin(20);
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Bebedouros', $isUTF8=TRUE);
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
    $pdf->MultiCell(0, 3, "Consumo dos Bebedouros", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 205, $lin);
    $pdf->SetDrawColor(200); // cinza claro 
    $pdf->ln();

    if($Acao == "listaMes"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
    }
    if($Acao == "listaAno"){
        $Ano = filter_input(INPUT_GET, 'ano'); 
    }

    if($Acao == "listaMes"){
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(0, 5, $mes_extenso[$Mes]." / ".$Ano, 0, 'C', false);
 
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed WHERE ativo = 1 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0];
                $rsCons = pg_query($Conec, "SELECT SUM(volume) FROM ".$xProj.".bebed_ctl 
                WHERE bebed_id = $Cod And ativo = 1 And DATE_PART('MONTH', datatroca) = '$Mes' And DATE_PART('YEAR', datatroca) = '$Ano' ");
                $tblCons = pg_fetch_row($rsCons);
                if(is_null($tblCons[0])){
                    $Cons = 0;
                }else{
                    $Cons = $tblCons[0];
                }
                pg_query($Conec, "UPDATE ".$xProj.".bebed SET consumo = $Cons WHERE id = $Cod");
            }
        }

        $rs0 = pg_query($Conec, "SELECT ".$xProj.".bebed.id, numapar, descmarca, modelo, desctipo, localinst, consumo 
        FROM ".$xProj.".bebed_tipos INNER JOIN (".$xProj.".bebed INNER JOIN ".$xProj.".bebed_marcas ON ".$xProj.".bebed.codmarca = ".$xProj.".bebed_marcas.id) ON ".$xProj.".bebed.codtipo =  ".$xProj.".bebed_tipos.id 
        WHERE ".$xProj.".bebed.ativo = 1 ORDER BY consumo DESC, numapar ");

        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->Cell(10, 3, "Núm", 0, 0, 'C');
            $pdf->Cell(25, 3, "Marca", 0, 0, 'L');
            $pdf->Cell(35, 3, "Modelo", 0, 0, 'L');
            $pdf->Cell(28, 3, "Tipo", 0, 0, 'L');
            $pdf->Cell(67, 3, "Local", 0, 0, 'L');
            $pdf->Cell(10, 3, "Consumo", 0, 1, 'L');

            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 205, $lin);
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(1);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                if(!is_null($tbl0[6])){ // por causa da formatação
                    $Cons = $tbl0[6];
                }else{
                    $Cons = 0;
                }
                $pdf->SetX(20); 
                $pdf->Cell(10, 4, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->Cell(25, 5, substr($tbl0[2], 0, 12), 0, 0, 'L');
                $pdf->Cell(35, 5, substr($tbl0[3], 0, 20), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[4], 0, 15), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[5], 0, 40), 0, 0, 'L');
                $pdf->Cell(45, 4, number_format($Cons, 0, ",","."), 0, 0, 'R');
                $pdf->Cell(10, 4, "litros", 0, 1, 'R');
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 205, $lin);
                $pdf->ln(1);
            }
            $pdf->SetX(10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 205, $lin);
            $pdf->ln(10);
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
        }
    }

    if($Acao == "listaAno"){
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(0, 5, "Ano: ".$Ano, 0, 'C', false);

        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed WHERE ativo = 1 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $Cod = $tbl[0];
                $rsCons = pg_query($Conec, "SELECT SUM(volume) FROM ".$xProj.".bebed_ctl 
                WHERE bebed_id = $Cod And ativo = 1 And DATE_PART('YEAR', datatroca) = '$Ano' ");
                $tblCons = pg_fetch_row($rsCons);
                if(is_null($tblCons[0])){
                    $Cons = 0;
                }else{
                    $Cons = $tblCons[0];
                }
                pg_query($Conec, "UPDATE ".$xProj.".bebed SET consumo = $Cons WHERE id = $Cod");
            }
        }

        $rs0 = pg_query($Conec, "SELECT ".$xProj.".bebed.id, numapar, descmarca, modelo, desctipo, localinst, consumo 
        FROM ".$xProj.".bebed_tipos INNER JOIN (".$xProj.".bebed INNER JOIN ".$xProj.".bebed_marcas ON ".$xProj.".bebed.codmarca = ".$xProj.".bebed_marcas.id) ON ".$xProj.".bebed.codtipo =  ".$xProj.".bebed_tipos.id 
        WHERE ".$xProj.".bebed.ativo = 1 ORDER BY consumo DESC, numapar ");

        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->Cell(10, 3, "Núm", 0, 0, 'C');
            $pdf->Cell(25, 3, "Marca", 0, 0, 'L');
            $pdf->Cell(35, 3, "Modelo", 0, 0, 'L');
            $pdf->Cell(28, 3, "Tipo", 0, 0, 'L');
            $pdf->Cell(67, 3, "Local", 0, 0, 'L');
            $pdf->Cell(10, 3, "Consumo", 0, 1, 'L');

            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 205, $lin);
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(1);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                if(!is_null($tbl0[6])){ // por causa da formatação
                    $Cons = $tbl0[6];
                }else{
                    $Cons = 0;
                }
                $pdf->SetX(20); 
                $pdf->Cell(10, 4, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->Cell(25, 5, substr($tbl0[2], 0, 12), 0, 0, 'L');
                $pdf->Cell(35, 5, substr($tbl0[3], 0, 20), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[4], 0, 15), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[5], 0, 40), 0, 0, 'L');
                $pdf->Cell(45, 4, number_format($Cons, 0, ",","."), 0, 0, 'R');
                $pdf->Cell(10, 4, "litros", 0, 1, 'R');
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 205, $lin);
                $pdf->ln(1);
            }
            $pdf->SetX(10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 205, $lin);
            $pdf->ln(10);
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
        }
    }

    if($Acao == "listaIndiv"){
        $Ano = date('Y');
        $Bebed = filter_input(INPUT_GET, 'bebedouro'); 

        $rs = pg_query($Conec, "SELECT id, numapar FROM ".$xProj.".bebed WHERE ativo = 1 And id = $Bebed");
        $tbl = pg_fetch_row($rs);
        $Cod = $tbl[0];
        $Num = str_pad($tbl[1], 3, 0, STR_PAD_LEFT);

        $rsCons = pg_query($Conec, "SELECT SUM(volume) FROM ".$xProj.".bebed_ctl WHERE bebed_id = $Cod And ativo = 1 ");
        $tblCons = pg_fetch_row($rsCons);
        if(is_null($tblCons[0])){
            $Cons = 0;
        }else{
            $Cons = $tblCons[0];
        }
        pg_query($Conec, "UPDATE ".$xProj.".bebed SET consumo = $Cons WHERE id = $Cod");

        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(0, 5, "Bebedouro: ".$Num, 0, 'C', false);

        $rs0 = pg_query($Conec, "SELECT ".$xProj.".bebed.id, numapar, descmarca, modelo, desctipo, localinst, consumo 
        FROM ".$xProj.".bebed_tipos INNER JOIN (".$xProj.".bebed INNER JOIN ".$xProj.".bebed_marcas ON ".$xProj.".bebed.codmarca = ".$xProj.".bebed_marcas.id) ON ".$xProj.".bebed.codtipo =  ".$xProj.".bebed_tipos.id 
        WHERE ".$xProj.".bebed.id = $Bebed ");

        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->Cell(10, 3, "Núm", 0, 0, 'C');
            $pdf->Cell(25, 3, "Marca", 0, 0, 'L');
            $pdf->Cell(35, 3, "Modelo", 0, 0, 'L');
            $pdf->Cell(28, 3, "Tipo", 0, 0, 'L');
            $pdf->Cell(67, 3, "Local", 0, 0, 'L');
            $pdf->Cell(10, 3, "Consumo", 0, 1, 'L');

            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 205, $lin);
            $pdf->SetFont('Arial', '', 10);
            $pdf->ln(1);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                if(!is_null($tbl0[6])){ // por causa da formatação
                    $Cons = $tbl0[6];
                }else{
                    $Cons = 0;
                }
                $pdf->SetX(20); 
                $pdf->Cell(10, 4, str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), 0, 0, 'C');
                $pdf->Cell(25, 5, substr($tbl0[2], 0, 12), 0, 0, 'L');
                $pdf->Cell(35, 5, substr($tbl0[3], 0, 20), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[4], 0, 15), 0, 0, 'L');
                $pdf->Cell(28, 5, substr($tbl0[5], 0, 40), 0, 0, 'L');
                $pdf->Cell(45, 4, number_format($Cons, 0, ",","."), 0, 0, 'R');
                $pdf->Cell(10, 4, "litros", 0, 1, 'R');
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 205, $lin);
                $pdf->ln(1);
            }
            $pdf->SetX(10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(10, $lin, 205, $lin);
            $pdf->ln(10);
            $pdf->Cell(150, 5, "Abastecimentos: ", 0, 1, 'L');

            $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(datatroca, 'DD/MM/YYYY'), volume, TO_CHAR(datains, 'DD/MM/YYYY HH24:MI'), TO_CHAR(datains, 'YYYY') FROM ".$xProj.".bebed_ctl 
            WHERE bebed_id = $Bebed And ativo = 1 ORDER BY datatroca DESC");
            $row0 = pg_num_rows($rs0);
            $pdf->ln(2);
            if($row0 > 0){
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->SetX(20);
                $pdf->Cell(15, 3, "Data", 0, 0, 'C');
                $pdf->Cell(25, 3, "Volume", 0, 0, 'R');
                $pdf->Cell(35, 3, "Data Lanç", 0, 1, 'C');
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 205, $lin);
                $pdf->SetFont('Arial', '', 8);
                $pdf->ln(1);
                while($tbl0 = pg_fetch_row($rs0)){
                    if($tbl0[4] == "3000"){
                        $DataIns = "";
                    }else{
                        $DataIns = $tbl0[3];
                    }
                    $pdf->SetX(20); 
                    $pdf->Cell(15, 3, $tbl0[1], 0, 0, 'C');
                    $pdf->Cell(25, 3, $tbl0[2]." litros", 0, 0, 'R');
                    $pdf->Cell(35, 3, $DataIns, 0, 1, 'C');

                }
            }else{
                $pdf->SetFont('Arial', 'I', 11);
                $pdf->SetX(50);
                $pdf->Cell(40, 5, 'Nenhum lançamento encontrado.', 0, 1, 'L');
            }
        }else{
            $pdf->SetFont('Arial', 'I', 11);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
        }
    }

 }
 $pdf->Output();