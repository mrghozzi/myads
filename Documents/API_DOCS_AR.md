# دليل واجهات برمجة التطبيقات MYADS v4.6.3 (REST & Real-Time API)
> **إصدار المواصفة:** `v4.6.3` (الإصدار المستقر)  
> **إطار العمل المستهدف:** Laravel 12 (PHP 8.2+)  
> **محركات المصادقة:** Laravel Sanctum (لتطبيق الهاتف وواجهات الويب)، OAuth 2.0 (منصة المطورين)، وبث الأحداث اللحظية Server-Sent Events (SSE).  
> **تاريخ آخر تحديث:** أكتوبر 2026  

---

## 1. نظرة عامة والمعمارية العامة (Overview & Architecture)

توفر بيئة واجهات برمجة التطبيقات في **MYADS v4.6.3** منظومة متكاملة، فائقة السرعة، وعالية الأمان تربط واجهات الويب، وتطبيق الهاتف المحمول الرديف (Flutter Companion App v1.8.0+22)، وتطبيقات المطورين والأنظمة الخارجية.

### الأنظمة الفرعية الرئيسية
1. **الواجهة الداخلية لتطبيق الهاتف والويب (`/api/*`):** مدعومة بواسطة Laravel Sanctum لتوفير تجربة سلسة لتطبيق الهاتف وطلبات AJAX المتزامنة.
2. **محرك بث الأحداث اللحظية (`/live/stream` & `/api/live/stream` — RT-04):** بث مباشر منخفض الاستهلاك بتقنية Server-Sent Events (SSE) لتحديث عدادات الرسائل غير المقروءة، التنبيهات، والمنشورات الجديدة فورياً.
3. **منصة المطورين ونظام OAuth 2.0 (`/oauth/*` & `/api/developer/v1/*`):** 27 تصريحاً دقيقاً عبر 7 فئات للتطبيقات الخارجية المسجلة عبر `/developer`.
4. **محرك سوق الطلبات والخدمات (`/api/orders/*` & `/orders/*`):** نموذج النشر المباشر (Peer-to-Peer Bulletin) مع إدارة كاملة لدورة حياة العقود: المرفقات والمواصفات الفنية، تقديم العروض، التعاقد، مراحل الإنجاز، تسليم الملفات، طلب التعديلات (`requestRevision`)، وتقييم الخدمات.
5. **محرك المتجر الرقمي والمراجعات والمعاينات (`/api/store/*` & `/store/*`):** سوق للمنتجات الرقمية يدعم تقييمات 5 نجوم، شارة المشتري المؤكد، معرض لقطات الشاشة Lightbox، الروابط التجريبية ومعاينات الفيديو، ودعم كامل للروابط والعناوين العربية وUnicode، والشراء الفوري بالنقاط وكوبونات الخصم.
6. **نظام تراخيص البرمجيات وتغذية الإضافات (`/api/marketplace/extensions/*`, `/api/license/verify`):** التحقق من تراخيص الإضافات والقوالب، وفحص التحديثات التلقائية لمواقع ووردبريس والأنظمة الخارجية، وتنزيل الحزم المشفرة.
7. **محرك تقديم وتبادل الإعلانات (`/ads/*`, `/bn.php`, `/link.php`, `/smart.php`, `/embed/*.js`):** خدمة إعلانات البانر، والروابط، والإعلانات الذكية، والإعلانات المخصصة بين الأعضاء مع الحماية من النقرات الوهمية.
8. **البحث المباشر والذكاء اللحظي (`/api/search/live`, `/api/ads/stats`):** بحث متعدد الكيانات (الأعضاء، المنتجات، مواضيع المنتدى، والمنشورات) وإحصاءات الأداء الإعلاني اللحظية.
9. **محرك التعليقات والوسائط الموحد (`/comment/*`, `/api/statuses/*`):** رفع الوسائط بالسحب والإفلات واللصق (`Ctrl+V`) مع الفحص الثنائي الصارم لنوع الملف وتحويل الصور تلقائياً إلى صيغة WebP المحسّنة.
10. **خرائط الموقع المقسمة الذكية (`/sitemap.xml`, `/sitemap/*.xml`):** خرائط XML مجزأة تدعم التخزين المؤقت المشروط ETag ورمز HTTP 304 لتعزيز الأرشفة ومحركات البحث.

---

## 2. سياسة المصادقة والأمان وحدود الطلبات

### أ. الرموز المميزة لتطبيق الهاتف (Laravel Sanctum)
مخصصة لتطبيق Flutter الرديف والتطبيقات الرسمية للمنصة.

**بروتوكول الأمان ثنائي الطبقات:**
1. `X-API-KEY` (ترويسة): مفتاح واجهة برمجة التطبيقات العام المضبوط في لوحة التحكم الإدارية (`mobile_api.api_key`). **يجب** إرساله دائماً كترويسة HTTP لتجنب تسجيله في سجلات الخادم وروابط الإحالة.
2. `Authorization: Bearer {token}` (ترويسة): رمز Sanctum الخاص بالمستخدم بعد تسجيل الدخول.  
   *(ملاحظة: تدعم المنصة ترويسة `X-Authorization: Bearer {token}` كبديل على خوادم الاستضافة التي تحذف ترويسة Authorization القياسية).*

**نقطة نهاية تسجيل الدخول:** `POST /api/login`  
- **الترويسات:** `X-API-KEY: {YOUR_GLOBAL_KEY}`, `Content-Type: application/json`  
- **الحمولة:** `{"login": "username_or_email", "password": "..."}`  
- **الاستجابة:**
  ```json
  {
      "status": "success",
      "token": "1|sanctum_token_string...",
      "user": {
          "id": 42,
          "username": "developer",
          "name": "Developer Name",
          "avatar_url": "https://domain.com/upload/avatar.png"
      }
  }
  ```

