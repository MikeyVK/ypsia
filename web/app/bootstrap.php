<?php

declare(strict_types=1);

const YPSIA_ROOT = __DIR__ . '/..';
const YPSIA_PUBLIC_ROOT = YPSIA_ROOT . '/public_html';
const YPSIA_TEMPLATE_ROOT = YPSIA_ROOT . '/templates';

function ypsia_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function ypsia_asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

function ypsia_page(string $path): string
{
    return ltrim($path, '/');
}

function ypsia_render(string $template, array $vars = []): void
{
    $templatePath = YPSIA_TEMPLATE_ROOT . '/' . ltrim($template, '/');

    if (!is_file($templatePath)) {
        throw new RuntimeException(sprintf('Template not found: %s', $template));
    }

    extract($vars, EXTR_SKIP);
    require $templatePath;
}

function ypsia_starts_with(string $haystack, string $needle): bool
{
    return strpos($haystack, $needle) === 0;
}

function ypsia_slugify(string $value): string
{
    $ascii = strtr($value, [
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A', 'Å' => 'A',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ý' => 'Y', 'Ÿ' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
        'Ç' => 'C', 'ç' => 'c', 'Ñ' => 'N', 'ñ' => 'n',
    ]);

    $ascii = strtolower($ascii);
    $ascii = preg_replace('/[^a-z0-9]+/', '-', $ascii) ?? '';
    $ascii = trim($ascii, '-');

    return $ascii !== '' ? $ascii : 'section';
}

function ypsia_get_charter_sections(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $markdown = file_get_contents($path);
    if ($markdown === false) {
        return [];
    }

    $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($markdown)));
    $sections = [];
    $seen = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (preg_match('/^##\s+(.*)$/', $trimmed, $matches) !== 1) {
            continue;
        }

        $title = trim($matches[1]);
        $id = ypsia_slugify($title);
        if (isset($seen[$id])) {
            continue;
        }

        $seen[$id] = true;
        $sections[] = [
            'title' => $title,
            'id' => $id,
        ];
    }

    return $sections;
}
function ypsia_render_markdown_file(string $path): string
{
    if (!is_file($path)) {
        throw new RuntimeException(sprintf('Markdown file not found: %s', $path));
    }   

    $markdown = file_get_contents($path);

    if ($markdown === false) {
        throw new RuntimeException(sprintf('Failed to read markdown file: %s', $path));
    }

    return ypsia_render_markdown($markdown);
}

function ypsia_render_markdown(string $markdown): string
{
    $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
    $lines = explode("\n", $markdown);
    $html = [];
    $index = 0;
    $count = count($lines);

    while ($index < $count) {
        $trimmed = trim($lines[$index]);

        if ($trimmed === '' || $trimmed === '---') {
            $index++;
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $matches) === 1) {
            $level = strlen($matches[1]);
            $html[] = sprintf('<h%d>%s</h%d>', $level, ypsia_markdown_inline($matches[2]), $level);
            $index++;
            continue;
        }

        if (ypsia_starts_with($trimmed, '>')) {
            $quoteLines = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    $quoteLines[] = '';
                    $index++;
                    continue;
                }

                if (!ypsia_starts_with($candidate, '>')) {
                    break;
                }

                $quoteLines[] = ltrim(substr($candidate, 1));
                $index++;
            }

            $html[] = ypsia_markdown_quote($quoteLines);
            continue;
        }

        if (preg_match('/^-\s+/', $trimmed) === 1) {
            $items = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    $index++;
                    break;
                }

                if (preg_match('/^-\s+(.*)$/', $candidate, $matches) !== 1) {
                    break;
                }

                $items[] = '<li>' . ypsia_markdown_inline($matches[1]) . '</li>';
                $index++;
            }

            $html[] = '<ul>' . implode('', $items) . '</ul>';
            continue;
        }

        if (ypsia_starts_with($trimmed, '|')) {
            $tableLines = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    break;
                }

                if (!ypsia_starts_with($candidate, '|')) {
                    break;
                }

                $tableLines[] = $candidate;
                $index++;
            }

            $tableHtml = ypsia_markdown_table($tableLines);
            if ($tableHtml !== null) {
                $html[] = $tableHtml;
                continue;
            }
        }

        $paragraph = [$trimmed];
        $index++;

        while ($index < $count) {
            $candidate = trim($lines[$index]);
            if (
                $candidate === ''
                || $candidate === '---'
                || preg_match('/^(#{1,6})\s+/', $candidate) === 1
                || ypsia_starts_with($candidate, '>')
                || preg_match('/^-\s+/', $candidate) === 1
                || ypsia_starts_with($candidate, '|')
            ) {
                break;
            }

            $paragraph[] = $candidate;
            $index++;
        }

        $html[] = '<p>' . ypsia_markdown_inline(implode(' ', $paragraph)) . '</p>';
    }

    return implode("\n", $html);
}

