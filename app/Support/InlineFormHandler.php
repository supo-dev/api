<?php

declare(strict_types=1);

namespace App\Support;

use function Termwind\render;
use function Termwind\terminal;

final class InlineFormHandler
{
    /**
     * @var array<int, array{name: string, label: string, value: string, placeholder: string, multiline: bool, maxLength: int}>
     */
    private array $fields = [];

    private int $focusedFieldIndex = 0;

    private int $cursorPosition = 0;

    private bool $cancelled = false;

    /**
     * Add a text field to the form.
     */
    public function addTextField(
        string $name,
        string $label,
        string $default = '',
        string $placeholder = '',
        int $maxLength = 255
    ): self {
        $this->fields[] = [
            'name' => $name,
            'label' => $label,
            'value' => $default,
            'placeholder' => $placeholder,
            'multiline' => false,
            'maxLength' => $maxLength,
        ];

        return $this;
    }

    /**
     * Add a textarea field to the form.
     */
    public function addTextareaField(
        string $name,
        string $label,
        string $default = '',
        string $placeholder = '',
        int $maxLength = 500
    ): self {
        $this->fields[] = [
            'name' => $name,
            'label' => $label,
            'value' => $default,
            'placeholder' => $placeholder,
            'multiline' => true,
            'maxLength' => $maxLength,
        ];

        return $this;
    }

    /**
     * Run the form and return the field values.
     *
     * @return array<string, string>|null Returns null if cancelled
     */
    public function run(string $title = 'Edit Form'): ?array
    {
        $this->enableRawMode();
        $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);

        try {
            while (true) {
                terminal()->clear(); // @phpstan-ignore-line
                $this->renderForm($title);

                $input = $this->readInput();

                if ($input === "\e" || $input === "\e\e") { // Escape
                    $this->cancelled = true;

                    return null;
                }

                if ($input === "\n" || $input === "\r") { // Enter
                    // Move to next field or submit
                    if ($this->fields[$this->focusedFieldIndex]['multiline']) {
                        // For multiline, Enter adds a newline
                        $this->insertCharacter("\n");
                    } elseif ($this->focusedFieldIndex < count($this->fields) - 1) {
                        // For single line, move to next field or submit
                        $this->focusedFieldIndex++;
                        $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);
                    } else {
                        // Submit the form
                        break;
                    }

                    continue;
                }

                if ($input === "\t") { // Tab - move to next field
                    $this->focusedFieldIndex = ($this->focusedFieldIndex + 1) % count($this->fields);
                    $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);

                    continue;
                }

                if ($input === "\e[Z") { // Shift+Tab - move to previous field
                    $this->focusedFieldIndex = ($this->focusedFieldIndex - 1 + count($this->fields)) % count($this->fields);
                    $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);

