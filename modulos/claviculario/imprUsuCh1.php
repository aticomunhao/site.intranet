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

    $EscChave = parAdm("esc_chaves1", $Conec, $xProj); // marca para aparecer/ocultar escolha de chaves a retirar por usuário 

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
    $pdf->SetTitle('Claviculário Portaria', $isUTF8=TRUE);
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
    if($Acao == "listaUsuarios"){
        $pdf->MultiCell(0, 3, "Claviculário da Portaria", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "listaUsuarios"){
        $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE clav = 1 And ativo = 1 ORDER BY nomecompl");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(0, 3, "Usuários autorizados a registrar a Entrega e Devolução de Chaves na Portaria:", 0, 'L', false);
        $pdf->ln(2);
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
            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Nome", 0, 0, 'L');
            $pdf->Cell(150, 4, "Nome Completo", 0, 1, 'L');
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum usuário encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }


        $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE clav_edit = 1 And ativo = 1 ORDER BY nomecompl");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(0, 3, "Usuários autorizados a inserir, editar e apagar chaves do claviculário da Portaria:", 0, 'L', false);
        $pdf->ln(2);
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

            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Nome", 0, 0, 'L');
            $pdf->Cell(150, 4, "Nome Completo", 0, 1, 'L');
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum usuário encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }


        $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE fisc_clav = 1 And ativo = 1 ORDER BY nomecompl");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(0, 3, "Usuários autorizados a fiscalizar o funcionamento do claviculário da Portaria:", 0, 'L', false);
        $pdf->ln(2);
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

            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Nome", 0, 0, 'L');
            $pdf->Cell(150, 4, "Nome Completo", 0, 1, 'L');
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum usuário encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }




        $rs0 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, cpf FROM ".$xProj.".poslog WHERE chave = 1 And ativo = 1 ORDER BY nomecompl");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(0, 3, "Pessoas autorizadas a retirar chaves na Portaria:", 0, 'L', false);
        $pdf->ln(5);
        if($row0 > 0){
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(20);
            $pdf->Cell(30, 3, "Nome", 0, 0, 'L');
            $pdf->Cell(65, 3, "Nome Completo", 0, 0, 'L');
            $pdf->Cell(25, 3, "CPF", 0, 0, 'L');
            $pdf->Cell(35, 3, "Telefone", 0, 0, 'L');
            $pdf->Cell(20, 3, "Retiradas", 0, 0, 'R');
            $pdf->ln(4);
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);

            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $pdf->SetX(20);   //substr('abcdef', 1, 3); // bcd
                $pdf->Cell(30, 4, substr($tbl0[2], 0, 17), 0, 0, 'L');
                $pdf->Cell(65, 4, substr($tbl0[1], 0, 40), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(25, 4, Mask("###.###.###-##",$tbl0[3]), 0, 0, 'L');

                //Procura telefones fornecidos ao retirar chaves
                $rs2 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves_ctl WHERE usuretira = $Cod ORDER BY datasaida DESC");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);
                    $Telef = $tbl2[0];
                }else{
                    $Telef = "";
                }
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(35, 4, substr($Telef, 0, 40), 0, 0, 'L');
    
                //Conta quantas retiradas de chave
                $rs2 = pg_query($Conec, "SELECT COUNT(usuretira) FROM ".$xProj.".chaves_ctl WHERE usuretira = $Cod And ativo = 1");
                $tbl2 = pg_fetch_row($rs2);
                $pdf->Cell(20, 4, $tbl2[0], 0, 1, 'R');

                if($EscChave == 1){
                    $rs3 = pg_query($Conec, "SELECT chavenum, chavecompl, chaves_id FROM ".$xProj.".chaves INNER JOIN ".$xProj.".chaves_aut ON ".$xProj.".chaves.id = ".$xProj.".chaves_aut.chaves_id 
                    WHERE pessoas_id = $Cod And ".$xProj.".chaves_aut.ativo = 1 ORDER BY chavenum, chavecompl");
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->SetX(70);
                        $pdf->Cell(20, 3, "Chaves vinculadas: ", 0, 0, 'R');
                        while($tbl3 = pg_fetch_row($rs3)){
                            $CodChave = $tbl3[2];
                            //Conta quantas retiradas de chave
                            $rs4 = pg_query($Conec, "SELECT COUNT(usuretira) FROM ".$xProj.".chaves_ctl WHERE usuretira = $Cod And ativo = 1 And chaves_id = $CodChave");
                            $tbl4 = pg_fetch_row($rs4);
                            $pdf->SetX(90);
                            $pdf->Cell(15, 3, str_pad($tbl3[0], 3, 0, STR_PAD_LEFT)." ".$tbl3[1], 0, 0, 'L');
                            $pdf->Cell(15, 3, "Retiradas: ", 0, 0, 'R');
                            $pdf->Cell(10, 3, $tbl4[0], 0, 1, 'R');

                            $lin = $pdf->GetY();
                            $pdf->Line(90, $lin, 130, $lin);
                        }
                    }else{
                        $pdf->SetFont('Arial', 'I', 8);
                        $pdf->SetX(80);
                        $pdf->Cell(20, 3, "Chaves vinculadas: Nenhuma", 0, 1, 'R');
                    }
                }

                $pdf->ln(2);
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 200, $lin);
            }

            $pdf->SetX(20);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(150, 5, "Total: ".$row0." Usuários", 0, 1, 'L');
            $pdf->SetFont('Arial', '', 10);
            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
       
        }else{
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(50);
            $pdf->Cell(40, 4, "Nome", 0, 0, 'L');
            $pdf->Cell(150, 4, "Nome Completo", 0, 1, 'L');
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhum usuário encontrado.', 0, 1, 'L');
            $lin = $pdf->GetY();
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->SetFont('Arial', '', 10);
        }

    }
 }
 $pdf->Output();