<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

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

    <div class="border-t border-gray-800 pt-12 max-w-2xl">
        <h2 class="text-2xl font-semibold text-white mb-4">De kluis gaat binnenkort open voor de eerste pioniers.</h2>
        <p class="text-gray-400 mb-6">Laat je e-mailadres achter voor vroege toegang. We sturen geen wekelijkse nieuwsbrieven of marketing-spam. Je hoort van ons op de dag dat we live gaan.</p>

        <form class="flex flex-col sm:flex-row gap-3" onsubmit="event.preventDefault(); alert('Koppeling met e-mail provider volgt nog!');">
            <input type="email" placeholder="Jouw e-mailadres" required class="flex-grow bg-ypsiaPanel border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-colors">
            <button type="submit" class="bg-white text-black font-medium rounded-lg px-6 py-3 hover:bg-gray-200 transition-colors whitespace-nowrap">
                Hou mij op de hoogte
            </button>
        </form>
    </div>
</main>
<?php
ypsia_render('partials/footer.php', ['variant' => 'landing']);
ypsia_render('partials/document-end.php');
