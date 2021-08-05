## Setting Up Project

##### 1. Clone the project in `C:/xampp/htdocs` using the following commands

```bash
    git clone https://github.com/yasirrose/LaravelAuthAndProfile.git laravel_assignment
    cd laravel_assignment
    composer update
```

##### 2. Create the VirtualHost to run the Project (Windows Only)

Add the following code in `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apacheconf
<VirtualHost *:80>
    ServerAdmin webmaster@laravel_assignment.test
    DocumentRoot "C:/xampp/htdocs/laravel_assignment/public"
    ServerName laravel_assignment.test
    ServerAlias www.laravel_assignment.test
</VirtualHost>
```

Add the following in `hosts` file located at `C:\Windows\System32\drivers\etc\`

```text
    127.0.0.1  laravel_assignment.test
```

Note: For Linux or mac Users Create the virtual host based upon the webserver

##### 3. Rename the `.env.example` to `.env` and update the Environment variables for the Database, Email and Passport Keys

| NAME                                      | default             | required                      | type    | description                                                                             |
|-------------------------------------------|---------------------|-------------------------------|---------|------------------------------------|
| DB_HOST                                   | "unspecified"       | :white_check_mark:            | str     | DATABASE HOST                      |
| DB_PORT                                   | False               | :white_check_mark:            | str     | DATABASE PORT                      |
| DB_DATABASE                               | "unspecified"       | :white_check_mark:            | str     | DATABSE NAME                       |
| DB_USERNAME                               | "unspecified"       | :white_check_mark:            | str     | DATABSE USERNAME                   |
| DB_PASSWORD                               | "unspecified"       | :white_check_mark:            | str     | DATABASE PASSWORD                  |
| MAIL_HOST                                 | "unspecified"       | :white_check_mark:            | str     | EMAIL HOST                         |
| MAIL_PORT                                 | "unspecified"       | :white_check_mark:            | str     | EMAIL USERNAME                     |
| MAIL_USERNAME                             | "unspecified"       | :white_check_mark:            | str     | EMAIL USRNAME                      |
| MAIL_PASSWORD                             | "unspecified"       | :white_check_mark:            | str     | EMAIL PASSWORD                     |
| MAIL_ENCRYPTION                           | "unspecified"       | :white_check_mark:            | Boolean | MAIL ENCRYPTION                    |
| MAIL_FROM_ADDRESS                         | "unspecified"       | :white_check_mark:            | str     | MAIL FROM ADDRESS                  |
| MAIL_FROM_NAME                            | "unspecified"       | :white_check_mark:            | str     | MAIL FROM NAME                     |
| PASSPORT_PERSONAL_ACCESS_CLIENT_ID        | "unspecified"       | :white_check_mark:            | str     | PASSPORT CLIENT ID                 |
| PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET    | "unspecified"       | :white_check_mark:            | str     | PASSPORT CLIENT SECRET             |

For Passport client id and client secret run the following commands and follow instructions this will generate the client id and client secret and paste that values in the `.env` file

```bash
php artisan key:generate
php artisan passport:client --password

```
##### 4. Run the migrations and seed the database

```bash
php artisan migrate
php artisan db:seed
```

##### Credentials
For Admin:
UN: admin@admin.com
PW: 123456

For User:
UN:test@gmail.com
PW: 123456

NOTE: Not for testing import the provided insonmia Collection in the application and you will get the workspace and API endpoints ready for testing.

![img.png](img.png)

APIs Testing Note:

Test the Api's as they are listed in the Insonmia collection.
