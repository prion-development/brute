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