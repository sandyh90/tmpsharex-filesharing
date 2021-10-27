<h1 align="center">TMPShareX</p>

<h3 align="center">Basic anonymous and registered upload storage for temporary share file self hosted.</h3>

## Server Requirement

- PHP 7.4.8 [Support PHP 8]
- Nginx 1.19.1 Or Apache 2.4.46
- MariaDB 10.4.13

## Login Account (Default)
- Username: admin
- Password: 12345678

## Limitation

-   Upload become error if upload more than 2GB Due problem from library, For now i suggested you limit upload under 3 GB [Still Search Issue].
-   Upload chunk use a lot cpu usage and sometime memory usage too.
-   Merge chunk upload slow and triggering max execution time limit PHP **[For now temporary patched by ini_set]**.

## How to install

- Extract the ZIP file into a safe folder so it doesn't get mixed up.
- Copy & Paste the "basic_temp_filesharing" folder or copy the entire contents of the folder into the htdocs folder or www folder.
- Change the file ".env.example" to ".env" and change the settings to be as in the .env settings section (APP_KEY is done using artisan)
- First please run the following command " composer install " to install the required dependency libraries.
- Second, please run the command " php artisan key:generate " to generate APP_KEY automatically.
- Thirdly, please run the command " php artisan migrate " to migrate create a new database to mysql or mariaDB. (if there is a problem when migrating via the terminal or hosting does not support using the terminal, you can use SQL Dump)
- Fourth, please run the command " php artisan db:seed " to create a default application account (If you use SQL Dump, you don't need to use this command).

## .env Setting File

```
APP_NAME=TMPShareX
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

#--------------------------------------------------------------------
# Setting Apps
#--------------------------------------------------------------------

# Allowing new user to create account on your apps
AUTH_ALLOW_REGISTER_SELF=true

# Storage limit must be filled in MB example:[20 GB = 20480 MB]
STORAGE_LIMIT_ACCOUNTS=20480

# Upload limit must be filled in MB example:[1 GB = 1024 MB] Due problem chunk merge from library limit it under 3GB
UPLOAD_LIMIT_ALLOW=1024

# Provide Upload Dir Custom example: ["upload_storage"] Caution!: Change this before use for upload
UPLOAD_NAME_FOLDER="upload_storage"

#--------------------------------------------------------------------

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basic_temp_filesharing
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=database
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

## Screenshot / Demo

![Screenshot 2021-10-21 at 20-15-23 Home](https://user-images.githubusercontent.com/30236529/138286070-d7713a3c-44aa-4a67-b4be-99cc24f26285.png)
![Screenshot 2021-10-21 at 20-16-38 Upload](https://user-images.githubusercontent.com/30236529/138286167-811ec5c9-c5fd-44f2-a856-8ac3a256591f.png)
![Screenshot 2021-10-21 at 20-17-06 Dashboard](https://user-images.githubusercontent.com/30236529/138286197-de998a39-363f-43e5-8a91-12e7c1f26d5b.png)
![Screenshot 2021-10-21 at 20-17-20 Download Files](https://user-images.githubusercontent.com/30236529/138286234-a1ec7fd5-cdfe-4ac2-8794-30b5d51edc16.png)
![Screenshot 2021-10-21 at 20-17-59 My Files](https://user-images.githubusercontent.com/30236529/138286290-ccefb6c5-c2bc-42a5-97f7-429fc9912570.png)
![Screenshot 2021-10-21 at 20-17-47 User Settings](https://user-images.githubusercontent.com/30236529/138286306-3ccf1349-19b9-4ea4-a2b0-a30e6b5df12c.png)

Ingin mencoba aplikasi web ini silakan kunjungi

[Demo Web](#)

## Change Log

### 10-21-2021
- First init files
