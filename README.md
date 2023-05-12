# Reporting for iGOT

## Setup on Windows
1. Install XAMPP 8.2.4 (PHP 8.2.4)
1. Install PostgreSQL 15.2 with pgAdmin 4
1. Create database named "igot_reporting" and restore the database backup
1. Execute query `CREATE EXTENSION pg_trgm;` 
1. Edit file app/Config/Database.php to update the database name
1. Install Composer
1. In php.ini uncomment "extension=intl", "extension=gd", "extension=pgsql", "extension=pdo_pgsql", "extension=zip" and restart XAMPP
1. Copy this source code (v1.3 or any other branch) inside htdocs dirctory of XAMPP
1. Run "composer install"
1. Run "php spark serve"
1. Open URL http://localhost:8080/login in browser

## Deployment on server
1. In `.env` file, change the value of `CI_ENVIRONMENT` to `production`
1. Update database details in `app/Config/Database.php` 
1. Update the values `$GLOBALS['IGOT_URL']` and `$GLOBALS['API_KEY']` in `app/Config/Constants.php`
1. Update the values of `BASE_URL` and `ASSETS_URL` in `app/Config/Constants.php`
1. Execute query `CREATE EXTENSION pg_trgm;` in Postgres
1. In `Views/keyCloakLogin.php`, edit the following lines:
    1. `const keycloak = Keycloak('/assets/keycloak.json');` -> `const keycloak = Keycloak('/reporting/public/assets/keycloak.json');`
    1. `window.location.replace("/login");` -> `window.location.replace("/reporting/login");`
    1. `window.location.replace("/unauthorized");` -> `window.location.replace("/reporting/unauthorized");`

