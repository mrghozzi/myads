<?php

namespace App\Services\CustomAds;

use App\Models\CustomAdCreative;
use App\Models\CustomAdDeal;
use App\Models\CustomAdEvent;
use App\Models\CustomAdPlacement;
use App\Support\AdsSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomAdServingService
{
    public function selectDeal(CustomAdPlacement $placement): ?CustomAdDeal
    {
        if (!$placement->isActive()) {
            return null;
        }

        $now = now();

        return CustomAdDeal::query()
            ->with('creative')
            ->where('placement_id', $placement->id)
            ->where('status', CustomAdDeal::STATUS_ACTIVE)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->whereHas('creative', function ($query) {
                $query->where('status', CustomAdCreative::STATUS_APPROVED);
            })
            ->inRandomOrder()
            ->first();
    }

    public function recordImpression(
        CustomAdPlacement $placement,
        CustomAdDeal $deal,
        CustomAdCreative $creative,
        Request $request,
        string $countryCode,
        string $deviceType
    ): CustomAdEvent {
        return $this->recordEvent($placement, $deal, $creative, CustomAdEvent::TYPE_IMPRESSION, $request, $countryCode, $deviceType);
    }

    public function recordClick(
        CustomAdCreative $creative,
        Request $request,
        string $countryCode,
        string $deviceType
    ): ?CustomAdEvent {
        $deal = $creative->deal()->with('placement')->first();
        $placement = $deal?->placement;

        if (!$deal || !$placement) {
            return null;
        }

        return $this->recordEvent($placement, $deal, $creative, CustomAdEvent::TYPE_CLICK, $request, $countryCode, $deviceType);
    }

    public function renderMarkup(CustomAdPlacement $placement, CustomAdCreative $creative): string
    {
        return match ((string) $creative->format) {
            CustomAdPlacement::FORMAT_TEXT => $this->renderTextMarkup($placement, $creative),
            CustomAdPlacement::FORMAT_NATIVE => $this->renderNativeMarkup($placement, $creative),
            default => $this->renderBannerMarkup($placement, $creative),
        };
    }

    public function renderFallbackMarkup(CustomAdPlacement $placement): string
    {
        $name = $this->escape($placement->name);
        $brand = $this->escape(AdsSettings::brandName());
        $marketplaceUrl = route('ads.custom.marketplace');
        $accent = $this->safeColor($placement->accent_color, '#615dfa');

        return "<div style='box-sizing:border-box;width:100%;min-height:96px;border:1px dashed #c7d2fe;border-radius:8px;background:#f8fbff;color:#1f2937;font-family:Arial,sans-serif;display:flex;align-items:center;justify-content:center;padding:16px;text-align:center;'><div><div style='font-size:12px;font-weight:700;color:{$accent};text-transform:uppercase;letter-spacing:.04em;'>{$brand}</div><div style='font-size:15px;font-weight:700;margin-top:6px;'>{$name}</div><a href='{$marketplaceUrl}' target='_blank' rel='noopener noreferrer' style='display:inline-block;margin-top:10px;color:{$accent};font-size:13px;font-weight:700;text-decoration:none;'>" . $this->escape(__('messages.custom_ads_advertise_here')) . "</a></div></div>";
    }

    public function renderInsertionScript(string $html, ?string $slotId = null): string
    {
        $htmlLiteral = $this->toJavaScriptLiteral($html);
        $slotLiteral = $this->toJavaScriptLiteral($slotId);

        return <<<JS
(function(d){
  var slotId={$slotLiteral};
  var html={$htmlLiteral};
  function currentScript(){return d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();}
  function createTarget(anchor){
    var node=d.createElement('div');
    node.setAttribute('data-myads-custom-slot','1');
    if(anchor && anchor.parentNode && String(anchor.parentNode.tagName || '').toLowerCase() !== 'head'){anchor.parentNode.insertBefore(node, anchor);return node;}
    if(d.body){d.body.insertBefore(node, d.body.firstChild || null);return node;}
    return null;
  }
  function resolveTarget(){
    if(slotId){
      var byId=d.getElementById(slotId);
      if(byId){return byId;}
    }
    var script=currentScript();
    return createTarget(script);
  }
  function render(){
    var target=resolveTarget();
    if(target){target.innerHTML=html;}
  }
  if(d.readyState === 'loading'){d.addEventListener('DOMContentLoaded', render);}else{render();}
})(document);
JS;
    }

    public function renderLoaderScript(): string
    {
        $serveUrl = route('ads.custom.serve');

        return <<<JS
(function(w,d){
  var current=d.currentScript || document.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function decode(value){try{return decodeURIComponent((value || '').replace(/\\+/g,' '));}catch(e){return value || '';}}
  function getParams(script){
    var params={};
    var src=script && script.src ? script.src : '';
    var queryIndex=src.indexOf('?');
    if(queryIndex === -1){return params;}
    src.slice(queryIndex + 1).split('&').forEach(function(part){
      if(!part){return;}
      var pairIndex=part.indexOf('=');
      var key=pairIndex === -1 ? decode(part) : decode(part.slice(0, pairIndex));
      var value=pairIndex === -1 ? '' : decode(part.slice(pairIndex + 1));
      params[key]=value;
    });
    return params;
  }
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-custom-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-custom-slot','1');
    if(script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head'){
      script.parentNode.insertBefore(slot, script);
    } else if(d.body) {
      d.body.appendChild(slot);
    }
    return slot;
  }
  function boot(){
    var params=getParams(current);
    var placement=(current && current.getAttribute('data-placement')) || params.placement || params.p || '';
    if(!placement){return;}
    var slot=createSlot(current);
    var vt;
    try {
      vt=(w.localStorage && w.localStorage.getItem('myads_custom_vt')) || '';
      if(!vt && w.localStorage){vt='v' + Math.random().toString(36).slice(2) + Date.now().toString(36);w.localStorage.setItem('myads_custom_vt', vt);}
    } catch(e) { vt=''; }
    var script=d.createElement('script');
    script.async=true;
    script.src='{$serveUrl}?placement=' + encodeURIComponent(placement) + '&slot=' + encodeURIComponent(slot.id) + '&vt=' + encodeURIComponent(vt);
    if(current && current.getAttribute('data-device')){script.src += '&dv=' + encodeURIComponent(current.getAttribute('data-device'));}
    (d.head || d.documentElement).appendChild(script);
  }
  if(d.readyState === 'loading'){d.addEventListener('DOMContentLoaded', boot);}else{boot();}
})(window,document);
JS;
    }

    private function recordEvent(
        CustomAdPlacement $placement,
        CustomAdDeal $deal,
        CustomAdCreative $creative,
        string $type,
        Request $request,
        string $countryCode,
        string $deviceType
    ): CustomAdEvent {
        return DB::transaction(function () use ($placement, $deal, $creative, $type, $request, $countryCode, $deviceType) {
            $event = CustomAdEvent::create([
                'placement_id' => $placement->id,
                'deal_id' => $deal->id,
                'creative_id' => $creative->id,
                'publisher_id' => $deal->publisher_id,
                'advertiser_id' => $deal->advertiser_id,
                'event_type' => $type,
                'visitor_key' => $this->visitorKey($request),
                'country_code' => $countryCode,
                'device_type' => $deviceType,
                'referrer' => $request->headers->get('referer'),
                'ip_hash' => hash('sha256', (string) $request->ip()),
                'user_agent' => $request->userAgent(),
                'occurred_at' => now(),
            ]);

            $column = $type === CustomAdEvent::TYPE_CLICK ? 'clicks' : 'impressions';
            CustomAdPlacement::whereKey($placement->id)->increment($column);
            CustomAdDeal::whereKey($deal->id)->increment($column);
            CustomAdCreative::whereKey($creative->id)->increment($column);

            return $event;
        });
    }

    private function renderBannerMarkup(CustomAdPlacement $placement, CustomAdCreative $creative): string
    {
        $size = $this->resolveSize((string) $placement->size);
        $widthStyle = $size ? "max-width:{$size['width']}px;min-height:{$size['height']}px;" : 'max-width:100%;min-height:120px;';
        $image = trim((string) $creative->image_url);
        $imageStyle = $image !== '' ? "background-image:url('" . $this->escape($image) . "');background-size:cover;background-position:center;" : '';
        $clickUrl = route('ads.custom.click', $creative->token);
        $headline = $this->escape($creative->headline);
        $body = $this->escape((string) $creative->body);
        $label = $this->escape(__('messages.custom_ads_ad_label'));
        $brand = $this->escape(AdsSettings::brandName());
        $bg = $this->safeColor($creative->background_color, '#ffffff');
        $text = $this->safeColor($creative->text_color, '#1f2937');
        $accent = $this->safeColor($creative->accent_color, '#615dfa');

        return "<div style='box-sizing:border-box;width:100%;{$widthStyle}margin:0 auto;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:{$bg};color:{$text};font-family:Arial,sans-serif;position:relative;{$imageStyle}'><a href='{$clickUrl}' target='_blank' rel='noopener noreferrer' aria-label='{$headline}' style='position:absolute;inset:0;z-index:2;text-decoration:none;'></a><div style='position:absolute;top:6px;left:6px;z-index:3;background:rgba(0,0,0,.58);color:#fff;border-radius:4px;padding:3px 7px;font-size:11px;font-weight:700;'>{$label}</div><div style='position:relative;z-index:1;min-height:inherit;padding:22px;display:flex;flex-direction:column;justify-content:flex-end;background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(0,0,0,.34));'><div style='font-size:11px;text-transform:uppercase;font-weight:700;color:{$accent};margin-bottom:6px;'>{$brand}</div><div style='font-size:18px;line-height:1.25;font-weight:800;color:inherit;text-shadow:0 1px 2px rgba(255,255,255,.25);'>{$headline}</div><div style='font-size:13px;line-height:1.45;margin-top:6px;color:inherit;'>{$body}</div></div></div>";
    }

    private function renderTextMarkup(CustomAdPlacement $placement, CustomAdCreative $creative): string
    {
        $clickUrl = route('ads.custom.click', $creative->token);
        $headline = $this->escape($creative->headline);
        $body = $this->escape((string) $creative->body);
        $label = $this->escape(__('messages.custom_ads_ad_label'));
        $bg = $this->safeColor($creative->background_color, '#ffffff');
        $text = $this->safeColor($creative->text_color, '#1f2937');
        $accent = $this->safeColor($creative->accent_color, '#615dfa');

        return "<div style='box-sizing:border-box;width:100%;border:1px solid #e5e7eb;border-radius:8px;background:{$bg};font-family:Arial,sans-serif;padding:14px;color:{$text};'><div style='font-size:11px;font-weight:700;color:{$accent};text-transform:uppercase;margin-bottom:6px;'>{$label}</div><a href='{$clickUrl}' target='_blank' rel='noopener noreferrer' style='font-size:15px;font-weight:800;color:{$accent};text-decoration:none;'>{$headline}</a><p style='font-size:13px;line-height:1.5;margin:6px 0 0;color:{$text};'>{$body}</p></div>";
    }

    private function renderNativeMarkup(CustomAdPlacement $placement, CustomAdCreative $creative): string
    {
        $clickUrl = route('ads.custom.click', $creative->token);
        $headline = $this->escape($creative->headline);
        $body = $this->escape((string) $creative->body);
        $button = $this->escape((string) ($creative->button_label ?: __('messages.custom_ads_learn_more')));
        $label = $this->escape(__('messages.custom_ads_ad_label'));
        $bg = $this->safeColor($creative->background_color, '#ffffff');
        $text = $this->safeColor($creative->text_color, '#1f2937');
        $accent = $this->safeColor($creative->accent_color, '#615dfa');
        $image = trim((string) $creative->image_url);
        $imageHtml = $image !== ''
            ? "<div style='width:96px;min-height:76px;border-radius:6px;background:url(\"" . $this->escape($image) . "\") center/cover no-repeat;flex:0 0 96px;'></div>"
            : '';

        return "<div style='box-sizing:border-box;width:100%;border:1px solid #e5e7eb;border-radius:8px;background:{$bg};font-family:Arial,sans-serif;padding:14px;color:{$text};'><a href='{$clickUrl}' target='_blank' rel='noopener noreferrer' style='display:flex;gap:14px;align-items:center;text-decoration:none;color:inherit;'>{$imageHtml}<div style='min-width:0;flex:1;'><div style='font-size:11px;font-weight:700;color:{$accent};text-transform:uppercase;margin-bottom:5px;'>{$label}</div><div style='font-size:16px;font-weight:800;line-height:1.25;color:{$text};'>{$headline}</div><div style='font-size:13px;line-height:1.45;margin-top:5px;color:{$text};'>{$body}</div><span style='display:inline-block;margin-top:10px;padding:7px 12px;border-radius:6px;background:{$accent};color:#fff;font-size:12px;font-weight:800;'>{$button}</span></div></a></div>";
    }

    private function visitorKey(Request $request): string
    {
        $token = trim((string) $request->query('vt', ''));

        if ($token !== '') {
            return 'vt:' . hash('sha256', $token);
        }

        return 'fp:' . hash('sha256', $request->ip() . '|' . ($request->userAgent() ?? ''));
    }

    private function resolveSize(string $size): ?array
    {
        if (preg_match('/^(\d{2,4})x(\d{2,4})$/', $size, $matches) !== 1) {
            return null;
        }

        return [
            'width' => max(1, (int) $matches[1]),
            'height' => max(1, (int) $matches[2]),
        ];
    }

    private function safeColor(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        return preg_match('/^#[0-9a-f]{6}$/i', $value) === 1 ? $value : $fallback;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function toJavaScriptLiteral($value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?: 'null';
    }
}