function ypsia_markdown_quote(array $lines): string
{
    $paragraphs = [];
    $buffer = [];

    foreach ($lines as $line) {
        if ($line === '') {
            if ($buffer !== []) {
                $paragraphs[] = '<p>' . ypsia_markdown_inline(implode(' ', $buffer)) . '</p>';
                $buffer = [];
            }
            continue;
        }

        $buffer[] = $line;
    }

    if ($buffer !== []) {
        $paragraphs[] = '<p>' . ypsia_markdown_inline(implode(' ', $buffer)) . '</p>';
    }

    return '<blockquote>' . implode('', $paragraphs) . '</blockquote>';
}

function ypsia_markdown_table(array $lines)
{
    if (count($lines) < 2) {
        return null;
    }

    $header = ypsia_markdown_table_cells($lines[0]);
    $separator = ypsia_markdown_table_cells($lines[1]);

    if (count($header) === 0 || count($header) !== count($separator)) {
        return null;
    }

    foreach ($separator as $cell) {
        if (preg_match('/^:?-{3,}:?$/', trim($cell)) !== 1) {
            return null;
        }
    }

    $headerCells = implode('', array_map('ypsia_markdown_table_header_cell', $header));

    $rows = [];
    for ($rowIndex = 2, $rowCount = count($lines); $rowIndex < $rowCount; $rowIndex++) {
        $cells = ypsia_markdown_table_cells($lines[$rowIndex]);
        if (count($cells) !== count($header)) {
            return null;
        }

        $rows[] = '<tr>' . implode('', array_map('ypsia_markdown_table_body_cell', $cells)) . '</tr>';
    }

    return '<table><thead><tr>' . $headerCells . '</tr></thead><tbody>' . implode('', $rows) . '</tbody></table>';
}

function ypsia_markdown_table_cells(string $line): array
{
    $trimmed = trim(trim($line), '|');

    return array_map('trim', explode('|', $trimmed));
}

function ypsia_markdown_table_header_cell(string $cell): string
{
    return '<th>' . ypsia_markdown_inline($cell) . '</th>';
}

function ypsia_markdown_table_body_cell(string $cell): string
{
    return '<td>' . ypsia_markdown_inline($cell) . '</td>';
}

function ypsia_markdown_inline(string $text): string
{
    $escaped = ypsia_e($text);
    $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped) ?? $escaped;
    $escaped = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $escaped) ?? $escaped;
    $escaped = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped) ?? $escaped;

    return $escaped;
}

function ypsia_render_charter_file(string $path): string
{
    if (!is_file($path)) {
        throw new RuntimeException(sprintf('Markdown file not found: %s', $path));
    }

    $markdown = file_get_contents($path);

    if ($markdown === false) {
        throw new RuntimeException(sprintf('Failed to read markdown file: %s', $path));
    }

    return ypsia_render_charter_markdown($markdown);
}

