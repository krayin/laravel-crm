<p align="center">
<a href="http://www.bagisto.com"><img src="https://bagisto.com/wp-content/themes/bagisto/images/logo.png" alt="Total Downloads"></a>
</p>

<p align="center">
<a href="https://packagist.org/packages/bagisto/bagisto-crm"><img src="https://poser.pugx.org/bagisto/bagisto-crm/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/bagisto/bagisto-crm"><img src="https://poser.pugx.org/bagisto/bagisto-crm/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/bagisto/bagisto-crm"><img src="https://poser.pugx.org/bagisto/bagisto-crm/license.svg" alt="License"></a>
</p>

## Topics
1. [Introduction](#introduction)
2. [Documentation](#documentation)
3. [Requirements](#requirements)
4. [Installation & Configuration](#installation-and-configuration)
5. [License](#license)
6. [Security Vulnerabilities](#security-vulnerabilities)

### Introduction

[Bagisto CRM](https://www.bagisto.com) is a hand tailored CRM framework built on some of the hottest opensource technologies
such as [Laravel](https://laravel.com) (a [PHP](https://secure.php.net/) framework) and [Vue.js](https://vuejs.org)
a progressive Javascript framework.

**Free & Opensource Laravel CRM solution for SMEs and Enterprises for complete customer lifecycle management.**

**Read our documentation: [Bagisto CRM Docs](https://devdocs.bagisto.com/)**

**We also have a forum for any type of concerns, feature requests, or discussions. Please visit: [Bagisto CRM Forums](https://forums.bagisto.com/)**

# Visit our live [Demo](https://crm.webkul.com)

It packs in lots of features that will allow your E-Commerce business to scale in no time:

* Descriptive and Simple Admin Panel.
* Admin Dashboard.
* Custom Attributes.
* Built on Modular Approach.
* Email parsing via Sendgrid.
* Check out [these features and more](https://bagisto.com/features/).

**For Developers**:
Take advantage of two of the hottest frameworks used in this project -- Laravel and Vue.js -- both of which have been used in Bagisto CRM.

### Documentation

#### Bagisto Documentation [https://devdocs.bagisto.com](https://devdocs.bagisto.com)

### Requirements

* **SERVER**: Apache 2 or NGINX.
* **RAM**: 3 GB or higher.
* **PHP**: 7.3 or higher.
* **For MySQL users**: 5.7.23 or higher.
* **For MariaDB users**: 10.2.7 or Higher.
* **Node**: 8.11.3 LTS or higher.
* **Composer**: 1.6.5 or higher.

### Installation and Configuration

**1. You can install Bagisto CRM by using the GUI installer.**

##### a. Download zip from the link below:

[Download the latest release](https://github.com/bagisto/bagisto-crm/releases/latest)

##### b. Extract the contents of zip and execute the project in your browser:

~~~
http(s)://example.com
~~~

**2. Or you can install Bagisto CRM from your console.**

##### Execute these commands below, in order

~~~
1. composer create-project bagisto/bagisto-crm
~~~

~~~
2. php artisan bagisto-crm:install
~~~

**To execute Bagisto**:

##### On server:

Warning: Before going into production mode we recommend you uninstall developer dependencies.
In order to do that, run the command below:

> composer install --no-dev

~~~
Open the specified entry point in your hosts file in your browser or make an entry in hosts file if not done.
~~~

##### On local:

~~~
php artisan serve
~~~


**How to log in as admin:**

> *http(s)://example.com/admin/login*

~~~
email:admin@example.com
password:admin123
~~~


### License
Bagisto CRM is a truly opensource CRM framework which will always be free under the [MIT License](https://github.com/bagisto/bagisto-crm/blob/master/LICENSE).

### Security Vulnerabilities
Please don't disclose security vulnerabilities publicly. If you find any security vulnerability in Bagisto CRM then please email us: mailto:support@bagisto.com.