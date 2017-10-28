# PHP Utilities

General PHP functions you can use in any project. Things like strings, validation, ... .


## Using this package in your project

Add the following to your `composer.json` file

```json
{
    "require": {
        "digitalkreativ/php-utilities": "v0.0.1"
    }
}
```


## Example usage

Checking if a specified email is valid.

```php
use Digitalkreativ\Utilities\EmailUtils;

var_dump( EmailUtils::isEmail( 'email@example.org' );

```

Checking IP address version

```php 
use Digitalkreativ\Utilities\ValidationUtils;

var_dump( ValidationUtils::isIpV4( '10.10.100.1' ) );
var_dump( ValidationUtils::isIpV6( '10.10.100.1' ) );

```

## Components used

See [composer.json](composer.json) for latest and versions.

* [illuminate/support](https://github.com/illuminate/support)
* [nesbot/carbon](https://github.com/briannesbitt/Carbon)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.