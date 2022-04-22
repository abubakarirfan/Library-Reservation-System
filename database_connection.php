<?php
    if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));

    $password = "";

    if (isset($_ENV['db-password'])) {
        $password = $_ENV['db-password'];
    } elseif (file_exists("./password.txt")) {
        fwrite(STDOUT, PHP_EOL);
        fwrite(STDOUT, '** Warning **' . PHP_EOL);
        fwrite(STDOUT, 'Please note, storing your DB password in a plaintext file should only be done in' . PHP_EOL);
        fwrite(STDOUT, 'development environments. Please consider setting up an environment variable instead.' . PHP_EOL);
        fwrite(STDOUT, PHP_EOL);

        $password = trim(file_get_contents("./password.txt"));
    } else {
        fwrite(STDOUT, PHP_EOL);
        fwrite(STDOUT, '** Warning **' . PHP_EOL);
        fwrite(STDOUT, 'No password has been found for the database. Please consider configuring your user with a password' . PHP_EOL);
        fwrite(STDOUT, 'In the future, this action will not be allowed' . PHP_EOL);
        fwrite(STDOUT, PHP_EOL);
    }

    $connect = null;

    try {
        // Going to consider adding a way to change username as well
        $connect = new PDO("mysql:host=localhost; dbname=lms", "root", $password);
    } catch (PDOException $e) {
        die("An error occurred when connecting to database. Please ensure that a password is set, and that it is correct!");
    }

	session_start();
?>