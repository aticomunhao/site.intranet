<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION['AdmUsu'])){
    echo " A sessão foi encerrada.";
    return false;
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
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
$Semana_Extract = array(
    '0' => 'D',
    '1' => '2ª',
    '2' => '3ª',
    '3' => '4ª',
    '4' => '5ª',
    '5' => '6ª',
    '6' => 'S',
    'xª'=> ''
);


 if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once('../../class/fpdf/fpdf.php'); // adaptado ao PHP 7.2 - 8.2
    define('FPDF_FONTPATH', '../../class/fpdf/font/');  
    $Dom = "logo_comunhao_completa_cor_pos_150px.png";
    
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }

    $rs = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual 
    FROM ".$xProj.".poslog 
    WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl ");
    $Efet = pg_num_rows($rs); // efetivo do grupo


    $rsGr = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo");
    $tblGr = pg_fetch_row($rsGr);
    $Cabec3 = $tblGr[0];

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
//    $Cabec3 = $tblCabec[2];

    class PDF extends FPDF{
        function Footer(){
           // Vai para 1 cm da parte inferior
           $this->SetY(-10);
           // Seleciona a fonte Arial itálico 8
           $this->SetFont('Arial','I',8);
           // Imprime o número da página corrente e o total de páginas
           $this->Cell(0, 5, 'Pag '.$this->PageNo().'/{nb}',0,0,'R');
         }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages(); // pega o número total de páginas
    if($Efet < 15){
        $pdf->AddPage(); 
    }else{
        $pdf->AddPage("L", "A4");
    }
    $pdf->SetLeftMargin(20);
    $pdf->SetAutoPageBreak(false);

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
    $pdf->Cell(0, 5, $Cabec2, 0, 2, 'C');
//    $pdf->Cell(0, 5, 'Diretoria Administrativa e Financeira', 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(0, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    $pdf->SetFillColor(255, 255, 255); // branco
    $pdf->SetTextColor(0, 0, 0);

    if($Acao == "imprDescanso"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        $Ano = $Proc[1];
        $Data = date('01/'.$Mes.'/'.$Ano);
        $DescMes = $mes_extenso[$Mes];

        $pdf->SetTitle('Escala '.$Busca, $isUTF8=TRUE);
        $pdf->SetFont('Arial', '' , 10);
        $pdf->SetTextColor(25, 25, 112);
        $pdf->MultiCell(0, 3, "Horários de Descanso ".$DescMes."/".$Ano, 0, 'C', false);
        $pdf->ln();
        $pdf->SetDrawColor(200);

        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 290, $lin);
        $pdf->ln(3);

        $Cont = 1;
        $Tam = 30;
        if($Efet > 0){ // número de escalados

            
            if($Efet > 5){
                $pdf->SetFont('Arial', '' , 8);
            }else{
                $pdf->SetFont('Arial', '' , 10);
            }

            $Marg = 20;
            $Corte = 16;
            if($Efet == 1){
                $Marg = 70;
            }
            if($Efet == 2){
                $Marg = 60;
            }
            if($Efet == 3){
                $Marg = 50;
            }
            if($Efet == 4){
                $Marg = 40;
            }
            if($Efet == 5){
                $Marg = 30;
            }
            if($Efet == 6){
                $Marg = 30;
                $Tam = 25;
            }
            if($Efet == 7){
                $Marg = 30;
                $Tam = 23;
            }
            if($Efet == 8){
                $Marg = 20;
                $Tam = 20;
                $Corte = 15;
            }
            if($Efet == 9){
                $Marg = 20;
                $Tam = 20;
                $Corte = 14;
            }
            if($Efet == 10){
                $Marg = 20;
                $Tam = 18;
                $Corte = 14;
            }
            if($Efet == 11){
                $Marg = 20;
                $Tam = 17;
                $Corte = 13;
            }
            if($Efet == 12){
                $Marg = 20;
                $Tam = 15;
                $Corte = 11;
            }
            if($Efet == 13){
                $Marg = 20;
                $Tam = 14;
                $Corte = 10;
                $pdf->SetFont('Arial', '' , 7);
            }
            if($Efet == 14){
                $Marg = 18;
                $Tam = 13.6;
                $Corte = 10;
                $pdf->SetFont('Arial', '' , 7);
            }

//Landscape
            if($Efet == 15){
                $Marg = 20;
                $Tam = 18;
                $Corte = 13;
                $pdf->SetFont('Arial', '' , 8);
            }

            if($Efet == 16){
                $Marg = 20;
                $Tam = 17;
                $Corte = 12;
                $pdf->SetFont('Arial', '' , 8);
            }
            //Nome
            $pdf->SetX($Marg); 
            while($tbl = pg_fetch_row($rs)){
                $PessoasId = $tbl[0];
                $Nome = substr($tbl[2], 0, $Corte);
                if(is_null($Nome) || $Nome == ""){
                    $Nome = substr($tbl[1], 0, $Corte);
                }
                $pdf->Cell($Tam, 5, $Nome, 1, 0, 'C', true);
            }
            $pdf->Cell(7, 5, "", 0, 1, 'L');
            $pdf->ln(2);

            //Dia
            $rs1 = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala)  
            FROM ".$xProj.".escaladaf 
            WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo ORDER BY dataescala");

            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1)){
                    $Dia = $tbl1[1];
                    $pdf->SetX($Marg-12); 
                    $pdf->Cell(27, 5, $Dia."  ".$Semana_Extract[$tbl1[2]], 0, 0, 'L');

                    $rs2 = pg_query($Conec, "SELECT horafolga, date_part('dow', dataescalains), destaque, turnoturno, infotexto 
                    FROM ".$xProj.".escaladaf_turnos INNER JOIN (".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog on ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".escaladaf_turnos.id = ".$xProj.".escaladaf_ins.turnos_id
                    WHERE ".$xProj.".poslog.ativo = 1 And ".$xProj.".poslog.eft_daf = 1 And TO_CHAR(dataescalains, 'DD') = '$Dia' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' And esc_grupo = $NumGrupo And ".$xProj.".escaladaf_ins.ativo = 1 ORDER BY nomeusual");

                    $row2 = pg_num_rows($rs2);
                    $pdf->SetX($Marg); 
                    if($row2 > 0){
                        while($tbl2 = pg_fetch_row($rs2)){
                            $InfoTexto = $tbl2[4];
                            if($InfoTexto == 1){
                                $pdf->Cell($Tam, 5, $tbl2[3], 1, 0, 'C', true);    
                            }else{
                                $pdf->Cell($Tam, 5, $tbl2[0], 1, 0, 'C', true);
                            }
                        }
                    }
                    $pdf->Cell(5, 5, "", 0, 1, 'C', true);
                }
            }
        }else{
            $pdf->SetFont('Arial', '' , 10);
            $pdf->MultiCell(0, 5, "Nada foi Encontrado.", 0, 'C', false);
        }
    }
}
$pdf->Output();