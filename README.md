# Task Manager

A small PHP task manager web app with a MySQL database and a Docker setup for running the full stack in one command.

## What the app does

- Add a new task
- View the list of saved tasks
- Delete a task when it is finished

## How it works

- `app/index.php` shows the page in the browser
- `app/add_task.php` saves a new task
- `app/delete_task.php` removes a task
- `app/db.php` connects to the database and creates the `tasks` table if needed
- `app/style.css` controls the layout and design

## Project structure

```text
app/
  add_task.php
  db.php
  delete_task.php
  index.php
  style.css

docker/
  apache/
    Dockerfile

docker-compose.yml
```

## Run with Docker

This is the easiest way to run the full app, including MySQL.

### Requirements

- Docker Desktop installed and running
- Docker Desktop WSL integration enabled for your Ubuntu distro

### Start the app

From the project root:

```bash
docker compose up --build
```

Open the app in your browser:

```text
http://localhost:8080
```

### What Docker starts

- `app` container: runs PHP and Apache
- `db` container: runs MySQL 8.0

The app container gets these database settings from `docker-compose.yml`:

- `DB_HOST=db`
- `DB_NAME=tasks`
- `DB_USER=task_user`
- `DB_PASSWORD=task_password`

## Run locally without Docker

If you want to run only the PHP app in WSL, you need PHP and a local MySQL server.

### Start MySQL locally

```bash
sudo service mysql start
```

### Create the database and user

```bash
sudo mysql -u root << 'EOF'
CREATE DATABASE tasks;
CREATE USER 'task_user'@'localhost' IDENTIFIED BY 'task_password';
GRANT ALL PRIVILEGES ON tasks.* TO 'task_user'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### Run the PHP server

Start it from the `app` folder:

```bash
cd app
php -S 0.0.0.0:8000
```

Open:

```text
http://localhost:8000
```

## Database settings

`app/db.php` reads these environment variables when Docker is used:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`

If no environment variables are provided, it defaults to:

- host: `localhost`
- database: `tasks`
- user: `task_user`
- password: `task_password`

## Troubleshooting

- If Docker says it cannot connect to the daemon, start Docker Desktop first.
- If the app shows a database error in local mode, make sure MySQL is running and the `tasks` database exists.
- If `php -S 0.0.0.0:8000` shows a 404, make sure you started it inside the `app` directory.

## Notes

The app creates the `tasks` table automatically the first time it connects to MySQL.