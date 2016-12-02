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

The Laravel mail service provides a number of elegant ways to send e-mails, such as `Mailer` (the `Mail` facade), `Mailable`, `MailableMailer`, and the new [`Mail Notification`][Mail Notification]. Before getting started using this package, make sure you have read the [official mail documentation][]. This package will not change the way you are already familiar with sending e-mails, but help you customize the Laravel mail service, such as managing multi mail drivers at runtime, handling messages that are ultimately sent.

<!-- MarkdownTOC -->

- [Installation](#installation)
- [Architecture](#architecture)
- [Usage Examples](#usage-examples)
    - [Custom Mail Drivers](#custom-mail-drivers)
    - [Changing The Default Driver](#changing-the-default-driver)
    - [Processing The Final Messages](#processing-the-final-messages)
    - [Handling The Ultimate Driver](#handling-the-ultimate-driver)
    - [Resetting Swift Mailers](#resetting-swift-mailers)
- [Testing](#testing)
- [License](#license)

<!-- /MarkdownTOC -->

## Installation

1. Install this package using the [Composer][] manager:

    ```sh
    $ composer require elfsundae/laravel-multi-mail
    ```

2. **Replace** `Illuminate\Mail\MailServiceProvider::class` with `ElfSundae\Multimail\MailServiceProvider::class` in the `config/app.php` file.

## Architecture

- `ElfSundae\Multimail\Mailer` _(extends `Illuminate\Mail\Mailer`)_

    The `Mailer` class is the facade and the maincenter of the Laravel mail system, all sending tasks will be handled by this class. You may access it using the `Mail` facade or `app('mailer')` helper function, as well as the `Mailer` type-hint or dependency injection.

- `ElfSundae\Multimail\SwiftMailerManager`

    The `SwiftMailerManager` singleton manages all Swift Mailer instances and their corresponding Swift Transport instances for the `Mailer`, it creates, caches, resets or destroys them. Each Swift Mailer instance is identified by the driver name of its transporter, such as `smtp`, `mailgun`, etc. You may access the manager via `Mail::getSwiftMailerManager()`, `app('swift.manager')`, `SwiftMailerManager` type-hint or dependency injection.

- `ElfSundae\Multimail\MessageHelper`

    It provides several helper methods for operating the mail messages, such as getting domains of the email addresses for the message recipients.

## Usage Examples

Below are several examples of usage. Remember, you can do any customization as you want.

### Custom Mail Drivers

Laravel ships with a handful of mail drivers, but you may want to write your own drivers to send emails via other mail services. Laravel makes it simple, by using the `extend` method of the `TransportManager` singleton, you can register a custom driver creator.

```php
<?php

namespace App\Providers;

use Illuminate\Mail\TransportManager;
use Illuminate\Support\ServiceProvider;
use App\Support\Mail\FooTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving(function (TransportManager $manager) {
            $manager->extend('foo', function ($app) {
                $config = $app['config']['services.foo'];

                return new FooTransport($config['key'], $config['secret']);
            });
        });
    }
}
```

### Changing The Default Driver

Instead of using the mail driver that specified in the `config/mail.php` file, you may change the default driver at runtime via the `mailDriver` method.

```php
Mail::mailDriver('mailgun')->to($user)->send(new OrderShipped($order));
```

:bulb: **Note:** Changing the mail driver by using `mailDriver` will not affect the driver of a queued sending job, it is only effectual during the current app lifetime.

### Processing The Final Messages

This package makes it possible to process every final mail message just before sending the mail. To do so, register a global message handler via the `registerSendingMessageHandler` method.

```php
<?php

namespace App\Providers;

use ElfSundae\Multimail\Mailer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving(function (Mailer $mailer) {
            $mailer->registerSendingMessageHandler(function ($message) {
                $message->bcc('syslog@example.com');
            });
        });
    }
}
```

The first parameter passed to the handler is the mail message typed of `Illuminate\Mail\Message`, and you are free to type-hint additional dependencies.

```php
$mailer->registerSendingMessageHandler(
    function (CacheRepository $cache, SwiftMailerManager $swift, $message, $mailer) {
        $cache->increment('sending-mails-'.$swift->getDefaultDriver());
    }
);
```

In addition to `Closure`, the handler can also be registered with a class name:

```php
$mailer->registerSendingMessageHandler('App\Mail\Handler\SendingMessage');
```

Before sending mail messages, the `sendingMail` method of this class will be called.

Of course you can specify the method name:

```php
$mailer->registerSendingMessageHandler('App\Mail\Handler\SendingMessage@sendingMailHandler');
```

### Handling The Ultimate Driver

__TODO__

### Resetting Swift Mailers

_TODO_

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
