<?php

declare(strict_types=1);

$source = dirname(__DIR__) . '/PANEL_FUNCTIONALITIES.md';
$output = sys_get_temp_dir() . '/travelomine-panel-functionalities.html';

$lines = file($source, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    throw new RuntimeException("Unable to read {$source}");
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function inlineMarkdown(string $value): string
{
    $value = esc($value);
    $value = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $value);
    $value = preg_replace('/`(.+?)`/', '<code>$1</code>', $value);

    return $value;
}

$body = '';
$inList = false;
$paragraph = [];

$flushParagraph = function () use (&$body, &$paragraph): void {
    if ($paragraph !== []) {
        $body .= '<p>' . inlineMarkdown(implode(' ', $paragraph)) . "</p>\n";
        $paragraph = [];
    }
};

$closeList = function () use (&$body, &$inList): void {
    if ($inList) {
        $body .= "</ul>\n";
        $inList = false;
    }
};

for ($i = 0, $count = count($lines); $i < $count; $i++) {
    $line = trim($lines[$i]);

    if ($line === '') {
        $flushParagraph();
        $closeList();
        continue;
    }

    if ($line === '---') {
        $flushParagraph();
        $closeList();
        $body .= "<hr>\n";
        continue;
    }

    if (preg_match('/^(#{1,3})\s+(.+)$/', $line, $matches)) {
        $flushParagraph();
        $closeList();
        $level = strlen($matches[1]);
        $body .= sprintf('<h%d>%s</h%d>', $level, inlineMarkdown($matches[2]), $level) . "\n";
        continue;
    }

    if (str_starts_with($line, '|') && isset($lines[$i + 1]) && preg_match('/^\|[\s|:\-]+\|$/', trim($lines[$i + 1]))) {
        $flushParagraph();
        $closeList();
        $headers = array_map('trim', array_filter(explode('|', trim($line, '|')), static fn ($cell) => $cell !== ''));
        $body .= "<table><thead><tr>";
        foreach ($headers as $header) {
            $body .= '<th>' . inlineMarkdown($header) . '</th>';
        }
        $body .= "</tr></thead><tbody>\n";
        $i += 2;
        while ($i < $count && str_starts_with(trim($lines[$i]), '|')) {
            $cells = array_map('trim', explode('|', trim(trim($lines[$i]), '|')));
            $body .= '<tr>';
            foreach ($cells as $cell) {
                $body .= '<td>' . inlineMarkdown($cell) . '</td>';
            }
            $body .= "</tr>\n";
            $i++;
        }
        $body .= "</tbody></table>\n";
        $i--;
        continue;
    }

    if (str_starts_with($line, '- ')) {
        $flushParagraph();
        if (! $inList) {
            $body .= "<ul>\n";
            $inList = true;
        }
        $body .= '<li>' . inlineMarkdown(substr($line, 2)) . "</li>\n";
        continue;
    }

    $paragraph[] = $line;
}

$flushParagraph();
$closeList();

$body = preg_replace(
    '/<h1>Travelomine Panel Functionality Document<\/h1>/',
    '<section class="cover"><div class="brand">TRAVELOMINE</div><h1>Panel Functionality Document</h1><p class="subtitle">Role-wise operational and administrative functionality</p><div class="cover-meta"><strong>Version:</strong> 1.0<br><strong>Reviewed:</strong> July 31, 2026<br><strong>Source:</strong> Current application routes, controllers, layouts, and views</div></section><div class="page-break"></div>',
    $body,
    1
);

$html = '<!doctype html><html><head><meta charset="UTF-8"><style>
@page { margin: 0.7in; }
body { color: #1f2937; font-family: Arial, Helvetica, sans-serif; font-size: 10.5pt; line-height: 1.45; }
.cover { text-align: center; padding-top: 150px; min-height: 650px; }
.brand { color: #147a8a; font-size: 15pt; font-weight: bold; letter-spacing: 3px; margin-bottom: 32px; }
.cover h1 { border: 0; color: #123b51; font-size: 30pt; margin: 0 0 18px; padding: 0; }
.subtitle { color: #64748b; font-size: 14pt; margin: 0 auto 80px; }
.cover-meta { border-top: 2px solid #20a4b7; color: #475569; display: inline-block; line-height: 1.8; padding-top: 18px; text-align: left; }
.page-break { page-break-after: always; }
h1 { border-bottom: 3px solid #20a4b7; color: #123b51; font-size: 22pt; margin: 0 0 18px; padding-bottom: 8px; }
h2 { border-bottom: 1px solid #b8dce2; color: #147a8a; font-size: 16pt; margin: 25px 0 10px; padding-bottom: 5px; page-break-after: avoid; }
h3 { color: #123b51; font-size: 12.5pt; margin: 17px 0 6px; page-break-after: avoid; }
p { margin: 5px 0 9px; }
ul { margin: 4px 0 12px 20px; padding-left: 8px; }
li { margin: 0 0 4px; }
table { border-collapse: collapse; margin: 12px 0 20px; width: 100%; }
th { background: #147a8a; border: 1px solid #0e6875; color: #fff; font-weight: bold; padding: 8px; text-align: left; }
td { border: 1px solid #b8c6cc; padding: 7px; vertical-align: top; }
tr:nth-child(even) td { background: #f1f7f8; }
code { background: #e7f1f3; color: #0f5965; font-family: Consolas, monospace; padding: 1px 3px; }
hr { border: 0; border-top: 1px solid #b8c6cc; margin: 24px 0; }
strong { color: #123b51; }
</style></head><body>' . $body . '</body></html>';

if (file_put_contents($output, $html) === false) {
    throw new RuntimeException("Unable to write {$output}");
}

echo $output;
