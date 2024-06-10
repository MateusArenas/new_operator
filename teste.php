<?php
// Parâmetros de conexão
$host = 'ep-solitary-lab-a4xsr6zv-pooler.us-east-1.aws.neon.tech';
$port = '5432'; // Porta padrão do PostgreSQL
$dbname = 'verceldb';
$user = 'default';
$password = '6Oo2xVuScGfP';

// String de conexão
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";
$conn_string = "host={$host} port=5432 dbname={$dbname} user={$user} password={$password} sslmode=require options='endpoint=ep-solitary-lab-a4xsr6zv'";


// Estabelecer a conexão
$conn = pg_connect($conn_string);

// Verificar se a conexão foi bem-sucedida
if (!$conn) {
    echo "Erro ao conectar ao banco de dados PostgreSQL.";
} else {
    echo "Conexão bem-sucedida!";
    // Aqui você pode executar consultas SQL e outras operações no banco de dados
}

// Fechar a conexão
pg_close($conn);
?>