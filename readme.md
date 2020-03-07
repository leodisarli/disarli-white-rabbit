# WhiteRabbit - RabbitMq on PHP using php-amqplib

WhiteRabbit Class - Simple Library to use RabbitMQ in PHP with PHPAMQPLib PHP Version 5

## Version

1.0.0

## Getting Started

These instructions will get you install the lib and running on your project for development and testing purposes.

### Prerequisites

What things you need to install the software

PHP 5.3

### Installing

Require via composer

```
composer require disarli/white-rabbit
```

Install via composer
```
composer install
```

On your project load the library

```
use DiSarli\WhiteRabbit\WhiteRabbit;
```

Construct with your config data
```
$config = [
    'host' => 'localhost',
    'port' => '5672',
    'user' => 'username',
    'pass' => 'pass',
    'vhost' => '/',
];
$rabbitmq = new WhiteRabbit($config);
```

Use your choosen method
```
$rabbitmq->push('test_queue', $data, false, [], true);
```


## Docs

The list bellow is quick help to primary methods

### Push

Push in the specified queue

```
$this->rabbitmq->push('queue', $content, true, [], false);

@param string $queue - Queue
@param mixed (string/array) $data - Data
@param boolean $permanent - Permanent mode
@param array $params - Parameters
@param bool $output - Show output
@return bool
```

### Pull

Get items from the specified queue

```
$this->rabbitmq->pull('queue', true, []);

@param string $queue - Queue
@param bool $permanent - Permanent mode
@param array $callback - Callback
@return void
```

## Versioning

I use [SemVer](http://semver.org/) for versioning. 

## Authors

* **Leonardo Di Sarli** - *Initial work* - [DiSarli](http://disarli.com.br)

## License

This project is licensed under the GNU GENERAL PUBLIC LICENSE - see the [LICENSE.md](LICENSE.md) file for details

## To do list

* Better design patterns
* Tests
* Better docs
* Error handling
