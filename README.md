## Prerequisite
- PHP **8.0** or above
- Extensions: **json**, **swoole**

## Install dependencies
```bash
# development
composer install

# production
composer install  --no-dev
```

## Env
Create `.env` file based on `.env.example` and correct the values

## Migrations (should be run after deploying a new version)
```bash
php artisan migrate
```

## JWT Secret 
```bash
php artisan jwt:secret
```

## Start application
```bash
php artisan octane:start {--host=0.0.0.0} {--port=8000}
```
Optional parameters:
- `host`: default **127.0.0.1**
- `port`: default **8000**

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
