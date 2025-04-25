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
//    $pdf->AddPage("L", "A4"); // L landscape
    $pdf->AddPage();
    $pdf->SetLeftMargin(30);       
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

    if($Acao == "imprGrupos"){
        $pdf->SetTitle('Grupos Tarefas', $isUTF8=TRUE);
        $pdf->MultiCell(0, 3, "Grupos Tarefas", 0, 'C', false);
    }
    if($Acao == "imprOrganogr"){
        $pdf->SetTitle('Níveis Tarefas', $isUTF8=TRUE);
        $pdf->MultiCell(0, 3, "Níveis para Tarefas - Tipo Organograma", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "imprGrupos"){
        $rs0 = pg_query($Conec, "SELECT grupotarefa, siglasetor, descsetor FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.grupotarefa = ".$xProj.".setores.codset WHERE grupotarefa > 1 
        GROUP BY grupotarefa, siglasetor, descsetor ORDER BY ".$xProj.".poslog.grupotarefa");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(15);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(0, 3, "Grupos específicos para Tarefas:", 0, 'L', false);
        $pdf->ln(2);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 10);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(20); 
                $pdf->Cell(40, 5, "Grupo ".$tbl0[1]." - ".$tbl0[2], 0, 1, 'L');
                $rs1 = pg_query($Conec, "SELECT nomeusual, nomecompl FROM ".$xProj.".poslog 
                WHERE grupotarefa = $Cod And ativo = 1 ORDER BY nomecompl");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1)){
                        $pdf->SetX(50); 
                        $pdf->Cell(40, 5, $tbl1[0], 0, 0, 'L');
                        $pdf->Cell(150, 5, $tbl1[1], 0, 1, 'L');
                    }
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(150, 5, "Total: ".$row1, 0, 1, 'L');
                    $pdf->ln(10);
                    $pdf->SetFont('Arial', '', 10);
                }
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 200, $lin);
            }
            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0, 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);

            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Grupo", 0, 0, 'L');
            $pdf->Cell(150, 4, "Nome Setor", 0, 1, 'L');
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum grupo encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }
    }


    if($Acao == "imprOrganogr"){
        $rs0 = pg_query($Conec, "SELECT orgtarefa FROM ".$xProj.".poslog WHERE orgtarefa > 0 
        GROUP BY orgtarefa ORDER BY orgtarefa");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(15);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->ln(2);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->SetFont('Arial', '', 10);
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(20); 
                $Cod = $tbl0[0];
                    switch ($Cod){
                        case 10:
                            $Desc = "Conselho";
                            break;
                        case 20:
                            $Desc = "Presidência";
                            break;
                        case 30:
                            $Desc = "Diretoria/Assessoria";
                            break;
                        case 40:
                            $Desc = "Divisão";
                            break;
                        case 50:
                            $Desc = "Gerência";
                            break;
                        case 60:
                            $Desc = "Funcionário";
                            break;
                    }
                
                $pdf->Cell(40, 5, "Nível ".$Desc, 0, 1, 'L');
                $rs1 = pg_query($Conec, "SELECT nomeusual, nomecompl, pessoas_id FROM ".$xProj.".poslog WHERE orgtarefa = $Cod And ativo = 1 ORDER BY nomecompl");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1)){
                        $pdf->SetX(50); 
                        $pdf->Cell(40, 5, $tbl1[0], 0, 0, 'L');
                        $pdf->Cell(150, 5, $tbl1[1], 0, 1, 'L');
                    }
                    $pdf->SetX(50);
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(150, 5, "Total: ".$row1, 0, 1, 'L');
                    $pdf->ln(10);
                    $pdf->SetFont('Arial', '', 10);
                }
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 200, $lin);
            }
            $pdf->SetX(20);


            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nada foi encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }
    }



 }
 $pdf->Output();