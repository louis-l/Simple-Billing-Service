## Set Up Project

1. Pull this repo
2. Create a `.env` file in the root of this project and copy the content from below:
3. Open terminal and run:
   ```shell
   docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/var/www/html" \
      -w /var/www/html \
      laravelsail/php83-composer:latest \
      composer install --ignore-platform-reqs
   ```
4. Run `./vendor/bin/sail artisan migrate`
5. Run `./vendor/bin/sail db:seed`

Sample env file:

```dotenv
APP_NAME="Simple Billing Service"
APP_ENV=local
APP_KEY=base64:/ZqDz30caPlTso3XKBnwk/+w1wvvKBvJY+yONxBHfL0=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

---

## Run Tests

1. Create a `testing.sqlite` file in `/database` folder
2. Then run `./vendor/bin/sail artisan test`

---

## Coding Principles

- **SOLID**: applied in `BillingManager` service. That service is responsible for handling the subscription billing. However, due to the simplicity of this task, it is not necessary to create subclasses (or interfaces). In real world scenario, we could use "driver pattern" to implement different payment gateways to handle the payments (e.g. Stripe, Paypal...)
- **DRY**: I tried not to have duplicate code logic. Even through, there are some minor snippets that are repeated somewhere, I would say that is WET. Again, given the simple task like this, it would be overkill to implement too many abstract layers
- **KISS**: applied in `RunApp` command where early-terminate is used to avoid too many nested code level. Also I believe the code is quite lean, easy to read and maintain. 

While additional principles could be implemented, they are beyond the scope of this task, so I will omit them for now.

---

## Assumptions

1. each user can only have 1 active subscription at a time
2. subscription period can only be either "monthly" or "yearly"
3. simple timezone (using UTC)
