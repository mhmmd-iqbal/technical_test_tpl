# Konco Studio

## Requirement
- PHP 8.3
- MySQL
- redis

## Installation
- set .env file
- install package
```
composer install
```
- run migration
```
php artisan migrate
php artisan key:generate
```
- create link folder to storage
```
php artisan storage:link
```
- create passport key
```
php artisan passport:client --password
```
- run project
```
php artisan serve
```
### TO OPEN SWAGGER DOCUMENTATION
```
{baseurl}/api/documentation
```

### TO RUN TEST
```
php artisan test
```
