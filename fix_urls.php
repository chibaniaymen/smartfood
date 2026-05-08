<?php
$dir = __DIR__ . '/smartfood';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getPathname(), 'router.php') === false && strpos($file->getPathname(), 'index.php') === false) {
        $content = file_get_contents($file->getPathname());
        $isController = strpos($file->getPathname(), 'controllers') !== false;
        
        if ($isController) {
            // Replace header("Location: /smartfood/...") with header("Location: " . BASE_URL . "/...")
            $content = preg_replace('/"Location:\s*\/smartfood\/?([^"]*)"/', '"Location: " . BASE_URL . "/$1"', $content);
        } else {
            // Replace /smartfood/ and /smartfood in views
            $content = str_replace('/smartfood/', '<?= BASE_URL ?>/', $content);
            $content = preg_replace('/href="\/smartfood"/', 'href="<?= BASE_URL ?>"', $content);
        }
        
        file_put_contents($file->getPathname(), $content);
    }
}
echo "Done replacing URLs.";
