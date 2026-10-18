# PHP Login System

A simple and secure user registration and login system built with PHP, MySQL and jQuery.

## Features
- User registration with validation (name, email, password length, password match)
- Login with session handling
- Passwords stored with `password_hash()` and checked with `password_verify()`
- SQL injection protection with PDO prepared statements
- CSRF protection on all forms
- Session ID regenerated after login
- Simple brute-force limit: 5 wrong attempts lock login for 1 minute
- jQuery: show/hide password and live password check
- Protected dashboard page and secure logout

## Tech stack
PHP 8, MySQL, jQuery, HTML, CSS

## How to run locally
1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy this project folder into `xampp/htdocs/` (for example `htdocs/php-login-system`).
3. Open `http://localhost/phpmyadmin`, go to **Import**, and import `database.sql`.
4. If your MySQL settings are different, edit `config.php`.
5. Open `http://localhost/php-login-system/register.php` and create an account.

## Files
| File | Purpose |
|------|---------|
| `config.php` | Database connection and helper functions |
| `register.php` | Sign-up page |
| `login.php` | Login page |
| `dashboard.php` | Page only logged-in users can see |
| `logout.php` | Ends the session |
| `database.sql` | Creates the database and `users` table |
| `style.css` | Styling |

## Author
MD Tofazzal Hosein
