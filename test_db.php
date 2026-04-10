<?php
try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=architect_ai', 'root', 'archpass123');
    echo 'Connected successfully';
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}