### ب. قواعد حدود معدل الطلبات (Rate Limiting)
تتم حماية نقاط النهاية العامة والمصادق عليها بمحددات معدل الطلبات عبر نافذة زمنية منزلقة:

| مجموعة نقاط النهاية | الحد المسموح | الغرض والحماية |
|---|---|---|
| `POST /api/login` | 5 طلبات / دقيقة / IP | منع هجمات التخمين وتجربة كلمات المرور |
| `POST /api/register` | 3 طلبات / دقيقة / IP | منع التسجيل الآلي العشوائي (Spam) |
| `POST /api/license/verify` | 10 طلبات / دقيقة / IP | منع تخمين مفاتيح التراخيص |
| `GET /api/search/live` & `GET /search/live` | 40 طلباً / دقيقة / IP | حماية قواعد البيانات من إغراق البحث الشامل |
| `POST /status/create` ورفع الملفات | 20 طلباً / دقيقة | منع إغراق مساحة التخزين وهجمات الحرمان |
| `GET /share` | 15 طلباً / دقيقة / IP | حماية توليد معاينات الروابط من الكشط |
| `POST /post` (المنتدى) | 15 طلباً / دقيقة | منع النشر التلقائي المزعج في المنتدى |
| `GET/POST /api/developer/v1/*` | 30 طلباً / دقيقة / IP | منع استنزاف واجهات منصة المطورين |
| `GET /api/live/stream` | اتصال واحد / مستخدم | جلسة بث متصلة ومستمرة لكل عضو |

*عند تجاوز الحد، يعيد الخادم رمز الحالة `HTTP 429 Too Many Requests`.*

### ج. التعريب الديناميكي (`Accept-Language`)
تدعم جميع الاستجابات ورسائل الأخطاء والتنبيهات التعريب الديناميكي:
- **الترويسة:** `Accept-Language: ar` (أو `en`, `fr`, إلخ).
- **القيمة الافتراضية:** لغة الموقع الافتراضية.

### د. إخفاء الهوية وحماية معرفات الأعضاء
عند تفعيل خيار `public_member_ids_enabled` في إعدادات الأمان، تقوم جميع الموارد البرمجية (`UserResource`, `UserProfileResource`, `StatusResource`, `SearchApiController`) بتقديم معرفات عامة عشوائية (`public_uid`) أو اسم المستخدم بدلاً من المعرف الرقمي في قاعدة البيانات (`users.id`) لحظر هجمات التعداد.

### هـ. سياسة إخلاء المسؤولية لسوق الخدمات (Peer-to-Peer Bulletin)
وفقاً لشروط الخدمة ومعمارية النشر المباشر (v4.6.3)، تعمل منصة MYADS كلوحة إعلانية وتواصلية حرة بين الأعضاء. المنصة لا تلعب دور الضامن المالي (Non-Escrow) ولا تضمن تسليم أو جودة المشاريع، وتقع المسؤولية التعاقدية والتنفيذية حصراً على عاتق طرفي التعامل.

---

## 3. محرك بث الأحداث اللحظية (SSE Live Stream — RT-04)

يوفر **محرك الأحداث اللحظية** بثاً مستمراً للأحداث عبر معيار Server-Sent Events (SSE) القياسي دون الحاجة إلى الاستعلام الدوري المتكرر (Polling).

### نقاط النهاية
- **بث متصفح الويب:** `GET /live/stream` (مصادقة عبر جلسة الويب)
- **بث التطبيق وواجهات API:** `GET /api/live/stream` (مصادقة عبر رمز Sanctum Bearer)

### ترويسات البروتوكول
```http
HTTP/1.1 200 OK
Content-Type: text/event-stream; charset=UTF-8
Cache-Control: no-cache, no-store, must-revalidate
X-Accel-Buffering: no
Connection: keep-alive
```

### قنوات الأحداث المدعومة

#### 1. `handshake` (بدء الاتصال)
يُرسل فور نجاح الاتصال:
```text
event: handshake
data: {
    "status": "connected",
    "user_id": 42,
    "username": "developer",
    "unread_notifications": 3,
    "unread_messages": 1,
    "timestamp": 1787534000,
    "server_time": "2026-10-07T22:00:00+00:00"
}
```

#### 2. `notifications` (التنبيهات الجديدة والعدادات)
يُرسل عند ورود إشعار جديد أو تغير عدد الإشعارات غير المقروءة:
```text
event: notifications
data: {
    "unread_count": 4,
    "has_new": true,
    "latest": {
        "id": 105,
        "name": "قام أحمد بالتعليق على منشورك",
        "url": "/post/123#comment-45",
        "logo": "comment",
        "time": 1787534015
    }
}
```

#### 3. `messages` (الرسائل المباشرة والمحادثات)
يُرسل عند وصول رسالة خاصة جديدة أو تحديث حالة القراءة:
```text
event: messages
data: {
    "unread_count": 2,
    "has_new": true,
    "latest": {
        "id": 204,
        "sender_id": 15,
        "sender_name": "Sarah",
        "sender_avatar": "https://example.com/upload/avatar.jpg",
        "text_preview": "Hello, could you please review the proposal?",
        "time": 1787534020
    }
}
```

