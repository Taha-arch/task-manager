<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php');
	exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
	$_SESSION['flash_type'] = 'error';
	$_SESSION['flash_message'] = 'Please choose a valid task to delete.';
	header('Location: index.php');
	exit;
}


try {
	$statement = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
	$statement->execute(['id' => $id]);

	if ($statement->rowCount() > 0) {
	$_SESSION['flash_type'] = 'success';
	$_SESSION['flash_message'] = 'Task deleted successfully.';
	} else {
	$_SESSION['flash_type'] = 'error';
	$_SESSION['flash_message'] = 'Task could not be deleted.';
}
} catch (Throwable $exception) {
	$_SESSION['flash_type'] = 'error';
	$_SESSION['flash_message'] = 'Could not delete the task.';
}

header('Location: index.php');
exit;

