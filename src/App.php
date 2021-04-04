<?php


namespace App;


use App\Util\Util;
use Manialib\Gbx\Map;

class App
{
    private string $basePath;

    public function __construct()
    {
        if (count($_SERVER['argv']) !== 2) {
            Util::echoScriptUsage();
            exit(1);
        }

        $requestedPath = $_SERVER['argv'][1];

        $basePath = realpath($_SERVER['argv'][1]);

        if ($basePath === false) {
            echo "Path $requestedPath not found";
            exit(1);
        }

        if (!is_dir($basePath)) {
            echo "Path $requestedPath is not a directory";
            exit(1);
        }

        $this->basePath = $basePath;
    }

    public function run()
    {
        Util::println("Base path: $this->basePath");

        $changedFilenameCount = $this->processDirectory($this->basePath);

        Util::println("========================================");
        Util::println("Changed $changedFilenameCount filenames");
    }

    /**
     * Function inspired by the EvoSC stripAll function {@link https://github.com/EvoTM/EvoSC}
     *
     * @param string $styledName
     * @return string
     */
    private function getCleanMapName(string $styledName)
    {
        return preg_replace('/(?<![$])\${1}(([lh])(?:\[.+?])|[iwngosz<>]{1}|[\w\d]{1,3})/i', '', $styledName);
    }

    private function processDirectory($path, $relativePath = '.'): int
    {
        $changedFilenameCount = 0;

        if (!is_dir($path)) {
            throw new \RuntimeException("$path is not a directory");
        }

        $directoryContent = scandir($path);

        foreach ($directoryContent as $directoryItem) {
            if (in_array($directoryItem, [
                '.',
                '..',
            ])) {
                continue;
            }

            $itemAbsolutePath = "{$path}/{$directoryItem}";
            $itemRelativePath = "{$relativePath}/{$directoryItem}";

            if (is_file($itemAbsolutePath)) {
                if ($this->processFile($itemAbsolutePath, $itemRelativePath)) {
                    ++$changedFilenameCount;
                }
            } elseif (is_dir($itemAbsolutePath)) {
                $changedFilenameCount += $this->processDirectory($itemAbsolutePath, $itemRelativePath);
            }
        }

        return $changedFilenameCount;
    }

    /**
     * @param $path
     * @param $relativePath
     * @return bool true if the filename has been changed, false otherwise
     */
    private function processFile($path, $relativePath): bool
    {
        if (!is_file($path)) {
            throw new \RuntimeException("$path is not a directory");
        }

        Util::println("=== Processing $relativePath ===");

        $pathSlug = Util::getPathSlug($path);
        $filename = $pathSlug[count($pathSlug)-1];
        Util::println("Current filename: $filename");

        try {
            $map = Map::loadFile($path);
        } catch (\InvalidArgumentException $e) {
            Util::println($e->getMessage());
            return false;
        }

        $mapStyledName = $map->getName();
        $mapCleanName = $this->getCleanMapName($mapStyledName);

        $cleanFileName = "{$mapCleanName}.Map.Gbx";

        if ($filename === $cleanFileName) {
            Util::println("The current filename is correct");
            return false;
        }

        Util::println("New filename: $cleanFileName");

        unset($pathSlug[count($pathSlug)-1]);

        $pathSlug[] = $cleanFileName;

        $newPath = Util::getPathFromSlug($pathSlug);

        var_dump($newPath);

        if (!rename($path, $newPath)) {
            Util::println("WARNING: Unable to rename file $path to $newPath");
            return false;
        }

        return true;
    }
}
