Hoard is an Event Based Storage Engine for processing large amounts of real-time data, it can act as a restful endpoint to collect various metrics.

Because of its schema-less nature, Hoard can collect all sorts of data in any manner, including but not limited to, server/system statistics like load, memory consumption and process metrics, or even scores for games, client side events and anything else you mght want to aggregate and filter.

Hoard is very much in development at the moment, but we're working hard to stabilise it. I welcome feedback, contributions and suggestions, but I do not accept any liability for any data loss due to the usage of this application.

## Dependencies
Phalcon
Mongo DB
Hoard utils extension, instructions [here](https://github.com/marcqualie/hoard-utils)

## Installing and running Hoard
### Installation
```bash
git clone https://github.com/marcqualie/hoard.git
cd hoard
composer update
```

### Running 
Because hoard is a php application, you can run it in any way you see fit. Be it the in built PHP server:

PHP server
```php
php -S 127.0.0.1:8000 -t public
```
Or we've provided some basic configuration files in the config folder for nginx.

### Docker
If you run docker, you can get an isolated instance of hoard running including all dependencies via our dockerfile.

```bash
cd path/to/hoard
docker build -t my/hoard:1.0 .
```
You can run this as a daemonised instance like so.

```bash
docker run -d my/hoard:1.0
```
You'll find hoard up and running by default on port 8000.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/marcqualie/hoard/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