#### 4. `feed` (تحديثات الخلاصة العامة)
يُرسل عند نشر أعضاء آخرين لمنشورات عامة جديدة:
```text
event: feed
data: {
    "new_posts_count": 3,
    "timestamp": 1787534030
}
```

#### 5. `admin` (تنبيهات الإدارة — للمدير العام فقط)
يُرسل عند وجود بلاغات جديدة في النظام:
```text
event: admin
data: {
    "pending_reports": 2,
    "timestamp": 1787534030
}
```

#### 6. `ping` و `reconnect`
- `ping`: نبضة دورية كل بضع ثوانٍ للحفاظ على الاتصال مفتوحاً عبر جدران الحماية والبروكسي.
- `reconnect`: إشعار إغلاق نظيف يُرسل عند انقضاء دورة البث (20 ثانية) لإعادة الاتصال التلقائي عبر متصفح العميل.

---

## 4. منصة المطورين ونظام OAuth 2.0

تتيح المنصة للمطورين تسجيل تطبيقات خارجية في `/developer` للتكامل عبر تدفق OAuth 2.0 Authorization Code Flow.

### روابط المصادقة
- **شاشة منح الإذن:** `GET /oauth/authorize`
- **تبادل الرمز:** `POST /oauth/token`

### كتالوج تصاريح OAuth 2.0 (27 تصريحاً عبر 7 فئات)

| الفئة | معرف التصريح | الوصف | حساس؟ |
|---|---|---|:---:|
| **الهوية والملف** | `user.identity.read` | قراءة المعرف الأساسي وهوية العضو العامة | لا |
| | `user.profile.read` | قراءة بيانات الملف الشخصي، الغلاف، الرصيد | لا |
| | `user.email.read` | الوصول إلى البريد الإلكتروني المؤكد للمستخدم | **نعم** |
| | `user.social_links.read` | قراءة روابط الشبكات الاجتماعية للملف | لا |
| | `user.follows.read` | قراءة المتابعين ومن يتابعهم المستخدم | لا |
| | `user.follows.write` | متابعة أو إلغاء متابعة الأعضاء نيابة عن المستخدم | **نعم** |
| **المحتوى والتفاعل** | `user.content.read` | قراءة المنشورات العامة الخاصة بالمستخدم | لا |
| | `user.content.write` | إنشاء وتحديث ونشر المنشورات نيابة عن المستخدم | **نعم** |
| | `user.reactions.write` | إضافة التفاعلات والإعجابات نيابة عن المستخدم | لا |
| | `user.comments.write` | نشر التعليقات والردود نيابة عن المستخدم | **نعم** |
| **الرسائل والتنبيهات** | `user.messages.read` | قراءة المحادثات والرسائل الخاصة للمستخدم | **نعم** |
| | `user.messages.write` | إرسال رسائل خاصة نيابة عن المستخدم | **نعم** |
| | `user.notifications.read` | قراءة التنبيهات والإشعارات والعدادات | لا |
| **المحفظة والمكافآت** | `user.wallet.read` | قراءة رصيد النقاط (PTS) وسجل المعاملات | **نعم** |
| | `user.badges.read` | قراءة شارات الإنجاز وحالة المهام | لا |
| **المجتمع والوسائط** | `user.clips.read` | تصفح خلاصة مقاطع الفيديو القصيرة والمحفوظات | لا |
| | `user.clips.write` | حفظ وإلغاء حفظ مقاطع الفيديو القصيرة | لا |
| | `user.forums.read` | قراءة أقسام ومواضيع وردود المنتدى | لا |
| | `user.forums.write` | إنشاء مواضيع جديدة والرد عليها نيابة عن العضو | **نعم** |
| **المتجر والإعلانات** | `user.store.read` | تصفح منتجات المتجر وقاعدة المعرفة | لا |
| | `user.orders.read` | قراءة سجل طلبات الشراء والخدمات والعروض | **نعم** |
| | `user.ads.read` | قراءة إحصاءات ظهور ونقرات الإعلانات | لا |
| **تكامل مالك التطبيق** | `owner.profile.read` | قراءة الملف الشخصي لمالك التطبيق المعتمد | لا |
| | `owner.content.read` | قراءة منشورات وخلاصة مالك التطبيق | لا |
| | `owner.follow.write` | متابعة الأعضاء نيابة عن مالك التطبيق | **نعم** |
| | `owner.messages.read` | قراءة رسائل ومحادثات مالك التطبيق | **نعم** |
| | `owner.messages.write` | إرسال رسائل خاصة نيابة عن مالك التطبيق | **نعم** |

### إدارة التطبيقات والمصادقة
- **تسجيل تطبيق جديد:** `POST /developer/apps`
- **تحديث التطبيق:** `PUT /developer/apps/{id}`
- **تدوير المفتاح السري (Client Secret):** `POST /developer/apps/{id}/rotate-secret`
- **حذف التطبيق:** `DELETE /developer/apps/{id}`
- **طلب التفويض:**
  ```http
  GET /oauth/authorize?client_id={client_id}&redirect_uri={redirect_uri}&response_type=code&scope={scope}&state={state}
  ```
- **تبادل الرمز المميز:**
  ```http
  POST /oauth/token
  Content-Type: application/json

  {
    "grant_type": "authorization_code",
    "client_id": "{client_id}",
    "client_secret": "{client_secret}",
    "redirect_uri": "{redirect_uri}",
    "code": "{authorization_code}"
  }
  ```

---

## 5. واجهات منصة المطورين (Developer API v1)

تتطلب جميع نقاط النهاية في هذا القسم ترويسة المصادقة القياسية:
```http
Authorization: Bearer {access_token}
```
مع معدل طلبات أقصاه 30 طلباً / دقيقة (`throttle:30,1`).

