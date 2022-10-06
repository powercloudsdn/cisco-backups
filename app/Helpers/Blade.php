<?php

namespace App\Helpers;

use Exception;
use Throwable;

class Blade
{
    /**
     * @param  string  $path generates a URL for the PowerCloud frontend application
     */
    public static function render($__php, $__data)
    {
        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);
        try {
            eval('?' . '>' . $__php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            //  throw new FatalThrowableError($e);
        }
        return ob_get_clean();
    }

    public static function getVariablesFromView($contents)
    {
        $re = '/{!![\ ]*\$[a-zA-Z]+[\ ]*!!}/m';
        preg_match_all($re, $contents, $matches, PREG_SET_ORDER, 0);

        $flatternArray = [];
        array_walk_recursive($matches, function ($a) use (&$flatternArray) {
            $flatternArray[] = $a;
        });
        $variables = str_replace(['{!!', '!!}', '$', ' '], '', $flatternArray);

        return $variables;
    }
}