function ypsia_render_charter_markdown(string $markdown): string
{
    $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
    $lines = explode("\n", $markdown);
    $html = [];
    $index = 0;
    $count = count($lines);
    $currentSection = '';
    $currentSubheading = '';

    while ($index < $count) {
        $trimmed = trim($lines[$index]);

        if ($trimmed === '' || $trimmed === '---') {
            $index++;
            continue;
        }

        if (preg_match('/^#\s+(.*)$/', $trimmed, $matches) === 1) {
            $title = $matches[1];
            $index++;
            $metaLines = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    $index++;
                    continue;
                }

                if (!ypsia_starts_with($candidate, '>')) {
                    break;
                }

                $metaLines[] = ltrim(substr($candidate, 1));
                $index++;
            }

            $html[] = ypsia_render_charter_header($title, $metaLines);
            continue;
        }

        if (preg_match('/^##\s+(.*)$/', $trimmed, $matches) === 1) {
            $currentSection = trim($matches[1]);
            $currentSubheading = '';
            $sectionId = ypsia_slugify($currentSection);

            if ($currentSection === 'Versiegeschiedenis') {
                $html[] = '<h2 id="' . ypsia_e($sectionId) . '" class="mt-16 scroll-mt-28">' . ypsia_markdown_inline($currentSection) . '</h2>';
            } else {
                $html[] = '<h2 id="' . ypsia_e($sectionId) . '" class="scroll-mt-28">' . ypsia_markdown_inline($currentSection) . '</h2>';
            }

            $index++;

            if ($currentSection === 'Principes' || $currentSection === 'Anti-Principes') {
                $intro = ypsia_collect_paragraph($lines, $index);
                if ($intro !== null && preg_match('/^\*(.+)\*$/u', $intro, $introMatches) === 1) {
                    $html[] = '<p class="text-slate-400 italic mb-8">' . ypsia_markdown_inline($introMatches[1]) . '</p>';
                }

                $cards = [];
                while ($index < $count) {
                    $candidate = trim($lines[$index]);
                    if ($candidate === '') {
                        $index++;
                        continue;
                    }
                    if (!ypsia_is_charter_card_title($candidate)) {
                        break;
                    }

                    $card = ypsia_parse_charter_card($lines, $index);
                    $cards[] = ypsia_render_charter_card_item($card['title'], $card['paragraphs']);
                }

                $html[] = '<ul class="not-prose space-y-6 list-none pl-0">' . implode('', $cards) . '</ul>';
            }

            continue;
        }

        if (preg_match('/^###\s+(.*)$/', $trimmed, $matches) === 1) {
            $currentSubheading = trim($matches[1]);
            $html[] = '<h3>' . ypsia_markdown_inline($currentSubheading) . '</h3>';
            $index++;

            if ($currentSubheading === 'Het Model') {
                $cards = [];
                while ($index < $count) {
                    $candidate = trim($lines[$index]);
                    if ($candidate === '') {
                        $index++;
                        continue;
                    }
                    if (preg_match('/^\*\*(Laag\s+\d+\s+.+)\*\*(.*)$/u', $candidate) !== 1) {
                        break;
                    }

                    $card = ypsia_parse_charter_card($lines, $index);
                    $cards[] = ypsia_render_charter_card_item($card['title'], $card['paragraphs']);
                }

                $html[] = '<ul class="not-prose space-y-6 list-none pl-0 mb-12">' . implode('', $cards) . '</ul>';
            }

            continue;
        }

        if (preg_match('/^\*\*(.+)\*\*$/u', $trimmed, $matches) === 1) {
            $heading = trim($matches[1]);
            $nextIndex = $index + 1;
            while ($nextIndex < $count && trim($lines[$nextIndex]) === '') {
                $nextIndex++;
            }

            if ($nextIndex < $count && preg_match('/^-\s+/', trim($lines[$nextIndex])) === 1) {
                $index = $nextIndex;
                $items = ypsia_parse_markdown_list($lines, $index);

                if ($heading === 'Wat dit betekent in de praktijk:') {
                    $html[] = ypsia_render_charter_list_panel($heading, $items, 'default');
                    continue;
                }

                if ($heading === 'Wat we NIET meten als succes:') {
                    while ($index < $count && trim($lines[$index]) === '') {
                        $index++;
                    }

                    $pairedHtml = '';
                    if ($index < $count && preg_match('/^\*\*(Wat we W(?:É|E)L meten:)\*\*$/u', trim($lines[$index]), $pairedMatches) === 1) {
                        $index++;
                        while ($index < $count && trim($lines[$index]) === '') {
                            $index++;
                        }
                        $pairedItems = ypsia_parse_markdown_list($lines, $index);
                        $pairedHtml = ypsia_render_charter_list_panel($pairedMatches[1], $pairedItems, 'wel');
                    }

                    $html[] = '<div class="not-prose my-10 space-y-6"><div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
                        . ypsia_render_charter_list_panel($heading, $items, 'niet')
                        . $pairedHtml
                        . '</div></div>';
                    continue;
                }

                $html[] = '<p><strong>' . ypsia_markdown_inline($heading) . '</strong></p>';
                $html[] = ypsia_render_charter_list($items, $currentSection, $currentSubheading);
                continue;
            }
        }

        if (ypsia_starts_with($trimmed, '>')) {
            $quoteLines = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    $quoteLines[] = '';
                    $index++;
                    continue;
                }

                if (!ypsia_starts_with($candidate, '>')) {
                    break;
                }

                $quoteLines[] = ltrim(substr($candidate, 1));
                $index++;
            }

            $html[] = ypsia_render_charter_quote($quoteLines, $currentSection, $currentSubheading);
            continue;
        }

        if (ypsia_starts_with($trimmed, '|')) {
            $tableLines = [];

            while ($index < $count) {
                $candidate = trim($lines[$index]);
                if ($candidate === '') {
                    break;
                }

                if (!ypsia_starts_with($candidate, '|')) {
                    break;
                }

                $tableLines[] = $candidate;
                $index++;
            }

            $tableHtml = ypsia_markdown_table($tableLines);
            if ($tableHtml !== null) {
                $html[] = ypsia_render_charter_table($tableHtml, $currentSection, $currentSubheading);
                continue;
            }
        }

        if (preg_match('/^-\s+/', $trimmed) === 1) {
            $items = ypsia_parse_markdown_list($lines, $index);
            $html[] = ypsia_render_charter_list($items, $currentSection, $currentSubheading);
            continue;
        }

        $paragraph = ypsia_collect_paragraph($lines, $index);
        if ($paragraph === null) {
            continue;
        }

        if (preg_match('/^\*(.+)\*$/u', $paragraph, $matches) === 1) {
            $html[] = '<p class="text-slate-400 italic mb-8">' . ypsia_markdown_inline($matches[1]) . '</p>';
            continue;
        }

        if (preg_match('/^\*\*De kern van het probleem:\*\*\s*(.+)$/u', $paragraph, $matches) === 1) {
            $html[] = '<p class="mt-6 p-4 bg-slate-800/50 rounded-lg border border-slate-700"><strong>De kern van het probleem:</strong> ' . ypsia_markdown_inline($matches[1]) . '</p>';
            continue;
        }

        if (preg_match('/^\*\*(Wat dit concreet betekent:)\*\*\s*(.+)$/u', $paragraph, $matches) === 1) {
            $html[] = '<p class="p-6 my-8 bg-ypsiaAccent/10 border border-ypsiaAccent/30 rounded-xl text-ypsiaAccentLight leading-relaxed"><strong>' . ypsia_markdown_inline($matches[1]) . '</strong> ' . ypsia_markdown_inline($matches[2]) . '</p>';
            continue;
        }

        if ($paragraph === 'Dat is het model. Niet perfect. Maar eerlijk.') {
            $html[] = '<p class="font-medium text-white text-xl mt-12 mb-16 text-center">' . ypsia_markdown_inline($paragraph) . '</p>';
            continue;
        }

        $html[] = '<p>' . ypsia_markdown_inline($paragraph) . '</p>';
    }

    return implode("\n", $html);
}