- **الهوية والملف الشخصي:**
  - `GET /api/developer/v1/me`: المعرف الأساسي، اسم المستخدم، وتاريخ التسجيل *(التصريح: `user.identity.read`)*.
  - `GET /api/developer/v1/me/profile`: الاسم الظاهر، النبذة، النقاط، والصورة *(التصريح: `user.profile.read`)*.
  - `GET /api/developer/v1/me/email`: البريد الإلكتروني المؤكد *(التصريح: `user.email.read`)*.
  - `GET /api/developer/v1/me/social-links`: الروابط الاجتماعية المربوطة *(التصريح: `user.social_links.read`)*.
  - `GET /api/developer/v1/me/follows`: المتابعون ومن يتابعهم العضو *(التصريح: `user.follows.read`)*.
  - `POST /api/developer/v1/me/follows`: متابعة أو إلغاء متابعة (`{"target_user_id": 123, "action": "follow"}`) *(التصريح: `user.follows.write`)*.
- **المحتوى والتفاعل:**
  - `GET /api/developer/v1/me/content`: أحدث المنشورات المؤلفة *(التصريح: `user.content.read`)*.
  - `POST /api/developer/v1/me/content`: نشر منشور جديد في الخلاصة العامة *(التصريح: `user.content.write`)*.
  - `POST /api/developer/v1/me/reactions`: إضافة أو تبديل تفاعل على منشور *(التصريح: `user.reactions.write`)*.
  - `GET /api/developer/v1/me/messages`: المحادثات الخاصة النشطة *(التصريح: `user.messages.read`)*.
  - `POST /api/developer/v1/me/messages`: إرسال رسالة خاصة لمستخدم آخر *(التصريح: `user.messages.write`)*.
  - `GET /api/developer/v1/me/notifications`: خلاصة الإشعارات والعدادات *(التصريح: `user.notifications.read`)*.
  - `GET /api/developer/v1/forums`: أقسام المنتدى مع العدادات *(التصريح: `user.forums.read`)*.
  - `GET /api/developer/v1/me/clips`: مقاطع الفيديو القصيرة العامة *(التصريح: `user.clips.read`)*.
- **المتجر والإعلانات والاقتصاد:**
  - `GET /api/developer/v1/me/wallet`: رصيد محفظة النقاط والمكاسب *(التصريح: `user.wallet.read`)*.
  - `GET /api/developer/v1/me/badges`: الشارات المفتوحة ومسار التقدم *(التصريح: `user.badges.read`)*.
  - `GET /api/developer/v1/store/products`: تصفح منتجات المتجر *(التصريح: `user.store.read`)*.
  - `GET /api/developer/v1/me/orders`: سجل طلبات الخدمات والعروض *(التصريح: `user.orders.read`)*.
  - `GET /api/developer/v1/me/ads/stats`: إحصاءات ظهور ونقرات الإعلانات *(التصريح: `user.ads.read`)*.
- **نقاط نهاية مالك التطبيق (Owner Scopes):**
  - `GET /api/developer/v1/owner/profile` *(التصريح: `owner.profile.read`)*
  - `GET /api/developer/v1/owner/content` *(التصريح: `owner.content.read`)*
  - `POST /api/developer/v1/owner/follow` *(التصريح: `owner.follow.write`)*
  - `POST /api/developer/v1/owner/messages` *(التصريح: `owner.messages.write`)*

---

## 6. عناصر التضمين الخارجية (Embed Widgets Catalog)

أكواد JavaScript جاهزة لتضمين وظائف المنصة في المواقع الخارجية:
- **زر المتابعة:** `GET /embed/developer/{app_id}/follow.js`
- **بطاقة الملف الشخصي:** `GET /embed/developer/{app_id}/profile.js`
- **خلاصة المنشورات:** `GET /embed/developer/{app_id}/content.js`
- **إعلانات البانر:** `GET /embed/banner.js` (أو `/bn.php`)
- **إعلانات الروابط:** `GET /embed/link.js` (أو `/link.php`)
- **الإعلانات الذكية:** `GET /embed/smart.js` (أو `/smart.php`)
- **الإعلانات المخصصة:** `GET /embed/custom.js` (أو `/ads/custom/serve`)

---

## 7. واجهة المشاركة العامة (External Web Share API)

تسمح لأي موقع خارجي بتمرير نصوص وروابط مسبقة إلى صندوق إنشاء المنشورات في MYADS.

**نقطة النهاية:** `GET /share`  
**معدل الطلبات:** 15 طلباً / دقيقة / IP  
**المعلمات:**
- `text`: نص مشفر بصيغة URL.

**مثال:**
```text
https://myads.com/share?text=Check+out+this+awesome+platform!+https://example.com
```

---

## 8. واجهات تطبيق الهاتف والأنظمة الفرعية (Sanctum Endpoints)

جميع نقاط النهاية في هذا القسم تتطلب ترويسة المفتاح العام وترويسة المصادقة:
- `X-API-KEY: {YOUR_GLOBAL_KEY}`
- `Authorization: Bearer {token}`

---

### أ. إدارة الحساب والإعدادات (Settings & Account)
تقبل نقاط النهاية الخاصة بالتحديث طرق HTTP التالية: `POST` و `PUT` و `PATCH`.

- `GET /api/settings/overview`: استرجاع نظرة عامة شاملة عن حساب العضو:
  ```json
  {
      "user": {
          "id": 42,
          "name": "Jane Doe",
          "username": "janedoe",
          "email": "jane@example.com",
          "pts": 2500,
          "avatar": "https://domain.com/upload/avatar.png",
          "is_verified": true
      }
  }
  ```
