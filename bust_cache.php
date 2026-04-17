<?php
$dir = __DIR__;
$files = glob("$dir/*.php");

foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'href="assets/style.css?v=<?php echo time(); ?>"') !== false) {
            $content = str_replace('href="assets/style.css?v=<?php echo time(); ?>"', 'href="assets/style.css?v=<?php echo time(); ?>"', $content);
            file_put_contents($file, $content);
            echo "Updated " . basename($file) . "\n";
        }
    }
}
?>
