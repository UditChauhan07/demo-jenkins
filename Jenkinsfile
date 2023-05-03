pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                git 'https://github.com/UditChauhan07/demo-jenkins.git'
                sh 'composer install'
                sh 'cp .env'
                sh 'php artisan key:generate'
            }
        }
        stage('Test') {
            steps {
                sh './vendor/bin/phpunit'
            }
        }
    }
}