function ypsia_collect_paragraph(array $lines, int &$index)
{
    $count = count($lines);

    while ($index < $count && trim($lines[$index]) === '') {
        $index++;
    }

    if ($index >= $count) {
        return null;
    }

    $trimmed = trim($lines[$index]);
    if (
        $trimmed === '---'
        || preg_match('/^(#{1,6})\s+/', $trimmed) === 1
        || ypsia_starts_with($trimmed, '>')
        || ypsia_starts_with($trimmed, '|')
        || preg_match('/^-\s+/', $trimmed) === 1
    ) {
        return null;
    }

    $paragraph = [$trimmed];
    $index++;

    while ($index < $count) {
        $candidate = trim($lines[$index]);
        if (
            $candidate === ''
            || $candidate === '---'
            || preg_match('/^(#{1,6})\s+/', $candidate) === 1
            || ypsia_starts_with($candidate, '>')
            || ypsia_starts_with($candidate, '|')
            || preg_match('/^-\s+/', $candidate) === 1
        ) {
            break;
        }

        $paragraph[] = $candidate;
        $index++;
    }

    return implode(' ', $paragraph);
}

function ypsia_is_charter_card_title(string $line): bool
{
    return preg_match('/^\*\*(Principe\s+\d+\s+.+|Nooit:\s+.+|Laag\s+\d+\s+.+)\*\*/u', $line) === 1;
}

