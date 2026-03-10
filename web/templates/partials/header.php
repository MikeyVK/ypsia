<?php

declare(strict_types=1);

$variant = $variant ?? 'landing';
$showManifestMenuToggle = $showManifestMenuToggle ?? false;
?>
<?php if ($variant === 'manifest'): ?>
    <nav class="w-full border-b border-slate-800/60 bg-ypsiaBg/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-3xl mx-auto px-6 py-4 flex justify-between items-center gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <?php if ($showManifestMenuToggle): ?>
                    <button
                        type="button"
                        id="manifest-toc-toggle"
                        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-slate-950/80 text-white shadow-lg shadow-black/20 transition hover:border-white/35 hover:bg-slate-900/90 focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="Open inhoudsopgave"
                        aria-expanded="false"
                        aria-controls="manifest-toc-panel"
                    >
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="M5 7h14"></path>
                            <path d="M5 12h14"></path>
                            <path d="M5 17h14"></path>
                        </svg>
                    </button>
                <?php endif; ?>
                <a href="<?= ypsia_e(ypsia_page('index.php')) ?>" class="text-xl font-semibold tracking-tight text-white hover:text-ypsiaAccentLight transition-colors">Ypsia.</a>
            </div>
            <span class="text-xs font-medium tracking-wider text-ypsiaAccentLight uppercase bg-ypsiaAccent/10 px-3 py-1 rounded-full border border-ypsiaAccent/20">Manifest</span>
        </div>
    </nav>
<?php else: ?>
    <header class="w-full max-w-4xl mx-auto px-6 py-8 flex justify-between items-center">
        <a href="<?= ypsia_e(ypsia_page('index.php')) ?>" class="text-2xl font-semibold tracking-tight text-white hover:text-gray-200 transition-colors">Ypsia.</a>
    </header>
<?php endif; ?>
