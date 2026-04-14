<?php
$content = file_get_contents('e:/xampp/htdocs/myads/lang/ar/messages.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'يعمل') !== false) {
        echo ($i + 1) . ": " . trim($line) . "\n";
    }
}
