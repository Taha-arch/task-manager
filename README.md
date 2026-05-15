# Task Manager

A small PHP task manager web app with a MySQL database, containerized with Docker and deployable via Kubernetes. A Jenkins CI pipeline automates the build and deployment process.

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
Jenkinsfile
deployment.yaml
service.yaml
mysql-deployment.yaml
mysql-secret.yaml
```

---

## Run with Docker Compose

This is the easiest way to run the full app locally, including MySQL.

### Requirements

- Docker Desktop installed and running
- Docker Desktop WSL integration enabled for your Ubuntu distro

### Start the app

From the project root:

```bash
docker compose up --build
```

Open the app in your browser:

```
http://localhost:8080
```

### What Docker Compose starts

- `app` container: runs PHP 8.2 and Apache
- `db` container: runs MySQL 8.0

The app container gets these database settings from `docker-compose.yml`:

- `DB_HOST=db`
- `DB_NAME=tasks`
- `DB_USER=task_user`
- `DB_PASSWORD=task_password`

### Stop and remove containers

```bash
docker compose down
```

To also delete the MySQL data volume:

```bash
docker compose down -v
```

---

## CI/CD with Jenkins

Jenkins automates the build and deployment of the Docker image.

### Requirements

- Docker installed and running
- Jenkins running in Docker (see below)

### Launch Jenkins

```bash
docker run -d \
  --name jenkins \
  -p 8082:8080 \
  -p 50000:50000 \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v jenkins_home:/var/jenkins_home \
  jenkins/jenkins:lts
```

Then install Docker CLI inside Jenkins:

```bash
docker exec -u root jenkins bash -c "apt-get update && apt-get install -y docker.io"
docker exec -u root jenkins chmod 666 /var/run/docker.sock
```

Access Jenkins at:

```
http://localhost:8082
```

Get the initial admin password:

```bash
docker exec jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```

### Pipeline

The `Jenkinsfile` at the root of the project defines three stages:

- **Build** — builds the Docker image `php-task-app`
- **Test** — runs a basic smoke test
- **Run** — starts the app container on port 8090

To use it, create a Pipeline job in Jenkins pointing to this repository with:

- Branch: `*/main`
- Script Path: `Jenkinsfile`

Once the pipeline runs successfully, the app is available at:

```
http://localhost:8090
```

---

## Deploy with Kubernetes (Minikube)

### Requirements

- Minikube installed
- kubectl installed

### Start Minikube

```bash
minikube start --driver=docker
```

### Build the image inside Minikube

```bash
eval $(minikube docker-env)
docker build -t php-task-app -f docker/apache/Dockerfile .
```

### Deploy the app and database

```bash
kubectl apply -f mysql-secret.yaml
kubectl apply -f mysql-deployment.yaml
kubectl apply -f deployment.yaml
kubectl apply -f service.yaml
```

### Verify everything is running

```bash
kubectl get pods
```

You should see three pods running:

```
php-app-xxxx   1/1   Running
php-app-xxxx   1/1   Running
mysql-xxxx     1/1   Running
```

### Open the app

```bash
minikube service php-service
```

Minikube will open the app automatically in your browser.

### Kubernetes environment variables

The `deployment.yaml` passes these variables to the PHP containers:

- `DB_HOST=db`
- `DB_NAME=tasks`
- `DB_USER=task_user`
- `DB_PASSWORD=task_password`

### Test auto-healing

Kubernetes automatically restarts pods that go down. To test this:

```bash
kubectl delete pod <pod-name>
kubectl get pods
```

A new pod will be created immediately to replace the deleted one.

### Tear down

```bash
kubectl delete -f service.yaml
kubectl delete -f deployment.yaml
kubectl delete -f mysql-deployment.yaml
kubectl delete -f mysql-secret.yaml
minikube stop
```

---

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

```
http://localhost:8000
```

---

## Database settings

`app/db.php` reads these environment variables when Docker or Kubernetes is used:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`

If no environment variables are provided, it defaults to:

- host: `localhost`
- database: `tasks`
- user: `task_user`
- password: `task_password`

The app creates the `tasks` table automatically the first time it connects to MySQL.

---

## Port reference

| Port | Service |
|------|---------|
| 8080 | App via Docker Compose |
| 8082 | Jenkins dashboard |
| 8090 | App deployed by Jenkins pipeline |
| Minikube URL | App deployed on Kubernetes |

---

## Troubleshooting

- If Docker says it cannot connect to the daemon, start Docker Desktop first.
- If the app shows a database error in local mode, make sure MySQL is running and the `tasks` database exists.
- If `php -S 0.0.0.0:8000` shows a 404, make sure you started it inside the `app` directory.
- If Jenkins shows `docker: not found`, make sure you mounted the Docker socket and installed Docker CLI inside the Jenkins container.
- If Kubernetes pods show `ErrImageNeverPull`, make sure you built the image inside Minikube's Docker environment using `eval $(minikube docker-env)` first.
- If the app shows a database error on Kubernetes, check that `mysql-deployment.yaml` was applied and the MySQL pod is running.

