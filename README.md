# Setup 

### 1. Clone 
```
git clone https://github.com/chaulapatrice/code-infinity-test-1.git
```
```
cd code-infinity-test-1
```

### 2. Run containers

Create .env file 

```
cp .example.env .env
```

Please run the `docker-compose up` for the first time 

```
docker-compose up 
```
You will notice `mongo-express` exited because it couldn't connect to  `mongo`. This is because `mongo` was still initialising. Quit using   `Ctrl + C` and run `docker-compose down`.

```
docker-compose down
```

Run `docker-compose up -d` again, this time mongo-express will run successfully because mongo was initialized the first time. Mongo's data is persisted in a volume called `mongodb_data`

```
docker-compose up -d
```

### 3. Initialize the database 

Run the following command to login into the `web` container.

```
docker-compose exec web /bin/sh
```

Install dependencies

```
composer update
```

Create database 

```
php init_database.php
```

### 4. View in the browser
The application is accessible at http://localhost:8888