function ypsia_parse_charter_card(array $lines, int &$index): array
{
    $trimmed = trim($lines[$index]);
    preg_match('/^\*\*(Principe\s+\d+\s+.+|Nooit:\s+.+|Laag\s+\d+\s+.+)\*\*(.*)$/u', $trimmed, $matches);
    $title = trim($matches[1]);
    $lead = trim($matches[2]);
    $paragraphs = [];

    if ($lead !== '') {
        $paragraphs[] = $lead;
    }

    $index++;
    $count = count($lines);

    while ($index < $count) {
        $candidate = trim($lines[$index]);
        if ($candidate === '') {
            $index++;
            continue;
        }
        if (
            $candidate === '---'
            || preg_match('/^(#{1,6})\s+/', $candidate) === 1
            || ypsia_starts_with($candidate, '>')
            || ypsia_starts_with($candidate, '|')
            || preg_match('/^-\s+/', $candidate) === 1
            || ypsia_is_charter_card_title($candidate)
        ) {
            break;
        }

        $paragraph = [$candidate];
        $index++;

        while ($index < $count) {
            $continuation = trim($lines[$index]);
            if (
                $continuation === ''
                || $continuation === '---'
                || preg_match('/^(#{1,6})\s+/', $continuation) === 1
                || ypsia_starts_with($continuation, '>')
                || ypsia_starts_with($continuation, '|')
                || preg_match('/^-\s+/', $continuation) === 1
                || ypsia_is_charter_card_title($continuation)
            ) {
                break;
            }

            $paragraph[] = $continuation;
            $index++;
        }

        $paragraphs[] = implode(' ', $paragraph);
    }

    return [
        'title' => $title,
        'paragraphs' => $paragraphs,
    ];
}

function ypsia_parse_markdown_list(array $lines, int &$index): array
{
    $items = [];
    $count = count($lines);

    while ($index < $count) {
        $candidate = trim($lines[$index]);
        if ($candidate === '') {
            $index++;
            break;
        }

        if (preg_match('/^-\s+(.*)$/', $candidate, $matches) !== 1) {
            break;
        }

        $items[] = $matches[1];
        $index++;
    }

    return $items;
}

