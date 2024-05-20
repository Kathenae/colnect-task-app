# HTML Element Counter

You can access the website at [elemizer.worksper.com](https://elemizer.worksper.com). 

### Deployment Details 

This project is deployed on a VPS running the the following:
- NGINX version 1.21.4
- PHP version 8.2.11
- MySQL version 8.0.34
- Composer 2.6.5

### Project Setup

1. Extract project files to the web servers public directory
2. Create a new database and ensure you also have a user with read and write permission to this database.
3. copy `.env.example` to a `.env` file and update the variables to match the credentials for the new database and user you have created
4. run `composer install` to install the project dependencies dependencies. the main dependencies are doctrine for ORM, twig for html templating and pecee/simple-router for easy routing. 
5. run `php bin/doctrine orm:schema-tool:create`. this apply the database schema resolved using the Entity mappings defined in src/models.

### Time tracking

The time taken to complete this project is as follows:

1. Planning: 2 hours.
2. Development: 5 days, average of 5 to 12 hours each day. 
4. Testing: 5 days, average 15 minutes each day.
3. Deployment: 5 hours.

### Browsers used for testing
The application has been tested on the following browsers:

1. Edge Windows
3. Chrome Desktop
2. Edge for Android
4. Chrome for Android
