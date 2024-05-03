<?php
require_once("abrealas.php");
$Sen = filter_input(INPUT_GET, 'senha');

pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS icustom varchar(255);"); 
$Senha = password_hash($Sen, PASSWORD_DEFAULT);
$rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET icustom = '$Senha' WHERE idpar = 1");
echo "<br>";

if($rs){
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS datainiagua date DEFAULT '2024-03-01'");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS valoriniagua float8 DEFAULT 1696.485");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insleituraagua smallint NOT NULL DEFAULT 3 ");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editleituraagua smallint NOT NULL DEFAULT 6 ");

    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS datainieletric date DEFAULT '2024-03-01'");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS valorinieletric float8 DEFAULT 1000.001");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insleituraeletric smallint NOT NULL DEFAULT 3 ");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editleituraeletric smallint NOT NULL DEFAULT 6 ");


    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS datainileitura ");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS valorinileitura ");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS insleitura ");
    pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editleitura ");
    echo "Acerto de icustom em paramsis: ".$Sen."<br>";
}



pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".poslog (
    id SERIAL PRIMARY KEY, 
    pessoas_id bigint NOT NULL DEFAULT 0, 
    ativo smallint NOT NULL DEFAULT 1, 
    adm smallint NOT NULL DEFAULT 1, 
    codsetor smallint NOT NULL DEFAULT 1, 
    numacessos integer NOT NULL DEFAULT 0, 
    logini timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    logfim timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    usuins integer NOT NULL DEFAULT 0, 
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    usumodif integer NOT NULL DEFAULT 0, 
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    usuinat integer NOT NULL DEFAULT 0, 
    datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    motivoinat smallint NOT NULL DEFAULT 0, 
    avcalend smallint NOT NULL DEFAULT 1, 
    avhoje date, 
    cpf character varying(20), 
    nomecompl character varying(150), 
    senha character varying(255) 
    ) ");

    

pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS nome_completo varchar(100);"); // auxiliar para compor caixas de seleção de usuários para tarefas
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS cpf varchar(20);"); // guardar para eventualidades
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS sexo smallint NOT NULL DEFAULT 1;"); 
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS senha varchar(255);"); // hash
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS dt_nascimento date;"); 


$rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE cpf = '13652176049' ");
$row1 = pg_num_rows($rs1);
if($row1 > 0){
    echo "Já cadastrado em poslog"."<br>";
}else{
    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1); 

    pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, ativo, adm, codsetor, numacessos, datains, logini, nomecompl, cpf, senha)  VALUES ($CodigoNovo, 1, 1, 7, 2, 1, NOW(), NOW(), 'Ludinir Picelli', '13652176049', '$Senha') "); 
    echo "Inserido"."<br>";
}
pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha' WHERE senha IS NULL "); 
pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha' WHERE cpf = '13652176049'"); 


pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".pessoas (
    id SERIAL PRIMARY KEY, 
    pessoas_id bigint NOT NULL DEFAULT 0, 
    cpf character varying(20), 
    nome_completo character varying(200), 
    dt_nascimento date, 
    sexo smallint NOT NULL DEFAULT 0, 
    status smallint NOT NULL DEFAULT 1, 
    datains date 
) ");


$rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".pessoas WHERE cpf = '13652176049' ");
$row1 = pg_num_rows($rs1);
if($row1 > 0){
    echo "Já cadastrado em pessoas"."<br>";
}else{
    pg_query($Conec, "INSERT INTO ".$xProj.".pessoas (pessoas_id, cpf, nome_completo, dt_nascimento, sexo, status, datains) VALUES (1, '13652176049', 'Ludinir Picelli', '1952-11-12', 1, 1, NOW() ) "); 
    echo "Inserido"."<br>";
}

pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS avcalend smallint NOT NULL DEFAULT 1;"); // 1 - emitir avisos do calendário
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS avhoje date ;"); // não quer mais avisos odo calendário só por hoje
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".calendev ADD COLUMN IF NOT EXISTS avobrig smallint NOT NULL DEFAULT 0 ;"); // marca para mensagem com aviso obrigatório
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".calendev ADD COLUMN IF NOT EXISTS avok smallint NOT NULL DEFAULT 0 ;"); // marca de que o aviso foi dispensado 

pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".setores ADD COLUMN IF NOT EXISTS cabec1 VARCHAR(200) ;"); // Cabeçaçho relat
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".setores ADD COLUMN IF NOT EXISTS cabec2 VARCHAR(200) ;"); // Cabeçaçho relat
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".setores ADD COLUMN IF NOT EXISTS cabec3 VARCHAR(200) ;"); // Cabeçaçho relat
pg_query($Conec, "UPDATE ".$xProj.".setores SET cabec1 = 'COMUNHÃO ESPÍRITA DE BRASÍLIA' WHERE cabec1 IS NULL And codset > 1");
pg_query($Conec, "UPDATE ".$xProj.".setores SET cabec2 = descsetor WHERE cabec2 IS NULL");


