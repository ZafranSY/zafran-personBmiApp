<?php
function getPDO(){
    $host ='localhost';
    $port ='3008';
    $db="bmi-app";
    $user ='root';
    $password="adj2425";

    $dsn ="mysql:host=$host;port:$port; dbname:$db;";
    try{
        return new PDO($dsn,$user,$password ,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die('DB Connection failed: ' . $e->getMessage());
    }
}
?>