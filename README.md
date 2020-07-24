## Instruction

1. Go to your project directory and run. 
```composer update```
```npm install```
2. Rename .env.example to .env and setup your database.
3. Generate app key. 
```php artisan key:generate```
4. Generate jwt key. 
```php artisan jwt:secret```
5. Run database migration. 
```php artisan migrate —seed```
6. Clear and cache config. 
```php artisan config:cache ```
7. Open your .env file and add this. MIX_SENTRY_DSN_PUBLIC=http://yourdomain.com

Default user
email: sample@yahoo.com
pw: password

## Functions
Inventory functions:
1. Basic transactions like in/restock, take out, transfer
2. Dashboard with information like no. of pendings and current inventory status.
3. Able to support multiple branches/warehouses.
4. Able to see product transaction history.
5. Able to adjust inventory.
