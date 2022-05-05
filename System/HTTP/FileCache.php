<?php

namespace System\HTTP;

use System\HTTP\Request\Request;
use System\HTTP\Request\RequestType;

class FileCache
{
    /**
     * @var array
     */
    private $_fileMapping = [];

    /**
     * @var Request
     */
    private $_request;

    /**
     * @var array
     */
    private $_config;

    /**
     * FileCache constructor.
     * @param array $fileMapping
     * @param Request $request
     * @param array $config
     */
    public function __construct(array $fileMapping, Request $request, array $config)
    {
        $this->_fileMapping = $fileMapping;
        $this->_request = $request;
        $this->_config = $config;
    }

    public function isFileRequested(): bool
    {
        $isFileRequested = false;

        if ($this->_request->getMethod() == RequestType::GET) {
            $uri = parse_url($this->_request->getUri())['path'];
            $uriSplit = explode('.', $uri);
            $fileEnding = strtolower($uriSplit[count($uriSplit) - 1]);
            $file = str_replace($this->_config['requestDir'], '', $uri);

            if (isset($this->_fileMapping['.' . $fileEnding])) {
                if (file_exists(WWW_PATH . $file)) {
                    $isFileRequested = true;
                    $cachePath = $this->getCachePath();
                    $cachedFilePath = $cachePath . $fileEnding . DIRECTORY_SEPARATOR . explode($fileEnding, $file)[1] . $fileEnding;

                    while (!file_exists($cachedFilePath)) {
                        $this->createFileCache($cachedFilePath, WWW_PATH . $file, $fileEnding);
                    }

                    header("Content-Type: " . FileCacheType::getFileType($fileEnding));
                    $this->setCachingHeaders();
                    echo file_get_contents($cachedFilePath);
                }
            } else if (substr(strtolower($file), 0, strlen($this->_config['resourceDir'])) == $this->_config['resourceDir']) {
                if (file_exists(WWW_PATH . $file) && is_file(WWW_PATH . $file)) {
                    $isFileRequested = true;

                    $content = file_get_contents(WWW_PATH . $file);
                    $mimeType = mime_content_type(WWW_PATH . $file);

                    header("Content-Type: " . $mimeType);
                    $this->setCachingHeaders();
                    echo $content;
                }
            }
        }

        return $isFileRequested;
    }

    private function setCachingHeaders()
    {
        $cacheTime = 60 * 60 * 24 * 14; // 14 days
        $ts = gmdate("D, d M Y H:i:s", time() + $cacheTime) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$cacheTime");
    }

    private function getFormattedSource(string $context, string $fileType): string
    {
        $timestamp = date('d.m.Y - H:i:s');
        $version = $this->_config['version'];
        $formatted = <<<FORMATTED
/**
 * Created: $timestamp
 * Version: $version
 **/

FORMATTED;

        switch (strtolower($fileType)) {
            case FileCacheType::JS:
                $formatted .= str_replace(["\n", "\r"], "", $context);
                break;
            case FileCacheType::CSS:
                $formatted .= $this->getMinifiedCSS($context);
                break;
        }

        return $formatted;
    }

    private function createFileCache(string $destinationFilePath, string $sourceFilePath, string $fileType)
    {
        $fileSource = file_get_contents($sourceFilePath);

        $destinationFilePathSplit = explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destinationFilePath));
        array_pop($destinationFilePathSplit);
        $destinationDirectory = implode(DIRECTORY_SEPARATOR, $destinationFilePathSplit);
        if (!is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0777, true);
        }

        file_put_contents($destinationFilePath, $this->getFormattedSource($fileSource, $fileType));
    }

    private function getCachePath(): string
    {
        $cachePath = BASE_PATH . $this->_config['cacheDir'];

        while (!is_dir($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        return $cachePath;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Methods to minify source code
    //////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Source: https://gist.github.com/webgefrickel/3339063
     * @param string $source
     * @return string
     */
    private function getMinifiedCSS(string $source): string
    {
        // some of the following functions to minimize the css-output are directly taken
        // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
        // all credits to Christian Schaefer: http://twitter.com/derSchepp
        // remove comments
        $source = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $source);
        // backup values within single or double quotes
        preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $source, $hit, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($hit[1]); $i++) {
            $source = str_replace($hit[1][$i], '##########' . $i . '##########', $source);
        }
        // remove traling semicolon of selector's last property
        $source = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $source);
        // remove any whitespace between semicolon and property-name
        $source = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $source);
        // remove any whitespace surrounding property-colon
        $source = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $source);
        // remove any whitespace surrounding selector-comma
        $source = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $source);
        // remove any whitespace surrounding opening parenthesis
        $source = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $source);
        // remove any whitespace between numbers and units
        $source = preg_replace(' / ([\d\.] +)[\s\r\n\t] + (px | em | pt |%)/ims', '$1$2', $source);
        // shorten zero-values
        $source = preg_replace(' / ([^\d\.]0)(px | em | pt |%)/ims', '$1', $source);
        // constrain multiple whitespaces
        $source = preg_replace(' / \p{Zs}+/ims', ' ', $source);
        // remove newlines
        $source = str_replace(array("\r\n", "\r", "\n"), '', $source);
        // Restore backupped values within single or double quotes
        for ($i = 0; $i < count($hit[1]); $i++) {
            $source = str_replace('##########' . $i . '##########', $hit[1][$i], $source);
        }

        return $source;
    }

}