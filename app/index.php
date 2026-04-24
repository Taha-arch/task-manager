<?php
session_start();
require_once __DIR__ . '/db.php';

$tasks = [];
$flashMessage = $_SESSION['flash_message'] ?? null;
$flashType = $_SESSION['flash_type'] ?? 'success';

try {
	$statement = $pdo->query('SELECT id, task, created_at FROM tasks ORDER BY created_at DESC, id DESC');
	$tasks = $statement->fetchAll();
} catch (Throwable $exception) {
	$flashMessage = 'Could not load tasks.';
	$flashType = 'error';
}

unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Task Manager</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<main class="app-shell">
		<section class="hero">
			<p class="eyebrow">Task Manager</p>
			<h1>Keep the day organized.</h1>
			<p class="hero-copy">Add tasks quickly, clear them when finished, and keep the list focused on what matters next.</p>
		</section>

		<?php if ($flashMessage): ?>
			<div class="alert alert-<?php echo htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8'); ?>">
				<?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?>
			</div>
		<?php endif; ?>

		<section class="panel add-panel">
			<h2>Add a task</h2>
			<form class="task-form" action="add_task.php" method="post">
				<label for="task">Task</label>
				<div class="task-form-row">
					<input type="text" id="task" name="task" placeholder="What needs to get done?" maxlength="255" required>
					<button type="submit">Add task</button>
				</div>
			</form>
		</section>

		<section class="panel list-panel">
			<div class="panel-header">
				<h2>Your tasks</h2>
				<span class="task-count"><?php echo count($tasks); ?> total</span>
			</div>

			<?php if (empty($tasks)): ?>
				<div class="empty-state">
					<p>No tasks yet. Add one above to get started.</p>
				</div>
			<?php else: ?>
				<ul class="task-list">
					<?php foreach ($tasks as $task): ?>
						<li class="task-item">
							<div class="task-meta">
								<span class="task-title"><?php echo htmlspecialchars($task['task'], ENT_QUOTES, 'UTF-8'); ?></span>
								<span class="task-date"><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($task['created_at'])), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
							<form action="delete_task.php" method="post" onsubmit="return confirm('Delete this task?');">
								<input type="hidden" name="id" value="<?php echo (int) $task['id']; ?>">
								<button type="submit" class="delete-button">Delete</button>
							</form>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
	</main>
</body>
</html>

