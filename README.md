# AER Website

The webinterface for the Augmented-Reality-Resources platform.

## Requirements
* [PHP](https://php.net) (with PDO support for the DB)
* [Composer](https://getcomposer.org/)
* A relational database like MySQL or PostgreSQL

## Deployment
1. Clone the repository including submodules to the desired location.
2. Install the required dependencies with [Composer](https://getcomposer.org/).
3. Create the download and upload directories and grant the webserver write permission for the selected directories.
4. Create a database with the required schema.
5. Create a config file in the [config](config) directory according to the existing example.
6. Define the [public](public) directory of the project as the root directory.

## External API
The `api/app.php` part of the API is publicly available and is used by the official mobile app.

### Available API Arguments

| Argument     | Value | Response                                          |
|--------------|-------|---------------------------------------------------|
| mqtt         | -     | The MQTT broker address and the default topic     |
| institutions | -     | A list of all available institutions              |
| institution  | ID    | A list of all courses of the selected institution |
| course       | ID    | A list of all course contents                     |
| content      | ID    | Starts the download of the requested content      |

## Project Structure

* [config](config) - Contains the config file.
* [docker](docker) - Contains all additional scripts required for the docker image.
* [public](public) - Contains the public part of the webapp.
  * [api](public/api) - Contains the API.
  * [css](public/css) - Contains all stylesheets of the webapp.
  * [js](public/js) - Contains all JavaScript scripts of the webapp.
  * [media](public/media) - Contains all media files (e.g. images and audio files).
* [resources](resources) - Contains the file converter and the uploaded and converted files.
  * [converter](resources/converter) - Contains the file converter.
  * [api](resources/download) - Contains the converted files.
  * [api](resources/upload) - Contains the uploaded files.
* [src](src) - Contains all PHP scripts.
  * [modules](src/modules) - Contains the PHP scripts that implement the logic of the application.
  * [modules](src/templates) - Contains the PHP scripts that implement the rendering of the website.
  * [modules](src/vendor) - Contains the PHP-Composer dependencies.

## Dependencies

* [php-mqtt/client](https://github.com/php-mqtt/client)
