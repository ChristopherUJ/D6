
# D6 Assessment

## Prerequisites

- Docker
- Docker Compose

## Getting Started

### 1. Clone the Repository

git clone git@github.com:ChristopherUJ/D6.git

### 2. Start the Docker Containers

Run the following command to build and start all services:

This will start three containers: docker-compose up -d

- **web** (Nginx) - Web server on port 80
- **php** (PHP-FPM 8.2) - PHP application server
- **mysql** (MySQL 8.0) - Database server on port 3306

### 3. Verify the Containers are Running

You should see three containers running: `d6_web`, `d6_php`, and `d6_mysql`.

### 4. Initialize the Database

- Connect to the database and run `create_schema.sql` in the `sql/` directory

```bash
docker-compose exec mysql mysql -u user -ppassword d6_db < sql/create_schema.sql

- **Host:** mysql (or `localhost` from your machine)
- **Database:** d6_db
- **Username:** user
- **Password:** password
- **Port:** 3306
```

### 5. Access the Application
http://localhost



