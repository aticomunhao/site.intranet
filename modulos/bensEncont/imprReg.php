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
    $Hoje = date('d/m/Y');

    $rsCabec = pg_query($Conec, "SELECT cabec1, cabec2, cabec3 FROM ".$xProj.".setores WHERE codset = ".$_SESSION["CodSetorUsu"]." ");
    $rowCabec = pg_num_rows($rsCabec);
    $tblCabec = pg_fetch_row($rsCabec);
    $Cabec1 = $tblCabec[0];
    $Cabec2 = $tblCabec[1];
    $Cabec3 = $tblCabec[2];

    $rs = pg_query($Conec, "SELECT to_char(".$xProj.".bensachados.datareceb, 'DD/MM/YYYY'), numprocesso, descdobem, to_char(".$xProj.".bensachados.dataachou, 'DD/MM/YYYY'), localachou, nomeachou, telefachou, to_char(NOW(), 'DD/MM/YYYY'), usuguarda, to_char(".$xProj.".bensachados.dataguarda, 'DD/MM/YYYY'), nomepropriet, 
    cpfpropriet, telefpropriet, usurestit, to_char(".$xProj.".bensachados.datarestit, 'DD/MM/YYYY'), usucsg, to_char(".$xProj.".bensachados.datarcbcsg, 'DD/MM/YYYY'), setordestino, nomerecebeudestino, destinonodestino, to_char(".$xProj.".bensachados.datadestino, 'DD/MM/YYYY'), 
    usuarquivou, to_char(".$xProj.".bensachados.dataarquivou, 'DD/MM/YYYY'), usudestino, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, CURRENT_DATE-datareceb As Dias, descencdestino, descencprocesso 
    FROM ".$xProj.".bensachados INNER JOIN ".$xProj.".poslog ON ".$xProj.".bensachados.codusuins = ".$xProj.".poslog.pessoas_id
    WHERE ".$xProj.".bensachados.id = $Num ");
    $row = pg_num_rows($rs);
    $tbl = pg_fetch_row($rs);
    $Processo = $tbl[1];
    $UsuGuarda = $tbl[8];
    $UsuRestit = $tbl[13];
    $UsuCSG = $tbl[15];
    $DestinoBem = (int) $tbl[19];
    $UsuDestino = $tbl[23];
    $UsuArquiv = $tbl[21];
//    $Intervalo = (int) $tbl[24];
    $Dias = (int) $tbl[25];
    $DestSetor = $tbl[26];
    $DestProcesso = $tbl[27];
//    8 e 9 usuguarda e dataguarda
// 10 nomeprop
//usurestit 13   data restit 14
//setordestino 17  destino do bem 19   datadestino 20  data arquivou 22

    $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuGuarda");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $NomeGuarda = $tbl1[0];
    }else{
        $NomeGuarda = "";
    }
    $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuRestit");
    $row2 = pg_num_rows($rs2);
    if($row2 > 0){
        $tbl2 = pg_fetch_row($rs2);
        $NomeRestit = $tbl2[0];
    }else{
        $NomeRestit = "";
    }
    $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuCSG");
    $row3 = pg_num_rows($rs3);
    if($row3 > 0){
        $tbl3 = pg_fetch_row($rs3);
        $NomeCSG = $tbl3[0];
    }else{
        $NomeCSG = "";
    }
    $rs4 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuArquiv");
    $row4 = pg_num_rows($rs4);
    if($row4 > 0){
        $tbl4 = pg_fetch_row($rs4);
        $NomeArquivou = $tbl4[0];
    }else{
        $NomeArquivou = "";
    }
    $rs5 = pg_query($Conec, "SELECT descdest FROM ".$xProj.".bensdestinos WHERE numdest = $DestinoBem");
    $row5 = pg_num_rows($rs5);
    if($row5 > 0){
        $tbl5 = pg_fetch_row($rs5);
        $DescDestino = $tbl5[0];
    }else{
        $DescDestino = "";
    }
    $rs6 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuDestino");
    $row6 = pg_num_rows($rs6);
    if($row6 > 0){
        $tbl6 = pg_fetch_row($rs6);
        $NomeUsuDestino = $tbl6[0];
    }else{
        $NomeUsuDestino = "";
    }

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
    $pdf->SetTitle('Processo '.$Processo, $isUTF8=TRUE);
    if($Dom != "" && $Dom != "NULL"){
        if(file_exists('../../imagens/'.$Dom)){
            if(getimagesize('../../imagens/'.$Dom)!=0){
                $pdf->Image('../../imagens/'.$Dom,12,8,16,20);
            }
        }
    }
    if($Cabec1 == ""){
        $Cabec1 = "COMUNHÃO ESPÍRITA DE BRASÍLIA";
    }
    $pdf->SetX(40); 
    $pdf->SetFont('Arial','' , 14); 
    $pdf->Cell(150, 5, $Cabec1, 0, 2, 'C');
    $pdf->SetFont('Arial','' , 12); 
