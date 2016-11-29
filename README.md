# Laravel Multi Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-multi-mail)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/ElfSundae/laravel-multi-mail/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/laravel-multi-mail)
[![StyleCI](https://styleci.io/repos/74790931/shield)](https://styleci.io/repos/74790931)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/e3c829ad-2ea3-4f44-a3b2-de5fd60770eb.svg?style=flat-square)](https://insight.sensiolabs.com/projects/e3c829ad-2ea3-4f44-a3b2-de5fd60770eb)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-multi-mail)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/laravel-multi-mail/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-multi-mail/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-multi-mail)

The missing multi-mail implementation for [Laravel][] 5.3.

# Contents

<!-- MarkdownTOC -->

- [Installation](#installation)
- [Documentation](#documentation)
- [Testing](#testing)
- [License](#license)

<!-- /MarkdownTOC -->

## Installation

1. Install this package using the [Composer][] manager:

    ```sh
    $ composer require elfsundae/laravel-multi-mail
    ```

2. **Replace** `Illuminate\Mail\MailServiceProvider::class` with `ElfSundae\Multimail\MailServiceProvider::class` in the `config/app.php` file.

## Documentation


## Testing

```sh
$ composer test
```

## License

The [MIT License](LICENSE).

[Laravel]: https://laravel.com
[Composer]: https://getcomposer.org
