<?php
$di = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
$it = new RecursiveIteratorIterator($di);
foreach ($it as $file_name) {
    if (pathinfo($file_name, PATHINFO_EXTENSION) == "php") {
        echo $file_name, PHP_EOL;
        exec("$file_name", $output);

        $error = shell_exec("php -l $file_name");
        error_log("$error", 3, "error.log");
    }
}
?>