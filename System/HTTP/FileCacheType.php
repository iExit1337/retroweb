<?php


namespace System\HTTP;


final class FileCacheType
{
    public const JS = 'js';
    public const CSS = 'css';
    public const PNG = 'png';
    public const JPG = 'jpg';
    public const GIF = 'GIF';

    public static function getFileType(string $type): string
    {
        switch (strtolower($type)) {
            case self::JS:
                return 'application/javascript';
                break;
            case self::CSS:
                return 'text/css';
                break;
            case self::PNG:
            case self::JPG:
            case self::GIF:
                return 'image/'.$type;
                break;
        }

        return '';
    }

}