- `GET /api/settings/profile`: استرجاع البيانات الشخصية القابلة للتعديل (`email`, `about_me`, `avatar`).
- `POST|PUT|PATCH /api/settings/profile`: تحديث الملف الشخصي. البريد الإلكتروني اختياري، وإذا لم يُرسل يُحتفظ بالبريد الحالي تلقائياً.
- `POST /api/settings/2fa/enable`: تفعيل التحقق بخطوتين (2FA) عبر البريد الإلكتروني. يُرجع 8 رموز استرداد طوارئ (`recovery_codes`).
- `POST /api/settings/2fa/disable`: تعطيل التحقق بخطوتين.
- `GET /api/settings/privacy`: استرجاع إعدادات الخصوصية. يدعم النصوص القياسية (`profile_visibility`) والأرقام المختصرة لتطبيق الهاتف (`visibility`: 0=عام، 1/2=متابعون، 3=خاص؛ `dm`: 2=معطل؛ `mention`: 2=معطل).
- `POST|PUT|PATCH /api/settings/privacy`: تحديث إعدادات الخصوصية.
- `GET /api/settings/social`: استرجاع روابط الشبكات الاجتماعية المربوطة. يُرجع كلاً من قاموس `links` ومصفوفة الكائنات `socials`.
- `POST|PUT|PATCH /api/settings/social`: تحديث الروابط الاجتماعية.
- `GET /api/settings/notification-preferences`: استرجاع تفضيلات الإشعارات بالبريد والتطبيق.
- `POST|PUT|PATCH /api/settings/notification-preferences`: تحديث تفضيلات الإشعارات.
- `GET /api/settings/sessions`: استرجاع الجلسات النشطة ورموز أجهزة Sanctum المسجلة.
- `POST /api/settings/sessions/{id}/revoke`: إنهاء جلسة ويب محددة بالمعرف.
- `POST /api/settings/tokens/{id}/revoke`: إلغاء صلاحية رمز جهاز محدد.
- `POST /api/settings/device-token`: تسجيل رمز جهاز FCM لإشعارات الهاتف Push Notifications (`{"token": "..."}`).
- `GET /api/settings/badges`: استرجاع الشارات المكتسبة ومسار العرض المميز (`earned`, `showcase`, `badges`).
- `POST|PUT|PATCH /api/settings/badges`: تخصيص وترتيب شارات العرض المميز (`{"showcase": [1, 2, 3]}`).
- `GET /api/settings/history`: استرجاع سجل حركة النقاط (PTS Ledger) مع دعم التصفح والفرز الزمني الديناميكي.
- `GET /api/settings/apps`: استرجاع التطبيقات المعتمدة الخارجية.
- `POST /api/settings/apps/{id}/revoke`: إلغاء اعتماد تطبيق خارجي.
- `GET /api/settings/blocks`: استرجاع قائمة المستخدمين المحظورين.

---

### ب. الخلاصة والوسائط ومركز الفيديو (Community Feed & Video Hub)

- `GET /api/portal/feed`: استرجاع خلاصة المنشورات مع التفاعلات والوسائط وإعلانات الترويج.
- `GET /api/video/feed`: تصفح مركز الفيديو المخصص لمقاطع الفيديو والمقاطع القصيرة (`s_type` 10 و 2 و 4 و 100 و 14).
  - المعلمات: `filter` (`all`, `trending`, `latest`, `videos`, `clips`)، استعلام البحث `q`، والصفحة.
  - يُرجع كائن الفيديو المميز (`spotlight_video`)، ورف المقاطع القصيرة (`clips`)، ومقاطع الفيديو المتوافقة مع البحث.
- `GET /api/statuses/saved`: استرجاع المنشورات المحفوظة/المفضلة الخاصة بالمستخدم (مع تقسيم الصفحات).
- `POST /api/statuses/save-toggle` و `POST /api/statuses/{id}/save-toggle`: تبديل حالة حفظ المنشور (`saved: true/false`).
- `GET /api/tags/suggest`: الإكمال التلقائي واقتراح الوسوم والهاشتاجات (`?q=keyword`).
- `GET /api/mentions/users`: الإكمال التلقائي للإشارات للأعضاء مع الصور والأسماء (`?q=keyword`).
- `GET /api/statuses/{id}`: استرجاع تفاصيل منشور محدد. لمنشورات الفيديو يتضمن الفيديوهات المقترحة (`suggested_videos`).
- `GET /api/composer/options`: استرجاع خيارات صندوق النشر (المجموعات، التصنيفات، وأنواع المنشورات).
- `POST /api/statuses/link-preview`: فحص وجلب المعاينة الفورية لعنوان URL (`{"link_url": "..."}`).
- `POST /api/statuses`: نشر منشور جديد متعدد الوسائط (صور، فيديو، صوت، ملفات، وروابط).
- `POST /api/statuses/{id}/update`: تعديل منشور موجود.
- `DELETE /api/statuses/{id}`: حذف المنشور.

---

### ج. التعليقات والتفاعلات (Comments & Reactions)

- `GET /api/statuses/{id}/comments`: استرجاع التعليقات على المنشور مع الصفحات.
- `POST /api/statuses/{id}/comments`: نشر تعليق جديد على المنشور (`{"text": "..."}`).
- `POST /api/reactions/toggle`: إضافة أو تبديل تفاعل على أي كيان مدعوم (`subject_id`, `type`, `reaction_name`: `like`, `love`, `funny`, `wow`, `sad`, `angry`, `care`).

