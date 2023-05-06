# ProtonSign

ProtonSign is a blockchain app written for the Proton Blockchain. The app may be used by a Proton account with the Proton mobile wallet app to initiate a document signing request, to be signed by 1 or more Proton accounts, with signatures being logged as transactions to the Proton blockchain.

Software developed by Adrian Scott ([www.adrianscott.com](http://www.adrianscott.com), [@AdrianScottcom](https://www.twitter.com/adrianscottcom)).


## Architecture Overview

The front-end is a React.js application, which speaks to a PHP back-end accessible in a subdirectory on the web server, receiving POST requests and returning JSON replies with a "result" or "error" object in the replies. Data storage on the server-side consists of a "docinfo" directory with JSON files with info on the document signing request, and a "uploads" directory with the files uploaded. Uploaded files are only available through the PHP API for a configurable, limited number of days, a default of 7 days. Logins and transactions are made through the Proton-Web-SDK by the React front-end. This app leverages code from Proton's Taskly demo.


## Server Requirements

    PHP 7.2+ (may work in newer/greater php version)
    A web server such as Apache
    Node.js e.g. 12.x LTS and npm
    sha256sum


## Installation


### Directory Configuration

Installation can be made to a directory such as /data/protonsign

Create a directory to store user logs, e.g.:

    mkdir /data/protonsign/docinfo /data/protonsign/uploads
    chgrp apache /data/protonsign/docinfo /data/protonsign/uploads
    chmod 770 /data/protonsign/docinfo /data/protonsign/uploads

Each day a user answers a question, a JSON file named username-date.json is created in this directory. Date is calculated using hourly offset of UTC in config.php.


### PHP

The "php" directory contents goes into a subdirectory of the web server docroot with PHP processing of .php files enabled in the web server, e.g. /var/www/html/psignapi

Rename config-sample.php to config.php and change parameters to desired settings and relevant directory locations and URL bases.


#### Production config.php

```php
<?php
$domain_root = "http://sign.protonchain.com/"; # root of these web/php urls
$data_dir = "/data/protonsign/"; # directory where overall app is
$login_link = "http://sign.protonchain.com/"; # link for proton login
$sign_link = "http://sign.protonchain.com/"; # link for signing
$email_from = "Proton Sign <sign@protonchain.com>"; # from email address
$upload_dir = "/data/protonsign/uploads/"; # directory for file uploads
$json_dir = "/data/protonsign/docinfo/"; # directory for request json's
$max_upload_size = 5000000; # max size of file upload accepted
$expiry_days = 7; # Number of days signing request expires in
$max_signers = 5; # Maximum number of signers to a doc request
?>
```

#### Development config.php

```php
<?php
$domain_root = "http://35.236.54.120/"; # root of these web/php urls
$data_dir = "/data/protonsign/"; # directory where overall app is
$login_link = "http://35.236.54.120/"; # link for proton login
$sign_link = "http://35.236.54.120/sign"; # link for signing
$email_from = "Proton Sign <sign@protonchain.com>"; # from email address
$upload_dir = "/data/protonsign/uploads/"; # directory for file uploads
$json_dir = "/data/protonsign/docinfo/"; # directory for request json's
$max_upload_size = 5000000; # max size of file upload accepted
$expiry_days = 7; # Number of days signing request expires in
$max_signers = 5; # Maximum number of signers to a doc request
?>
```

### React

Files are in the react directory

This part handles the front-end user interface.

To install:

    npm install
    npm audit fix

Rename sample.env to .env.local, edit parameters and copy to .env.production and edit parameters, or else soft link it (e.g. ln -s) if contents will be the same.
Note that we now have a REACT_APP_VERIFY_ON if you want to test as if all logins are by a verified user, e.g. for testing with unverified accounts.

    npm run build
    cp -r build/* /var/www/html

Note: The app currently charges 1 FOOBAR for the signing transaction. This can be changed in react/src/pages/Sign/SignContainer.jsx . The Proton account setting for receiving the FOOBAR and doing the login is set in react/src/utils/proton.js


## To Run the App

Start web server, e.g.:

    service httpd start



Disclaimers: This code has not been subjected to a security audit. The react app has lots of dependencies, which also presents a risk vector. PHP configuration & security is important, c.f. https://phptherightway.com/
