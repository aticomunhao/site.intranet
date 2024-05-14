<?php


 try {
    $db = new PDO("pgsql:host=localhost dbname=pessoal user=postgres password=postgres");
 } catch (PDOException  $e) {
    print $e->getMessage();
 }

 

$con_string = "host= 192.168.1.137 
port=5432 
dbname=pessoal 
user=postgres 
password=scga2298";

$bdcon4 = pg_connect($con_string);

$Campos = array("id"=>"");

$rs = pg_select($bdcon4, "pessoas", $Campos);

print_r($rs);
echo "<br>";
echo "<br><br>";

print_r($rs[1]);


//pg_select(
//    PgSql\Connection $connection,
//    string $table_name,
//    array $conditions,
//    int $flags = PGSQL_DML_EXEC,
//    int $mode = PGSQL_ASSOC
//): array|string|false

echo "<br><br>";


//    $db = pg_connect($con_string);
//    $selectfields = array("cpf" => "");
//    $records = pg_select($db,"pessoas",$selectfields);
//    print_r($records);


    $conn = pg_pconnect($con_string);
    if (!$conn) {
        echo "Erro conexão.\n";
        exit;
    }

    $result = pg_query($conn, "SELECT id, cpf, nome_completo FROM pessoas WHERE id = 11 ORDER BY id");
    if (!$result) {
      echo "Erro no Select.\n";
      exit;
    }
    while ($row = pg_fetch_row($result)) {
        echo "id: $row[0]    cpf: $row[1]    Nome: $row[2]";
        echo "<br />\n";
    }

    echo "<br><br>";

    $result2 = pg_query($conn, "SELECT id, cpf, nome_completo, dt_nascimento FROM pessoas WHERE id > 10 ORDER BY id");
      echo "<table>";
        
        while ($row = pg_fetch_row($result2)) {
            echo "<tr>";
                echo "<td> id:" . $row[0] . "</td>";
                echo "<td> cpf:" . $row[1] . "</td>";
                echo "<td> Nome:" . $row[2] . "</td>";
                echo "<td> Nasc:" . $row[3] . "</td>";
            echo "</tr>";
            
        }

        
      echo "</table>";




