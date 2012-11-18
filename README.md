# Storage Engine for Event based Data

Hoard is currently a prototype for logging event based data. It's currently in early alpha testing, and not suitable for production.

## Install

### Console:

	php install/shell.php
	
### Heroku:
	
	heroku config:add LD_LIBRARY_PATH=/app/php/ext:/app/apache/lib
	heroku addons:add mongolab:starter
	heroku config:set HOARD_MONGO_URI=mongodb://<user>:<password>@<unique>.mongolab.com/<dbname>
	heroku run bash
	$ /app/php/bin/php -c /app/www /app/www/install/heroku.php

## Clients

- PHP - [marcqualie/hoard-php-client](https://github.com/marcqualie/hoard-php-client)
- Tail - [marcqualie/hoard-tail-client](https://github.com/marcqualie/hoard-tail-client)
	
## Support

Official support is currently not available, but if you have any questions or suggections you can contact me over at [My Site](https://www.marcqualie.com/contact/)
