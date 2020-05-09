# Prion Brute (Lumen/Laraval 5 Package)

Prion Brute monitors and enforces brute force attempts.

Tested on Lumen 5.6

## Installation

`composer require "prion-development/brute:5.6.*"`

In config/app.php, add the following provider:
`PrionDevelopment\Providers\BruteProviderService::class`

## Automated Setup
Run the following command for automated setup.
`php artisan prionbrute:setup`

Clear or reset your Laravel config cache.
`php artisan config:clear`
`php artisan config:cache`


## License

Prion Brute is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


## Check for Lock
Check and see if $key is blocked:
`Brute::isLocked($key);`

Unlock $key:
`Brute::unlock($key);`

Lock $key:
This will only lock a key if the number of attempts is great than or equal to the maximum.
`Brute::lock($key);`

Force a lock on $key:
`Brute::lock($key, true);`

## Manage Attempts

To add an attempt:
If the number of tries matches or is greater than the max number of tries, we will block $key.
`Brute::attempt($key);`

Attempts on $key:
`Brute::attempts($key);`

Remove an attempt on $key:
`Brute::removeAttempt($key)`

Remove "x" attempts on $key:
`Brute::removeAttempt($key, x)`

Maximum number of attempts on $key:
`Brute::max($key);`

Remove all attempts and blocks on a key
`Brute::clear($key);`

## How Brute works
You can push a string into brute as an "attempt". An attempt records a UTC timestamp in
the default cache database using a unique key. When the number of attempts is greater than the
set limit, the key is locked.

We automatically expire attempts if the timestamp is outside of the expiration config.

## Running Tests
1. Make sure composer packages are installed (`composer update`)
2. vendor/bin/phpunit
