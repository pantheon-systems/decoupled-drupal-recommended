This repository is a reference implementation and start state for a modern Drupal 9 workflow utilizing [Composer](https://getcomposer.org/), Continuous Integration (CI), Automated Testing, and Pantheon. It uses CircleCI for continuous integration and includes sane defaults for an enterprise Drupal 9 site. This includes default settings, utility modules with default configuration, environment-specific configuration via Config Split, Quicksilver automation scripts, local development tooling, and basic tests.

## Quickstart
1. Clone this repo:
```
git clone git@github.com:%GH_ORG%/%SITE_NAME%.git
```
2. Install composer dependencies:
```
cd <your repo> && composer install
```
3. Start local development environment:
```
lando start
```

## Local Development
This repo includes a .lando.yml file which is pre-configured. You simply just need to run `lando start` in the directory where the .lando.yml file resides and this will spin up a local Pantheon environment which closely mirrors the architecture of Pantheon hosting. The first time you run `lando start` you will be prompted to enter a Pantheon token if you don't have one set up already on your machine. Once lando successfully starts your project, you should see output in your terminal similar to the below:

```
 NAME                  %SITE_NAME%
 LOCATION              /Users/myuser/sites/%SITE_NAME%
 SERVICES              appserver_nginx, appserver, database, cache, edge_ssl, edge, index
 APPSERVER_NGINX URLS  https://localhost:51725
                       http://localhost:51726
 EDGE_SSL URLS         https://localhost:51730
 EDGE URLS             http://localhost:51729
                       http://%SITE_NAME%.lndo.site/
                       https://%SITE_NAME%.lndo.site/
```


## Helpful Local Development commands
Sync dev environment database to local environment:
```
lando pull --database=dev --files=none --code=none
```


Sync dev environment database and files to local environment:
```
lando pull --database=dev --files=dev --code=none
```

## Managing secrets

Install [terminus-secrets-plugin](https://github.com/pantheon-systems/terminus-secrets-plugin):

```
terminus self:plugin:install pantheon-systems/terminus-secrets-plugin
terminus self:plugin:reload
```
After that use the following terminus commands to set a secret value in your pantheon site:

- To set a secret:
  ```
  terminus secrets:set siteName.env preview.secret value
  ```
  For example, if we wanted to set a secret for the example decoupled preview site created by the Pantheon Decoupled module, we could use the following command:
  ```
  terminus secrets:set decoupled-drupal.dev example_decoupled_preview.secret mySecret
  ```

In this example, the key name for the secret is `example_decoupled_preview.secret`. If set, this value is used in [decoupled.settings.php](web/sites/default/decoupled.settings.php) to overwrite the related preview site entity. A similar approach could be used in other project settings files.

Get complete list of available commands for terminus-secrets-plugin:
```
terminus list secret
```

## Decoupled Preview

Decoupled preview can be configured at admin/structure/dp-preview-site
(Structure -> Preview Sites.)

Local config includes a preview site for a local NextJS instance. The preview secret
must be set manually. Or alternatively it can be overridden in `settings.local.php`:

```
$config['decoupled_preview.dp_preview_site.nextjs_demo']['secret'] = 'mysecret';
```

After configuring decoupled preview, a preview link will display on the preview
tab for all nodes.


## Important files and directories

### `/web`

Pantheon will serve the site from the `/web` subdirectory due to the configuration in `pantheon.yml`. This is necessary for a Composer based workflow. Having your website in this subdirectory also allows for tests, scripts, and other files related to your project to be stored in your repo without polluting your web document root or being web accessible from Pantheon. They may still be accessible from your version control project if it is public. See [the `pantheon.yml`](https://pantheon.io/docs/pantheon-yml/#nested-docroot) documentation for details.

#### `/config`

One of the directories moved to the git root is `/config/default`. This directory holds Drupal's `.yml` configuration files. In more traditional repo structure these files would live at `/sites/default/config/`.

Config Split has been pre-configured to support environment-specific configuration. This means that you can have different configuration for different environments such as local, ci, dev, test, and live. This configuration stored in the respective environment directories at `/config/envs/env-name`. Read more about config split in the [Config Split documentation](https://www.drupal.org/docs/contributed-modules/configuration-split/).

### `composer.json`
This project uses Composer to manage third-party PHP dependencies.

The `require` section of `composer.json` should be used for any dependencies your web project needs, even those that might only be used on non-Live environments. All dependencies in `require` will be pushed to Pantheon.

The `require-dev` section should be used for dependencies that are not a part of the web application but are necesarry to build or test the project. Some example are `php_codesniffer` and `phpunit`. Dev dependencies will not be deployed to Pantheon.

If you are just browsing this repository on GitHub, you may not see some of the directories mentioned above. That is because Drupal core and contrib modules are installed via Composer and ignored in the `.gitignore` file.

This project uses the following required dependencies:

- **composer/installers**: Relocates the installation location of certain Composer projects by type; for example, this component allows Drupal modules to be installed to the `modules` directory rather than `vendor`.

- **drupal/core-composer-scaffold**: Allows certain necessary files, e.g. index.php, to be copied into the required location at installation time.

- **drupal/core-recommended**: This package contains Drupal itself, including the Drupal scaffold files.

- **pantheon-systems/drupal-integrations**: This package provides additional scaffold files required to install this site on the Pantheon platform. These files do nothing if the site is deployed elsewhere.

The following optional dependencies are also included as suggestions:

- **pantheon-systems/quicksilver-pushback**: This component allows commits from the Pantheon Dashboard to be automatically pushed back to GitHub for sites using the Build Tools Workflow. This package does nothing if that workflow has not been set up for this site.

- **drush/drush**: Drush is a commandline tool that provides ways to interact with site maintenance from the command line.

- **cweagans/composer-patches**: Allows a site to be altered with patch files at installation time.

- **drush-ops/behat-drush-endpoint**: Used by Behat tests.

- **zaporylie/composer-drupal-optimizations**: This package makes `composer update` operations run more quickly when updating Drupal and Drupal's dependencies.

Any of the optional dependencies may be removed if they are not needed or desired.

### `.ci`
This `.ci` directory is where all of the scripts that run on Continuous Integration are stored. Provider specific configuration files, such as `.circle/config.yml` and `.gitlab-ci.yml`, make use of these scripts.

The scripts are organized into subdirectories of `.ci` according to their function: `build`, `deploy`, or `test`.

#### Build Scripts `.ci/build`
Steps for building an artifact suitable for deployment. Feel free to add other build scripts here, such as installing Node dependencies, depending on your needs.

- `.ci/build/php` installs PHP dependencies with Composer

#### Build Scripts `.ci/deploy`
Scripts for facilitating code deployment to Pantheon.

- `.ci/deploy/pantheon/create-multidev` creates a new [Pantheon multidev environment](https://pantheon.io/docs/multidev/) for branches other than the default Git branch
  - Note that not all users have multidev access. Please consult [the multidev FAQ doc](https://pantheon.io/docs/multidev-faq/) for details.
- `.ci/deploy/pantheon/dev-multidev` deploys the built artifact to either the Pantheon `dev` or a multidev environment, depending on the Git branch

#### Automated Test Scripts `.ci/tests`
Scripts that run automated tests. Feel free to add or remove scripts here depending on your testing needs.

**Static Testing** `.ci/test/static` and `tests/unit`
Static tests analyze code without executing it. It is good at detecting syntax error but not functionality.

- `.ci/test/static/run` Runs [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [Drupal coding standards](https://www.drupal.org/project/coder), PHP Unit, and [PHP syntax checking](https://www.php.net/manual/en/function.php-check-syntax.php).
- `tests/unit/bootstrap.php` Bootstraps the Composer autoloader
- `tests/unit/TestAssert.php` An example Unit test. Project specific test files will need to be created in `tests/unit`.

**Visual Regression Testing** `.ci/test/visual-regression`
Visual regression testing uses a headless browser to take screenshots of web pages and compare them for visual differences.

- `.ci/test/visual-regression/run` Runs [BackstopJS](https://github.com/garris/BackstopJS) visual regression testing.
- `.ci/test/visual-regression/backstopConfig.js` The [BackstopJS](https://github.com/garris/BackstopJS) configuration file. Setting here will need to be updated for your project. For example, the `pathsToTest` variable determines the URLs to test.

**Behat Testing** `.ci/test/behat` and `tests/behat`
[Behat](http://behat.org/en/latest/) is an acceptance/end-to-end testing framework written in PHP. It faciliates testing the fully built Drupal site on Pantheon infrastucture. [The Drupal Behat Extension](https://www.drupal.org/project/drupalextension) is used to help with integrating Behat and Drupal.

- `.ci/test/behat/initialize` creates a backup of the environment to be tested
- `.ci/test/behat/run` sets the `BEHAT_PARAMS` environment variable with dynamic information necessary for Behat and configure it to use Drush via [Terminus](https://pantheon.io/docs/terminus/) and starts headless Chrome, and runs Behat
- `.ci/test/behat/cleanup` restores the previously made database backup and saves screenshots taken by Behat
- `tests/behat/behat-pantheon.yml` Behat configuration file compatible with running tests against a Pantheon site
- `tests/behat/tests/behat/features` Where Behat test files, with the `.feature` extension, should be stored. The provided example tests will need to be replaced with project specific tests.
  - `tests/behat/tests/behat/features/content.feature` A Behat test file which logs into the Drupal dashboard, creates nodes, users and terms, and verifies their existience in the Drupal admin interface and the front end of the site


## Updating your site

When using this repository to manage your Drupal site, you will no longer use the Pantheon dashboard to update your Drupal version. Instead, you will manage your updates using Composer. Ensure your site is in Git mode, clone it locally, and then run composer commands from there.  Commit and push your files back up to Pantheon as usual.


## Contributing

Contributions are welcome in the form of GitHub pull requests. However, the
`pantheon-upstreams/decoupled-drupal` repository is a mirror that does not
directly accept pull requests.

Instead, to propose a change, please fork [pantheon-systems/decoupled-drupal](https://github.com/pantheon-systems/decoupled-drupal)
and submit a PR to that repository.

