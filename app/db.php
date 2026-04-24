<?php

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'tasks';
$user = getenv('DB_USER') ?: 'task_user';
$pass = getenv('DB_PASSWORD') ?: 'task_password';

try {
	$pdo = new PDO(
		"mysql:host={$host};dbname={$dbname};charset=utf8mb4",
		$user,
		$pass,
		[
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]
	);

	$pdo->exec(
		'CREATE TABLE IF NOT EXISTS tasks (
			id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			task VARCHAR(255) NOT NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
	);
} catch (Throwable $exception) {
	die('Database initialization failed: ' . $exception->getMessage());
}