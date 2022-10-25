<?php

const BASE_URL = "";
const PYTHON = "";
define("ROOT_DIR", realpath(__DIR__ . "/../"));
define("UPLOAD_DIR", realpath(ROOT_DIR . "/resources/upload/"));
define("DOWNLOAD_DIR", realpath(ROOT_DIR . "/resources/download/"));
define("CONVERTER", realpath(ROOT_DIR . "/resources/converter/converter.py"));

// File Upload
const MAX_FILESIZE = 10000000; //10MB
const ALLOWED_FORMATS = array("stl", "off", "obj", "ares"); //getSupportedFileFormats() will fetch the list of supported formats dynamically

// Database
$db = new PDO("dsn", "user", "password");

// Security
const SECURITY_SECRET = ""; //Any random non-predictable string used to prevent session hijacking
const HTTPS_ONLY = true;

// MQTT
const MQTT_SERVER = "";
const MQTT_SERVER_EXTERN = "";
const MQTT_TOPIC = "";

// User WatchDog
const WATCHDOG_ENABLE = false;
const WATCHDOG_INTERVAL_TIME = 10;
const WATCHDOG_RESPONSE_TIME = 3;

// Language
const LANGUAGE_DIR = ROOT_DIR . "/src/modules/language/";
const LANGUAGE_DEFAULT = "en";
const LANGUAGES = array("en" => LANGUAGE_DIR . "english.php", "de" => LANGUAGE_DIR . "german.php");