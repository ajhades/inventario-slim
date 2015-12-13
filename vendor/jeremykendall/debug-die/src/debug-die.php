<?php

/**
 * Debug Helper: Convenience wrapper around var_dump() and die(var_dump()).
 *
 * @copyright 2015 Jeremy Kendall
 * @license https://github.com/jeremykendall/debug-die/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/debug-die
 */

namespace {
    if (!function_exists('d') || !function_exists('dd')) {
        /**
         * Dumps information about a variable
         *
         * @link http://xdebug.org/
         *
         * @param mixed $expression The variable to dump.
         */
        function d($expression)
        {
            foreach (func_get_args() as $ex) {
                var_dump($ex);
            }
        }

        /**
         * Dumps information about a variable and dies
         *
         * @link http://xdebug.org/
         *
         * @param mixed $expression The variable to dump.
         */
        function dd($expression)
        {
            foreach (func_get_args() as $ex) {
                var_dump($ex);
            }

            die();
        }
    } else {
        trigger_error(
            'd() or dd() already exist as functions. debug-die cannot be used.',
            E_USER_ERROR
        );
    }
}