---

### د. الملفات الشخصية والعلاقات (Profiles & Social)

- `GET /api/profile/{identifier}`: جلب بيانات الملف الشخصي (يقبل اسم المستخدم، المعرف الرقمي، أو `'me'`).
- `GET /api/profile/{identifier}/statuses`: استرجاع منشورات العضو.
- `POST /api/profile/{identifier}/follow`: متابعة أو إلغاء متابعة العضو.
- `POST /api/profile/{identifier}/block`: حظر العضو (`full_platform` أو `messages_only`).
- `DELETE /api/profile/{identifier}/unblock`: إلغاء الحظر.

---

### هـ. الرسائل والمحادثات الخاصة (Private Messaging)

- `GET /api/messages`: استرجاع قائمة المحادثات النشطة مع أحدث رسالة وعدادات غير المقروء.
- `GET /api/messages/updates`: جلب التحديثات الجديدة أثناء الدردشة المباشرة (`?conversation={key}&after_id={id}`).
- `GET /api/messages/{identifier}`: سجل الرسائل مع طرف محادثة محدد.
- `POST /api/messages/{identifier}`: إرسال رسالة خاصة جديدة (`{"text": "..."}`).
- `POST /api/messages/{identifier}/read`: تعيين جميع رسائل المحادثة كمقروءة.

---

### و. التنبيهات ونظام المهام والمكافآت (Notifications & Quests)

- `GET /api/notifications`: قائمة إشعارات المستخدم.
- `GET /api/notifications/unread-count`: العداد الرقمي للإشعارات غير المقروءة.
- `POST|GET /api/notifications/{id}/read` (أو `mark-read`): تعيين إشعار كمقروء.
- `POST|GET /api/notifications/read-all` (أو `mark-all-read`): تعيين الكل كمقروء.
- `GET /api/wallet/balance`: استرجاع رصيد النقاط (PTS) وأرصدة العضو.
- `GET /api/quests`: استرجاع المهام اليومية والأسبوعية النشطة ونسبة الإنجاز.
- `POST /api/quests/{id}/claim`: المطالبة بمكافأة إتمام المهمة وإضافتها فورياً للرصيد.
- `POST /api/pts/transfer`: تحويل نقاط لعضو آخر (`{"username": "...", "amount": 100}`).
- `POST /api/pts/vouchers/create`: توليد قسيمة شحن نقاط جديدة (`{"amount": 50}`).
- `POST /api/pts/vouchers/claim`: شحن واسترداد قسيمة نقاط عبر الرمز (`{"code": "..."}`).

---

### ز. سوق المتجر والمراجعات والوسائط (Store Marketplace & Reviews — v4.6.3)

يوفر سوق المتجر في إصداره المحدث منظومة متكاملة لبيع وتوزيع المنتجات الرقمية مع تقييمات العملاء 5 نجوم ومعارض الوسائط.

#### 1. تصفح المنتجات والتفاصيل
- `GET /api/store/products`: تصفح المنتجات مع البحث والفلترة (`category`, `q`, `sort`: `latest`, `price_asc`, `price_desc`, `free`, `paid`).
- `GET /api/store/products/{id}`: حمولة تفصيلية تشمل بيانات البائع وتقييمات العملاء ووسائط العرض:
  ```json
  {
      "id": 12,
      "title": "Pro Classifieds Marketplace Theme",
      "description": "Comprehensive and responsive marketplace template...",
      "price": 100,
      "original_price": 100,
      "sale_price": 80,
      "current_price": 80,
      "is_on_sale": true,
      "sales": 45,
      "downloads": 45,
      "downloads_count": 45,
      "is_pending": false,
      "moderation_status": "approved",
      "thumbnail": "upload/store/thumb.jpg",
      "rating": 4.8,
      "average_rating": 4.8,
      "reviews_count": 15,
      "live_demo_url": "https://demo.example.com",
      "video_preview_url": "https://youtube.com/watch?v=...",
      "screenshots": [
          {
              "id": 101,
              "url": "upload/screenshots/ss_1.jpg",
              "full_url": "https://domain.com/upload/screenshots/ss_1.jpg",
              "caption": "Main Dashboard Overview"
          }
      ],
      "seller": {
          "id": 7,
          "username": "ahmed",
          "name": "Ahmed",
          "avatar": "https://domain.com/upload/avatar.png"
      },
      "category_id": 3,
      "created_at": "2026-10-01T12:00:00Z"
  }
  ```
- `GET /api/store/products/{id}/knowledgebase`: مقالات الشرح والتوثيق المربوطة بالمنتج.

#### 2. تقييمات ومراجعات العملاء (Store Reviews Engine)
- `POST /store/{id}/reviews`: إرسال أو تحديث تقييم 5 نجوم:
  - **الحمولة:** `{"rating": 5, "title": "عنوان التقييم", "comment": "نص المراجعة والتجربة..."}`
  - **شارة المشتري المؤكد:** يتم فحص جدول التراخيص `product_licenses` تلقائياً، وإذا كان المستخدم قد اشترى المنتج تُمنح له الشارة فورياً (`is_verified_buyer: true`).
  - **الاستجابة:**
    ```json
    {
        "success": true,
        "message": "Review submitted successfully",
        "review": {
            "id": 31,
            "rating": 5,
            "title": "Excellent product and great support",
            "comment": "The template installed smoothly...",
            "is_verified_buyer": true,
            "created_at": "1 minute ago",
            "user": { "id": 42, "username": "developer", "avatar": "..." }
        },
        "average_rating": 4.9,
        "reviews_count": 16,
        "rating_breakdown": {
            "5": 90,
            "4": 10,
            "3": 0,
            "2": 0,
            "1": 0
        }
    }
    ```
