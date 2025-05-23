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
    $Dom = "Logo2.png";
    
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }

    $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
    $Proc = explode("/", $Busca);
    $Mes = $Proc[0];
    $Ano = $Proc[1];
    $Data = date('01/'.$Mes.'/'.$Ano);
    $DescMes = $mes_extenso[$Mes];


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
    $pdf->SetLeftMargin(20);
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

    if($Acao == "imprNotasGrupo" || $Acao == "imprNotasIndiv"){
        $pdf->MultiCell(0, 4, "Escala ".$SiglaGrupo, 0, 'C', false);
        if($Acao == "imprNotasGrupo"){
            $pdf->MultiCell(0, 5, "Anotações ".$DescMes."/".$Ano, 0, 'C', false);
        }else{
            $pdf->MultiCell(0, 5, "Anotações Individuais: ".$DescMes."/".$Ano, 0, 'C', false);
           if(isset($_REQUEST["codigo"])){
                $Cod = $_REQUEST["codigo"]; 
            }else{
                $Cod = 0;
            }
            $rs = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
            $tbl = pg_fetch_row($rs);
            $Nome = $tbl[0];
            $pdf->MultiCell(0, 5, $Nome, 0, 'C', false);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->ln();
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->SetDrawColor(200); // cinza claro  


        $pdf->ln(4);
        $pdf->SetFont('Arial', 'I', 11);
        if($Acao == "imprNotasGrupo"){ // grupo todo
            $rs0 = pg_query($Conec, "SELECT poslog_id, nomecompl 
            FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escaladaf_func ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escaladaf_func.poslog_id 
            WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo 
            GROUP BY poslog_id, nomecompl ORDER BY nomecompl");
        }else{ // individual
            $rs0 = pg_query($Conec, "SELECT poslog_id, nomecompl 
            FROM ".$xProj.".poslog INNER JOIN ".$xProj.".escaladaf_func ON ".$xProj.".poslog.pessoas_id = ".$xProj.".escaladaf_func.poslog_id 
            WHERE poslog_id = $Cod And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo 
            GROUP BY poslog_id, nomecompl ");
        }
        
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $CodPosLog = $tbl0[0];
                $pdf->SetFont('Arial', 'I', 10);
                 if($Acao == "imprNotasGrupo"){ // grupo todo
                        $pdf->MultiCell(0, 3, $tbl0[1], 0, 'L', false);
                 }
                $pdf->ln(1);

                $rs1 = pg_query($Conec, "SELECT TO_CHAR(dataescala, 'DD/MM/YYYY'), letra, turno, observ, id_ocor, id_mot, id_stat, id_adm 
                FROM ".$xProj.".escaladaf_func 
                WHERE poslog_id = $CodPosLog And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo ORDER BY dataescala");
                $row1 = pg_num_rows($rs1);
                while($tbl1 = pg_fetch_row($rs1)){
                    $pdf->SetX(40); 
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->MultiCell(0, 4, $tbl1[0]." - Letra: ".$tbl1[1]." - Turno: ".$tbl1[2], 0, 'L', false);

                    $idOcor = $tbl1[4];
                    $idMot = $tbl1[5];
                    $idStat = $tbl1[6];
                    $idAdm = $tbl1[7];
                    $rs2 = pg_query($Conec, "SELECT descocor FROM ".$xProj.".escaladaf_funcoc WHERE id = $idOcor And ativo = 1");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $DescOc = $tbl2[0];
                    }else{
                        $DescOc = "";
                    }
                    $rs3 = pg_query($Conec, "SELECT descmot FROM ".$xProj.".escaladaf_funcmot WHERE id = $idMot And ativo = 1");
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $DescMot = $tbl3[0];
                    }else{
                        $DescMot = "";
                    }
                    $rs4 = pg_query($Conec, "SELECT descstat FROM ".$xProj.".escaladaf_funcstat WHERE id = $idStat And ativo = 1");
                    $row4 = pg_num_rows($rs4);
                    if($row4 > 0){
                        $tbl4 = pg_fetch_row($rs4);
                        $DescStat = $tbl4[0];
                    }else{
                        $DescStat = "";
                    }
                    $rs5 = pg_query($Conec, "SELECT descadm FROM ".$xProj.".escaladaf_funcadm WHERE id = $idAdm And ativo = 1");
                    $row5 = pg_num_rows($rs5);
                    if($row5 > 0){
                        $tbl5 = pg_fetch_row($rs5);
                        $DescAdm = $tbl5[0];
                    }else{
                        $DescAdm = "";
                    }

                    $pdf->SetX(40); 
                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(16, 4, "Ocorrência:", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'U', 8);
                    $pdf->Cell(32, 4, $DescOc, 0, 0, 'L');

                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(10, 4, "Motivo:", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'U', 8);
                    $pdf->Cell(30, 4, $DescMot, 0, 0, 'L');

                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(10, 4, "Status:", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'U', 8);
                    $pdf->Cell(30, 4, $DescStat, 0, 0, 'L');

                    $pdf->SetFont('Arial', 'I', 8);
                    $pdf->Cell(8, 4, "Ação:", 0, 0, 'L');
                    $pdf->SetFont('Arial', 'U', 8);
                    $pdf->Cell(30, 4, $DescAdm, 0, 1, 'L');

                    $pdf->SetFont('Arial', '', 8);
                    $pdf->SetX(40); 
                    $pdf->Cell(30, 4, "Observ:", 0, 0, 'L');
                    $pdf->SetX(53); 
                    $pdf->MultiCell(0, 4, $tbl1[3], 0, 'J', false);
                    if($row1 > 1){
                        $lin = $pdf->GetY();               
                        $pdf->Line(38, $lin, 200, $lin);
                    }
                    $pdf->ln(1);
                }
                $pdf->ln(1);
                $lin = $pdf->GetY();               
                $pdf->Line(20, $lin, 200, $lin);
                $pdf->ln(1);
            }
        }else{
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 4, "Nada foi encontrado.", 0, 'C', false);
            $pdf->ln(4);
            $lin = $pdf->GetY();               
            $pdf->Line(20, $lin, 200, $lin);
            $pdf->ln(10);
        }
    }
}
$pdf->Output();