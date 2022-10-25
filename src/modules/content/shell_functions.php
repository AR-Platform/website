<?php

require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/modules/util/utility_functions.php");

/**
 * Fetches all supported formats directly from the file converter.
 *
 * @return array An array containing all supported file formats.
 * @noinspection PhpUnused
 */
function getSupportedFileFormats(): array
{
    $command = array(
        PYTHON,
        CONVERTER,
        0
    );
    return executeShellCommand($command);
}

/**
 * Executes the given shell command and returns the shell output.
 *
 * @param array $array The shell command as an array.
 * @return array The shell output lines formatted as an array.
 */
function executeShellCommand(array $array): array
{
    $command = implode(' ', array_map("escapeshellarg", $array));
    return shellOutputToArray(shell_exec($command));
}

/**
 * Executes the given shell command asynchronously without returning the shell output.
 *
 * @param array $array The shell command as an array.
 * @return void No return value.
 */
function executeShellCommandAsync(array $array)
{
    $command = implode(' ', array_map("escapeshellarg", $array));
    if (PHP_OS_FAMILY == "Windows")
    {
        pclose(popen("start cmd /c \"$command\"", "r"));
    }
    else
    {
        shell_exec($command . " > /dev/null 2>&1 &");
    }
}

/**
 * Converts the shell output to an array representation where each line is an element in the array.
 *
 * @param string $string The shell output.
 * @return array The shell output reformatted into an array.
 */
function shellOutputToArray(string $string): array
{
    return array_filter(preg_split("/\r\n|\r|\n/", $string));
}

/**
 * Analyzes the given file according to the specified file extension.
 *
 * @param string $inputFile The path to the input file.
 * @param string $fileExt The actual file extension of the input file.
 * @return array The options array containing all possible config options for the conversion process.
 */
function analyzeFile(string $inputFile, string $fileExt): array
{
    $command = array(
        PYTHON,
        CONVERTER,
        1,
        $inputFile,
        $fileExt
    );
    return executeShellCommand($command);
}

/**
 * Converts the given file according to the file extension and options and saves the converted file to the desired location.
 *
 * @param string $inputFile The path to the input file.
 * @param string $fileExt The actual file extension of the input file.
 * @param string $outputFile The desired path for the converted file.
 * @param string $options The optional options string to configure the conversion process.
 * @return void No return value.
 */
function convertFile(string $inputFile, string $fileExt, string $outputFile, string $options)
{
    $command = array(
        PYTHON,
        CONVERTER,
        2,
        $inputFile,
        $fileExt,
        $outputFile,
        $options
    );
    executeShellCommandAsync($command);
}
