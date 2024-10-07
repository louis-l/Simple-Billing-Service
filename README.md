## Set Up Project

1. Pull this repo
2. Create new `.env` file by duplicate the `.env.example`
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
