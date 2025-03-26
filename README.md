**Crawler description:**

* Build Project

For starting project with docker run command `docker compose up -d --build` in root dir.
<br>
<br>
After build go to php container with command `docker compose exec php /bin/bash`
<br>
<br>
Then run `composer install`

* Start crawling

Now project should work.On index page should be a table with articles (it'll be blank, bcs db is empty).
<br>
Run command from php container `php artisan migrate` . 
<br>

Command for start crawling `php artisan app:update-articles`
<br>

After starting this command crawler will start working.
In console You will see success or fail result.
In case of success on index page will be table with parsed articles.

