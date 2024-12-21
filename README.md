
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
    - [Using valguard Helper](#using-valguard-helper)
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

You can **publish** the **validator-guard** config file with:
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
     * Here add all classes that you use attribute validation in order to bind them to ValidatorGuardCore in Service Provider.
     * Basically whenever these classes are resolved by container, we initiate ValidatorGuardCore to mimic them as a wrapper and handle validation.
     * ! Note: You do NOT need to add classes that you use valguard helper method (Check the Usage section for more details).
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
And the details of **validator-guard** config file options as follows:
- **attributes**: Here add the attributes that are used for Validation Guard. You can add the attributes that will be handled before method execution to the **before** array, and the attributes that will be handled after method execution to the **after** array.
Some attributes are already added to the config file. You can remove or add new attributes as needed. You can check the details of the attributes in the [Attributes](#attributes) section.
  - **before**: Attributes processed before method execution, allowing validation to be handled upfront, which avoids unnecessary method calls and improves performance.
  - **after**: Attributes processed after method execution, enabling validations that depend on method results or cases where method execution is needed (e.g., logging, database operations) even if validation fails.
- **class_list**: Here add all classes that you use attribute validation in order to bind them to ValidatorGuardCore in the service provider.
Whenever these classes are resolved by the container, the package will initiate the ValidatorGuardCore to mimic the classes as a wrapper and handle validation.
Check the [Using Service Container Bindings](#using-service-container-bindings) section for more details.
- **throw_exceptions**: Enable/Disable throwing exceptions in case of validation failure. (🚩default: true)
- **log_exceptions**: Enable/Disable logging exceptions in case of validation failure. (🚩default: false)
- **log_channel**: Set an option for the default channel for logging so that it can be configured when needed (only applicable if VALIDATOR_GUARD_LOG_EXCEPTIONS is enabled). (🚩default: stack)

You can also set the **throw_exceptions**, **log_exceptions**, and **log_channel** options in the **.env** file as follows:

```env
VALIDATOR_GUARD_THROW_EXCEPTIONS=
VALIDATOR_GUARD_LOG_EXCEPTIONS=
VALIDATOR_GUARD_LOG_CHANNEL=
```

## 🎨 Usage
There are two ways to use Validator Guard, either by using the **valguard helper** or **service container bindings**.

> [!IMPORTANT]
> `Service container bindings` is not recommended for classes that cannot be resolved by the container, such as facades or helpers,
> or for classes requiring parameters like runtime-specific data.
> It is also unsuitable for objects that are short-lived, require complex setup, and so on.

### Using valguard Helper
Helper method **valguard** offers a simple way to use Validator Guard for classes that cannot be resolved by the container or require runtime-specific data.
For example, if you have a class named **UserService**, and method named **getTransactionAmount** that you want to use for attribute validation:

```php
// class UserService
#[IntervalGuard(lowerBound: 100, operator: '<=', upperBound: 10000)] // Transaction amount (method result) must be between 100 and 10,000
public function getTransactionAmount(int $transactionId): float
{
    // Logic of transaction amount calculation
}
```

You can use the **valguard** helper as follows:
```php
$userService = new UserService(); // It does NOT have to be resolved by the container

// Call the method by wrapping user service with valguard helper
$amount = valguard($userService)->getTransactionAmount(1344); 
/*
 * If the transaction amount is not between 100 and 10,000, the exception will be thrown/logged (based on throwing or logging enabled in config).
 * If the transaction amount is between 100 and 10,000, the method will be executed and give the result.
 */
```
(You can check the details of the **IntervalGuard** attribute in the [Attributes](#attributes) section.)


> [!NOTE]
> For **valguard** helper method, you do **NOT** need to add the classes that you use for attribute validation to the **class_list** in the configuration file.
> And classes do **NOT** have to be resolved by the container.

### Using Service Container Bindings
By using service container bindings, you need to add the classes that you use for attribute validation to the **class_list** in the configuration file.
For the classes that you add to the **class_list**, the package will bind them to the ValidatorGuardCore in the service provider.
So whenever these classes are resolved by the container, the package will initiate the ValidatorGuardCore to mimic the classes as a wrapper and handle validation.
    
For example, if you have a class named **UserService** that you want to use for attribute validation, you need to add the class to the **class_list** in the configuration file as follows:
```php
"class_list" => [
    UserService::class,
]
```

And let's say you have a method named **getTransactionAmount** in the **UserService** class that you want to validate the attributes.
You can add the attributes that you want to validate to the method as follows:
```php
// class UserService
#[IntervalGuard(lowerBound: 100, operator: '<=', upperBound: 10000)] // Transaction amount (method result) must be between 100 and 10,000
public function getTransactionAmount(int $transactionId): float
{
    // Logic of transaction amount calculation
}
```

In this example, the **getTransactionAmount** method will be validated by the **IntervalGuard** attribute after the method execution.
(You can check the details of the **IntervalGuard** attribute in the [Attributes](#attributes) section.)

And whenever UserService is resolved by the container (e.g. Dependency Injection, app() helper etc.), the package will initiate the ValidatorGuardCore to mimic the UserService as a wrapper and handle validation:
```php
// Resolve UserService from the container
$userService = app(UserService::class);

// Call the method
$amount = $userService->getTransactionAmount(1344); 
/*
 * If the transaction amount is not between 100 and 10,000, the exception will be thrown/logged (based on throwing or logging enabled in config).
 * If the transaction amount is between 100 and 10,000, the method will be executed and give the result.
 */
```

### Attributes
More attributes will be added in the future. You can also create/add your custom attributes as explained in the [Create Your Own Attribute](#create-your-own-attribute) section.
We will cover the following attributes in this section:
* [IntervalGuard](#intervalguard)
* [DateGuard](#dateguard)
* [AllowedValuesGuard](#allowedvaluesguard)
* [CallbackGuard](#callbackguard)
* [ArrayKeysExistGuard](#arraykeysexistguard)

> [!TIP]
> Attribute flags as follows:
> - TARGET_METHOD : Marks that attribute declaration is allowed only in class methods. 
> - IS_REPEATABLE : Attribute declaration in the same place is allowed multiple times.
> - TARGET_PARAMETER: Marks that attribute declaration is allowed only in function or method parameters.

Glossary:

#### IntervalGuard
The `IntervalGuard` attribute is used to validate the method result within a specified interval.
Attribute flags for the `IntervalGuard`: TARGET_METHOD, IS_REPEATABLE

`IntervalGuard` is listed in the **after** array in the configuration file **attributes** option because it validates the method result after the method execution.

Sample usage:
```php
// class UserService
#[IntervalGuard(lowerBound: 100, operator: '<=', upperBound: 10000)] // Transaction amount (method result) must be between 100 and 10,000
public function getTransactionAmount(int $transactionId): float
{
    // Logic of transaction amount calculation
}
```

And when the `getTransactionAmount` method is called, the result will be validated by the `IntervalGuard` attribute after the method execution.
```php
// Initiate UserService class
$userService = new UserService();
// Call the method
$amount = valguard($userService)->getTransactionAmount(1344); 
```

`InntervalGuard` attribute parameters:
* **lowerBound** (float): The lower bound of the interval. (🚩required)
* **operator** (string): The operator to be used for comparison. (🚩required)
    * **'<'**: Less than
    * **'<='**: Less than or equal to
    * **'=='**: Equal to
    * **'!='**: Not equal to
    * **'>'**: Greater than
    * **'>='**: Greater than or equal to
* **upperBound** (float|null): The upper bound of the interval.

`IntervalGuard` attribute is **repeatable**, so you can add multiple `IntervalGuard` attributes to the same method.
For instance:
```php
#[IntervalGuard(lowerBound: 10, operator: '<')]
#[IntervalGuard(lowerBound: 30, operator: '>=')]
public function getTransactionAmount(int $transactionId): float
{
    // Logic of transaction amount calculation
}
```

In this example, the `getTransactionAmount` method result will be validated by the `IntervalGuard` attribute twice after the method execution.
The first validation will check if the transaction amount is bigger than 10, and the second validation will check if the transaction amount is less than or equal to 30.

- Potential Use Cases for `IntervalGuard` Attribute:
    - Age Validation:
    ```php
    #[IntervalGuard(18, '<=')] // Age must be bigger than 18 (inclusive)
    public function calculateUserAge(): int
    {
        // Logic of user age calculation
    }
    ```
  - Login Attempt Monitoring:
    ```php
    #[IntervalGuard(0, '<=', 5)] // Maximum 5 login attempts allowed
    public function loginAttempts(string $username): int
    {
        // Logic of login attempts calculation
    }
    ```
    - Credit Score Validation:
    ```php
    #[IntervalGuard(100, '<')] // Credit score must be bigger than 800
    public function getCreditScore(int $userId): int
    {
        // Logic of credit score calculation
    }
    ```

There can be many other use cases for the `IntervalGuard` attribute. You can use it for any method that requires interval validation for the method result.

#### DateGuard
The `DateGuard` attribute is used to validate whether the given date parameter is in the future, past, weekdays, weekends, today, tomorrow or between two dates and so on.
Attribute flag for the `DateGuard`: TARGET_PARAMETER

`DateGuard` is listed in the **before** array in the configuration file **attributes** option because it validates the method parameter before the method execution,
which benefits the performance by avoiding unnecessary method execution.

Sample usage:
```php
// class UserService
public function createEvent(
    #[DateGuard(paramPosition: 0, boundary: DateGuard::FUTURE)] string $eventDate
): void {
    // Logic to create an event
}
```

And when the `createEvent` method is called, the `eventDate` parameter will be validated by the `DateGuard` attribute before the method execution.
```php
// Initiate UserService class
$userService = new UserService();

// Call the method
valguard($userService)->createEvent('2023-12-31'); 
```

`DateGuard` attribute parameters:
* **paramPosition** (int): The position of the parameter in the method. (🚩required)
* **boundary** (string): The boundary to be used for comparison. (🚩required)
    * **DateGuard::FUTURE**: The date must be in the future.
    * **DateGuard::FUTURE_OR_PRESENT**: The date must be in the future or now.
    * **DateGuard::PAST**: The date must be in the past.
    * **DateGuard::PAST_OR_PRESENT**: The date must be in the past or now.
    * **DateGuard::BETWEEN**: The date must be between two dates. (! range parameter is required)
    * **DateGuard::NOT_BETWEEN**: The date must not be between two dates. (! range parameter is required)
    * **DateGuard::WEEKDAYS**: The date must be a weekday.
    * **DateGuard::WEEKENDS**: The date must be a weekend.
    * **DateGuard::TODAY**: The date must be today.
    * **DateGuard::TOMORROW**: The date must be tomorrow.
* **range** (array|null): The range of dates, it must include **upper_bound** and **lower_bound** keys. (🚩required if boundary is BETWEEN or NOT_BETWEEN)
    * **upper_bound** (string): The upper bound of the date range.
    * **lower_bound** (string): The lower bound of the date range.

- Potential Use Cases for `DateGuard` Attribute:
  - Subscription Management:
    ```php
    public function subscribeUser(
        int $userId,
        #[DateGuard(paramPosition: 1, boundary: DateGuard::FUTURE)] string $subscriptionEndDate
    ): void {
        // Logic to subscribe user
    }
    ```
  - Historical Data Retrieval:
    ```php
    public function fetchHistoricalData(
    #[DateGuard(paramPosition: 0, boundary: DateGuard::PAST)] string $queryDate
    ): array {
        // Fetch data for the given date
    }
    ```
  - Event Scheduling:
    ```php
    public function scheduleEvent(
        #[DateGuard(paramPosition: 0, boundary: DateGuard::WEEKDAYS)] string $eventDate
    ): void {
        // Logic to schedule an event
    }
    ```
  - Reservation System:
  ```php
    public function makeReservation(
        bool $isRoomAvailable,
        #[DateGuard(
            paramPosition: 1,
            boundary: DateGuard::BETWEEN,
            range: ['lower_bound' => '2023-01-01', 'upper_bound' => '2023-12-31'])
        ] string $reservationDate
    ): void {
        // Logic to make a reservation
    }
  ```
  
There can be many other use cases for the `DateGuard` attribute. You can use it for any method that requires date validation for the method parameter.

#### AllowedValuesGuard

#### CallbackGuard

#### ArrayKeysExistGuard

### Create Your Own Attribute

## 💫 Contributing

> **Your contributions are welcome!** f you'd like to improve this package, simply create a pull request with your changes. Your efforts help enhance its functionality and documentation. 

> If you find this package useful, please consider ⭐ it to show your support!

## 📜 License
Validator Guard is an open-sourced software licensed under the **[MIT license](LICENSE)**.
