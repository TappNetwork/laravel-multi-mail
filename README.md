# Laravel Multi Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-multi-mail)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/ElfSundae/laravel-multi-mail/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/laravel-multi-mail)
[![StyleCI](https://styleci.io/repos/74790931/shield)](https://styleci.io/repos/74790931)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/e3c829ad-2ea3-4f44-a3b2-de5fd60770eb.svg?style=flat-square)](https://insight.sensiolabs.com/projects/e3c829ad-2ea3-4f44-a3b2-de5fd60770eb)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-multi-mail)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/laravel-multi-mail/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-multi-mail/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/ElfSundae/laravel-multi-mail.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-multi-mail)

This package provides a flexible way to assist you in extending the [Laravel][] mail service, it is the missing multi-mail implementation for Laravel 5.3.

The Laravel mail service provides so many elegant ways to send emails, such as `Mailer` (the `Mail` facade), `Mailable`, `MailableMailer`, and the new [`Mail Notification`][Mail Notification]. Before getting started using this package, make sure you have read the [official mail documentation][]. This package will not change the way you are already familiar with sending emails, but help you customize the Laravel mail service, such as managing multi mail drivers at runtime, handling messages that are ultimately sent.

## Installation

1. Install this package using the [Composer][] manager:

    ```sh
    $ composer require elfsundae/laravel-multi-mail
    ```

2. **Replace** `Illuminate\Mail\MailServiceProvider::class` with `ElfSundae\Multimail\MailServiceProvider::class` in the `config/app.php` file.

## Architecture

- `ElfSundae\Multimail\Mailer` _extends `Illuminate\Mail\Mailer`_
  The `Mailer` class is the facade and the maincenter of the Laravel mail system.

## Testing

```sh
$ composer test
```

## License

The [MIT License](LICENSE).

[Laravel]: https://laravel.com
[Composer]: https://getcomposer.org
[Mail Notification]: https://laravel.com/docs/5.3/notifications#mail-notifications
[official mail documentation]: https://laravel.com/docs/mail
