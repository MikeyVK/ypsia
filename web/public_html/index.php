<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$waitlistStatus = isset($_GET['waitlist']) ? (string) $_GET['waitlist'] : '';

ypsia_render('partials/document-start.php', [
    'pageTitle' => 'Ypsia | Jouw data spreekt voor zich',
    'pageDescription' => 'Ypsia bouwt een eerlijk alternatief voor persoonlijke data: inzicht zonder engagementmodel of data-handel.',
    'bodyClass' => 'bg-ypsiaDark text-gray-300 font-sans antialiased min-h-screen flex flex-col selection:bg-gray-700 selection:text-white',
]);

ypsia_render('partials/header.php', ['variant' => 'landing']);
?>
<main id="top" class="flex-grow w-full max-w-4xl mx-auto px-6 py-12 md:py-20 flex flex-col justify-center">
    <div class="max-w-3xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold text-white tracking-tight leading-tight mb-6">
            Jouw data spreekt voor zich. <br>
            <span class="text-gray-500">Binnenkort tenminste.</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-400 leading-relaxed mb-10 max-w-2xl font-light">
            Wij bouwen de plek waar mensen zichzelf eerlijk kunnen begrijpen — in een wereld die daar actief op tegen werkt.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        <div>
            <p class="text-gray-400 leading-relaxed mb-4">
                Je verzamelt meer persoonlijke data dan ooit. Slaap, beweging, hartslag, focus. Maar de platforms die deze data beheren, zijn gebouwd om je aandacht te oogsten. Ze tonen je streaks en scores om je binnen te houden, geen verbanden om je verder te helpen.
            </p>
            <p class="text-gray-400 leading-relaxed">
                Ypsia bouwt het alternatief. Eén zwaarbeveiligde kluis waar al jouw silo-data samenkomt. Geen oordeel, geen positiviteitsfilter en absoluut geen data-handel. Alleen de naakte waarheid over jouw eigen basislijn, geanalyseerd door AI.
            </p>
        </div>

        <div class="bg-ypsiaPanel rounded-xl p-8 border border-gray-800">
            <ul class="space-y-6">
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-gray-500 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <div>
                        <strong class="text-white block mb-1">Jij bezit de sleutel</strong>
                        <span class="text-sm text-gray-400">Open-source architectuur. Wij kunnen jouw data niet lezen.</span>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-gray-500 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <div>
                        <strong class="text-white block mb-1">Geen engagement-model</strong>
                        <span class="text-sm text-gray-400">Wij optimaliseren op inzicht, niet op jouw schermtijd.</span>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-gray-500 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                    <div>
                        <strong class="text-white block mb-1">Eerlijkheid boven comfort</strong>
                        <span class="text-sm text-gray-400">De data toont wat de data zegt. Niets meer, niets minder.</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <section class="mb-12 max-w-2xl">
        <a href="<?= ypsia_e(ypsia_page('charter.php')) ?>" class="group block rounded-2xl border border-slate-800 bg-slate-900/40 p-5 transition hover:border-slate-700 hover:bg-slate-900/60">
            <div class="flex items-start gap-4">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-700 bg-slate-900 text-slate-200 transition group-hover:border-slate-500 group-hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 mb-2">Nieuw hier?</p>
                    <p class="text-white font-medium mb-1">Lees eerst het founding charter</p>
                    <p class="text-sm text-gray-400">Een rustige introductie tot eigenaarschap, eerlijkheid en waarom Ypsia bewust geen engagementmodel bouwt.</p>
                </div>
            </div>
        </a>
    </section>

    <section id="waitlist" class="border-t border-gray-800 pt-12 max-w-2xl scroll-mt-24">
        <h2 class="text-2xl font-semibold text-white mb-4">De kluis gaat binnenkort open voor de eerste pioniers.</h2>
        <p class="text-gray-400 mb-6">Laat je e-mailadres achter voor vroege toegang. We sturen geen wekelijkse nieuwsbrieven of marketing-spam. Je hoort van ons op de dag dat we live gaan.</p>

        <form action="<?= ypsia_e(ypsia_page('forms/waitlist.php')) ?>" method="post" class="flex flex-col gap-4">
            <input type="hidden" name="action" value="subscribe">
            <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                <label for="website">Laat dit veld leeg als je een mens bent</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <input type="email" name="email" placeholder="Jouw e-mailadres" required autocomplete="email" class="flex-grow bg-ypsiaPanel border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-colors">
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="interest" placeholder="Welke data wil je combineren? (bijv. slaap, Garmin)" maxlength="240" class="flex-grow bg-ypsiaPanel border border-gray-700 text-white text-sm rounded-lg px-4 py-3 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-colors">
                <button type="submit" class="bg-white text-black font-medium rounded-lg px-6 py-3 hover:bg-gray-200 transition-colors whitespace-nowrap">
                    Hou mij op de hoogte
                </button>
            </div>

            <?php if ($waitlistStatus === 'success'): ?>
                <p class="text-emerald-400 text-sm mt-1">Dank je. Je staat op de lijst. We mailen pas als we live zijn.</p>
            <?php elseif ($waitlistStatus === 'already'): ?>
                <p class="text-slate-300 text-sm mt-1">Dit e-mailadres staat al op de wachtlijst. Je hoeft niets meer te doen.</p>
            <?php elseif ($waitlistStatus === 'error'): ?>
                <p class="text-rose-400 text-sm mt-1">Er ging iets mis met je inschrijving. Controleer je e-mailadres en probeer het opnieuw.</p>
            <?php endif; ?>
        </form>

        <div class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/30 p-5">
            <p class="text-sm text-slate-400 mb-3">Sta je al op de lijst, of wil je juist niet meer op de wachtlijst staan? Verwijderen moet net zo eenvoudig zijn.</p>
            <form action="<?= ypsia_e(ypsia_page('forms/waitlist.php')) ?>" method="post" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="action" value="unsubscribe">
                <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label for="website-unsubscribe">Laat dit veld leeg als je een mens bent</label>
                    <input type="text" name="website" id="website-unsubscribe" tabindex="-1" autocomplete="off">
                </div>
                <input type="email" name="email" placeholder="Jouw e-mailadres om te verwijderen" required autocomplete="email" class="flex-grow bg-ypsiaPanel border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-colors">
                <button type="submit" class="border border-slate-600 text-slate-200 font-medium rounded-lg px-6 py-3 hover:border-slate-400 hover:text-white transition-colors whitespace-nowrap">
                    Verwijder mij van de lijst
                </button>
            </form>

            <?php if ($waitlistStatus === 'removed'): ?>
                <p class="text-emerald-400 text-sm mt-3">Als dit e-mailadres op de wachtlijst stond, is het nu verwijderd.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
ypsia_render('partials/footer.php', ['variant' => 'landing']);
ypsia_render('partials/document-end.php');
