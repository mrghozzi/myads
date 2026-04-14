<?php
$file = 'e:/xampp/htdocs/myads/lang/ar/messages.php';
try {
    include($file);
    echo "Syntax is OK\n";
} catch (ParseError $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    
    // Print context
    $lines = file($file);
    $errorLine = $e->getLine();
    $start = max(0, $errorLine - 5);
    $end = min(count($lines), $errorLine + 5);
    for ($i = $start; $i < $end; $i++) {
        $prefix = ($i + 1 == $errorLine) ? ">>> " : "    ";
        echo $prefix . ($i + 1) . ": " . $lines[$i];
    }
}
