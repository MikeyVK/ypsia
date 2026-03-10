<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$charterPath = YPSIA_ROOT . '/app/content/CHARTER.md';
$charterHtml = ypsia_render_charter_file($charterPath);
$charterSections = ypsia_get_charter_sections($charterPath);

$hasCharterSections = $charterSections !== [];

ypsia_render('partials/document-start.php', [
    'pageTitle' => 'Grondwet van Ypsia (Founding Charter)',
    'pageDescription' => 'De grondwet van Ypsia: missie, visie, principes en businessmodel.',
    'bodyClass' => 'bg-ypsiaBg text-slate-300 font-sans antialiased min-h-screen selection:bg-ypsiaAccent selection:text-white',
]);
?>
<div id="page-top"></div>
<?php

ypsia_render('partials/header.php', [
    'variant' => 'manifest',
    'showManifestMenuToggle' => $hasCharterSections,
]);
?>
<?php if ($hasCharterSections): ?>
    <div id="manifest-toc-backdrop" class="fixed inset-0 z-30 hidden bg-slate-950/35 backdrop-blur-sm"></div>

    <aside
        id="manifest-toc-panel"
        class="fixed left-4 top-24 md:left-6 md:top-28 z-40 hidden w-[min(19rem,calc(100vw-2rem))] rounded-2xl border border-slate-700/60 bg-slate-950/95 p-4 shadow-2xl shadow-black/40 backdrop-blur-md"
        aria-label="Inhoudsopgave"
    >
        <div class="mb-3 flex items-center justify-between gap-4">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Inhoud</p>
            <button
                type="button"
                id="manifest-toc-close"
                class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-white/10 text-white transition hover:border-white/25 hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-white/40"
                aria-label="Sluit inhoudsopgave"
            >
                <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <nav class="max-h-[70vh] overflow-y-auto pr-1">
            <ul class="space-y-1 text-sm">
                <?php foreach ($charterSections as $section): ?>
                    <li>
                        <a
                            href="#<?= ypsia_e($section['id']) ?>"
                            class="block rounded-xl px-3 py-2 text-slate-200 transition hover:bg-white/5 hover:text-white"
                            data-toc-link="true"
                        >
                            <?= ypsia_e($section['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>
<?php endif; ?>
<main id="top" class="max-w-3xl mx-auto px-6 py-12 md:py-16">
    <article class="prose prose-invert max-w-none prose-headings:text-white prose-headings:font-semibold prose-headings:tracking-tight prose-h2:text-2xl prose-h2:mt-16 prose-h2:mb-6 prose-h2:pb-2 prose-h2:border-b prose-h2:border-slate-800 prose-h3:text-xl prose-h3:mt-10 prose-h3:mb-4 prose-p:text-slate-300 prose-p:leading-relaxed prose-p:mb-6 prose-strong:text-white prose-strong:font-medium prose-ul:list-disc prose-ul:pl-6 prose-ul:mb-6 prose-ul:space-y-2 prose-li:text-slate-300">
        <?= $charterHtml ?>
    </article>
</main>
<a
    href="#page-top"
    class="fixed bottom-5 right-4 md:bottom-6 md:right-6 z-30 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/15 bg-slate-950/80 text-white shadow-lg shadow-black/30 backdrop-blur-md transition hover:border-white/35 hover:bg-slate-900/90 focus:outline-none focus:ring-2 focus:ring-white/50"
    aria-label="Terug naar boven"
>
    <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 19V5"></path>
        <path d="M6 11l6-6 6 6"></path>
    </svg>
</a>
<?php if ($hasCharterSections): ?>
    <script>
        (function () {
            var toggle = document.getElementById('manifest-toc-toggle');
            var panel = document.getElementById('manifest-toc-panel');
            var backdrop = document.getElementById('manifest-toc-backdrop');
            var close = document.getElementById('manifest-toc-close');
            var links = document.querySelectorAll('[data-toc-link="true"]');

            if (!toggle || !panel || !backdrop || !close) {
                return;
            }

            function setOpen(isOpen) {
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                panel.classList.toggle('hidden', !isOpen);
                backdrop.classList.toggle('hidden', !isOpen);
                document.body.classList.toggle('overflow-hidden', isOpen);
            }

            toggle.addEventListener('click', function () {
                setOpen(toggle.getAttribute('aria-expanded') !== 'true');
            });

            close.addEventListener('click', function () {
                setOpen(false);
            });

            backdrop.addEventListener('click', function () {
                setOpen(false);
            });

            links.forEach(function (link) {
                link.addEventListener('click', function () {
                    setOpen(false);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });
        })();
    </script>
<?php endif; ?>
<?php
ypsia_render('partials/footer.php', ['variant' => 'manifest']);
ypsia_render('partials/document-end.php');
