<?php

namespace App\Services;

use App\Helpers\Hooks;
use App\Models\Option;

class RichTextEditorService
{
    /**
     * Get all registered rich text editors.
     * Allows 3rd-party plugins under plugins/ to register custom editors via hook.
     *
     * @return array<string, string> Key => Label map
     */
    public static function getAvailableEditors(): array
    {
        $editors = [
            'quill' => 'Quill.js (Modern WYSIWYG)',
            'sceditor' => 'SCEditor (Classic BBCode/WYSIWYG)',
        ];

        if (class_exists(Hooks::class)) {
            $editors = Hooks::apply_filters('registered_rich_text_editors', $editors);
        }

        return is_array($editors) ? $editors : [];
    }

    /**
     * Get the active rich text editor key.
     * Defaults to 'quill'.
     * Can be overridden by plugins via hook.
     */
    public static function getActiveEditor(): string
    {
        $default = 'quill';
        $active = Option::where('o_type', 'system_setting')
            ->where('name', 'rich_text_editor')
            ->value('o_valuer') ?? $default;

        if (class_exists(Hooks::class)) {
            $active = Hooks::apply_filters('active_rich_text_editor', $active);
        }

        $available = static::getAvailableEditors();

        if (!array_key_exists($active, $available)) {
            return 'quill';
        }

        return (string) $active;
    }

    /**
     * Set the active rich text editor in database options.
     */
    public static function setActiveEditor(string $editorKey): void
    {
        $available = static::getAvailableEditors();
        if (!array_key_exists($editorKey, $available)) {
            $editorKey = 'quill';
        }

        Option::updateOrInsert(
            ['name' => 'rich_text_editor', 'o_type' => 'system_setting'],
            ['o_valuer' => $editorKey, 'updated_at' => now()]
        );
    }
}
