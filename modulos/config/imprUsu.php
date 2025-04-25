<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }
 date_default_timezone_set('America/Sao_Paulo');

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
    $Dom = "Logo2.png";

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
    $pdf->SetTitle('Usuários', $isUTF8=TRUE);
    if($Dom != "" && $Dom != "NULL"){
        if(file_exists('../../imagens/'.$Dom)){
            if(getimagesize('../../imagens/'.$Dom)!=0){
                $pdf->Image('../../imagens/'.$Dom,12,8,16,20);
            }
        }
    }
//    $pdf->SetX(40); 
    $pdf->SetFont('Arial','' , 14); 
    $pdf->Cell(0, 5, $Cabec1, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 12); 
    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    
    $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE ativo = 1");
    $row0 = pg_num_rows($rs0);
    if($Acao == "listaUsuarios"){
        $pdf->MultiCell(0, 3, "Usuários Cadastrados (".$row0.")", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "listaUsuarios"){
        $rs = pg_query($Conec, "SELECT id, adm_fl, adm_nome, ativo FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl DESC");
        $row = pg_num_rows($rs);
        $Cont = 0;
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $Adm = $tbl[1];
                $pdf->ln(5);
                $pdf->SetX(20);
                $pdf->SetFont('Arial', 'I', 11);
                $pdf->Cell(50, 3, $tbl[2].":", 0, 0, 'L');
                $pdf->ln(2);

                $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE adm = $Adm And ativo = 1 ORDER BY nomeusual, nomecompl");
                $row0 = pg_num_rows($rs0);

                if($row0 > 0){
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->SetX(50);
                    $pdf->Cell(40, 3, "Nome", 0, 0, 'L');
                    $pdf->Cell(150, 3, "Nome Completo", 0, 0, 'L');
                    $pdf->ln(4);
                    $lin = $pdf->GetY();
                    $pdf->Line(50, $lin, 200, $lin);
                    $pdf->SetFont('Arial', '', 10);

                    while($tbl0 = pg_fetch_row($rs0)){
                        $Cod = $tbl0[0];
                        $pdf->SetX(50); 
                        $pdf->Cell(40, 5, $tbl0[2], 0, 0, 'L');
                        $pdf->Cell(150, 5, $tbl0[1], 0, 1, 'L');

                        $lin = $pdf->GetY();
                        $pdf->Line(50, $lin, 200, $lin);
                    }
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
                    $pdf->SetFont('Arial', '', 10);
                    $Cont = $Cont+$row0;

                    $lin = $pdf->GetY();               
                    $pdf->Line(20, $lin, 200, $lin);
                    $pdf->ln(5);
                }else{
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->SetX(50);
                    $pdf->Cell(40, 4, "Nome", 0, 0, 'L');
                    $pdf->Cell(150, 4, "Nome Completo", 0, 1, 'L');
                    $pdf->SetX(50);
                    $pdf->Cell(40, 5, 'Nenhum encontrado.', 0, 1, 'L');
                    $lin = $pdf->GetY();
                    $pdf->Line(20, $lin, 200, $lin);
                    $pdf->ln(5);
                    $pdf->SetFont('Arial', '', 10);
                }
            }
            $pdf->ln(5);
            $lin = $pdf->GetY();
            $pdf->Line(10, $lin, 200, $lin);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Soma: ".$Cont, 0, 0, 'L');
            
        }
    }
 }
 $pdf->Output();