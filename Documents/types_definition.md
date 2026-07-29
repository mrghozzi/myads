# دليل أنواع المنشورات والتفاعلات (Types Definition)

هذا الملف يوضح دلالة الأرقام المستخدمة في أعمدة النوع (type) في قواعد البيانات الخاصة بالمنشورات (status) والتفاعلات (like).

## 1. أنواع المنشورات في جدول `status` (عمود `s_type`)

| الرقم (`s_type`) | نوع المنشور (Post Type) | ملاحظات |
| :--- | :--- | :--- |
| `0` (أو فارغ) | منشور مجتمعي عادي (نص، معرض صور، رابط، إعادة نشر) | `tp_id` = NULL |
| `1` | موقع في الدليل (Directory Listing) | `tp_id` → `directory.id` |
| `2` | موضوع نقاش عادي في المنتدى (Forum Topic) | `tp_id` → `forum.id` |
| `4` | منشور صور/معرض في المنتدى (Forum Image/Gallery Post) | `tp_id` → `forum.id` — يُنشأ عند إرفاق صور |
| `5` | مقال إخباري (News Article) | `tp_id` → `news.id` |
| `6` | طلب خدمة / طلب في السوق (Order Request) | `tp_id` → `order_requests.id` |
| `10` | فيديو (Video Post) | `tp_id` → `forum.id` |
| `11` | مقطع صوتي (Audio Post) | `tp_id` → `forum.id` |
| `12` | ملف (File Post) | `tp_id` → `forum.id` |
| `13` | موسيقى (Music Post) | `tp_id` → `forum.id` |
| `14` | مقطع قصير (Clips Post) | `tp_id` → `forum.id` |
| `100` | منشور نصي في المنتدى (Forum Text Post) | `tp_id` → `forum.id` — يُنشأ عند حذف جميع الصور من موضوع كان `s_type=4` |
| `205` | مقال في قاعدة المعرفة (Knowledgebase Article) | `tp_id` → `options.id` |
| `7867` | منتج في المتجر (Store Product) | `tp_id` → `options.id` |

### ملاحظة حول أنواع المنتدى (Forum-Linked Types)
الأنواع `2`, `4`, `100`, `10`, `11`, `12`, `13`, `14` جميعها **مرتبطة بسجل في جدول `forum`** عبر `tp_id`.
يتم تجميعها في الاستعلامات عند الحاجة للعثور على جميع المنشورات المرتبطة بمواضيع المنتدى.

- `s_type=2` → موضوع نقاش عادي (الافتراضي عند إنشاء موضوع جديد)
- `s_type=4` → موضوع يحتوي صور (يُعيَّن تلقائياً عند إرفاق صور)
- `s_type=100` → موضوع نصي بحت (يُعيَّن تلقائياً عند حذف جميع الصور من موضوع `s_type=4`)

---

## 2. أنواع الإعجابات/التفاعلات في جدول `like` (عمود `type`)

| الرقم (`type`) | الهدف من التفاعل (Reaction Target) |
| :--- | :--- |
| `1` | متابعة مستخدم (Follow User) |
| `2` | موضوع في المنتدى (Forum Topic) |
| `3` | منتج في المتجر (Store Product) |
| `4` | تعليق في المنتدى (Forum Comment) |
| `6` | طلب خدمة (Order Request) |
| `14` | مقطع قصير (Clips) |
| `22` | موقع في الدليل (Directory Site) |
| `44` | تعليق على موقع في الدليل (Directory Comment) |
| `66` | تعليق على طلب خدمة (Order Comment) |
| `205` | مقال في قاعدة المعرفة (Knowledgebase Article) |
| `206` | تعليق على مقال في قاعدة المعرفة (Knowledgebase Comment) |
| `444` | تعليق في المتجر (Store Comment) |
