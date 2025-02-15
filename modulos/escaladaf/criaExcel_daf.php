<?php
    session_start();
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

    require_once(dirname(dirname(dirname(__FILE__)))."/vendor/autoload.php");
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $Semana_Extract = array(
        '0' => 'Dom',
        '1' => '2ª',
        '2' => '3ª',
        '3' => '4ª',
        '4' => '5ª',
        '5' => '6ª',
        '6' => 'Sáb',
        'xª'=> ''
    );

    if(isset($_REQUEST["acao"])){
        $Acao = $_REQUEST["acao"];
        $NumGrupo = $_REQUEST["numgrupo"];
    }else{
        $Acao = "listaturnos";
    }

    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(is_null($MesSalvo) || $MesSalvo == ""){
        $MesSalvo = date("m")."/".date("Y");
    }
    $Proc = explode("/", $MesSalvo);
    if(is_null($Proc[1])){
        $Mes = date("m");
    }else{
        $Mes = $Proc[0];
    }
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    if(is_null($Proc[1])){
        $Ano = date("Y");
        }else{
        $Ano = $Proc[1];
    }

    if($Acao == "listaturnos"){
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);
        $activeWorksheet = $objPHPExcel->getActiveSheet();

        $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $SiglaGrupo = $tbl[0];
        }else{
            $SiglaGrupo = "";
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Escala mês: ".$MesSalvo." - ".$SiglaGrupo);
        //Cabeçalho
        $objPHPExcel->getActiveSheet()->mergeCells('A1:F1', $activeWorksheet::MERGE_CELL_CONTENT_MERGE);
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Data');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', 'Nome');
        $objPHPExcel->getActiveSheet()->setCellValue('D2', 'Letra');
        $objPHPExcel->getActiveSheet()->setCellValue('E2', 'Turno');
        $objPHPExcel->getActiveSheet()->setCellValue('F2', 'Vale Ref');
        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

        $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD/MM/YYYY'), TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), feriado 
        FROM ".$xProj.".escaladaf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo 
        ORDER BY dataescala");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $Num = 4;
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $Data = $tbl0[1];
                $DiaId = $tbl0[0];
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$Num, $Data);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$Num, $Semana_Extract[$tbl0[3]]);
                
                $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual, letraturno, turnoturno, destaque, date_part('dow', dataescala), feriado, valepag 
                FROM ".$xProj.".escaladaf INNER JOIN (".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id  
                WHERE escaladaf_id = $DiaId And grupo_id = $NumGrupo And poslog.eft_daf = 1 And escaladaf.ativo = 1 And escaladaf_ins.ativo = 1 And poslog.ativo = 1 ORDER BY nomeusual, nomecompl");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    while($tbl1 = pg_fetch_row($rs1)){
                        if(is_null($tbl1[1]) || $tbl1[1] == ""){
                            $Nome = substr($tbl1[0], 0, 20); //nome completo
                        }else{
                            $Nome = substr($tbl1[1], 0, 20); //nome usual
                        }

                        $Letra = $tbl1[2];
                        $Turno = $tbl1[3];
                        $Vale = $tbl1[7];
                        $DescVale = "Ok";
                        if($Vale == 0){
                            $DescVale = "Sem Vale";
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num, $Nome);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$Num, $Letra);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$Num, $Turno);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$Num, $DescVale);
                        $Num++;
                    }
                }
                $Num++;
            }
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle('A:F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $writer = new Xlsx($objPHPExcel);
        $writer->save('..\conteudo\arquivos\ListaTurnos.xlsx');
    }

    if($objPHPExcel){
        $ObjPHP = 1;
    }else{
        $ObjPHP = 0;
    }

    if(file_exists("..\conteudo\arquivos\ListaTurnos.xlsx")){
        $Arquivo = 1;
    }else{
        $Arquivo = 0;
    }

    if($writer){
        $var = array("coderro"=>0, "criaobjphp"=>$ObjPHP, "arquivo"=>$Arquivo);
    }else{
        $var = array("coderro"=>1, "criaobjphp"=>$ObjPHP);
    }
    $responseText = json_encode($var);
    echo $responseText;