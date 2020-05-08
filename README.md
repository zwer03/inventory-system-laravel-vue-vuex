## Instruction

1. composer update
2. Generate app key. php artisan key:generate
3. Generate jwt key. php artisan jwt:secret
4. Run database migration. php artisan migrate —seed
5. Clear and cache config. php artisan config:cache 
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
