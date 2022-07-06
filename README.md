# Composer-enabled Decoupled Drupal Project

This is Pantheon's recommended starting point for creating new decoupled Drupal 9 backend sites. It builds on Pantheon's default Drupal 9 upstream to provide best practices, default configuration, and accellerator tools for using drupal 9 as a backend in decoupled archictures.

Unlike with earlier Pantheon upstreams, files such as Drupal Core that you are
unlikely to adjust while building sites are not in the main branch of the
repository. Instead, they are referenced as dependencies that are installed by
Composer.

## Installation

### Prerequisites
- Composer (required for CMS backends): [Install Globally](https://getcomposer.org/download/)
- [Generate machine token](https://pantheon.io/docs/machine-tokens#create-a-machine-token) & [Authenticate into Terminus](https://pantheon.io/docs/machine-tokens#authenticate-into-terminus)
- [Install Terminus](https://pantheon.io/docs/terminus/install) (3.0.0 above required)
- Also install the following plugins:
  - `terminus self:plugin:install terminus-build-tools-plugin`
  - `terminus self:plugin:install terminus-power-tools`
  - `terminus self:plugin:install terminus-secrets-plugin`
  - Reload the terminus plugins: `terminus self:plugin:reload`
  - Clear cache for composer: `composer clear-cache`
  - Validate that the required plugins are installed: `terminus self:plugin:list`
- Create [Github Personal access tokens](https://github.com/settings/tokens)
- Create [CircleCI Personal API Tokens](https://app.circleci.com/settings/user/tokens)

### Installation via `terminus build:project:create`

- Run the terminus build:project:create as follows:
    ```
    terminus build:project:create \
    --team='{My Team Name}' \
    --template-repository="git@github.com:pantheon-systems/decoupled-drupal-recommended.git" pantheon-systems/decoupled-drupal-recommended \
    --ci-template='git@github.com:pantheon-systems/advanced-ci-templates' \
    --visibility private {PROJECT_NAME} \
    --stability=dev \
    --profile="pantheon_decoupled_profile"
    ```
    Replace {PROJECT_NAME} with your project name - for example decoupled-drupal.

    Replace '{My Team Name}' with your team name - for example My Agency. This can also be omitted.

Note: This will result in a Github repository being created for this new codebase under the authenticated user's namespace (unless the --org option is used), a site being created on Pantheon and a CircleCI project being created for automated deployments.

The project create command can be used with a variety of additional options including specifying an alternate install profile, or using a variety of CI or Git providers. For more information, consult the [Additional Options](https://github.com/pantheon-systems/decoupled-kit-js/blob/canary/web/docs/Backend%20Starters/Decoupled%20Drupal/creating-new-project.md#additional-options) section in the Decoupled Kit developer documentation.

## Contributing

Contributions are welcome in the form of GitHub pull requests. However, the
`pantheon-systems/decoupled-drupal-recommended` repository is a mirror that does not
directly accept pull requests.

Instead, to propose a change, please fork [pantheon-systems/decoupled-drupal](https://github.com/pantheon-systems/decoupled-drupal)
and submit a PR to that repository.
