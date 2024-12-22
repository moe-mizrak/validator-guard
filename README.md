
# Validator Guard

<br />

[![Latest Version on Packagist](https://img.shields.io/badge/packagist-v1.0-blue)](https://packagist.org/packages/moe-mizrak/validator-guard)
<br />

ValidatorGuard enables attribute-driven validation to control Laravel method behavior.

---

_Attribute validation is a powerful way to validate method parameters, method results, and method behavior in a declarative way._

```php
// class UserService
#[IntervalGuard(lowerBound: 100, operator: '<=', upperBound: 10000)] // Transaction amount (method result) must be between 100 and 10,000
public function getTransactionAmount(int $transactionId): float
{
    // Logic of transaction amount calculation
}
```

```php
$userService = app(UserService::class);

$transactionId = 1344;
$amount = $userService->getTransactionAmount($transactionId); 
```

_If the transaction amount is not between 100 and 10,000, the exception will be thrown/logged (based on throwing or logging enabled in config)._

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
- **attributes**: Here add the attributes that are used for `Validation Guard`. You can add the attributes that will be handled before method execution to the **before** array, and the attributes that will be handled after method execution to the **after** array.
Some attributes are already added to the config file. You can remove or add new attributes as needed. You can check the details of the attributes in the [Attributes](#attributes) section.
  - **before**: Attributes processed before method execution, allowing validation to be handled upfront, which avoids unnecessary method calls and improves performance.
  - **after**: Attributes processed after method execution, enabling validations that depend on method results or cases where method execution is needed (e.g., logging, database operations) even if validation fails.
- **class_list**: Here add all classes that you use attribute validation in order to bind them to `ValidatorGuardCore` in the service provider.
Whenever these classes are resolved by the container, the package will initiate the `ValidatorGuardCore` to mimic the classes as a wrapper and handle validation.
Check the [Using Service Container Bindings](#using-service-container-bindings) section for more details.
- **throw_exceptions**: Enable/Disable throwing exceptions in case of validation failure. (🚩default: true)
- **log_exceptions**: Enable/Disable logging exceptions in case of validation failure. (🚩default: false)
- **log_channel**: Set an option for the default channel for logging so that it can be configured when needed (only applicable if `VALIDATOR_GUARD_LOG_EXCEPTIONS` is enabled). (🚩default: stack)

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
> 
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

$transactionId = 1344;
// Call the method by wrapping user service with valguard helper
$amount = valguard($userService)->getTransactionAmount($transactionId); 
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
By using **service container bindings**, you need to add the classes that you use for attribute validation to the **class_list** in the configuration file.
For the classes that you add to the **class_list**, the package will bind them to the **ValidatorGuardCore** in the service provider.
So whenever these classes are resolved by the container, the package will initiate the **ValidatorGuardCore** to mimic the classes as a wrapper and handle validation.
    
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

And whenever UserService is resolved by the container (e.g. Dependency Injection, app() helper etc.), the package will initiate the **ValidatorGuardCore** to mimic the UserService as a wrapper and handle validation:
```php
// Resolve UserService from the container
$userService = app(UserService::class);

// Call the method
$transactionId = 1344;
$amount = $userService->getTransactionAmount($transactionId); 
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
> - **TARGET_METHOD** : Marks that attribute declaration is allowed only in class methods. 
> - **IS_REPEATABLE** : Attribute declaration in the same place is allowed multiple times.
> - **TARGET_PARAMETER** : Marks that attribute declaration is allowed only in function or method parameters.

#### IntervalGuard
The `IntervalGuard` attribute is used to validate the method result within a specified interval.
Attribute flags for the `IntervalGuard`: **TARGET_METHOD**, **IS_REPEATABLE**

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

$transactionId = 1344;
// Call the method
$amount = valguard($userService)->getTransactionAmount($transactionId); 
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

Basically it checks: `lowerBound < result < upperBound` or `lowerBound <= result <= upperBound` based on the operator and son on.

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

- Potential use-cases for `IntervalGuard` Attribute:
    - Age Validation:
    ```php
    #[IntervalGuard(lowerBound: 18, operator: '<=')] // Age must be bigger than 18 (inclusive)
    public function calculateUserAge(): int
    {
        // Logic of user age calculation
    }
    ```
    - Login Attempt Monitoring:
    ```php
    #[IntervalGuard(lowerBound: 0, operator: '<=', upperBound: 5)] // Maximum 5 login attempts allowed
    public function loginAttempts(string $username): int
    {
        // Logic of login attempts calculation
    }
    ```
    - Credit Score Validation:
    ```php
    #[IntervalGuard(lowerBound: 100, operator: '<')] // Credit score must be bigger than 800
    public function getCreditScore(int $userId): int
    {
        // Logic of credit score calculation
    }
    ```

There can be many other use cases for the `IntervalGuard` attribute. You can use it for any method that requires interval validation for the method result.

#### DateGuard
The `DateGuard` attribute is used to validate whether the given date parameter is in the future, past, weekdays, weekends, today, tomorrow or between two dates and so on.
Attribute flag for the `DateGuard`: **TARGET_PARAMETER**

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

$eventDate = '2023-12-31';
// Call the method
valguard($userService)->createEvent($eventDate); 
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
    * **upper_bound** (string): The upper bound of the date range. (🚩required if range array is provided)
    * **lower_bound** (string): The lower bound of the date range. (🚩required if range array is provided)

- Potential use-cases for `DateGuard` Attribute:
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
The `AllowedValuesGuard` attribute is used to validate whether the given parameter is one of the allowed values.
Attribute flag for the `AllowedValuesGuard`: **TARGET_PARAMETER**

`AllowedValuesGuard` is listed in the **before** array in the configuration file **attributes** option because it validates the method parameter before the method execution.

Sample usage:
```php
// class UserService
public function createEvent(
    #[AllowedValuesGuard(paramPosition: 0, values: ['meeting', 'party', 'wedding'])] string $eventType
): void {
    // Logic to create an event
}
```

And when the `createEvent` method is called, the `eventType` parameter will be validated by the `AllowedValuesGuard` attribute before the method execution.
```php
// Initiate UserService class
$userService = new UserService();

$eventType = 'meeting';
// Call the method
valguard($userService)->createEvent($eventType); 
```

`AllowedValuesGuard` attribute parameters:
* **paramPosition** (int): The position of the parameter in the method. (🚩required)
* **values** (array): The allowed values for the parameter. (🚩required)

- Potential use-cases for `AllowedValuesGuard` Attribute:
  - Language Selection:
    ```php
    public function selectLanguage(
        #[AllowedValuesGuard(paramPosition: 0, values: ['en', 'tr', 'de', 'fr'])] string $language
    ): void {
        // Logic to select a language
    }
    ```
  - User Role Assignment:
    ```php
    public function assignRole(
        int $userId,
        #[AllowedValuesGuard(paramPosition: 1, values: ['admin', 'user', 'guest'])] string $role
    ): void {
        // Logic to assign a role
    }
    ```
  - Payment Method Validation:
    ```php
    public function makePayment(
        #[AllowedValuesGuard(paramPosition: 0, values: ['credit_card', 'paypal', 'bank_transfer'])] string $paymentMethod
    ): void {
        // Logic to make a payment
    }
    ```
  - Order Status Update:
    ```php
    public function updateOrderStatus(
        int $orderId,
        #[AllowedValuesGuard(paramPosition: 1, values: ['pending', 'shipped', 'delivered', 'cancelled'])] string $status,
        #[AllowedValuesGuard(paramPosition: 2, values: ['false', 'true'])] bool $isPaid
    ): bool {
        // Logic to update order status
    }
    ```
    
There can be many other use cases for the `AllowedValuesGuard` attribute. You can use it for any method that requires allowed values validation for the method parameter.

#### CallbackGuard
The `CallbackGuard` attribute is used to invoke a specified class method with given parameters and validate its result against the expected value.
Attribute flag for the `CallbackGuard`: **TARGET_METHOD**, **IS_REPEATABLE**

`CallbackGuard` is listed in the **before** array in the configuration file **attributes** option because it validates the method parameter before the method execution.

Sample usage:
```php
// class UserService
#[CallbackGuard(
    className: PaymentGateway::class,
    methodName: 'isPaymentMethodSupported',
    params: ['credit_card', 'US'],
    expectedResult: true
)]
public function processPayment(int $paymentId): void 
{
    // Logic of payment processing
}
```

And when the `processPayment` method is called, the `isPaymentMethodSupported` method of the `PaymentGateway` class will be invoked with the given parameters,
and the result of `isPaymentMethodSupported` method will be validated by the `CallbackGuard` attribute before the `processPayment` method execution.
```php
// Initiate UserService class
$userService = new UserService();

$paymentId = 134;
// Call the method
valguard($userService)->processPayment($paymentId); 
```

`CallbackGuard` attribute parameters:
* **className** (string): The class name of the callback method. (🚩required)
* **methodName** (string): The method name of the callback method. (🚩required)
* **params** (array|null): The parameters to be passed to the callback method.
* **expectedResult** (mixed): The expected result of the callback method. It can be any type: string, null, int, bool, array, object etc.

- Potential use-cases for `CallbackGuard` Attribute:
  - Active Payment Methods:
    ```php
    #[CallbackGuard(
        className: PaymentService::class,
        methodName: 'getPaymentMethods',
        expectedResult: new PaymentMethodsDTO([
            'credit_card',
            'paypal',
            'bank_transfer',
        ])
    )]
    public function processPayment(int $paymentId): void 
    {
        // Logic of payment processing
    }
    ```
  - User Permissions/Authentications:
    ```php
    #[CallbackGuard(
        className: PermissionService::class,
        methodName: 'allowedPermissions',
        params: ['admin'],
        expectedResult: ['read', 'write', 'delete']
    )]
    public function getUserData(int $userId): array
    {
        // Logic to get user data
    }
    ```
  - Withdraw Availability:
    ```php
    #[CallbackGuard(
        className: BalanceService::class,
        methodName: 'balanceStatus',
        expectedResult: "active"
    )]
    public function withdraw(): bool 
    {
        // Logic to withdraw
    }
    ```
  - Subscription Management:
    ```php
    #[CallbackGuard(
        className: SubscriptionService::class,
        methodName: 'isSubscriptionActive',
        params: ["monthly"],
        expectedResult: true
    )]
    public function fetchSubscriptionData(): array
    {
        // Fetch subscription data
    }
    ```
    
There can be many other use cases for the `CallbackGuard` attribute. You can use it for any method that requires callback method validation before the method execution.

#### ArrayKeysExistGuard
The `ArrayKeysExistGuard` attribute is used to validate whether array key exists in the method array result or array parameter.
Attribute flag for the `ArrayKeysExistGuard`: **TARGET_METHOD**, **IS_REPEATABLE**

`ArrayKeysExistGuard` is listed in the **after** array in the configuration file **attributes** option because it validates the method result after the method execution, along with method parameters.

Sample usage:
```php
// class UserService
#[ArrayKeysExistGuard(
    keys: ['name', 'email'],
    inMethodResult: true
)] // name and email keys must exist in the method result array
public function getUserData(int $userId): array
{
    // Logic to get user data
}
```

And when the `getUserData` method is called, the result array will be validated by the `ArrayKeysExistGuard` attribute after the method execution.
```php
// Initiate UserService class
$userService = new UserService();

