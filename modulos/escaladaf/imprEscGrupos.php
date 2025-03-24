<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
 }
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
    
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }

    $rsSig = pg_query($Conec, "SELECT siglagrupo, chefe_escdaf, enc_escdaf FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
    $rowSig = pg_num_rows($rsSig);
    if($rowSig > 0){
        $tblSig = pg_fetch_row($rsSig);
        $SiglaGrupo = $tblSig[0];
        $ChefeDiv = $tblSig[1];
        $Encarreg = $tblSig[2];
    }else{
        $SiglaGrupo = "";
    }



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
           $this->Cell(0,10,'Pag '.$this->PageNo().'/{nb}',0,0,'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    $pdf->AddPage();
    $pdf->SetLeftMargin(30);
    $pdf->SetTitle('Escala DAF', $isUTF8=TRUE);
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
//    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->MultiCell(0, 3, "Escalas de Serviço", 0, 'C', false);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
    $lin = $pdf->GetY();
    $pdf->Line(10, $lin, 200, $lin);
    $pdf->SetDrawColor(200); // cinza claro  

    if($Acao == "listaGrupos"){
        $rs0 = pg_query($Conec, "SELECT id, siglagrupo, chefe_escdaf, enc_escdaf FROM ".$xProj.".escalas_gr WHERE ativo = 1 ORDER BY siglagrupo");
        $row0 = pg_num_rows($rs0);
        $pdf->ln(5);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $CodGrupo = $tbl0[0];
                $ChefeDiv = $tbl0[2];
                $Encarreg = $tbl0[3];

                $pdf->SetFont('Arial', 'I', 8);
                $pdf->SetX(20);
                $pdf->Cell(40, 5, $tbl0[1], 0, 1, 'L');
                $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $ChefeDiv");
                $row1 = pg_num_rows($rs1);
                $tbl1 = pg_fetch_row($rs1);
                $pdf->Cell(22, 3, "Chefe Div Adm: ", 0, 0, 'L');
                if($row1 > 0){
                    $pdf->Cell(30, 3, $tbl1[0], 0, 1, 'L');
                }else{
                    $pdf->Cell(30, 3, "", 0, 1, 'L');
                }

                $pdf->ln(1);
                $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $Encarreg");
                $row2 = pg_num_rows($rs2);
                $tbl2 = pg_fetch_row($rs2);
                $pdf->Cell(22, 3, "Chefe Imediato: ", 0, 0, 'L');
                if($row2 > 0){
                    $pdf->Cell(30, 3, $tbl2[0], 0, 1, 'L');
                }else{
                    $pdf->Cell(30, 3, "", 0, 1, 'L');
                }

                $pdf->ln(1);
                $rs3 = pg_query($Conec, "SELECT usu_id, nomecompl 
                FROM ".$xProj.".escaladaf_esc INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_esc.usu_id = ".$xProj.".poslog.pessoas_id WHERE ".$xProj.".escaladaf_esc.ativo = 1 And grupo_id = $CodGrupo");
                $row3 = pg_num_rows($rs3);
                if($row3 < 2){
                    $pdf->SetX(36);
                    $pdf->Cell(14, 3, "Escalante: ", 0, 0, 'L');
                    if($row3 == 1){
                        $tbl3 = pg_fetch_row($rs3);
                        $pdf->SetX(52);
                        $pdf->Cell(50, 3, $tbl3[1], 0, 1, 'L');
                    }else{
                        $pdf->Cell(50, 3, "", 0, 1, 'L');
                    }
                }else{
                    $pdf->SetX(35);
                    $pdf->Cell(14, 3, "Escalantes: ", 0, 0, 'L');
                    while($tbl3 = pg_fetch_row($rs3)){
                        $pdf->SetX(52);
                        $pdf->Cell(50, 3, $tbl3[1], 0, 1, 'L');
                    }
                }

                $pdf->ln(1);
                $pdf->SetX(40);
                $pdf->Cell(10, 3, "Efetivo: ", 0, 0, 'L');
                $rs4 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $CodGrupo ORDER BY nomeusual");
                $row4 = pg_num_rows($rs4);
                if($row4 > 0){
                    while($tbl4 = pg_fetch_row($rs4)){
                        if(is_null($tbl4[1]) || $tbl4[1] == ""){
                            $NomeUsual = substr($tbl4[0], 0, 15);
                        }else{
                            $NomeUsual = $tbl4[1];
                        }
                        $pdf->SetX(52);
                        $pdf->Cell(25, 3, $NomeUsual, 0, 0, 'L');
                        $pdf->Cell(150, 3, $tbl4[0], 0, 1, 'L');
                    }
                }
                $pdf->ln(4);
                $lin = $pdf->GetY();
                $pdf->Line(20, $lin, 200, $lin);
                $pdf->SetFont('Arial', '', 10);
            }

            $lin = $pdf->GetY();
            $pdf->Line(50, $lin, 200, $lin);
            $pdf->ln(10);
        }else{
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetX(50);
            $pdf->Cell(40, 5, 'Nenhuma escala encontrada.', 0, 1, 'L');
            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
        }
    }
 }
 $pdf->Output();