- `DELETE /store/reviews/{id}`: حذف مراجعة (متاح لصاحب المراجعة، صاحب المنتج، أو الإدارة).

#### 3. إدارة وسائط المنتجات والمعاينات
- `POST /store/upload-screenshot`: رفع لقطة شاشة عبر AJAX (حتى 10MB).
- `POST /store/{name}/media`: ربط وسيط بالمنتج (`media_type`: `screenshot`, `video`, `demo_url`).
- `DELETE /store/{name}/media/{id}`: حذف وسيط مربوط.

#### 4. الشراء الفوري وتوليد التراخيص
- `POST /store/{id}/purchase`: الشراء المباشر بالنقاط مع دعم كوبونات الخصم (`code`)، وتوليد ترخيص رقمي فريد بصيغة `ADSTN-XXXX-XXXX-XXXX` وإرجاع رابط التحميل الآمن.
- `POST /store/discounts/validate`: التحقق من صلاحية كود الخصم قبل الشراء.
- `GET /download/{hash}`: التحميل الموثق للملفات عبر الهاش المشفر.

---

### ح. سوق الطلبات والخدمات ودورة حياة التعاقد (Service Orders — v4.6.3)

يقدم سوق الخدمات في `/orders` و `/api/orders` دورة عمل متكاملة للمشاريع البرمجية والتصميمية بنظام النشر المباشر.

#### مراحل المشروع
$$\text{مفتوح (Open)} \longrightarrow \text{تمت الترسية (Awarded)} \longrightarrow \text{قيد التنفيذ (In Progress)} \longrightarrow \text{تم التسليم (Delivered)} \longrightarrow \text{مكتمل (Completed)}$$
*(مع مسارات: الإلغاء `Cancelled` أو طلب التعديل `Revision Requested` الذي يعيد المشروع من "تم التسليم" إلى "قيد التنفيذ").*

#### 1. استعراض والبحث في الطلبات
- `GET /api/orders`: استعراض طلبات الخدمات مع فلاتر البحث (`search`, `category`, `status`: `all`, `open`, `under_review`, `awarded`, `in_progress`, `delivered`, `completed`, `cancelled`) والفرز (`newest`, `active`, `popular`, `budget_high`, `budget_low`).

#### 2. تفاصيل العقد والطلب
- `GET /api/orders/{id}`: حمولة العقد الكاملة وتشمل:
  - بيانات المشتري (`buyer`).
  - وجود وثيقة الشروط والمواصفات ورابط تحميلها (`attachment_download_url` حتى 25MB).
  - قائمة العروض المقدمة (`offers`) مع السعر ومدة التسليم وبيانات المنفذين.
  - بيانات العقد النشط (`contract`): حالة المرحلة، الموعد النهائي المحسوب (`deadline`)، حالة التأخير (`is_overdue`)، ملف التسليم، وعدد التعديلات المطلوبة (`revision_count`) والملاحظات.
  - عرض المستخدم الحالي إن وجد (`viewer_offer`).

#### 3. تقديم العروض والترسية والتنفيذ
- `POST /api/orders/{id}/offers`: تقديم عرض على الطلب:
  - المعلمات: `content` أو `txt` (نص العرض والمخرجات)، `price` (السعر)، `currency` (العملة)، و `delivery_days` (مدة التنفيذ بالأيام).
- `POST /api/orders/{id}/award`: ترسية الطلب على عرض محدد (`{"offer_id": 88}`).
- `POST /api/orders/{id}/start`: بدء المنفذ في التنفيذ (بدء عداد الموعد النهائي).
- `POST /api/orders/{id}/deliver`: تسليم العمل المنفذ (يقبل `delivery_note` ومرفق التسليم المضغوط `delivery_attachment` حتى 25MB).
- `POST /api/orders/{id}/revision`: طلب العميل لتعديلات على التسليم (`{"revision_note": "..."}`) مما يعيد المشروع لمرحلة التنفيذ ويزيد عداد التعديلات.
- `POST /api/orders/{id}/complete`: قبول التسليم النهائي وإتمام المشروع مع تقييم المنفذ (`rating`: 1 إلى 5، و `review`).
- `POST /api/orders/{id}/cancel`: إلغاء المشروع مع ذكر السبب (`{"note": "..."}`).
- **التنزيل الآمن للملفات:**
  - `GET /orders/{order}/attachment`: تنزيل ملف شروط المشروع.
  - `GET /orders/{order}/deliverable`: تنزيل ملف العمل المسلّم (محمي ومقصور على أطراف العقد والإدارة).

---

### ط. نظام المقاطع القصيرة (Clips System)

- `GET /api/clips`: تصفح خلاصة مقاطع الفيديو الرأسية القصيرة.
- `GET /api/clips/saved`: قائمة المقاطع المحفوظة للمستخدم.
- `POST /api/clips/{id}/save`: حفظ مقطع.
- `DELETE /api/clips/{id}/save`: إلغاء حفظ مقطع.

---

### ي. واجهات المنتدى (Forums API)

- `GET /api/forums/categories`: أقسام المنتدى مع عدادات المواضيع.
- `GET /api/forums/categories/{id}/topics`: تصفح المواضيع داخل قسم محدد.
- `POST /api/forums/categories/{id}/topics`: إنشاء موضوع جديد في القسم.
- `GET /api/forums/topics/{id}`: استعراض تفاصيل الموضوع والردود.
- `POST /api/forums/topics/{id}/replies`: كتابة رد على الموضوع.

