<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }

 date_default_timezone_set('America/Sao_Paulo');
 if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Num = $_REQUEST["codigo"];
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
    //Monta o arquivo pdf        
    $pdf->SetFont('Arial', '' , 12); 
    $pdf->SetTitle('Termo de Recebimento', $isUTF8=TRUE);
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
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(150, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "impr" ){
        $pdf->MultiCell(150, 3, "Bens Encontrados", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);

    $rs = pg_query($Conec, "SELECT to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, to_char(".$xProj.".bensachados.dataachou, 'DD/MM/YYYY'), localachou, nomeachou, telefachou, to_char(NOW(), 'DD/MM/YYYY')  
    FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
    WHERE ".$xProj.".bensachados.id = $Num ");

    $row = pg_num_rows($rs);
    $tbl = pg_fetch_row($rs);
        
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->ln(7);

    $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
    $pdf->SetFillColor(232, 232, 232); // fundo cinza

    if($Acao == "imprtermo"){    
        $pdf->MultiCell(0, 8, "TERMO DE RECEBIMENTO PELA DAF", 1, 'C', true);
        $pdf->ln(8);

        $pdf->Cell(0, 4, "- Processo: ".$tbl[1]." registrado em ".$tbl[0], 0, 1, 'L');
        $pdf->ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetDrawColor(200); // cinza claro

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Descrição do bem encontrado: ", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, $tbl[2], 0, 'J', true); //relato
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(40, 4, "Data em que foi encontrado: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[3], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Local em que foi encontrado: 	", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[4], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Nome do colaborador que encontrou: ", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(25); 
        $pdf->Cell(0, 4, $tbl[5], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Telefone do colaborador que encontrou: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[6], 0, 1, 'L');

        $pdf->ln(3);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(10);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->MultiCell(0, 5, "     Declaro que recebi o Bem acima descrito, ao qual efetuarei a guarda pelo período de 90 (noventa) dias. Após esse prazo, a destinação do bem seguirá o caminho estabelecido na NI-4.05-B (DAF).", 1, 'J', false);
        $pdf->ln(8);

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetX(20); 
        $pdf->Cell(70, 4, "Brasília, ".$tbl[7], 0, 0, 'L');
        $pdf->Cell(50, 4, "Assinatura __________________________________________", 0, 1, 'L');
        $pdf->SetX(100); 
        $pdf->Cell(15, 4, "Nome: ", 0, 1, 'L');
        $pdf->ln(5);

        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(3);
    }

    if($Acao == "imprrestit"){    
        $pdf->SetTitle('Restituição', $isUTF8=TRUE);
        $pdf->MultiCell(0, 8, "REGISTRO DE RESTITUIÇÃO DE BENS", 1, 'C', true);
        $pdf->ln(8);

        $pdf->Cell(0, 4, "- Processo: ".$tbl[1]." registrado em ".$tbl[0], 0, 1, 'L');
        $pdf->ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetDrawColor(200); // cinza claro

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Descrição do bem encontrado: ", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, $tbl[2], 0, 'J', true); //relato
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(40, 4, "Data em que foi encontrado: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[3], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Local em que foi encontrado: 	", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[4], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Nome do colaborador que encontrou: ", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(25); 
        $pdf->Cell(0, 4, $tbl[5], 0, 1, 'L');
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Telefone do colaborador que encontrou: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[6], 0, 1, 'L');

        $pdf->ln(3);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(10);



        $pdf->SetX(40); 
        $pdf->MultiCell(130, 8, "PROPRIETÁRIO", 1, 'C', true);
        $pdf->ln(5);

        $lin = $pdf->GetY();
        $pdf-> Rect(10, $lin, 190, 40);

        $pdf->ln(8);
        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Nome do Proprietário: ", 0, 1, 'L');
        $lin = $pdf->GetY();
        $pdf->Line(45, $lin, 190, $lin);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, "", 0, 1, 'L');

        $pdf->ln(5);
        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "CPF: ", 0, 0, 'L');

 
        $pdf->SetX(140); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(20, 4, "Telefone: ", 0, 1, 'L');

        $pdf->ln(25);
        $lin = $pdf->GetY();
        $pdf-> Rect(15, $lin, 180, 40);

        $pdf->ln(5);
        $pdf->SetX(20); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Funcionário da DAF: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, "", 0, 0, 'L');

        $pdf->SetX(115); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(70, 4, "Brasília, ".$tbl[7], 0, 1, 'L');

        $pdf->ln(15);
        $pdf->SetX(20); 
        $pdf->Cell(50, 4, "Assinatura __________________________________________", 0, 0, 'L');

        $pdf->SetX(115); 
        $pdf->Cell(50, 4, "__________________________________________", 0, 1, 'L');

        $pdf->SetX(20); 
        $pdf->Cell(15, 4, "Nome: ", 0, 0, 'L');

        $pdf->SetX(125); 
        $pdf->Cell(15, 4, "Assinatura do Proprietário", 0, 1, 'L');
    }

 }
 $pdf->Output();