function ypsia_render_charter_header(string $title, array $metaLines): string
{
    $parts = [];
    foreach ($metaLines as $line) {
        if (preg_match('/^\*\*(.+?):\*\*\s*(.+)$/u', trim($line), $matches) === 1) {
            $label = $matches[1];
            $value = $matches[2];
            $valueHtml = ypsia_markdown_inline($value);

            if ($label === 'Status') {
                $valueHtml = '<strong class="text-emerald-400 font-medium flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>' . $valueHtml . '</strong>';
            } else {
                $valueHtml = '<strong class="text-white font-medium">' . $valueHtml . '</strong>';
            }

            $parts[] = '<li><span class="text-slate-500 block text-xs uppercase tracking-wider mb-1">' . ypsia_e($label) . '</span>' . $valueHtml . '</li>';
        }
    }

    return '<header class="mb-16 not-prose">'
        . '<h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight leading-tight mb-8">' . ypsia_markdown_inline($title) . '</h1>'
        . '<div class="bg-ypsiaPanel border border-slate-700/50 rounded-xl p-6 shadow-lg shadow-black/20">'
        . '<ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm list-none p-0 m-0">' . implode('', $parts) . '</ul>'
        . '</div>'
        . '</header>';
}

function ypsia_render_charter_card_item(string $title, array $paragraphs): string
{
    $containerClass = 'bg-ypsiaPanel p-6 rounded-xl border border-slate-700/50';
    $titleClass = 'text-white text-lg block mb-2';
    $titleHtml = ypsia_markdown_inline($title);

    if (strpos($title, 'Nooit:') === 0) {
        $containerClass .= ' border-l-4 border-l-rose-500';
        $titleClass = 'text-rose-400 text-lg block mb-2';
    } elseif (strpos($title, 'Laag 2 ') === 0) {
        $containerClass .= ' border-l-4 border-l-ypsiaAccent shadow-lg shadow-ypsiaAccent/10';
        $titleClass = 'text-white block mb-2 text-lg';
        $titleHtml = ypsia_render_charter_layer_title($title, true);
    } elseif (strpos($title, 'Laag ') === 0) {
        $titleClass = 'text-white block mb-2 text-lg';
        $titleHtml = ypsia_render_charter_layer_title($title, false);
    } else {
        $containerClass .= ' border-l-4 border-l-ypsiaAccent';
        $titleClass = 'text-white text-lg block mb-2 text-ypsiaAccentLight';
    }

    $body = [];
    foreach ($paragraphs as $idx => $paragraph) {
        $marginClass = $idx === count($paragraphs) - 1 ? 'mb-0' : 'mb-3';
        $body[] = '<p class="' . $marginClass . ' text-slate-300 leading-relaxed">' . ypsia_markdown_inline($paragraph) . '</p>';
    }

    return '<li class="' . $containerClass . '"><strong class="' . $titleClass . '">' . $titleHtml . '</strong>' . implode('', $body) . '</li>';
}

function ypsia_render_charter_layer_title(string $title, bool $highlightSubtitle): string
{
    if (preg_match('/^(Laag\s+\d+\s+.+?)\s+(\(.+\))$/u', $title, $matches) !== 1) {
        return ypsia_markdown_inline($title);
    }

    $subtitleClass = $highlightSubtitle
        ? 'text-ypsiaAccentLight font-normal text-sm ml-2'
        : 'text-slate-400 font-normal text-sm ml-2';

    return ypsia_markdown_inline($matches[1])
        . ' <span class="' . $subtitleClass . '">' . ypsia_markdown_inline($matches[2]) . '</span>';
}