pg_query($Conec, "DROP TABLE IF EXISTS cesb.leituras");
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".leitura_agua (
    id SERIAL PRIMARY KEY, 
    dataleitura date, 
    leitura1 float8, 
    leitura2 float8, 
    leitura3 float8, 
    ativo smallint NOT NULL DEFAULT 1, 
    usuins integer NOT NULL DEFAULT 0, 
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
    usumodif integer NOT NULL DEFAULT 0, 
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP 
    ) ");

    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".leitura_agua ");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        echo "Tabela Leitura_agua OK"."<br>";
    }else{
        pg_query($Conec, "INSERT INTO ".$xProj.".leitura_agua (id, dataleitura, leitura1, leitura2, leitura3, ativo, usuins, datains, usumodif, datamodif)  VALUES 
        (1,'2024-03-01',1696.521,1701.985,1706.527,1,0,NOW(),0,NOW()),
        (2,'2024-03-02',1706.805,1711.366,1717.391,1,0,NOW(),0,NOW()),
        (3,'2024-03-03',1718.328,1719.167,1720.432,1,0,NOW(),0,NOW()),
        (4,'2024-03-04',1720.615,1724.922,1729.228,1,0,NOW(),0,NOW()),
        (5,'2024-03-05',1729.572,1733.429,1738.67, 1,0,NOW(),0,NOW()),
        (6,'2024-03-06',1740.52,1745.172,1748.33,  1,0,NOW(),0,NOW()),
        (7,'2024-03-07',1748.585,1753.175,1756.061,1,0,NOW(),0,NOW()),
        (8,'2024-03-08',1757.115,1763.958,1767.866,1,0,NOW(),0,NOW()),
        (9,'2024-03-09',1768.114,1772.556,1777.259,1,0,NOW(),0,NOW()),
        (10,'2024-03-10',1777.595,1778.973,1780.705,1,0,NOW(),0,NOW()),
        (11,'2024-03-11',1780.989,1784.418,1789.295,1,0,NOW(),0,NOW()),
        (12,'2024-03-12',1789.694,1793.76,1799.36,1,0,NOW(),0,NOW()),
        (13,'2024-03-13',1801.18,1805.226,1809.702,1,0,NOW(),0,NOW()),
        (14,'2024-03-14',1810.058,1813.774,1818.363,1,0,NOW(),0,NOW()),
        (15,'2024-03-15',1818.836,1826.774,1829.83,1,0,NOW(),0,NOW()),
        (16,'2024-03-16',1830.057,1836.036,1839.971,1,0,NOW(),0,NOW()),
        (17,'2024-03-17',1840.163,1842.689,1843.735,1,0,NOW(),0,NOW()),
        (18,'2024-03-18',1844.042,1848.176,1852.79,1,0,NOW(),0,NOW()),
        (19,'2024-03-19',1853.052,1858.251,1864.09,1,0,NOW(),0,NOW()),
        (20,'2024-03-20',1864.42,1869.885,1873.923,1,0,NOW(),0,NOW()),
        (21,'2024-03-21',1874.222,1879.726,1883.749,1,0,NOW(),0,NOW()),
        (22,'2024-03-22',1883.962,1892.607,1895.568,1,0,NOW(),0,NOW()),
        (23,'2024-03-23',1895.778,1900.531,1904.812,1,0,NOW(),0,NOW()),
        (24,'2024-03-24',1905.018,1905.944,1907.72,1,0,NOW(),0,NOW()),
        (25,'2024-03-25',1908.025,1912.464,1917.393,1,0,NOW(),0,NOW()),
        (26,'2024-03-26',1917.724,1923.103,1928.974,1,0,NOW(),0,NOW()),
        (27,'2024-03-27',1929.236,1933.302,1937.095,1,0,NOW(),0,NOW()),
        (28,'2024-03-28',1937.35,1941.715,1944.763,1,0,NOW(),0,NOW()),
        (29,'2024-03-29',1944.984,1946.457,1948.918,1,0,NOW(),0,NOW()),
        (30,'2024-03-30',1948.702,1953.521,1957.556,1,0,NOW(),0,NOW()),
        (31,'2024-03-31',1957.803,1958.332,1959.843,1,0,NOW(),0,NOW())
        "); 
        echo "Inseridos em Leitura_agua"."<br>";
    }


    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".leitura_eletric (
        id SERIAL PRIMARY KEY, 
        dataleitura date, 
        leitura1 float8,  
        ativo smallint NOT NULL DEFAULT 1, 
        usuins integer NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
        usumodif integer NOT NULL DEFAULT 0, 
        datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP 
        ) ");

    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".leitura_eletric ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            echo "Tabela Leitura_eletric OK"."<br>";
        }else{
            pg_query($Conec, "INSERT INTO ".$xProj.".leitura_eletric (id, dataleitura, leitura1, ativo, usuins, datains, usumodif, datamodif)  VALUES 
            (1,'2024-03-01',1000.5,1,0,NOW(),0,NOW()),
            (2,'2024-03-02',1000.6,1,0,NOW(),0,NOW()), 
            (3,'2024-03-03',1000.7,1,0,NOW(),0,NOW())
            "); 
            echo "Inseridos em Leitura_eletric"."<br>";
        }

        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".setores SET siglasetor = 'DPS' WHERE codset = 9 ");