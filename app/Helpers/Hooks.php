<?php

namespace App\Helpers;

class Hooks
{
    protected static $actions = [];
    protected static $filters = [];

    /**
     * Add a new action hook.
     *
     * @param string $hook
     * @param callable $callback
     * @param int $priority
     * @param int $accepted_args
     */
    public static function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
    {
        static::$actions[$hook][$priority][] = [
            'callback' => $callback,
            'accepted_args' => $accepted_args,
        ];
    }

    /**
     * Execute an action hook.
     *
     * @param string $hook
     * @param mixed ...$args
     */
    public static function do_action($hook, ...$args)
    {
        if (isset(static::$actions[$hook])) {
            ksort(static::$actions[$hook]);
            foreach (static::$actions[$hook] as $priority => $callbacks) {
                foreach ($callbacks as $callback_data) {
                    call_user_func_array($callback_data['callback'], array_slice($args, 0, $callback_data['accepted_args']));
                }
            }
        }
    }

    /**
     * Add a new filter hook.
     *
     * @param string $hook
     * @param callable $callback
     * @param int $priority
     * @param int $accepted_args
     */
    public static function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
    {
        static::$filters[$hook][$priority][] = [
            'callback' => $callback,
            'accepted_args' => $accepted_args,
        ];
    }

    /**
     * Apply a filter hook.
     *
     * @param string $hook
     * @param mixed $value
     * @param mixed ...$args
     * @return mixed
     */
    public static function apply_filters($hook, $value, ...$args)
    {
        if (isset(static::$filters[$hook])) {
            ksort(static::$filters[$hook]);
            foreach (static::$filters[$hook] as $priority => $callbacks) {
                foreach ($callbacks as $callback_data) {
                    $args_for_callback = array_merge([$value], array_slice($args, 0, $callback_data['accepted_args'] - 1));
                    $value = call_user_func_array($callback_data['callback'], $args_for_callback);
                }
            }
        }
        return $value;
    }
}
