<?php

/**
 * Resolve an answer value to its display label.
 * For scale/multiple_choice questions, looks up the option_text.
 * For yesno, capitalizes the value.
 * For other types, returns value as-is.
 */
function resolveAnswerLabel(?array $question, ?string $value): string
{
    if (!$question || $value === null || $value === '') {
        return '';
    }

    $type = $question['type'] ?? null;

    if (in_array($type, ['scale', 'multiple_choice'], true) && !empty($question['options'])) {
        foreach ($question['options'] as $opt) {
            if ($opt['value'] === $value) {
                return $opt['option_text'];
            }
        }
    }

    // Capitalize yesno
    if ($type === 'yesno') {
        return ucfirst($value);
    }

    // Return raw value for text, file_upload, etc.
    return $value;
}
