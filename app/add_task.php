<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php');
	exit;
}

$task = trim($_POST['task'] ?? '');

if ($task === '') {
	$_SESSION['flash_type'] = 'error';
	$_SESSION['flash_message'] = 'Task text cannot be empty.';
	header('Location: index.php');
	exit;
}


try {
	$statement = $pdo->prepare('INSERT INTO tasks (task) VALUES (:task)');
	$statement->execute(['task' => $task]);
	$_SESSION['flash_type'] = 'success';
	$_SESSION['flash_message'] = 'Task added successfully.';
	} catch (Throwable $exception) {
	$_SESSION['flash_type'] = 'error';
	$_SESSION['flash_message'] = 'Could not add the task.';
}

header('Location: index.php');
exit;