function ypsia_render_charter_list_panel(string $heading, array $items, string $variant): string
{
    $panelClass = 'bg-ypsiaPanel border border-slate-700/50 p-6 rounded-xl';
    $headingClass = 'text-white block mb-4 text-lg';
    $listClass = 'text-sm space-y-3 mb-0 pl-0 list-none text-slate-300';
    $iconHtml = '&bull;';
    $iconClass = 'text-slate-500 mt-0.5';
    $gapClass = 'gap-3';

    if ($variant === 'niet') {
        $panelClass = 'bg-rose-900/20 border border-rose-500/30 p-6 rounded-xl';
        $headingClass = 'text-rose-400 block mb-4 text-lg';
        $listClass = 'text-sm space-y-3 mb-0 pl-0 list-none text-rose-100/80';
        $iconHtml = '&#10007;';
        $iconClass = 'text-rose-500';
        $gapClass = 'gap-2';
    } elseif ($variant === 'wel') {
        $panelClass = 'bg-emerald-900/20 border border-emerald-500/30 p-6 rounded-xl';
        $headingClass = 'text-emerald-400 block mb-4 text-lg';
        $listClass = 'text-sm space-y-3 mb-0 pl-0 list-none text-emerald-100/80';
        $iconHtml = '&#10003;';
        $iconClass = 'text-emerald-500';
        $gapClass = 'gap-2';
    }

    $renderedItems = [];
    foreach ($items as $item) {
        $itemHtml = ypsia_markdown_inline($item);
        if ($variant === 'default') {
            if (strpos($item, 'gigantisch succes') !== false) {
                $itemHtml = preg_replace('/<strong>(.*?)<\/strong>/', '<span class="text-emerald-400 font-medium">$1</span>', $itemHtml, 1) ?? $itemHtml;
            } elseif (strpos($item, 'een mislukking') !== false) {
                $itemHtml = preg_replace('/<strong>(.*?)<\/strong>/', '<span class="text-rose-400 font-medium">$1</span>', $itemHtml, 1) ?? $itemHtml;
            } elseif (strpos($item, 'het beste dat ons kan overkomen') !== false) {
                $itemHtml = preg_replace('/<strong>(.*?)<\/strong>/', '<span class="text-ypsiaAccentLight font-medium">$1</span>', $itemHtml, 1) ?? $itemHtml;
            }
        }

        $renderedItems[] = '<li class="flex ' . $gapClass . '"><span class="' . $iconClass . '">' . $iconHtml . '</span><span>' . $itemHtml . '</span></li>';
    }

    return '<div class="' . $panelClass . '"><strong class="' . $headingClass . '">' . ypsia_markdown_inline($heading) . '</strong><ul class="' . $listClass . '">' . implode('', $renderedItems) . '</ul></div>';
}

function ypsia_render_charter_quote(array $quoteLines, string $section, string $subheading): string
{
    $textLines = [];
    foreach ($quoteLines as $line) {
        if ($line !== '') {
            $textLines[] = $line;
        }
    }

    $quoteText = implode(' ', $textLines);

    if ($section === 'Doel') {
        return '<blockquote class="border-l-4 border-ypsiaAccent bg-ypsiaAccent/5 text-white p-6 rounded-r-xl italic my-8 shadow-inner text-lg">' . ypsia_markdown_inline($quoteText) . '</blockquote>';
    }

    if ($section === 'De Noordster') {
        return '<blockquote class="border-l-4 border-ypsiaAccent bg-ypsiaAccent/5 text-white p-6 rounded-r-xl italic my-8 shadow-inner text-xl font-medium">' . ypsia_markdown_inline($quoteText) . '</blockquote>';
    }

    if (strpos($quoteText, 'De belofte in ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©n zin:') === 0) {
        return '<blockquote class="border-l-4 border-ypsiaAccent bg-ypsiaAccent/5 text-white p-6 rounded-r-xl italic my-8 shadow-inner font-medium">' . ypsia_markdown_inline($quoteText) . '</blockquote>';
    }

    $paragraphs = [];
    $buffer = [];
    foreach ($quoteLines as $line) {
        if ($line === '') {
            if ($buffer !== []) {
                $paragraphs[] = '<p class="mb-4">' . ypsia_markdown_inline(implode(' ', $buffer)) . '</p>';
                $buffer = [];
            }
            continue;
        }

        $buffer[] = $line;
    }

    if ($buffer !== []) {
        $paragraphs[] = '<p class="mb-0">' . ypsia_markdown_inline(implode(' ', $buffer)) . '</p>';
    }

    return '<blockquote class="border-l-4 border-ypsiaAccent bg-ypsiaAccent/5 text-white p-6 rounded-r-xl italic my-8 shadow-inner">' . implode('', $paragraphs) . '</blockquote>';
}

