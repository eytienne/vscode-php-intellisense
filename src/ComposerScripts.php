<?php

declare(strict_types=1);

namespace VscodePHPIntellisense;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;

foreach ([__DIR__ . '/../../../autoload.php', __DIR__ . '/../autoload.php', __DIR__ . '/../vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

$dotenv = new Dotenv();
try {
    $dotenv->load(__DIR__ . '/../.env.local');
} catch (PathException $th) {
    // ignoring because it has not to be git-indexed
}

class ComposerScripts
{
    const LANGUAGE_SERVER_PATH = __DIR__ . "/../vendor/felixfbecker/language-server";

    public static function setupDev()
    {
        if (isset($_ENV['LOCAL_LINK'])) {
            if (is_link(self::LANGUAGE_SERVER_PATH)) {
                // if second self launched `composer install` or already linked
                return;
            }

            rrmdir(self::LANGUAGE_SERVER_PATH);
            symlink(__DIR__ . "/../" . $_ENV['LOCAL_LINK'], self::LANGUAGE_SERVER_PATH);
            passthru("composer install");
        }
    }
}

// https://www.php.net/manual/en/function.rmdir.php#110489
function rrmdir($dir)
{
    if (is_link($dir)) return;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? rrmdir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}
