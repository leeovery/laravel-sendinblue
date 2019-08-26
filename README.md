# Laravel Mail Driver For SendInBlue

[![Latest Version on Packagist](https://img.shields.io/packagist/v/leeovery/laravel-sendinblue.svg?style=flat-square)](https://packagist.org/packages/leeovery/laravel-sendinblue)
[![Build Status](https://img.shields.io/travis/leeovery/laravel-sendinblue/master.svg?style=flat-square)](https://travis-ci.org/leeovery/laravel-sendinblue)
[![Quality Score](https://img.shields.io/scrutinizer/g/leeovery/laravel-sendinblue.svg?style=flat-square)](https://scrutinizer-ci.com/g/leeovery/laravel-sendinblue)
[![Total Downloads](https://img.shields.io/packagist/dt/leeovery/laravel-sendinblue.svg?style=flat-square)](https://packagist.org/packages/leeovery/laravel-sendinblue)

Mail driver for Laravel to send emails via the SendInBlue v3 API.

## Installation

You can install the package via composer:

```bash
composer require leeovery/laravel-sendinblue
```

## Usage

To use this driver simply set the following config in your `/config/services.php` file as follows:

``` php

'sendinblue' => [
    'secret'     => env('SENDINBLUE_SECRET'),
    
    // optionally provide this but will default to whats specified here...
    'options' => [
        'endpoint' => 'https://api.sendinblue.com/v3',
    ],
],

```

Add the following to your env with your SendInBlue v3 API key:

```
SENDINBLUE_SECRET={your SendInBlue v3 API key goes here}
MAIL_DRIVER=sendinblue
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email me@leeovery.com instead of using the issue tracker.

## Credits

- [Lee Overy](https://github.com/leeovery)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
