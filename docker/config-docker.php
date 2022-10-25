<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('display_startup_errors', '1');

define("BASE_URL", "http://{$_SERVER['SERVER_NAME']}");
const PYTHON = "python3";
define("ROOT_DIR", realpath(__DIR__ . "/../"));
define("UPLOAD_DIR", realpath(ROOT_DIR . "/resources/upload/"));
define("DOWNLOAD_DIR", realpath(ROOT_DIR . "/resources/download/"));
define("CONVERTER", realpath(ROOT_DIR . "/resources/converter/converter.py"));

// File Upload
const MAX_FILESIZE = 25000000;
const ALLOWED_FORMATS = array("stl", "off", "obj", "ares"); //getSupportedFileFormats()

// Database
$db = new PDO("pgsql:host=database;dbname={$_ENV["POSTGRES_DB"]}", $_ENV["POSTGRES_USER"], $_ENV["POSTGRES_PASSWORD"]);

// Security
define("SECURITY_SECRET", $_ENV["AER_SECURITY_SECRET"]);
const HTTPS_ONLY = false;

// MQTT
const MQTT_SERVER = "mqtt";
const MQTT_SERVER_EXTERN = BASE_URL;
const MQTT_TOPIC = "aer";

// User WatchDog
const WATCHDOG_ENABLE = true;
const WATCHDOG_INTERVAL_TIME = 10;
const WATCHDOG_RESPONSE_TIME = 3;

// Language
const LANGUAGE_DIR = ROOT_DIR . "/src/modules/language/";
const LANGUAGE_DEFAULT = "en";
const LANGUAGES = array("en" => LANGUAGE_DIR . "english.php", "de" => LANGUAGE_DIR . "german.php");