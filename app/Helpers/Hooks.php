<?php

namespace App\Helpers {

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
         * @param int|null $accepted_args
         */
        public static function add_action($hook, $callback, $priority = 10, $accepted_args = null)
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
                        $callArgs = $callback_data['accepted_args'] !== null
                            ? array_slice($args, 0, $callback_data['accepted_args'])
                            : $args;
                        call_user_func_array($callback_data['callback'], $callArgs);
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
         * @param int|null $accepted_args
         */
        public static function add_filter($hook, $callback, $priority = 10, $accepted_args = null)
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
                        $args_for_callback = $callback_data['accepted_args'] !== null
                            ? array_merge([$value], array_slice($args, 0, max(0, $callback_data['accepted_args'] - 1)))
                            : array_merge([$value], $args);
                        $value = call_user_func_array($callback_data['callback'], $args_for_callback);
                    }
                }
            }
            return $value;
        }

        /**
         * Check if an action hook has been registered.
         *
         * @param string $hook
         * @param callable|null $callback
         * @return bool
         */
        public static function has_action($hook, $callback = null): bool
        {
            if (!isset(static::$actions[$hook])) {
                return false;
            }

            if ($callback === null) {
                return !empty(static::$actions[$hook]);
            }

            foreach (static::$actions[$hook] as $priority => $callbacks) {
                foreach ($callbacks as $callback_data) {
                    if ($callback_data['callback'] === $callback) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * Check if a filter hook has been registered.
         *
         * @param string $hook
         * @param callable|null $callback
         * @return bool
         */
        public static function has_filter($hook, $callback = null): bool
        {
            if (!isset(static::$filters[$hook])) {
                return false;
            }

            if ($callback === null) {
                return !empty(static::$filters[$hook]);
            }

            foreach (static::$filters[$hook] as $priority => $callbacks) {
                foreach ($callbacks as $callback_data) {
                    if ($callback_data['callback'] === $callback) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * Remove a registered action hook.
         *
         * @param string $hook
         * @param callable $callback
         * @param int $priority
         * @return bool
         */
        public static function remove_action($hook, $callback, $priority = 10): bool
        {
            if (isset(static::$actions[$hook][$priority])) {
                foreach (static::$actions[$hook][$priority] as $index => $callback_data) {
                    if ($callback_data['callback'] === $callback) {
                        unset(static::$actions[$hook][$priority][$index]);
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * Remove a registered filter hook.
         *
         * @param string $hook
         * @param callable $callback
         * @param int $priority
         * @return bool
         */
        public static function remove_filter($hook, $callback, $priority = 10): bool
        {
            if (isset(static::$filters[$hook][$priority])) {
                foreach (static::$filters[$hook][$priority] as $index => $callback_data) {
                    if ($callback_data['callback'] === $callback) {
                        unset(static::$filters[$hook][$priority][$index]);
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * Get all registered action hooks.
         *
         * @return array
         */
        public static function getActions(): array
        {
            return static::$actions;
        }

        /**
         * Get all registered filter hooks.
         *
         * @return array
         */
        public static function getFilters(): array
        {
            return static::$filters;
        }

        /**
         * Reset all registered actions and filters.
         */
        public static function reset(): void
        {
            static::$actions = [];
            static::$filters = [];
        }
    }
}

namespace {
    if (!function_exists('add_action')) {
        /**
         * Global helper to add an action hook.
         */
        function add_action($hook, $callback, $priority = 10, $accepted_args = null)
        {
            \App\Helpers\Hooks::add_action($hook, $callback, $priority, $accepted_args);
        }
    }

    if (!function_exists('do_action')) {
        /**
         * Global helper to execute an action hook.
         */
        function do_action($hook, ...$args)
        {
            \App\Helpers\Hooks::do_action($hook, ...$args);
        }
    }

    if (!function_exists('add_filter')) {
        /**
         * Global helper to add a filter hook.
         */
        function add_filter($hook, $callback, $priority = 10, $accepted_args = null)
        {
            \App\Helpers\Hooks::add_filter($hook, $callback, $priority, $accepted_args);
        }
    }

    if (!function_exists('apply_filters')) {
        /**
         * Global helper to apply filter hooks.
         */
        function apply_filters($hook, $value, ...$args)
        {
            return \App\Helpers\Hooks::apply_filters($hook, $value, ...$args);
        }
    }

    if (!function_exists('has_action')) {
        /**
         * Global helper to check if an action hook exists.
         */
        function has_action($hook, $callback = null)
        {
            return \App\Helpers\Hooks::has_action($hook, $callback);
        }
    }

    if (!function_exists('has_filter')) {
        /**
         * Global helper to check if a filter hook exists.
         */
        function has_filter($hook, $callback = null)
        {
            return \App\Helpers\Hooks::has_filter($hook, $callback);
        }
    }

    if (!function_exists('remove_action')) {
        /**
         * Global helper to remove an action hook.
         */
        function remove_action($hook, $callback, $priority = 10)
        {
            return \App\Helpers\Hooks::remove_action($hook, $callback, $priority);
        }
    }

    if (!function_exists('remove_filter')) {
        /**
         * Global helper to remove a filter hook.
         */
        function remove_filter($hook, $callback, $priority = 10)
        {
            return \App\Helpers\Hooks::remove_filter($hook, $callback, $priority);
        }
    }
}