function ypsia_render_charter_table(string $tableHtml, string $section, string $subheading): string
{
    $tableHtml = str_replace('<table>', '<table class="w-full text-left border-collapse bg-ypsiaPanel rounded-lg overflow-hidden border border-slate-700">', $tableHtml);
    $tableHtml = str_replace('<thead>', '<thead class="bg-slate-800 border-b border-slate-700">', $tableHtml);
    $tableHtml = str_replace('<tbody>', '<tbody class="divide-y divide-slate-700/50">', $tableHtml);
    $tableHtml = str_replace('<th>', '<th class="p-4 text-white font-medium">', $tableHtml);
    $tableHtml = str_replace('<td>', '<td class="p-4 text-slate-300">', $tableHtml);

    if ($section === 'Versiegeschiedenis') {
        $tableHtml = str_replace('<table class="w-full text-left border-collapse bg-ypsiaPanel rounded-lg overflow-hidden border border-slate-700">', '<table class="w-full text-left text-sm border-collapse bg-ypsiaPanel rounded-lg overflow-hidden border border-slate-700">', $tableHtml);
        $tableHtml = preg_replace('/<th class="p-4 text-white font-medium">Versie<\/th>/', '<th class="p-4 text-white font-medium w-24">Versie</th>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<th class="p-4 text-white font-medium">Datum<\/th>/', '<th class="p-4 text-white font-medium w-32">Datum</th>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-300">1\.0<\/td>/', '<td class="p-4 text-slate-300 font-mono">1.0</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-300">2026\-03\-08<\/td>/', '<td class="p-4 text-slate-400">2026-03-08</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-300">/', '<td class="p-4 text-slate-300 leading-relaxed">', $tableHtml, 1) ?? $tableHtml;
    } elseif ($subheading === 'Het verschil met alternatieven:') {
        $tableHtml = str_replace('<td class="p-4 text-slate-300">', '<td class="p-4 text-slate-400">', $tableHtml);
        $tableHtml = preg_replace('/<td class="p-4 text-slate-400">Garmin Connect \/ Oura \/ Strava<\/td>/', '<td class="p-4 text-slate-300">Garmin Connect / Oura / Strava</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-400">Apple Health \/ Google Health<\/td>/', '<td class="p-4 text-slate-300">Apple Health / Google Health</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-400">ChatGPT \/ AI\-assistenten<\/td>/', '<td class="p-4 text-slate-300">ChatGPT / AI-assistenten</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-400">Therapie \/ coaching<\/td>/', '<td class="p-4 text-slate-300">Therapie / coaching</td>', $tableHtml, 1) ?? $tableHtml;
        $tableHtml = preg_replace('/<td class="p-4 text-slate-400">Niets doen<\/td>/', '<td class="p-4 text-slate-300">Niets doen</td>', $tableHtml, 1) ?? $tableHtml;
    }

    return '<div class="not-prose overflow-x-auto my-8">' . $tableHtml . '</div>';
}

function ypsia_render_charter_list(array $items, string $section, string $subheading): string
{
    $renderedItems = [];
    foreach ($items as $item) {
        $itemHtml = ypsia_markdown_inline($item);
        if ($section === 'Het Probleem') {
            $itemHtml = preg_replace('/^<strong>(.*?)<\/strong>\s*/', '<strong>$1</strong><br>', $itemHtml, 1) ?? $itemHtml;
        }

        $renderedItems[] = '<li>' . $itemHtml . '</li>';
    }

    if ($section === 'Het Probleem') {
        return '<ul class="space-y-4">' . implode('', $renderedItems) . '</ul>';
    }

    if ($subheading === 'Wat we nooit doen voor geld') {
        return '<ul class="mb-8">' . implode('', $renderedItems) . '</ul>';
    }


    return '<ul>' . implode('', $renderedItems) . '</ul>';
}