$userId = 134;
// Call the method
$userData = valguard($userService)->getUserData($userId); 
```

`ArrayKeysExistGuard` attribute parameters:
* **keys** (array): The keys to be checked in the array. (🚩required)
* **inMethodResult** (bool|null): If true, the keys will be checked in the method result array. If false while `inParam` is true, the keys will be checked in the method parameter array.
* **inParam** (bool|null): If true, the keys will be checked in the method parameter array.
* **paramPosition** (int|null): The position of the parameter in the method. (🚩required if `inParam` is true)

- Potential use-cases for `ArrayKeysExistGuard` Attribute:
  - Validating API Responses:
    ```php
    #[ArrayKeysExistGuard(
        keys: ['status', 'data'],
        inMethodResult: true
    )]
    public function fetchApiResponse(): array 
    {
        // API call logic
    }
    ```
  - Order Data Validation:
    ```php
    #[ArrayKeysExistGuard(
        keys: ['product_id', 'quantity'],
        inMethodResult: true
    )]
    public function getOrderData(int $orderId): array
    {
        // Logic to get order data
    }
    ```
  - Validating Request Parameters:
    ```php
    #[ArrayKeysExistGuard(
        keys: ['user_id', 'email'],
        inParam: true,
        paramPosition: 0
    )]
    public function handleRequest(array $request): void 
    {
        // Handle request logic
    }
    ```
  - Payment Gateway Payload Validation:
    ```php
    #[ArrayKeysExistGuard(
        keys: ['amount', 'currency', 'payment_method'],
        inParam: true,
        paramPosition: 1
    )]
    public function initiatePayment(int $paymentId, array $payload): void {
        // Payment processing logic
    }
    ```
    
There can be many other use cases for the `ArrayKeysExistGuard` attribute. You can use it for any method that requires array key validation for the method result or method parameter.

### Create Your Own Attribute
You can create your custom attribute quite easily and use it for attribute validation. For this purpose, follow the steps below:
- Create a new attribute class that implements the `ValidationAttributeInterface` interface. (`use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;`)

<details>
<summary>Sample attribute:</summary>

```php
use Illuminate\Support\Arr;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;
use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class NewAttribute implements ValidationAttributeInterface
{
    public function __construct(
        private int $paramPosition, // We get the param position to retrieve the parameter value from method params, this is the way we know which parameter to validate since name of the method parameter is not accessible
        private string $stringValue
    ) {}

    public function handle(MethodContextData $methodContextData): bool
    {
        // Validation logic
        $result = $methodContextData->methodResult; // Method result
        $methodParams = $methodContextData->methodParams; // Method parameters
        
        $paramData = Arr::get($methodParams, $this->paramPosition); // Get the parameter value from method params
        
        return ! is_null($paramData) && $result === $this->stringValue;
    }
}
```

</details>

- Add the new attribute class to the **attributes** array in the `validator-guard` config file. 
  - If the newly created attribute validation logic in `handle` method requires method execution 
(e.g. It makes use of method result, you do some logging or database operations which are crucial etc.) then add the attribute to the **after** array.
  - Otherwise, add it to the **before** array in case the attribute validation logic does not require method execution. This will improve performance by avoiding unnecessary method calls.

<details>
<summary>Config file:</summary>

```php
'attributes' => [
    
    'before' => [
        AllowedValuesGuard::class,
        DateGuard::class,
        CallbackGuard::class,
    ],
    
    'after' => [
        IntervalGuard::class,
        ArrayKeysExistGuard::class,
        NewAttribute::class, // Add the new attribute to the after array (or before array based on the requirement)
    ]
],
```
</details>

- Now you can use the new attribute for attribute validation in your methods as explained in the [Usage](#-usage) section.
> [!NOTE]
> Check out [Using Service Container Bindings](#using-service-container-bindings) and [Using valguard Helper](#using-valguard-helper) sections for more details
> in order to decide how to trigger the attribute validation.

<details>
<summary>Usage example:</summary>

```php
// class UserService
#[NewAttribute(paramPosition: 0, stringValue: 'test')]
public function testMethod(string $param): string
{
    return 'test';
}
```

```php
// Initiate UserService class
$userService = new UserService();

$param = 'validParam';
// Call the method
$result = valguard($userService)->testMethod($param);
```
</details>

## 💫 Contributing

> **Your contributions are welcome!** f you'd like to improve this package, simply create a pull request with your changes. Your efforts help enhance its functionality and documentation. 

> If you find this package useful, please consider ⭐ it to show your support!

## 📜 License
Validator Guard is an open-sourced software licensed under the **[MIT license](LICENSE)**.
