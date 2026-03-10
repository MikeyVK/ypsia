<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Ypsia';
$pageDescription = $pageDescription ?? '';
$bodyClass = $bodyClass ?? 'bg-ypsiaDark text-gray-300 font-sans antialiased min-h-screen';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ypsia_e($pageTitle) ?></title>
<?php if ($pageDescription !== ''): ?>
    <meta name="description" content="<?= ypsia_e($pageDescription) ?>">
<?php endif; ?>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ypsia_e(ypsia_asset('css/site.css')) ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        ypsiaDark: '#0f172a',
                        ypsiaBg: '#0f172a',
                        ypsiaPanel: '#1e293b',
                        ypsiaAccent: '#6366f1',
                        ypsiaAccentLight: '#818cf8',
                    }
                }
            }
        }
    </script>
</head>
<body class="<?= ypsia_e($bodyClass) ?>">
