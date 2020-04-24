# Drop_DeployUtils
- This module allows you to send a notification before and after a composer install

## Installation
- Install module through composer (recommended):
```sh
$ composer config repositories.drop.ddu vcs https://github.com/Dropsrl/Drop_DeployUtils
$ composer require drop/module-deploy-utils
```

- Install module manually:
    - Copy these files in app/code/Drop/Drop_DeployUtils/

- After installing the extension, run the following commands:
```sh
$ php bin/magento module:enable Drop_DeployUtils
$ php bin/magento setup:upgrade
$ php bin/magento setup:di:compile
$ php bin/magento setup:static-content:deploy
$ php bin/magento cache:clear
```

- Add this section to your composer.json
```
"scripts": {
    "send-deploy-alert": "php bin/magento drop:deploy:sendalert",
    "send-end-deploy-alert": "php bin/magento drop:deploy:endsendalert",
    "no-dev-pre-install-cmd": [
      "@send-deploy-alert"
    ],
    "no-dev-post-install-cmd": [
      "@send-end-deploy-alert"
    ]
},
```

- If you want to add the composer output you have to launch it like this:
```
composer install --no-dev 2>&1 | tee httpdocs/var/log/composer.log
```

## Requirements
- PHP >= 7.0.0

## Compatibility
- Magento >= 2.2
- Not tested on 2.1 and 2.0

## Support
If you encounter any problems or bugs, please create an issue on [Github](https://github.com/Dropsrl/Drop_DeployUtils/issues) 

## License
[GNU General Public License, version 3 (GPLv3)] http://opensource.org/licenses/gpl-3.0

## Copyright
(C) 2019 Drop S.R.L.

