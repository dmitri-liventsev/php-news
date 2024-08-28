# Symfony Sample Project | News

This project was completed as part of a test assignment.

## Technical Stack:

#### Backend
 * PHP 8.2
 * Symfony Framework 7.1
 * Doctrine 3.3

#### Frontend
* React 11
* Redux 9
* Redux Toolkit 2.2
* MUI 5

## Requirements

- Docker
- Docker Compose
- PHP CLI

## Setup Instructions

### 1. Start Docker Compose

Ensure Docker and Docker Compose are installed. Navigate to the project directory and start the Docker containers:

```bash
docker-compose up -d
```
This command will build and start the necessary Docker containers in detached mode.

### 2. Generate Test Data
   Once the containers are up and running, generate the test data using fixtures. Execute the following command:
   
```bash
docker-compose exec php bin/console doctrine:fixtures:load
```
This command will load the test data into the database.

###  3. Create a User
   Create an initial user by running the following command:
```bash
   docker-compose exec php bin/console app:create-user --email="sample@email.com" --password="supersecret"
```
Replace sample@email.com and supersecret with your desired email and password.

###  4. Access the Client Interface
   You can access the client part of the application in your browser at:
http://localhost:8080/

### 5. Access the Admin Interface
   To access the admin interface, open the following URL in your browser:
http://localhost:8080/admin

## Additional Commands
### Stop Docker Containers:
```bash
docker-compose down
```

### View Docker Logs:
```bash
docker-compose logs -f
```

## Troubleshooting
If you encounter any issues, make sure that:

Docker and Docker Compose are properly installed and running.
The Docker containers are correctly built and started.
You are executing commands from the project root directory.
For further assistance, please refer to the Symfony Documentation or contact the project maintainers.

## Contact
For any questions or suggestions, feel free to send a letter to Santa Claus.

Happy coding! 🚀