---

### ك. البحث اللحظي وإحصاءات الإعلانات (Search & Ads Stats)

- `GET /api/search/live`: بحث مباشر عبر 4 كيانات رئيسية في المنصة:
  - الحد: 40 طلباً / دقيقة (`throttle:40,1`).
  - الاستعلام: `?q=keyword` (حرفان كحد أدنى).
  - الكيانات: الأعضاء (`user`)، منتجات المتجر (`product`)، مواضيع المنتدى (`forum`)، ومنشورات الخلاصة (`post`).
- `GET /api/ads/stats`: إحصاءات الإعلانات الخاصة بالعضو:
  - زيارات التصفح (`visits.today`, `visits.total`).
  - ظهور الإعلانات (`banner_impressions`, `smart_impressions`).
  - رصيد النقاط (`wallet.pts`).

---

### ل. نظام تراخيص البرمجيات وتغذية الإضافات (Software Licensing & Extensions)

- `POST /api/license/verify`:
  - التحقق من تراخيص الإضافات والقوالب وربط النطاق المصرح به تلقائياً في أول تفعيل (`activated_at`).
  - الحمولة: `{"license_key": "...", "domain": "client.com", "plugin": "slug"}`.
- `GET|POST /api/marketplace/extensions/plugins`:
  - **وضع الكتالوج:** إرجاع قائمة الإضافات المتاحة للمتاجر الخارجية.
  - **وضع فحص التحديثات:** عند تمرير `slug` و `version` و `license_key` و `domain`، يفحص صلاحية الترخيص ويُرجع أحدث إصدار ورابط التحميل وسجل التغييرات (`changelog`).
- `GET /api/marketplace/extensions/themes`: كتالوج قوالب المنصة المتاحة.
- `GET /api/marketplace/extensions/download`: تنزيل حزمة الإضافة المرخصة بعد التأكد من صحة الترخيص والنطاق.

---

### م. محرك التعليقات والوسائط الموحد (Universal Comments API)

نظام موحد لإدارة النقاشات عبر الكيانات (المواضيع، المنتجات، أدلة المواقع، وقاعدة المعرفة):
- `POST /comment/store`: نشر تعليق مع صورة اختيارية (`multipart/form-data`):
  - المعلمات: `id` (معرف الكيان)، `type` (`forum`, `store`, `directory`, `knowledgebase`, `order`)، `comment` (نص التعليق)، و `attachment` (صورة حتى 5MB).
  - المعالجة: فحص ثنائي صارم (Magic Bytes)، تحويل فوري لـ WebP، وإدراج الصورة تلقائياً في النص بصيغة Markdown.
- `POST /comment/delete`: حذف تعليق (`{"trashid": 142, "type": "forum"}`).
- `POST /reaction/toggle`: تفاعل برمز إيموجي على منشور أو تعليق.

---

### ن. خرائط الموقع المقسمة الذكية (Smart Partitioned Sitemaps)

خرائط XML مقسمة متوافقة مع Google Sitemaps 0.9 و Schema.org:
- `GET /sitemap.xml`: الفهرس الرئيسي الشامل لجميع الأقسام.
- `GET /sitemap/pages.xml`: الصفحات الثابتة والرئيسية (كاش 24 ساعة + ETag).
- `GET /sitemap/topics.xml`: مواضيع المنتدى العامة (كاش ساعة + ETag).
- `GET /sitemap/products.xml`: منتجات وقوالب المتجر (كاش ساعتان + ETag).
- `GET /sitemap/directory.xml`: روابط وأقسام دليل المواقع (كاش 6 ساعات + ETag).
- `GET /sitemap/knowledgebase.xml`: مقالات المساعدة والتوثيق (كاش 12 ساعة + ETag).
- **دعم استجابة HTTP 304:** عند إرسال ترويسة `If-None-Match` مع مطابقة الـ ETag، يُرجع الخادم فورياً `304 Not Modified` لتوفير استهلاك الباندويث وتسريع الزحف.

---

## 9. غلاف الاستجابة الموحد ورموز الأخطاء (Response Envelopes & HTTP Codes)

### غلاف الاستجابة الناجحة (HTTP 200 / 201)
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```

### غلاف استجابة الخطأ (HTTP 4xx / 5xx)
```json
{
    "success": false,
    "message": "Validation failed / Unauthorized access.",
    "errors": {
        "field_name": [
            "Detailed error description."
        ]
    }
}
```

### أشهر رموز الاستجابة في النظام
| الرمز | المعنى | سيناريو الاستخدام |
|:---:|---|---|
| `200` | OK | نجاح معالجة الطلب واسترجاع البيانات |
| `201` | Created | نجاح إنشاء المورد أو السجل في النظام |
| `304` | Not Modified | تطابق كاش ETag (خرائط الموقع والملفات) |
| `401` | Unauthorized | فقدان أو عدم صلاحية الرمز أو مفتاح API |
| `403` | Forbidden | عدم امتلاك الصلاحيات أو تصريح OAuth المطلوب |
| `404` | Not Found | العنصر، الصفحة، أو العضو المطلوب غير موجود |
| `422` | Unprocessable Entity | خطأ في التحقق من صحة مدخلات النموذج |
| `429` | Too Many Requests | تجاوز الحد الأقصى لمعدل الطلبات في الدقيقة |
| `500` | Server Error | خطأ غير متوقع في الخادم (محمي أمنياً) |
