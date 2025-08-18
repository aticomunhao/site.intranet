<?php
    session_start();
    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

    require_once(dirname(dirname(dirname(__FILE__)))."/vendor/autoload.php");
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $Semana_Extract = array(
        '0' => 'Dom',
        '1' => 'Seg',
        '2' => 'Ter',
        '3' => 'Qua',
        '4' => 'Qui',
        '5' => 'Sex',
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

    //Procura o último dia do mês para inserir a contagem de vale refeição
    $rs2 = pg_query($Conec, "SELECT MAX(TO_CHAR(dataescala, 'DD')) FROM ".$xProj.".escaladaf WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano'");
    $tbl2 = pg_fetch_row($rs2);
    $UltDia = $tbl2[0];

    if($Acao == "listaturnos"){
        $objPHPExcel = new Spreadsheet();

        if(!isset($objPHPExcel)){
            $ObjPHP = 0;
            $var = array("coderro"=>0, "criaobjphp"=>$ObjPHP);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }

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
        FROM ".$xProj.".escaladaf 
        WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo 
        ORDER BY dataescala");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $Num = 4;
            while($tbl0 = pg_fetch_row($rs0)){
                $Cod = $tbl0[0];
                $Data = $tbl0[1];
                $DiaId = $tbl0[0];
                $Dia = $tbl0[2];
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$Num, $Data);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$Num, $Semana_Extract[$tbl0[3]]);
                
                $rs2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog 
                WHERE esc_grupo = $NumGrupo And poslog.eft_daf = 1 And poslog.ativo = 1 ORDER BY ordem_daf, nomeusual, nomecompl ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    while($tbl2 = pg_fetch_row($rs2)){
                        $PoslogId = $tbl2[0];
                        if(is_null($tbl2[2]) || $tbl2[2] == ""){
                            $Nome = substr($tbl2[1], 0, 20); //nome completo
                        }else{
                            $Nome = substr($tbl2[2], 0, 20); //nome usual
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num, $Nome);

                        $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual, letraturno, turnoturno, destaque, date_part('dow', dataescala), feriado, valepag, poslog_id 
                        FROM ".$xProj.".escaladaf INNER JOIN (".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id  
                        WHERE poslog.pessoas_id = $PoslogId And escaladaf_id = $DiaId And grupo_id = $NumGrupo And poslog.eft_daf = 1 And escaladaf.ativo = 1 And escaladaf_ins.ativo = 1 And poslog.ativo = 1 ORDER BY ordem_daf, nomeusual, nomecompl");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            while($tbl1 = pg_fetch_row($rs1)){
                                $PoslogId = $tbl1[8];
                                $Letra = $tbl1[2];
                                $Turno = $tbl1[3];
                                $Vale = $tbl1[7];
                                $DescVale = "Ok";
                                if($Vale == 0){
                                    $DescVale = "Sem Vale";
                                }
                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$Num, $Letra);
                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$Num, $Turno);
                                $objPHPExcel->getActiveSheet()->setCellValue('F'.$Num, $DescVale);
                            }
                        }else{ // se não encontrar, está em branco
                            $objPHPExcel->getActiveSheet()->setCellValue('D'.$Num, "-");
                            $objPHPExcel->getActiveSheet()->setCellValue('E'.$Num, "-");
                            $objPHPExcel->getActiveSheet()->setCellValue('F'.$Num, "-");
                        }
                        $Num++;
                    }
                }
            }
        }


        //Contabem de serviços
        $rs4 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog 
        WHERE esc_grupo = $NumGrupo And poslog.eft_daf = 1 And poslog.ativo = 1 ORDER BY ordem_daf, nomeusual, nomecompl ");
        $row4 = pg_num_rows($rs4);
        if($row4 > 0){
            $Num2 = ($Num+4); 
            $objPHPExcel->getActiveSheet()->getStyle('C'.$Num2)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num2, "Contagem:");
            $Num2++;

            while($tbl4 = pg_fetch_row($rs4)){
                $PoslogId = $tbl4[0];
                if(is_null($tbl4[2]) || $tbl4[2] == ""){
                    $Nome = substr($tbl4[1], 0, 20); //nome completo
                }else{
                    $Nome = substr($tbl4[2], 0, 20); //nome usual
                }

                $rs5 = pg_query($Conec, "SELECT COUNT(poslog_id) 
                FROM ".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".escaladaf_ins.turnos_id = ".$xProj.".escaladaf_turnos.id 
                WHERE poslog_id = $PoslogId And TO_CHAR(dataescalains, 'MM') = '$Mes' And  TO_CHAR(dataescalains, 'YYYY') = '$Ano' And grupo_ins = $NumGrupo And infotexto = 0 And valepag = 1");
                $tbl5 = pg_fetch_row($rs5);
                $Total = $tbl5[0];

                $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num2, $Nome);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$Num2, $Total);
                $Num2++;
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



        //Contabem Geral de serviços - todos os grupos
        $rs6 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog 
        WHERE poslog.eft_daf = 1 And poslog.ativo = 1 ORDER BY nomecompl, ordem_daf, nomeusual ");
        $row6 = pg_num_rows($rs6);
        if($row6 > 0){
            $Num3 = ($Num2+4); 
            $objPHPExcel->getActiveSheet()->getStyle('C'.$Num3)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num3, "Contagem Geral Mês: ".$MesSalvo);
            $Num3++;

            while($tbl6 = pg_fetch_row($rs6)){
                $PoslogId = $tbl6[0];
                $Nome = substr($tbl6[1], 0, 50); //nome completo
                $rs7 = pg_query($Conec, "SELECT COUNT(poslog_id) 
                FROM ".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".escaladaf_ins.turnos_id = ".$xProj.".escaladaf_turnos.id 
                WHERE poslog_id = $PoslogId And TO_CHAR(dataescalains, 'MM') = '$Mes' And  TO_CHAR(dataescalains, 'YYYY') = '$Ano' And infotexto = 0 And valepag = 1");
                $tbl7 = pg_fetch_row($rs7);
                $Total = $tbl7[0];

                $objPHPExcel->getActiveSheet()->setCellValue('C'.$Num3, $Nome);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$Num3, $Total);
                $Num3++;
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





//        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
        $writer = new Xlsx($objPHPExcel);
        $writer->save(dirname(dirname(__FILE__)).'/conteudo/arquivos/ListaTurnos.xlsx');

        if(!isset($writer)){
            $ArqSalvo = 0;
        }else{
            $ArqSalvo = 1;
        }
    }

    if(!isset($objPHPExcel)){
        $ObjPHP = 0;
    }else{
        $ObjPHP = 1;
    }

    if(file_exists(dirname(dirname(__FILE__))."/conteudo/arquivos/ListaTurnos.xlsx")){
        $Arquivo = 1;
    }else{
        $Arquivo = 0;
    }

    $var = array("coderro"=>0, "criaobjphp"=>$ObjPHP, "arquivo"=>$Arquivo, "salvo"=>$ArqSalvo);
    $responseText = json_encode($var);
    echo $responseText;