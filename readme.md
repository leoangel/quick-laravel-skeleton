Notice:
This Skeleton Base on Laravel 5.5 + PHP7 ,But You can Update to Laravel 5.6+PHP7.1

Step 1：
`composer update`

Step 2:
`composer dumpautoload`

Step 3:
`php artisan ide-helper:generate`

Step 4:
deploy redis and set redis configuration in `.env`

Step 5:
When More MySQL Database You Have to use, You can Configure multiple Database In `.env` AND `database.php`.
For example ,You can configure like current file.

Step 6:
The skeleton develops implementations for the API Service,So you must be sure Your project is based on API services.

Step 7:
API URL uniform `/api/`  prefix.

