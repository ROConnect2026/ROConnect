<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ROConnect | Translation Engine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #050505;
            --surface: rgba(25, 25, 25, 0.72);
            --surface-soft: rgba(255, 255, 255, 0.06);
            --surface-strong: rgba(255, 255, 255, 0.1);
            --text: #f4f4f5;
            --muted: #c9c9cf;
            --line: rgba(255, 255, 255, 0.18);
            --accent: #0066ff;
            --accent-strong: #0052cc;
            --ok: #3ddc97;
            --warn: #ffbf47;
            --err: #ff6b6b;
            --shadow: 0 18px 56px rgba(0, 0, 0, 0.5);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Poppins", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 6%, rgba(0, 102, 255, 0.22) 0%, transparent 35%),
                radial-gradient(circle at 85% 16%, rgba(255, 255, 255, 0.08) 0%, transparent 30%),
                radial-gradient(circle at 50% 100%, rgba(0, 102, 255, 0.14) 0%, transparent 50%),
                var(--bg);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.08) 1px, transparent 0);
            background-size: 40px 40px;
            opacity: 0.28;
            z-index: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 16px 44px;
            position: relative;
            z-index: 1;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: var(--shadow);
            padding: 22px;
            margin-bottom: 18px;
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(1.7rem, 3vw, 2.35rem);
            letter-spacing: -0.03em;
        }

        p {
            margin: 0;
            color: var(--muted);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .title-wrap p {
            max-width: 720px;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .home-link {
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            padding: 9px 15px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.06);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .home-link:hover {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.13);
        }

        .small-btn {
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 0.78rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 800;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .small-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.38);
        }

        label {
            display: block;
            margin: 0 0 6px;
            font-weight: 700;
            font-size: 0.74rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #d4d4d8;
        }

        textarea,
        button {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 11px;
            font-size: 0.98rem;
            font-family: inherit;
            color: var(--text);
            background: rgba(255, 255, 255, 0.05);
        }

        textarea {
            min-height: 220px;
            resize: vertical;
            line-height: 1.5;
            color: #ffffff;
        }

        .selector-row {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 14px;
            align-items: end;
            margin-bottom: 14px;
        }

        .lang-wrap {
            position: relative;
        }

        .lang-trigger {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            color: var(--text);
            font-size: 0.95rem;
        }

        .lang-trigger:hover {
            border-color: rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.09);
        }

        .lang-left {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }

        .flag {
            width: 20px;
            height: 14px;
            border-radius: 3px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            object-fit: cover;
            flex-shrink: 0;
            display: inline-block;
        }

        .flag-fallback {
            font-size: 0.92rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .lang-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lang-code {
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .menu {
            position: absolute;
            z-index: 40;
            width: 100%;
            margin-top: 6px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(18, 18, 18, 0.95);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.5);
            padding: 8px;
            display: none;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .menu.open {
            display: block;
        }

        .menu-search {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 8px 10px;
            margin-bottom: 8px;
            font-size: 0.88rem;
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }

        .menu-list {
            max-height: 230px;
            overflow: auto;
            display: grid;
            gap: 4px;
        }

        .menu-item {
            border: 1px solid transparent;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.02);
            padding: 8px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            color: var(--text);
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--line);
        }

        .menu-item.active {
            background: rgba(0, 102, 255, 0.18);
            border-color: rgba(0, 102, 255, 0.4);
        }

        .swap-btn {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            cursor: pointer;
            padding: 0;
        }

        .btn-accent {
            background: var(--accent);
            border: 0;
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.15s ease;
        }

        .btn-accent:hover {
            background: var(--accent-strong);
            transform: translateY(-1px);
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
        }

        .copy-btn {
            width: auto;
            padding: 8px 12px;
            font-size: 0.84rem;
            margin: 0;
            background: var(--accent);
            color: #fff;
            border: 0;
        }

        .copy-btn:hover {
            background: var(--accent-strong);
        }

        .clear-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--line);
            color: #ffffff;
        }

        .clear-btn:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .panel-title {
            margin: 0 0 8px;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 800;
            color: #e4e4e7;
        }

        .state {
            margin-top: 10px;
            padding: 9px 10px;
            border-radius: 10px;
            font-size: 0.92rem;
        }

        .state.ok { background: #e8f8ef; color: var(--ok); }
        .state.warn { background: #fff4e0; color: var(--warn); }
        .state.error { background: #ffe8e8; color: var(--err); }

        .state.ok,
        .state.warn,
        .state.error {
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .usage-panel {
            margin-top: 12px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
        }

        .usage-title {
            margin: 0 0 10px;
            font-size: 0.92rem;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .usage-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .usage-item {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            padding: 10px;
        }

        .usage-item span {
            display: block;
            font-size: 0.74rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .usage-item strong {
            display: block;
            margin-top: 4px;
            font-size: 1rem;
            color: #ffffff;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-top: 8px;
        }

        .meta div {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .meta strong {
            display: block;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .output-box {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: Consolas, "Courier New", monospace;
            min-height: 220px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(10, 10, 10, 0.72);
            padding: 12px;
        }

        .textarea-actions {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .grid-two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .gauge-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .gauge-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
        }

        .gauge-title {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .gauge-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .gauge {
            --pct: 0;
            width: 74px;
            height: 74px;
            border-radius: 999px;
            background: conic-gradient(var(--accent) calc(var(--pct) * 1%), rgba(255, 255, 255, 0.18) 0);
            position: relative;
            flex-shrink: 0;
        }

        .gauge::after {
            content: "";
            position: absolute;
            inset: 8px;
            border-radius: 999px;
            background: rgba(10, 10, 10, 0.88);
        }

        .gauge-label {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--text);
            z-index: 2;
        }

        .gauge-info p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .gauge-info strong {
            display: block;
            margin-top: 3px;
            font-size: 0.95rem;
            color: var(--text);
        }

        .muted {
            color: var(--muted);
            font-size: 0.88rem;
        }

        .status-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 600;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #9fb4c8;
        }

        .dot.loading { background: var(--accent); }
        .dot.success { background: var(--ok); }
        .dot.error { background: var(--err); }

        .pulse {
            animation: pulse 0.9s linear infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.45; }
            50% { opacity: 1; }
            100% { opacity: 0.45; }
        }

        @media (max-width: 760px) {
            .selector-row,
            .meta,
            .grid-two,
            .usage-grid,
            .gauge-grid {
                grid-template-columns: 1fr;
            }

            .swap-btn {
                width: 100%;
                border-radius: 14px;
                height: 40px;
            }

            .card {
                border-radius: 22px;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <div class="title-wrap">
                <h1>Translation Engine</h1>
                <p>Live multilingual translation that follows the same ROConnect visual language and interaction flow.</p>
            </div>
            <div class="toolbar">
                <button id="clear-input" class="small-btn" type="button">Clear Input</button>
                <a class="home-link" href="{{ route('home') }}">Back to Home</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="selector-row">
            <div class="lang-wrap">
                <label for="source_language">Source Language</label>
                <button id="source-trigger" class="lang-trigger" type="button"></button>
                <div id="source-menu" class="menu">
                    <input id="source-search" class="menu-search" type="text" placeholder="Search source language..." />
                    <div id="source-list" class="menu-list"></div>
                </div>
            </div>
            <button id="swap-languages" class="swap-btn" type="button" title="Swap languages">⇄</button>
            <div class="lang-wrap">
                <label for="target_language">Target Language</label>
                <button id="target-trigger" class="lang-trigger" type="button"></button>
                <div id="target-menu" class="menu">
                    <input id="target-search" class="menu-search" type="text" placeholder="Search target language..." />
                    <div id="target-list" class="menu-list"></div>
                </div>
            </div>
        </div>

        <div class="grid-two">
            <div>
                <h2 class="panel-title">Input</h2>
                <textarea id="text" name="text" placeholder="Start typing... translation appears automatically"></textarea>
                <div class="textarea-actions">
                    <button id="focus-input" class="copy-btn clear-btn" type="button">Focus</button>
                </div>
            </div>
            <div>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:8px;">
                    <h2 class="panel-title" style="margin:0;">Translated Output</h2>
                    <button id="copy-translation" class="copy-btn btn-accent" type="button">Copy</button>
                </div>
                <div id="result-panel" class="output-box">No translation yet.</div>
            </div>
        </div>

        <div class="status-row">
            <span class="status-chip"><span id="status-dot" class="dot"></span><span id="status-label">Idle</span></span>
            <span id="loading-state" class="muted" hidden>Translating...</span>
            <span id="debounce-label" class="muted">Debounce: 400ms</span>
        </div>

        <div id="form-state" class="state" hidden></div>
        <div id="error-panel" class="state" hidden></div>

        <h2 class="panel-title" style="margin-top: 14px;">Stats & Metadata</h2>
        <div class="meta">
            <div><strong>Status</strong><span id="meta-status">-</span></div>
            <div><strong>Engine</strong><span id="meta-engine">-</span></div>
            <div><strong>Translation Time</strong><span id="meta-duration">-</span></div>
            <div><strong>Provider HTTP</strong><span id="meta-http-status">-</span></div>
            <div><strong>Source Language</strong><span id="meta-source-language">-</span></div>
            <div><strong>Target Language</strong><span id="meta-target-language">-</span></div>
            <div><strong>Character Count</strong><span id="meta-original-chars">-</span></div>
            <div><strong>Translated Characters</strong><span id="meta-translated-chars">-</span></div>
            <div><strong>Total Success (local)</strong><span id="meta-total-success">-</span></div>
            <div><strong>Total Characters (local)</strong><span id="meta-total-chars">-</span></div>
            <div><strong>Last Duration (local)</strong><span id="meta-last-duration">-</span></div>
            <div><strong>Last Translated At</strong><span id="meta-last-at">-</span></div>
        </div>

        <div class="gauge-grid">
            <div class="gauge-card">
                <h3 class="gauge-title">Character Usage</h3>
                <div class="gauge-wrap">
                    <div class="gauge" id="gauge-characters"><span class="gauge-label" id="gauge-characters-label">0%</span></div>
                    <div class="gauge-info">
                        <p>Input size against per-request limit</p>
                        <strong id="gauge-characters-text">0 / {{ (int) config('services.translation.max_text_length', 5000) }}</strong>
                    </div>
                </div>
            </div>
            <div class="gauge-card">
                <h3 class="gauge-title">Response Speed</h3>
                <div class="gauge-wrap">
                    <div class="gauge" id="gauge-speed"><span class="gauge-label" id="gauge-speed-label">0%</span></div>
                    <div class="gauge-info">
                        <p>Relative to 2000ms latency budget</p>
                        <strong id="gauge-speed-text">0 ms</strong>
                    </div>
                </div>
            </div>
            <div class="gauge-card">
                <h3 class="gauge-title">Quota State</h3>
                <div class="gauge-wrap">
                    <div class="gauge" id="gauge-quota"><span class="gauge-label" id="gauge-quota-label">-</span></div>
                    <div class="gauge-info">
                        <p>Based on provider usage payload</p>
                        <strong id="gauge-quota-text">No quota metric yet</strong>
                    </div>
                </div>
            </div>
        </div>

        <section id="usage-panel" class="usage-panel" hidden>
            <h3 class="usage-title">Provider Usage Metadata</h3>
            <div id="usage-grid" class="usage-grid"></div>
        </section>
    </div>
</div>

<script>
    const textInput = document.getElementById('text');
    const clearInputButton = document.getElementById('clear-input');
    const focusInputButton = document.getElementById('focus-input');

    const sourceTrigger = document.getElementById('source-trigger');
    const sourceMenu = document.getElementById('source-menu');
    const sourceSearch = document.getElementById('source-search');
    const sourceList = document.getElementById('source-list');

    const targetTrigger = document.getElementById('target-trigger');
    const targetMenu = document.getElementById('target-menu');
    const targetSearch = document.getElementById('target-search');
    const targetList = document.getElementById('target-list');

    const swapButton = document.getElementById('swap-languages');
    const copyButton = document.getElementById('copy-translation');
    const loadingState = document.getElementById('loading-state');
    const statusLabel = document.getElementById('status-label');
    const statusDot = document.getElementById('status-dot');

    const resultPanel = document.getElementById('result-panel');
    const errorPanel = document.getElementById('error-panel');
    const formState = document.getElementById('form-state');
    const usagePanel = document.getElementById('usage-panel');
    const usageGrid = document.getElementById('usage-grid');

    const metaEngine = document.getElementById('meta-engine');
    const metaDuration = document.getElementById('meta-duration');
    const metaStatus = document.getElementById('meta-status');
    const metaHttpStatus = document.getElementById('meta-http-status');
    const metaSourceLanguage = document.getElementById('meta-source-language');
    const metaTargetLanguage = document.getElementById('meta-target-language');
    const metaOriginalChars = document.getElementById('meta-original-chars');
    const metaTranslatedChars = document.getElementById('meta-translated-chars');
    const metaTotalSuccess = document.getElementById('meta-total-success');
    const metaTotalChars = document.getElementById('meta-total-chars');
    const metaLastDuration = document.getElementById('meta-last-duration');
    const metaLastAt = document.getElementById('meta-last-at');

    const gaugeCharacters = document.getElementById('gauge-characters');
    const gaugeCharactersLabel = document.getElementById('gauge-characters-label');
    const gaugeCharactersText = document.getElementById('gauge-characters-text');

    const gaugeSpeed = document.getElementById('gauge-speed');
    const gaugeSpeedLabel = document.getElementById('gauge-speed-label');
    const gaugeSpeedText = document.getElementById('gauge-speed-text');

    const gaugeQuota = document.getElementById('gauge-quota');
    const gaugeQuotaLabel = document.getElementById('gauge-quota-label');
    const gaugeQuotaText = document.getElementById('gauge-quota-text');

    const debounceMs = 400;
    let debounceTimer = null;
    let activeRequestController = null;
    let lastPayloadFingerprint = '';
    let latestRequestId = 0;
    let lastGoodTranslation = 'No translation yet.';
    let quotaBaseline = null;
    const languageStorageKey = 'translation.languages.cache.v3';
    const languageStorageTtlMs = 24 * 60 * 60 * 1000;
    const maxTextLength = {{ (int) config('services.translation.max_text_length', 5000) }};

    const uiStats = {
        totalRequests: 0,
        averageResponseMs: 0,
    };

    const state = {
        languages: [],
        selectedSource: 'auto',
        selectedTarget: '',
    };

    const languageToCountry = {
        aa: 'et', ab: 'ge', af: 'za', ak: 'gh', am: 'et', ar: 'sa', as: 'in', az: 'az',
        be: 'by', bg: 'bg', bn: 'bd', bs: 'ba', ca: 'es', cs: 'cz', cy: 'gb', da: 'dk',
        de: 'de', el: 'gr', en: 'us', es: 'es', et: 'ee', eu: 'es', fa: 'ir', fi: 'fi',
        fr: 'fr', ga: 'ie', gl: 'es', gu: 'in', he: 'il', hi: 'in', hr: 'hr', hu: 'hu',
        hy: 'am', id: 'id', is: 'is', it: 'it', ja: 'jp', ka: 'ge', kk: 'kz', ko: 'kr',
        lt: 'lt', lv: 'lv', mk: 'mk', ml: 'in', mr: 'in', ms: 'my', nb: 'no', ne: 'np',
        nl: 'nl', nn: 'no', pa: 'in', pl: 'pl', pt: 'pt', ro: 'ro', ru: 'ru', sk: 'sk',
        sl: 'si', sq: 'al', sr: 'rs', sv: 'se', sw: 'tz', ta: 'in', te: 'in', th: 'th',
        tr: 'tr', uk: 'ua', ur: 'pk', uz: 'uz', vi: 'vn', zh: 'cn'
    };

    const setStatus = (label, type = 'idle') => {
        statusLabel.textContent = label;
        statusDot.className = 'dot';

        if (type === 'loading') {
            statusDot.classList.add('loading');
            return;
        }

        if (type === 'success') {
            statusDot.classList.add('success');
            return;
        }

        if (type === 'error') {
            statusDot.classList.add('error');
        }
    };

    const showState = (element, text, type) => {
        element.textContent = text;
        element.className = `state ${type}`;
        element.hidden = false;
    };

    const hideState = (element) => {
        element.hidden = true;
        element.textContent = '';
    };

    const toggleLoading = (isLoading) => {
        loadingState.hidden = !isLoading;
        swapButton.disabled = isLoading;

        if (isLoading) {
            loadingState.classList.add('pulse');
        } else {
            loadingState.classList.remove('pulse');
        }
    };

    const formatValue = (value, fallback = '-') => {
        if (value === null || value === undefined || value === '') {
            return fallback;
        }

        return value;
    };

    const updateGauge = (node, labelNode, percent, textOverride = null) => {
        node.style.setProperty('--pct', percent);
        labelNode.textContent = textOverride ?? `${percent}%`;
    };

    const updateMeta = (data) => {
        metaStatus.textContent = formatValue(data.status);
        metaEngine.textContent = formatValue(data.engine);
        metaDuration.textContent = `${formatValue(data.duration_ms, 0)} ms`;
        metaHttpStatus.textContent = formatValue(data.provider_http_status);
        metaSourceLanguage.textContent = formatValue(data.source_language);
        metaTargetLanguage.textContent = formatValue(data.target_language);
        metaOriginalChars.textContent = formatValue(data.original_character_count);
        metaTranslatedChars.textContent = formatValue(data.translated_character_count);

        const localStats = data.local_stats || {};

        uiStats.totalRequests += 1;
        const duration = Number(data.duration_ms || 0);
        if (duration > 0) {
            uiStats.averageResponseMs = uiStats.averageResponseMs === 0
                ? duration
                : Math.round(((uiStats.averageResponseMs * (uiStats.totalRequests - 1)) + duration) / uiStats.totalRequests);
        }

        metaTotalSuccess.textContent = formatValue(localStats.total_successful_translations, 0);
        metaTotalChars.textContent = formatValue(localStats.total_translated_characters, 0);
        metaLastDuration.textContent = `${formatValue(localStats.last_translation_duration_ms, '-')}${localStats.last_translation_duration_ms !== null && localStats.last_translation_duration_ms !== undefined ? ' ms' : ''} · avg ${uiStats.averageResponseMs || '-'} ms`;
        metaLastAt.textContent = formatValue(localStats.last_translated_at);

        updateGauge(gaugeCharacters, gaugeCharactersLabel, Math.min(100, Math.round(((data.original_character_count || 0) / maxTextLength) * 100)));
        gaugeCharactersText.textContent = `${data.original_character_count || 0} / ${maxTextLength}`;

        const speedPct = Math.min(100, Math.round(((data.duration_ms || 0) / 2000) * 100));
        updateGauge(gaugeSpeed, gaugeSpeedLabel, speedPct);
        gaugeSpeedText.textContent = `${data.duration_ms || 0} ms`;

        const usage = data.usage || null;

        if (!usage || Object.keys(usage).length === 0) {
            usagePanel.hidden = true;
            usageGrid.innerHTML = '';
            updateGauge(gaugeQuota, gaugeQuotaLabel, 0, '-');
            gaugeQuotaText.textContent = 'No quota metric yet';
            return;
        }

        const labels = {
            character_count: 'Character Count',
            credits_remaining: 'Credits Remaining',
            translation_time: 'Translation Time (s)',
            daily_limit: 'Daily Limit',
            remaining: 'Remaining',
            limit: 'Limit',
        };

        usageGrid.innerHTML = '';

        Object.entries(usage).forEach(([key, value]) => {
            const item = document.createElement('div');
            item.className = 'usage-item';

            const label = document.createElement('span');
            label.textContent = labels[key] || key.replace(/_/g, ' ');

            const numberValue = typeof value === 'number' ? value : Number(value);
            const formatted = Number.isFinite(numberValue)
                ? new Intl.NumberFormat().format(numberValue)
                : String(value);

            const valueNode = document.createElement('strong');
            valueNode.textContent = formatted;

            item.appendChild(label);
            item.appendChild(valueNode);
            usageGrid.appendChild(item);
        });

        const quotaLimit = Number(usage.daily_limit ?? usage.limit ?? 0);
        const quotaRemaining = Number(usage.credits_remaining ?? usage.remaining ?? 0);
        const hasCreditsRemaining = Number.isFinite(quotaRemaining) && quotaRemaining >= 0;

        if (quotaLimit > 0 && quotaRemaining >= 0) {
            const quotaPct = Math.max(0, Math.min(100, Math.round((quotaRemaining / quotaLimit) * 100)));
            updateGauge(gaugeQuota, gaugeQuotaLabel, quotaPct);
            gaugeQuotaText.textContent = `${quotaRemaining} remaining of ${quotaLimit}`;
        } else if (hasCreditsRemaining) {
            // Use the first seen credits value as a lightweight session baseline when limit is unavailable.
            if (quotaBaseline === null || quotaRemaining > quotaBaseline) {
                quotaBaseline = quotaRemaining;
            }

            const fallbackPct = quotaBaseline > 0
                ? Math.max(0, Math.min(100, Math.round((quotaRemaining / quotaBaseline) * 100)))
                : 0;

            updateGauge(gaugeQuota, gaugeQuotaLabel, fallbackPct);
            gaugeQuotaText.textContent = `${quotaRemaining} credits remaining`;
        } else {
            updateGauge(gaugeQuota, gaugeQuotaLabel, 0, '-');
            gaugeQuotaText.textContent = 'Provider did not return quota limit';
        }

        usagePanel.hidden = false;
    };

    const languageVisual = (language) => {
        if (!language || !language.code) {
            return {
                html: '<span class="flag-fallback">🌐</span>',
                name: 'Auto Detect',
                code: 'auto',
            };
        }

        const countryCode = (() => {
            if (typeof language.country_code === 'string' && language.country_code.length === 2) {
                return language.country_code.toLowerCase();
            }

            const parts = String(language.code).toLowerCase().split('-');

            if (parts.length > 1 && parts[1].length === 2) {
                return parts[1];
            }

            return languageToCountry[parts[0]] || '';
        })();

        const hasCountryCode = countryCode.length === 2;
        const emoji = language.flag_emoji || language.icon || '🌐';
        const flagUrl = hasCountryCode
            ? `https://flagcdn.com/24x18/${countryCode}.png`
            : '';

        return {
            html: hasCountryCode
                ? `<img class="flag" src="${flagUrl}" alt="${language.code} flag" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"><span class="flag-fallback" style="display:none">${emoji}</span>`
                : `<span class="flag-fallback">${emoji}</span>`,
            name: language.name || language.code,
            code: language.code,
        };
    };

    const renderTrigger = (trigger, selectedCode) => {
        if (selectedCode === 'auto') {
            trigger.innerHTML = `
                <span class="lang-left">
                    <span class="flag-fallback">🌐</span>
                    <span class="lang-name">Auto Detect</span>
                </span>
                <span class="lang-code">AUTO</span>
            `;
            return;
        }

        const lang = state.languages.find((item) => item.code === selectedCode);
        const visual = languageVisual(lang);

        trigger.innerHTML = `
            <span class="lang-left">${visual.html}<span class="lang-name">${visual.name}</span></span>
            <span class="lang-code">${visual.code.toUpperCase()}</span>
        `;
    };

    const renderList = (listNode, query, selectedCode, includeAuto = false) => {
        const q = query.trim().toLowerCase();
        listNode.innerHTML = '';

        const source = includeAuto
            ? [{ code: 'auto', name: 'Auto Detect', flag_emoji: '🌐', flag_url: '' }, ...state.languages]
            : [...state.languages];

        const filtered = source.filter((lang) => {
            const haystack = `${lang.name} ${lang.code}`.toLowerCase();

            return haystack.includes(q);
        });

        filtered.forEach((lang) => {
            const visual = languageVisual(lang);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `menu-item ${lang.code === selectedCode ? 'active' : ''}`;
            button.innerHTML = `
                <span class="lang-left">${visual.html}<span class="lang-name">${visual.name}</span></span>
                <span class="lang-code">${visual.code.toUpperCase()}</span>
            `;
            button.dataset.code = lang.code;
            listNode.appendChild(button);
        });
    };

    const openMenu = (menu) => {
        document.querySelectorAll('.menu').forEach((item) => item.classList.remove('open'));
        menu.classList.add('open');
    };

    const closeMenus = () => {
        sourceMenu.classList.remove('open');
        targetMenu.classList.remove('open');
    };

    const translateWhenReady = () => {
        if (!state.selectedTarget) {
            return;
        }

        debounceTranslate();
    };

    const clearForEmptyInput = () => {
        hideState(errorPanel);
        hideState(formState);
        usagePanel.hidden = true;
        usageGrid.innerHTML = '';
        setStatus('Idle');

        metaStatus.textContent = '-';
        metaDuration.textContent = '-';
        metaSourceLanguage.textContent = '-';
        metaTargetLanguage.textContent = formatValue(state.selectedTarget);
        metaOriginalChars.textContent = '0';
        metaTranslatedChars.textContent = '0';
        metaLastDuration.textContent = '-';
        metaLastAt.textContent = '-';

        updateGauge(gaugeCharacters, gaugeCharactersLabel, 0);
        updateGauge(gaugeSpeed, gaugeSpeedLabel, 0);
        updateGauge(gaugeQuota, gaugeQuotaLabel, 0, '-');
        gaugeQuotaText.textContent = 'No quota metric yet';

        resultPanel.textContent = lastGoodTranslation;
    };

    const setLanguages = (languages) => {
        state.languages = languages;
        state.selectedSource = 'auto';
        state.selectedTarget = languages[0]?.code || '';

        renderTrigger(sourceTrigger, state.selectedSource);
        renderTrigger(targetTrigger, state.selectedTarget);

        renderList(sourceList, '', state.selectedSource, true);
        renderList(targetList, '', state.selectedTarget, false);
    };

    const readCachedLanguages = () => {
        try {
            const raw = localStorage.getItem(languageStorageKey);

            if (!raw) {
                return null;
            }

            const parsed = JSON.parse(raw);
            const valid = Date.now() - parsed.cached_at < languageStorageTtlMs;

            return valid ? parsed.languages : null;
        } catch {
            return null;
        }
    };

    const cacheLanguages = (languages) => {
        try {
            localStorage.setItem(languageStorageKey, JSON.stringify({
                cached_at: Date.now(),
                languages,
            }));
        } catch {
            // Non-blocking if storage is unavailable.
        }
    };

    const populateLanguages = async () => {
        const cached = readCachedLanguages();

        if (Array.isArray(cached) && cached.length > 0) {
            setLanguages(cached);
            setStatus('Ready', 'success');
        }

        try {
            const response = await fetch('/api/translation-languages', { headers: { 'Accept': 'application/json' } });
            const payload = await response.json();

            if (!Array.isArray(payload.languages) || payload.languages.length === 0) {
                if (!cached || cached.length === 0) {
                    setLanguages([{ code: 'en', name: 'English', flag_emoji: '🌐', flag_url: '' }]);
                }

                showState(formState, 'Could not load language list from provider. Using fallback list.', 'warn');
                return;
            }

            cacheLanguages(payload.languages);
            setLanguages(payload.languages);

            setStatus('Ready', 'success');
            hideState(formState);
        } catch (error) {
            if (!cached || cached.length === 0) {
                setLanguages([{ code: 'en', name: 'English', flag_emoji: '🌐', flag_url: '' }]);
            }

            showState(formState, 'Language list request failed. Using cached/fallback options.', 'warn');
            setStatus('Limited', 'error');
        }
    };

    const translateNow = async () => {
        const text = textInput.value;

        if (text.trim() === '') {
            clearForEmptyInput();
            return;
        }

        hideState(errorPanel);
        hideState(formState);
        usagePanel.hidden = true;
        usageGrid.innerHTML = '';
        toggleLoading(true);
        setStatus('Translating...', 'loading');

        const payload = {
            text,
            target_language: state.selectedTarget,
            source_language: state.selectedSource || 'auto',
        };

        const payloadFingerprint = JSON.stringify(payload);

        if (payloadFingerprint === lastPayloadFingerprint) {
            toggleLoading(false);
            return;
        }

        lastPayloadFingerprint = payloadFingerprint;

        if (activeRequestController) {
            activeRequestController.abort();
        }

        activeRequestController = new AbortController();
        const currentRequestId = ++latestRequestId;

        try {
            const response = await fetch('/api/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
                signal: activeRequestController.signal,
            });

            if (currentRequestId !== latestRequestId) {
                return;
            }

            const data = await response.json();

            if (!response.ok) {
                const validationMessage = data.message || 'Request failed.';
                const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                showState(errorPanel, firstError || validationMessage, 'error');
                setStatus('Error', 'error');
                return;
            }

            resultPanel.textContent = data.translated_text || '';
            lastGoodTranslation = resultPanel.textContent;
            updateMeta(data);

            if (data.status === 'fallback') {
                const quotaMessage = data.error === 'provider_quota_exceeded' || data.provider_http_status === 402
                    ? 'Provider quota reached (402). Original text returned as fallback.'
                    : 'Provider failed. Original text returned as fallback.';

                showState(errorPanel, quotaMessage, 'warn');
                setStatus('Fallback', 'error');
            } else {
                showState(formState, 'Translated in real time.', 'ok');
                setStatus('Up to date', 'success');
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            showState(errorPanel, 'Network issue: showing latest available translation.', 'error');
            setStatus('Network issue', 'error');
        } finally {
            if (currentRequestId === latestRequestId) {
                toggleLoading(false);
            }
        }
    };

    const debounceTranslate = () => {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            translateNow();
        }, debounceMs);
    };

    textInput.addEventListener('input', debounceTranslate);

    swapButton.addEventListener('click', () => {
        const source = state.selectedSource;
        const target = state.selectedTarget;

        if (!source || source === 'auto') {
            showState(formState, 'Select a specific source language to swap.', 'warn');
            return;
        }

        state.selectedSource = target;
        state.selectedTarget = source;

        renderTrigger(sourceTrigger, state.selectedSource);
        renderTrigger(targetTrigger, state.selectedTarget);
        renderList(sourceList, sourceSearch.value, state.selectedSource, true);
        renderList(targetList, targetSearch.value, state.selectedTarget, false);
        translateWhenReady();
    });

    clearInputButton.addEventListener('click', () => {
        textInput.value = '';
        resultPanel.textContent = 'No translation yet.';
        lastGoodTranslation = 'No translation yet.';
        lastPayloadFingerprint = '';
        clearForEmptyInput();
    });

    focusInputButton.addEventListener('click', () => {
        textInput.focus();
    });

    sourceTrigger.addEventListener('click', () => {
        openMenu(sourceMenu);
        sourceSearch.focus();
    });

    targetTrigger.addEventListener('click', () => {
        openMenu(targetMenu);
        targetSearch.focus();
    });

    sourceSearch.addEventListener('input', () => {
        renderList(sourceList, sourceSearch.value, state.selectedSource, true);
    });

    targetSearch.addEventListener('input', () => {
        renderList(targetList, targetSearch.value, state.selectedTarget, false);
    });

    sourceList.addEventListener('click', (event) => {
        const item = event.target.closest('.menu-item');
        if (!item) {
            return;
        }

        state.selectedSource = item.dataset.code;
        renderTrigger(sourceTrigger, state.selectedSource);
        renderList(sourceList, sourceSearch.value, state.selectedSource, true);
        closeMenus();
        translateWhenReady();
    });

    targetList.addEventListener('click', (event) => {
        const item = event.target.closest('.menu-item');
        if (!item) {
            return;
        }

        state.selectedTarget = item.dataset.code;
        renderTrigger(targetTrigger, state.selectedTarget);
        renderList(targetList, targetSearch.value, state.selectedTarget, false);
        closeMenus();
        translateWhenReady();
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.lang-wrap')) {
            closeMenus();
        }
    });

    copyButton.addEventListener('click', async () => {
        const text = resultPanel.textContent || '';

        if (text.trim() === '' || text === 'No translation yet.') {
            showState(formState, 'Nothing to copy yet.', 'warn');
            return;
        }

        try {
            await navigator.clipboard.writeText(text);
            showState(formState, 'Translated text copied.', 'ok');
        } catch (error) {
            showState(errorPanel, 'Copy failed. Please copy manually.', 'error');
        }
    });

    updateGauge(gaugeCharacters, gaugeCharactersLabel, 0);
    updateGauge(gaugeSpeed, gaugeSpeedLabel, 0);
    updateGauge(gaugeQuota, gaugeQuotaLabel, 0, '-');

    populateLanguages();
</script>
</body>
</html>
