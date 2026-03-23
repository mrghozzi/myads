@php
    $layout = in_array($linkLayout ?? 'stacked', ['wide', 'stacked', 'compact'], true) ? $linkLayout : 'stacked';
@endphp
<style>
.myads-link-smart,
.myads-link-smart * {
    box-sizing: border-box;
}

.myads-link-smart {
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    border: 1px solid #e7eaff;
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(180deg, #ffffff 0%, #f7f8ff 100%);
    box-shadow: 0 18px 38px rgba(94, 92, 154, 0.12);
    font-family: "Open Sans", Arial, sans-serif;
    color: #2f3552;
}

.myads-link-smart[data-layout="stacked"] {
    max-width: 540px;
}

.myads-link-smart[data-layout="compact"] {
    max-width: 360px;
    border-radius: 16px;
}

.myads-link-smart__header,
.myads-link-smart__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
}

.myads-link-smart__header {
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    color: #ffffff;
}

.myads-link-smart__footer {
    border-top: 1px solid rgba(97, 93, 250, 0.12);
    background: rgba(255, 255, 255, 0.82);
}

.myads-link-smart__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.myads-link-smart__badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffffff;
    opacity: 0.85;
}

.myads-link-smart__report {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
}

.myads-link-smart__report img {
    width: 14px;
    height: 14px;
}

.myads-link-smart__body {
    padding: 16px;
}

.myads-link-smart__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
}

.myads-link-smart[data-layout="wide"] .myads-link-smart__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.myads-link-smart__card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 168px;
    padding: 16px;
    border: 1px solid #edf0ff;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 10px 26px rgba(94, 92, 154, 0.08);
}

.myads-link-smart__card::before {
    content: "";
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 4px;
    border-radius: 18px 18px 0 0;
    background: linear-gradient(90deg, #615dfa 0%, #23d2e2 100%);
}

.myads-link-smart__card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.myads-link-smart__pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 999px;
    background: rgba(97, 93, 250, 0.1);
    color: #615dfa;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.myads-link-smart__index {
    color: #9aa1c5;
    font-size: 12px;
    font-weight: 700;
}

.myads-link-smart__title {
    margin: 0;
    font-size: 18px;
    line-height: 1.3;
    font-weight: 800;
}

.myads-link-smart__title a {
    color: #2f3552;
    text-decoration: none;
}

.myads-link-smart__title a:hover {
    color: #615dfa;
}

.myads-link-smart__text {
    margin: 0;
    color: #5d6488;
    font-size: 13px;
    line-height: 1.65;
}

.myads-link-smart__meta {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px solid #eff2ff;
}

.myads-link-smart__byline {
    color: #8c93b5;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.myads-link-smart__byline a,
.myads-link-smart__footer a {
    color: #615dfa;
    text-decoration: none;
}

.myads-link-smart__cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 12px;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    text-decoration: none;
}

.myads-link-smart__cta-arrow {
    font-size: 12px;
    line-height: 1;
}

.myads-link-smart__footer-link {
    color: #7c84ab;
    font-size: 11px;
    font-weight: 700;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__header,
.myads-link-smart[data-layout="compact"] .myads-link-smart__footer {
    padding: 10px 12px;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__body {
    padding: 12px;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__card {
    min-height: 0;
    padding: 14px;
    border-radius: 14px;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__title {
    font-size: 16px;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__text {
    font-size: 12px;
    line-height: 1.55;
}

.myads-link-smart[data-layout="compact"] .myads-link-smart__meta {
    flex-direction: column;
    align-items: flex-start;
}
</style>
<div class="myads-link-smart" data-layout="{{ $layout }}">
    <div class="myads-link-smart__header">
        <span class="myads-link-smart__badge">
            <span class="myads-link-smart__badge-dot"></span>
            {{ config('app.name') }}
        </span>
        <a class="myads-link-smart__report" href="{{ url('/report') }}?link={{ $link1->id }}&link2={{ $link2->id }}" target="_blank" rel="noopener noreferrer">
            <img src="{{ asset('themes/default/assets/img/Alert-icon.png') }}" alt="">
            Report
        </a>
    </div>

    <div class="myads-link-smart__body">
        <div class="myads-link-smart__grid">
            <article class="myads-link-smart__card">
                <div class="myads-link-smart__card-head">
                    <span class="myads-link-smart__pill">Sponsored</span>
                    <span class="myads-link-smart__index">01</span>
                </div>
                <h3 class="myads-link-smart__title">
                    <a href="{{ route('ads.redirect', ['link' => $link1->id, 'clik' => $publisherId, 'type' => 'link']) }}" target="_blank" rel="noopener noreferrer">{!! htmlentities($link1Name, ENT_QUOTES, 'UTF-8') !!}</a>
                </h3>
                <p class="myads-link-smart__text">{!! htmlentities($link1Txt, ENT_QUOTES, 'UTF-8') !!}</p>
                <div class="myads-link-smart__meta">
                    <span class="myads-link-smart__byline">Ads by <a href="{{ url('/') }}?ref={{ $publisherId }}" target="_blank" rel="noopener noreferrer">{{ config('app.name') }}</a></span>
                    <a class="myads-link-smart__cta" href="{{ route('ads.redirect', ['link' => $link1->id, 'clik' => $publisherId, 'type' => 'link']) }}" target="_blank" rel="noopener noreferrer">
                        Open Ad
                        <span class="myads-link-smart__cta-arrow">></span>
                    </a>
                </div>
            </article>

            <article class="myads-link-smart__card">
                <div class="myads-link-smart__card-head">
                    <span class="myads-link-smart__pill">Sponsored</span>
                    <span class="myads-link-smart__index">02</span>
                </div>
                <h3 class="myads-link-smart__title">
                    <a href="{{ route('ads.redirect', ['link' => $link2->id, 'clik' => $publisherId, 'type' => 'link']) }}" target="_blank" rel="noopener noreferrer">{!! htmlentities($link2Name, ENT_QUOTES, 'UTF-8') !!}</a>
                </h3>
                <p class="myads-link-smart__text">{!! htmlentities($link2Txt, ENT_QUOTES, 'UTF-8') !!}</p>
                <div class="myads-link-smart__meta">
                    <span class="myads-link-smart__byline">Ads by <a href="{{ url('/') }}?ref={{ $publisherId }}" target="_blank" rel="noopener noreferrer">{{ config('app.name') }}</a></span>
                    <a class="myads-link-smart__cta" href="{{ route('ads.redirect', ['link' => $link2->id, 'clik' => $publisherId, 'type' => 'link']) }}" target="_blank" rel="noopener noreferrer">
                        Open Ad
                        <span class="myads-link-smart__cta-arrow">></span>
                    </a>
                </div>
            </article>
        </div>
    </div>

    <div class="myads-link-smart__footer">
        <a class="myads-link-smart__footer-link" href="{{ url('/') }}?ref={{ $publisherId }}" target="_blank" rel="noopener noreferrer">Ads by {{ config('app.name') }}</a>
    </div>
</div>