//    $pdf->Cell(150, 5, $Cabec2, 0, 2, 'C');
    $pdf->Cell(150, 5, "Diretoria Administrativa e Financeira", 0, 2, 'C');
    $pdf->SetFont('Arial','' , 10); 
    $pdf->Cell(150, 5, $Cabec3, 0, 2, 'C');
    $pdf->SetFont('Arial', '' , 10);
    $pdf->SetTextColor(25, 25, 112);
    if($Acao == "impr" ){
        $pdf->MultiCell(150, 3, "Achados e Perdidos", 0, 'C', false);
    }

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 6);
    $pdf->ln();
//    $lin = $pdf->GetY();
//    $pdf->Line(10, $lin, 200, $lin);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->ln(3);

    $pdf->SetTextColor(125, 125, 125); //cinza  //   $pdf->SetTextColor(190, 190, 190); //cinza claro   //  $pdf->SetTextColor(204, 204, 204); //cinza mais claro
    $pdf->SetFillColor(232, 232, 232); // fundo cinza
    if($Acao == "imprProcesso"){
        $pdf->MultiCell(0, 8, "REGISTRO DE RECEBIMENTO DE ACHADOS E PERDIDOS", 1, 'C', true);
        $pdf->ln(3);

        $pdf->Cell(0, 4, "- Processo: ".$tbl[1]." registrado em ".$tbl[0], 0, 1, 'L');

        $lin = $pdf->GetY();
        if($UsuRestit > 0){
            $pdf->SetY($lin-4); 
            $pdf->SetTextColor(184, 2, 2);
            $pdf->SetX(125); 
            $pdf->Cell(0, 4, "- RESTITUÍDO - ", 0, 1, 'L');
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->ln(2);
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
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Local em que foi encontrado: 	", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[4], 0, 1, 'L');
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Nome do colaborador que encontrou: ", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(25); 
        $pdf->Cell(0, 4, $tbl[5], 0, 1, 'L');
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Telefone do colaborador que encontrou: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[6], 0, 1, 'L');
        $pdf->ln(2);


//        $lin = $pdf->GetY();
//        $pdf->Line(10, $lin, 200, $lin);
//        $pdf->ln(5);
//        $pdf->SetFont('Arial', 'I', 8);
//        $pdf->MultiCell(0, 5, "TERMO DE RECEBIMENTO PELA DAF....", 1, 'L', true);
//        $pdf->ln(3);
//
//        $pdf->SetX(15); 
//        $pdf->SetFont('Arial', 'I', 9);
//        $pdf->MultiCell(0, 5, "     Declaro que recebi o Bem acima descrito, ao qual efetuarei a guarda pelo período de 90 (noventa) dias. Após esse prazo, a destinação do bem seguirá o caminho estabelecido na NI-4.05-B (DAF).", 1, 'J', false);
//        $pdf->ln(1);
//
//        $pdf->SetFont('Arial', 'I', 8);
//        $pdf->SetX(20); 
//
//        if($UsuGuarda == 0){
//            $pdf->MultiCell(0, 5, "Transferência para a DAF não realizada", 0, 'C', false);
//        }else{
//            $pdf->MultiCell(0, 5, "Brasília, ".$tbl[9]."                         (a) ".$NomeGuarda, 0, 'C', false);
//        }
//        $pdf->ln(7);


        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(5);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->MultiCell(0, 5, "TERMO DE RECEBIMENTO PELO SSV", 1, 'L', true);
        $pdf->ln(3);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->MultiCell(0, 5, "     Declaro que recebi o Bem acima descrito, ao qual efetuarei a guarda pelo período de 90 (noventa) dias. Após esse prazo, a destinação do bem seguirá o caminho estabelecido na NI-4.05-B (DAF).", 1, 'J', false);
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetX(20); 

        if($UsuCSG == 0){
            $pdf->MultiCell(0, 5, "Transferência para o SSV não realizada", 0, 'C', false);
        }else{
            $pdf->MultiCell(0, 5, "Brasília, ".$tbl[16]."                         (a) ".$NomeCSG, 0, 'C', false);
        }
        $pdf->ln(7);



        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(5);

        if($UsuRestit > 0){
            $pdf->SetFillColor(247, 196, 181); // 
        }
        $pdf->MultiCell(0, 5, "REGISTRO DE RESTITUIÇÃO DOS BENS DESCRITOS NESTE PROCESSO", 1, 'L', true);
        $pdf->SetFillColor(232, 232, 232); // fundo cinza

        if($UsuRestit > 0){
            $pdf->ln(2);
            $lin = $pdf->GetY();

            $pdf->SetDrawColor(184, 2, 2); // 
            $pdf-> Rect(23, $lin, 165, 15, 'F');
            $pdf->SetDrawColor(200); // cinza claro
            
            $pdf->ln(3);
            $pdf->SetX(35); 
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(55, 4, "Nome do Proprietário: ", 0, 0, 'L');
            $pdf->SetX(65); 
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(55, 4, $tbl[10] , 0, 1, 'L');

            $pdf->ln(1);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetX(56); 
            $pdf->Cell(10, 4, "CPF: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(55, 4, $tbl[11], 0, 0, 'L');

            $pdf->SetX(120); 
            $pdf->Cell(20, 4, "Telefone: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(20, 4, $tbl[12], 0, 1, 'L');

            $pdf->ln(5);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->MultiCell(0, 4, "Restituição efetuada em ".$tbl[14], 0, 'C', false);
            $pdf->MultiCell(0, 4, "(a): ". $NomeRestit, 0, 'C', false);

        }else{
            $pdf->MultiCell(0, 5, "Bem não procurado", 0, 'C', false);
            $pdf->ln(10);
        }

//        $pdf->ln(5);
//        $lin = $pdf->GetY();
//        $pdf->Line(10, $lin, 200, $lin);
//        $pdf->ln(5);
//
//        $pdf->MultiCell(0, 5, "ENCAMINHAMENTO PARA SSV", 1, 'L', true);
//        $pdf->ln(3);
//        $pdf->SetX(15); 
//        $pdf->SetFont('Arial', 'I', 9);
//        $pdf->MultiCell(0, 5, "     Declaro que recebi, nesta SSV, o bem constante do processo ".$tbl[1]." para armazenamento e destinação do Bem ou arquivamento do processo.", 1, 'J', false);
//        $pdf->ln(1);
//
//        $pdf->SetX(20); 
//        $pdf->SetFont('Arial', '', 8);
//        if($UsuCSG == 0){
//            $pdf->MultiCell(0, 5, "Encaminhamento para SSV não realizado.", 0, 'C', false);
//        }else{
//            $pdf->MultiCell(0, 5, "Brasília, ".$tbl[16]."                         (a) ".$NomeCSG, 0, 'C', false);
//        }

        $pdf->ln(7);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);

        $pdf->ln(5);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->MultiCell(0, 5, "DESTINAÇÃO APÓS O PRAZO DE 90 DIAS", 1, 'L', true);

        if($UsuDestino > 0){
            $pdf->ln(3);
            $pdf->SetX(15); 
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(30, 4, "Em: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $tbl[20], 0, 'J', false);

            $pdf->ln(1);
            $pdf->SetX(15); 
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(30, 4, "Setor de destino: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $DestSetor, 1, 'J', false);

            $pdf->ln(1);
            $pdf->SetX(15); 
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(30, 4, "Finalidade: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $DestProcesso, 1, 'J', false);

            $pdf->ln(1);
            $pdf->SetX(15); 
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(30, 4, "Recebido por: ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, $NomeUsuDestino, 1, 'J', false);
        }else{
            $pdf->ln(3);
            if($UsuRestit > 0){
                $pdf->MultiCell(0, 5, "Bem foi restituído.", 0, 'C', false);
            }else{
                $pdf->MultiCell(0, 5, "Bem não destinado.", 0, 'C', false);
            }
        }
        $pdf->ln(5);
        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(10);
        $pdf->SetX(15); 

        if($UsuArquiv > 0){
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 5, "Este processo (".$Processo.") foi encerrado e arquivado em ".$tbl[22].".", 1, 'C', true);
            $pdf->MultiCell(0, 5, "(a) ".$NomeArquivou, 0, 'C', false);
        }else{
            $pdf->SetFont('Arial', '', 10);
            if($Dias < 90){
                $pdf->MultiCell(0, 5, "Processo ".$Processo." está aberto aguardando prazo.", 1, 'C', true);    
            }else{
                $pdf->MultiCell(0, 5, "Processo ".$Processo." está aberto.", 1, 'C', true);
            }
        }
    }
    if($Acao == "imprReciboRest"){
        $NomePropriet = $_REQUEST["nomeproprietario"];
        $CpfPropriet = $_REQUEST["cpfproprietario"];
        $TelfPropriet = $_REQUEST["telefproprietario"];

        $pdf->MultiCell(0, 8, "RECIBO DE RESTITUIÇÃO DE BEM ENCONTRADO", 1, 'C', true);
        $pdf->ln(3);

        $pdf->Cell(0, 4, "- Processo: ".$tbl[1]." registrado em ".$tbl[0], 0, 1, 'L');

        $lin = $pdf->GetY();
        if($UsuRestit > 0){
            $pdf->SetY($lin-4); 
            $pdf->SetTextColor(184, 2, 2);
            $pdf->SetX(125); 
            $pdf->Cell(0, 4, "- RESTITUÍDO - ", 0, 1, 'L');
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->ln(2);
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
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Local em que foi encontrado: 	", 0, 1, 'L');
        $pdf->SetX(25); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[4], 0, 1, 'L');
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, "Nome do colaborador que encontrou: ", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(25); 
        $pdf->Cell(0, 4, $tbl[5], 0, 1, 'L');
        $pdf->ln(2);

        $pdf->SetX(15); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Telefone do colaborador que encontrou: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, $tbl[6], 0, 1, 'L');
        $pdf->ln(2);


        $lin = $pdf->GetY();
        $pdf->Line(10, $lin, 200, $lin);
        $pdf->ln(5);


        $pdf->ln(2);
        $lin = $pdf->GetY();

        $pdf->SetDrawColor(184, 2, 2); // 
        $pdf-> Rect(23, $lin, 165, 15, 'F');
        $pdf->SetDrawColor(200); // cinza claro
        
        $pdf->ln(3);
        $pdf->SetX(35); 
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(55, 4, "Nome do Proprietário: ", 0, 0, 'L');
        $pdf->SetX(65); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(55, 4, $NomePropriet , 0, 1, 'L');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetX(56); 
        $pdf->Cell(10, 4, "CPF: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(55, 4, $CpfPropriet, 0, 0, 'L');

        $pdf->SetX(120); 
        $pdf->Cell(20, 4, "Telefone: ", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 4, $TelfPropriet, 0, 1, 'L');

        $pdf->ln(10);
        $pdf->SetFont('Arial', 'I', 8);
        if($UsuRestit > 0){ // já foi feita a restit
            $pdf->MultiCell(0, 4, "Informo ter recebido o bem acima descrito em ".$tbl[14], 0, 'C', false);
        }else{
            $pdf->MultiCell(0, 4, "Informo ter recebido o bem acima descrito em ".$Hoje, 0, 'C', false);
        }
        $pdf->ln(10);
        $pdf->MultiCell(0, 4, "______________________________________________________", 0, 'C', false);
        $pdf->MultiCell(0, 4, $NomePropriet, 0, 'C', false);
    }
}
$pdf->Output();