<?php
// Database Connector

function connect()
{

include ('config.php');

$HOST = $db["host"];
$PORT = $db["port"];
$USER = $db["user"];
$DBNAME = $db["name"];
$PASSWORD = $db["password"];

$conn = pg_connect("host=$HOST port=$PORT dbname=$DBNAME user=$USER password=$PASSWORD");
return $conn;
}

function query($query){
$result = pg_query($query) or die('error: ' . pg_last_error());
return $result;
}

function close($connection){
pg_close($connection);
}

function getDBName($connection){
$gdbname = pg_dbname($connection);
return $gdbname;
}

?>