                    continue;
                }

                if ($input === "\e[A") { // Up arrow
                    if ($this->fields[$this->focusedFieldIndex]['multiline']) {
                        $this->moveCursorUpInMultiline();
                    } elseif ($this->focusedFieldIndex > 0) {
                        // Move to previous field
                        $this->focusedFieldIndex--;
                        $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);
                    }

                    continue;
                }

                if ($input === "\e[B") { // Down arrow
                    if ($this->fields[$this->focusedFieldIndex]['multiline']) {
                        $this->moveCursorDownInMultiline();
                    } elseif ($this->focusedFieldIndex < count($this->fields) - 1) {
                        // Move to next field
                        $this->focusedFieldIndex++;
                        $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);
                    }

                    continue;
                }

                if ($input === "\e[D") { // Left arrow
                    if ($this->cursorPosition > 0) {
                        $this->cursorPosition--;
                    }

                    continue;
                }

                if ($input === "\e[C") { // Right arrow
                    $maxPos = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);
                    if ($this->cursorPosition < $maxPos) {
                        $this->cursorPosition++;
                    }

                    continue;
                }

                if ($input === "\e[H" || $input === "\e[1~") { // Home
                    $this->cursorPosition = 0;

                    continue;
                }

                if ($input === "\e[F" || $input === "\e[4~") { // End
                    $this->cursorPosition = mb_strlen($this->fields[$this->focusedFieldIndex]['value']);

                    continue;
                }

                if ($input === "\x7f" || $input === "\b") { // Backspace
                    $this->deleteCharacterBefore();

                    continue;
                }

                if ($input === "\e[3~") { // Delete key
                    $this->deleteCharacterAfter();

                    continue;
                }

                // Ctrl+Enter to submit from multiline field
                if ($input === "\e\n" || $input === "\e\r") {
                    break;
                }

                // Regular character input
                if (mb_strlen($input) === 1 && ord($input) >= 32) {
                    $this->insertCharacter($input);
                }
            }

            return $this->getValues();
        } finally {
            $this->disableRawMode();
        }
    }

    /**
     * @return array<string, string>
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->fields as $field) {
            $values[$field['name']] = $field['value'];
        }

        return $values;
    }

    public function wasCancelled(): bool
    {
        return $this->cancelled;
    }

    private function renderForm(string $title): void
    {
        $html = $this->buildFormHtml($title);
        render($html);
    }

    private function buildFormHtml(string $title): string
    {
        $html = '<div class="w-full">';

        // Title
        $html .= '<div class="text-cyan font-bold">'.$title.'</div>';
        $html .= '<div class="text-cyan">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>';
        $html .= '<div class="mt-1"></div>';

        foreach ($this->fields as $index => $field) {
            $isFocused = $index === $this->focusedFieldIndex;
            $html .= $this->renderField($field, $isFocused, $index);
        }

        // Instructions
        $html .= '<div class="mt-1"></div>';
        $html .= '<div class="text-cyan">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>';
        $html .= '<div class="flex justify-between text-gray">';
        $html .= '<span>Tab: next field • Shift+Tab: previous • Enter: submit/next • Esc: cancel</span>';
        $html .= '<span>powered by supo ◉</span>';
        $html .= '</div>';

        return $html.'</div>';
    }

    /**
     * @param  array{name: string, label: string, value: string, placeholder: string, multiline: bool, maxLength: int}  $field
     */
    private function renderField(array $field, bool $isFocused, int $fieldIndex): string
    {
        $html = '';

        // Label
        $labelColor = $isFocused ? 'text-cyan' : 'text-gray';
        $html .= '<div class="'.$labelColor.' font-bold">'.$field['label'].'</div>';

        // Field box
        $borderColor = $isFocused ? 'text-cyan' : 'text-gray';
        $boxWidth = 60;

        // Top border
        $html .= '<div class="'.$borderColor.'">╭'.str_repeat('─', $boxWidth).'╮</div>';

        // Content
        $displayValue = $field['value'] !== '' ? $field['value'] : $field['placeholder'];
        $textColor = $field['value'] !== '' ? 'text-white' : 'text-gray';

        if ($field['multiline']) {
            // Multi-line textarea - show up to 4 lines
            $lines = explode("\n", $displayValue);
            $visibleLines = array_slice($lines, 0, 4);

            foreach ($visibleLines as $lineIndex => $line) {
                $paddedLine = $this->padAndTruncate($line, $boxWidth - 2);

                if ($isFocused && $fieldIndex === $this->focusedFieldIndex) {
                    // Add cursor indicator for focused field
                    $paddedLine = $this->addCursorToLine($paddedLine, $lines, $lineIndex, $boxWidth - 2);
                }

                $html .= '<div class="'.$borderColor.'">│<span class="'.$textColor.'">'.$paddedLine.'</span>│</div>';
            }

            // Fill remaining lines if less than 4
            for ($i = count($visibleLines); $i < 4; $i++) {
                $html .= '<div class="'.$borderColor.'">│'.str_repeat(' ', $boxWidth).'│</div>';
            }
        } else {
            // Single-line text field
            $paddedLine = $this->padAndTruncate($displayValue, $boxWidth - 2);

            if ($isFocused) {
                $paddedLine = $this->addCursorToSingleLine($field['value'], $boxWidth - 2, $field['placeholder']);
            }

            $html .= '<div class="'.$borderColor.'">│<span class="'.$textColor.'">'.$paddedLine.'</span>│</div>';
        }

        // Bottom border
        $html .= '<div class="'.$borderColor.'">╰'.str_repeat('─', $boxWidth).'╯</div>';

        // Character count for focused field
        if ($isFocused) {
            $currentLength = mb_strlen($field['value']);
            $maxLength = $field['maxLength'];
            $countColor = $currentLength > $maxLength ? 'text-red' : 'text-gray';
            $html .= '<div class="'.$countColor.'">'.$currentLength.'/'.$maxLength.' characters</div>';
        }

        return $html.'<div class="mt-1"></div>';
    }

    private function padAndTruncate(string $text, int $width): string
    {
        // Remove any newlines for display in single line context
        $text = str_replace("\n", ' ', $text);

        // Add single space padding on each side
        if (mb_strlen($text) > $width - 2) {
            return ' '.mb_substr($text, 0, $width - 5).'... ';
        }

        // Pad with spaces and add space on both sides
        $padded = mb_str_pad($text, $width - 2, ' ');

        return ' '.$padded.' ';
    }

    private function addCursorToSingleLine(string $value, int $width, string $placeholder): string
    {
        $displayValue = $value;

        // Insert cursor character at position
        $before = mb_substr($displayValue, 0, $this->cursorPosition);
        $after = mb_substr($displayValue, $this->cursorPosition);

        // Use block cursor character
        $cursor = '█';

        $withCursor = $before.$cursor.$after;

        // If empty, show placeholder after cursor
        if ($value === '') {
            $withCursor = $cursor.$placeholder;
        }

        return $this->padAndTruncate($withCursor, $width);
    }

    /**
     * @param  array<int, string>  $allLines
     */
    private function addCursorToLine(string $line, array $allLines, int $lineIndex, int $width): string
    {
        // Calculate which line the cursor is on
        $currentPos = 0;
        $cursorLineIndex = 0;
        $cursorPosInLine = $this->cursorPosition;

        foreach ($allLines as $idx => $l) {
            $lineLength = mb_strlen($l) + 1; // +1 for newline
            if ($currentPos + $lineLength > $this->cursorPosition) {
                $cursorLineIndex = $idx;
                $cursorPosInLine = $this->cursorPosition - $currentPos;
                break;
            }
            $currentPos += $lineLength;
            $cursorLineIndex = $idx + 1;
            $cursorPosInLine = 0;
        }

        if ($lineIndex !== $cursorLineIndex) {
            return $this->padAndTruncate($line, $width);
        }

        // Insert cursor at position in this line
        $actualLine = $allLines[$lineIndex] ?? '';
        $before = mb_substr($actualLine, 0, min($cursorPosInLine, mb_strlen($actualLine)));
        $after = mb_substr($actualLine, min($cursorPosInLine, mb_strlen($actualLine)));

        $cursor = '█';
        $withCursor = $before.$cursor.$after;

        return $this->padAndTruncate($withCursor, $width);
    }

    private function moveCursorUpInMultiline(): void
    {
        $value = $this->fields[$this->focusedFieldIndex]['value'];
        $lines = explode("\n", $value);

        // Find current line and position
        $currentPos = 0;
        $currentLine = 0;
        $posInLine = $this->cursorPosition;

        foreach ($lines as $idx => $line) {
            $lineLength = mb_strlen($line) + 1;
            if ($currentPos + $lineLength > $this->cursorPosition) {
                $currentLine = $idx;
                $posInLine = $this->cursorPosition - $currentPos;
                break;
            }
            $currentPos += $lineLength;
        }

        if ($currentLine > 0) {
            // Move to previous line
            $prevLineStart = 0;
            for ($i = 0; $i < $currentLine - 1; $i++) {
                $prevLineStart += mb_strlen($lines[$i]) + 1;
            }
            $prevLineLength = mb_strlen($lines[$currentLine - 1]);
            $this->cursorPosition = $prevLineStart + min($posInLine, $prevLineLength);
        }
    }

    private function moveCursorDownInMultiline(): void
    {
        $value = $this->fields[$this->focusedFieldIndex]['value'];
        $lines = explode("\n", $value);

        // Find current line and position
        $currentPos = 0;
        $currentLine = 0;
        $posInLine = $this->cursorPosition;

        foreach ($lines as $idx => $line) {
            $lineLength = mb_strlen($line) + 1;
            if ($currentPos + $lineLength > $this->cursorPosition) {
                $currentLine = $idx;
                $posInLine = $this->cursorPosition - $currentPos;
                break;
            }
            $currentPos += $lineLength;
        }

        if ($currentLine < count($lines) - 1) {
            // Move to next line
            $nextLineStart = 0;
            for ($i = 0; $i <= $currentLine; $i++) {
                $nextLineStart += mb_strlen($lines[$i]) + 1;
            }
            $nextLineLength = mb_strlen($lines[$currentLine + 1]);
            $this->cursorPosition = $nextLineStart + min($posInLine, $nextLineLength);
        }
    }

    private function insertCharacter(string $char): void
    {
        $field = &$this->fields[$this->focusedFieldIndex];

        if (mb_strlen($field['value']) >= $field['maxLength'] && $char !== "\n") {
            return;
        }

        $before = mb_substr($field['value'], 0, $this->cursorPosition);
        $after = mb_substr($field['value'], $this->cursorPosition);

        $field['value'] = $before.$char.$after;
        $this->cursorPosition++;
    }

    private function deleteCharacterBefore(): void
    {
        if ($this->cursorPosition === 0) {
            return;
        }

        $field = &$this->fields[$this->focusedFieldIndex];
        $before = mb_substr($field['value'], 0, $this->cursorPosition - 1);
        $after = mb_substr($field['value'], $this->cursorPosition);

        $field['value'] = $before.$after;
        $this->cursorPosition--;
    }

    private function deleteCharacterAfter(): void
    {
        $field = &$this->fields[$this->focusedFieldIndex];
        $maxPos = mb_strlen($field['value']);

        if ($this->cursorPosition >= $maxPos) {
            return;
        }

        $before = mb_substr($field['value'], 0, $this->cursorPosition);
        $after = mb_substr($field['value'], $this->cursorPosition + 1);

        $field['value'] = $before.$after;
    }

    private function readInput(): string
    {
        $char = (string) fread(STDIN, 1);

        // Handle escape sequences
        if ($char === "\e") {
            stream_set_blocking(STDIN, false);
            $seq = (string) fread(STDIN, 5);
            stream_set_blocking(STDIN, true);

            if ($seq === '') {
                return "\e"; // Just Escape key
            }

            return "\e".$seq;
        }

        return $char;
    }

    private function enableRawMode(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            system('stty -icanon -echo');
        }
    }

    private function disableRawMode(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            system('stty icanon echo');
        }
    }
}
