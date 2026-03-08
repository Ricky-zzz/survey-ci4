# Survey App (CodeIgniter 4)

## Overview
A simple survey application built with CodeIgniter 4.

## Requirements
- PHP 8.2 or higher
- Composer
- MySQL or compatible database

## Installation
1. Clone this repository:
	git clone <repo-url>
2. Install dependencies:
	composer install
3. Copy the environment file:
	cp env .env
4. Configure your .env file:
	- Set your baseURL
	- Set your database credentials
5. Run database migrations:
	php spark migrate
6. (Optional) Seed the database:
	php spark db:seed
7. Set the web server document root to the public folder.

## Usage
- Access the app via your configured base URL.
- Admin and public routes are available.

## Folder Structure
- app/: Application code (controllers, models, views)
- public/: Web root
- writable/: Logs, cache, uploads

## Support
For issues, use the repository issue tracker.

## License
See LICENSE file for details.
