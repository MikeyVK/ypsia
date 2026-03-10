<?php

declare(strict_types=1);

$variant = $variant ?? 'landing';
?>
<?php if ($variant === 'manifest'): ?>
    <footer class="w-full border-t border-slate-800/60 mt-12 bg-ypsiaBg">
        <div class="max-w-3xl mx-auto px-6 py-8 flex justify-between items-center text-sm text-slate-500">
            <p>&copy; 2026 Ypsia. Alle rechten voorbehouden.</p>
        </div>
    </footer>
<?php else: ?>
    <footer class="w-full border-t border-gray-800 mt-12">
        <div class="max-w-4xl mx-auto px-6 py-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
            <p>&copy; 2026 Ypsia.</p>
            <a href="<?= ypsia_e(ypsia_page('charter.php')) ?>" class="hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Lees ons Founding Charter
            </a>
        </div>
    </footer>
<?php endif; ?>
