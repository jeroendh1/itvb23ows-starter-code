pipeline {
    agent { label '!windows' }
    stages {
         stage('Test') {
            steps {
                sh 'echo "Started...!" '
            }
        }
        stage('SonarQube') {
            steps {
                script { scannerHome = tool 'SonarQube Scanner' }
                withSonarQubeEnv('SonarQube Scanner') {
                bat "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=ontwikkelstraten"
            }
         }
        }
    }
}