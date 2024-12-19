
# Validator Guard

<br />

[![Latest Version on Packagist](https://img.shields.io/badge/packagist-v1.0-blue)](https://packagist.org/packages/moe-mizrak/validator-guard)
<br />

ValidatorGuard enables attribute-driven validation to control Laravel method behavior.

## Table of Contents

- [🤖 Requirements](#-requirements)
- [🏁 Get Started](#-get-started)
- [🧩 Configuration](#-configuration)
- [🎨 Usage](#-usage)
    - [Using Helper](#using-helper)
    - [Using Service Container Bindings](#using-service-container-bindings)
    - [Attributes](#attributes)
        - [IntervalGuard](#intervalguard)
        - [DateGuard](#dateguard)
        - [AllowedValuesGuard](#allowedvaluesguard)
        - [CallbackGuard](#callbackguard)
        - [ArrayKeysExistGuard](#arraykeysexistguard)
    - [Create Your Own Attribute](#create-your-own-attribute)
- [💫 Contributing](#-contributing)
- [📜 License](#-license)

## 🤖 Requirements
- **PHP 8.2** or **higher**
- [<u>Validator Guard Core</u>](https://github.com/moe-mizrak/validator-guard-core)

## 🏁 Get Started
You can **install** the package via composer:
```bash
composer require moe-mizrak/validator-guard
```

You can **publish** the **config file** with:
```bash
php artisan vendor:publish --tag=validator-guard
```

<details>
<summary>This is the contents of the published config file:</summary>

```php
return [
    /**
     * Here add the attributes that are used for Validation Guard
     */
    'attributes' => [
        // Attributes that will be handled before method execution
        'before' => [
            AllowedValuesGuard::class,
            DateGuard::class,
            CallbackGuard::class,
        ],
        // Attributes that will be handled after method execution
        'after' => [
            IntervalGuard::class,
            ArrayKeysExistGuard::class,
        ]
    ],

    /**
     * Here we add all classes that we use attribute validation in order to bind them to ValidatorGuardCore in Service Provider.
     * Basically whenever these classes are resolved by container, we initiate ValidatorGuardCore to mimic them as a wrapper and handle validation.
     */
    'class_list' => [
    ],

    /**
     * Enable throwing exceptions in case of validation failure
     */
    'throw_exceptions' => env('VALIDATOR_GUARD_THROW_EXCEPTIONS', true),

    /**
     * Enable logging exceptions in case of validation failure
     */
    'log_exceptions' => env('VALIDATOR_GUARD_LOG_EXCEPTIONS', false),

    /**
     * Set an option for default channel for logging so that it can be configured when needed.
     */
    'log_channel' => env('VALIDATOR_GUARD_LOG_CHANNEL', 'stack'),
]
```
</details>

## 🧩 Configuration
After publishing the package configuration file, you'll need to add the following environment variables to your **.env** file:

```env
VALIDATOR_GUARD_THROW_EXCEPTIONS=
VALIDATOR_GUARD_LOG_EXCEPTIONS=
VALIDATOR_GUARD_LOG_CHANNEL=
```

- VALIDATOR_GUARD_THROW_EXCEPTIONS: Enable/Disable whether the package throws exceptions. Set true to enable and false to disable  (🚩default: true)
- VALIDATOR_GUARD_LOG_EXCEPTIONS: Enable/Disable whether the package logs exceptions. Set true to enable and false to disable  (🚩default: false)
- VALIDATOR_GUARD_LOG_CHANNEL: Set the default channel for logging exceptions (only applicable if VALIDATOR_GUARD_LOG_EXCEPTIONS is enabled). (🚩default: stack)

## 🎨 Usage

### Using Helper

### Using Service Container Bindings

### Attributes
More attributes will be added in the future. You can also create your custom attributes by implementing the `ValidationAttributeInterface` interface. How to create custom attributes is explained in the [Create Your Own Attribute](#create-your-own-attribute) section.
We will cover the following attributes in this section:
* [IntervalGuard](#intervalguard)
* [DateGuard](#dateguard)
* [AllowedValuesGuard](#allowedvaluesguard)
* [CallbackGuard](#callbackguard)
* [ArrayKeysExistGuard](#arraykeysexistguard)

#### IntervalGuard

#### DateGuard

#### AllowedValuesGuard

#### CallbackGuard

#### ArrayKeysExistGuard

### Create Your Own Attribute

## 💫 Contributing

> **Your contributions are welcome!** f you'd like to improve this package, simply create a pull request with your changes. Your efforts help enhance its functionality and documentation. 

> If you find this package useful, please consider ⭐ it to show your support!

## 📜 License
Validator Guard is an open-sourced software licensed under the **[MIT license](LICENSE)**.
