<?php

// id 134 em pessoas - Willian Massen Marinho


$con_string = "host= localhost port=5432 dbname=pessoal user=postgres password=postgres";

$Conec = @pg_connect($con_string) or die("Não foi possível conectar-se ao banco de dados.");

//$Senha = password_hash('123', PASSWORD_DEFAULT);

$rs = pg_query($Conec, "UPDATE pessoas SET cpf = '12345678900' WHERE id = 134");
$rs = pg_query($Conec, "UPDATE pessoas SET cpf = '12345678901' WHERE id = 6");
$rs = pg_query($Conec, "UPDATE pessoas SET cpf = '12345678902' WHERE id = 50");
$rs = pg_query($Conec, "UPDATE pessoas SET cpf = '12345678903' WHERE id = 152");


$cpf = "12345678901";
$Sen = "123";
$Achou = 0;
//$rs = pg_query($Conec, "select hash_senha from public.pessoas INNER JOIN public.usuario ON public.pessoas.id = public.usuario.id_pessoa where cpf='".$cpf."' ");
//$row = pg_num_rows($rs);
//if($row > 0){
//    $tbl = pg_fetch_row($rs);
//    if(password_verify($Sen, $tbl[0])){
//        $Achou = 1;
//    }
//}

echo "<br>";
//echo "Achou: ".$Achou;
echo "<br>";

$Senha = password_hash('123', PASSWORD_DEFAULT);


$rs = pg_query($Conec, "SELECT id FROM usuario WHERE id_pessoa = 6");
$row = pg_num_rows($rs);

if($row == 0){
    pg_query($Conec, "INSERT INTO usuario (id_pessoa, ativo, data_criacao, data_ativacao, hash_senha) 
    VALUES (6, true, NOW(), NOW(), '$Senha') ");
//    pg_query($Conec, "INSERT INTO funcionarios (id_pessoa, id_setor)  VALUES (6, 2) ");
}else{
    pg_query($Conec, "UPDATE usuario SET hash_senha = '$Senha' WHERE id_pessoa = 6");
    echo "6 - OK";
}
    
    pg_query($Conec, "UPDATE usuario SET hash_senha = '$Senha' WHERE id_pessoa = 6");
    pg_query($Conec, "UPDATE usuario SET hash_senha = '$Senha' WHERE id_pessoa = 134");
    pg_query($Conec, "UPDATE usuario SET hash_senha = '$Senha' WHERE id_pessoa = 152");
    pg_query($Conec, "UPDATE usuario SET hash_senha = '$Senha' WHERE id_pessoa = 50");


echo "<br>";

$Nome = 'Ludinir Picelli';
$cpf = "13652176049";
$Sen = "123";
$Senha = password_hash($Sen, PASSWORD_DEFAULT);

require_once("modulos/config/abrealas.php");
$rs = pg_query($ConecPes, "SELECT cpf FROM pessoas WHERE cpf = '13652176049'");
$row = pg_num_rows($rs);
if($row == 0){
    pg_query($ConecPes, "INSERT INTO pessoas (nome_completo, cpf)  VALUES ('$Nome', '$cpf') ");
    $rs1 = pg_query($ConecPes, "SELECT id FROM pessoas WHERE cpf = '$cpf'");
    $tbl1 = pg_fetch_row($rs1);
    $id = $tbl1[0];
    pg_query($ConecPes, "INSERT INTO usuario (id_pessoa, ativo, data_criacao, data_ativacao, hash_senha)  VALUES ($id, true, NOW(), NOW(), '$Senha') ");
//    pg_query($Conec, "UPDATE ");
}else{
    echo "Já tem <br>";
}

$rs1 = pg_query($ConecPes, "SELECT id FROM pessoas WHERE cpf = '$cpf'");
$tbl1 = pg_fetch_row($rs1);
$id = $tbl1[0];
$rs2 = pg_query($ConecPes, "SELECT id FROM usuario WHERE id_pessoa = $id");
$row2 = pg_num_rows($rs2);
if($row2 == 0){
    pg_query($ConecPes, "INSERT INTO usuario (id_pessoa, ativo, data_criacao, data_ativacao, hash_senha) 
    VALUES ($id, true, NOW(), NOW(), '$Senha') ");
}


$rs1 = pg_query($Conec, "SELECT id FROM cesb.poslog WHERE pessoas_id = $id");
$row1 = pg_num_rows($rs1);
if($row1 > 0){
    pg_query($Conec, "UPDATE cesb.poslog SET adm = 7, codsetor = 2 WHERE pessoas_id = $id");
}else{
    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1);
    pg_query($Conec, "INSERT INTO cesb.poslog (id, pessoas_id, logini, numacessos, adm, codsetor)
    VALUES ($CodigoNovo, $id, NOW(), 1, 7, 2) "); 
}




//pg_query($ConecPes, "INSERT INTO usuario (id_pessoa, ativo, data_criacao, data_ativacao, hash_senha)  VALUES (50, true, NOW(), NOW(), '$Senha') ");

echo "<br>";
echo "<br>";        


//        $rs2 = pg_query($Conec, "SELECT * FROM dblink('host=127.0.0.1  user=postgres  password=postgres   dbname=pessoal ',   
//					 'SELECT nome_completo FROM pessoas INNER JOIN cesb.poslog ON pessoas.id = cesb.poslog.pessoas_id  ') 
//					 t (nome_completo text);" );

//        $rs2 = pg_query($Conec, "SELECT * FROM dblink('host=127.0.0.1  user=postgres  password=postgres   dbname=pessoal ', 
//                'SELECT pessoas.id, pessoas.cpf, pessoas.nome_completo, ".$xProj.".poslog.ativo, CAST(".$xProj.".poslog.logini AS DATE) 
//                FROM pessoas INNER JOIN cesb.poslog ON pessoas.id = cesb.poslog.pessoas_id  ') 
//                t (id int, cpf text, nome_completo text, ativo text, logini text);" );


//        $row2 = pg_num_rows($rs2); 
//        echo "<br>";
//        echo "DBLINK ".$row2;
//        echo "<br>";
//        while($tbl2 = pg_fetch_row($rs2)){
//            $Data1 = date('d/m/Y', strtotime($tbl2[4]));
//            echo $tbl2[0]."   ".$tbl2[1]."  ".$tbl2[2]."<br>";
//            echo $tbl2[3]." - ".$tbl2[4]."<br>";
//            echo $Data1."<br>";
//        }

$rs0 = pg_query($Conec, "UPDATE cesb.tarefas SET usuins = 153");
$rs0 = pg_query($Conec, "UPDATE cesb.tarefas SET usuexec = 6");









