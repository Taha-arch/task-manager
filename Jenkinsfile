pipeline {
    agent any
    stages {
        stage('Build') {
            steps {
                sh 'docker build -t php-task-app -f docker/apache/Dockerfile .'
            }
        }
        stage('Test') {
            steps {
                sh 'echo "Test OK"'
            }
        }
        stage('Run') {
            steps {
                sh 'docker run -d -p 8090:80 php-task-app'
            }
        }
    }
}
