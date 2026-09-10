/**
 * MYADS Official Showcase & Portal Application Script
 * Features: Instant Bilingual Localization (EN/AR), Dark/Light Theme,
 * Feature Filtering, Quickstart Tabs, Clipboard Copy, Interactive FAQ.
 */

// --- Bilingual Translation Dictionary ---
const translations = {
  en: {
    // Navigation
    nav_brand: "MYADS",
    nav_features: "Features",
    nav_mobile: "Mobile App",
    nav_arch: "Architecture",
    nav_quickstart: "Quick Start",
    nav_docs: "Documentation",
    nav_community: "Community",
    nav_star: "Star",
    lang_btn: "العربية 🇸🇦",

    // Hero Section
    badge_release: "v4.5.6 Released",
    badge_tech: "Laravel 12 + Flutter 3.27+",
    badge_mit: "100% Free & Open Source (MIT)",
    hero_title: "The Ultimate Open-Source Platform for <span class='gradient-text'>Traffic Exchange, Social Network & Monetization</span>",
    hero_desc: "Empower your digital community with peer-to-peer advertising, YouTube-style video hub & TikTok shorts, high-throughput social timeline, digital marketplace, and a native Flutter mobile app.",
    btn_get_started: "Quick Start Guide 🚀",
    btn_github: "GitHub Repository",
    btn_mobile_app: "Explore Mobile App 📱",
    terminal_title: "Quick Install with Composer",
    btn_copy: "Copy",
    copied_toast: "Installation command copied to clipboard!",

    // Stats
    stat_engines_num: "6+",
    stat_engines_label: "Integrated Micro-Engines",
    stat_open_num: "100%",
    stat_open_label: "Open-Source & Free (MIT)",
    stat_speed_num: "95%+",
    stat_speed_label: "Faster Single-Pass SQL Queries",
    stat_langs_num: "14+",
    stat_langs_label: "Pre-Built Localizations",

    // Features Section
    features_badge: "Enterprise Capabilities",
    features_title: "One Unified Ecosystem, Infinite Possibilities",
    features_subtitle: "Replace dozens of fragmented plugins and scripts with a cohesive, ultra-optimized platform designed for high traffic and maximum monetization.",
    filter_all: "All Features",
    filter_ads: "Ad Exchange",
    filter_video: "Video & Clips",
    filter_social: "Social Community",
    filter_mobile: "Mobile App",
    filter_store: "Marketplace",
    filter_security: "Security & Shield",

    // Feature Cards
    f1_title: "High-Yield Ad Exchange & Traffic Surf",
    f1_desc: "Full-cycle advertising ecosystem supporting responsive banner ads, text links, auto-surf traffic exchange, and YouTube views exchange with daily PTS settlement.",
    f1_tag1: "Auto-Surf", f1_tag2: "Smart Ads", f1_tag3: "PTS Points",

    f2_title: "YouTube-Style Video Hub & Shorts",
    f2_desc: "Immersive video experience with 16:9 spotlight banner, floating Picture-in-Picture (PIP) mini-player, and vertical 9:16 Shorts with touch swipe gestures.",
    f2_tag1: "PIP Mini-Player", f2_tag2: "Vertical Clips", f2_tag3: "HLS Streaming",

    f3_title: "Next-Gen Social Timeline Feed",
    f3_desc: "N+1 query optimized timeline with multimedia posts (Audio waveform, Videos, 4-photo adaptive grid, Files), animated reactions, mentions, and real-time notifications.",
    f3_tag1: "Audio Waveforms", f3_tag2: "Reactions", f3_tag3: "SSE Real-Time",

    f4_title: "First-Party Flutter Mobile App",
    f4_desc: "Native Android & iOS application (myads_app) with 100% feed parity, Clips video player, Hexagon publisher avatars, and Android Keystore AES-256 token storage.",
    f4_tag1: "Flutter 3.27+", f4_tag2: "iOS & Android", f4_tag3: "Riverpod + Dio",

    f5_title: "Digital Marketplace & Freelance Escrow",
    f5_desc: "Multi-vendor digital store for scripts, plugins, and graphics with PTS pricing, dedicated product wiki documentation, and a full freelance services bidding escrow.",
    f5_tag1: "Digital Downloads", f5_tag2: "Escrow Bidding", f5_tag3: "Product Wikis",

    f6_title: "Superdesign Duralux Admin Suite",
    f6_desc: "Hyper-fast administrative control panel with dark-mode aesthetic, single-pass aggregated SQL queries, zero-reload AJAX plugin manager, and live theme customizer.",
    f6_tag1: "Zero-Reload AJAX", f6_tag2: "Theme Customizer", f6_tag3: "System Monitor",

    f7_title: "Anti-Fraud Shield v2 & Member Privacy",
    f7_desc: "Multi-layered security system featuring browser fingerprinting, proxy & VPN detection, public UID anti-enumeration, and Sanctum 2-layer authentication.",
    f7_tag1: "Anti-Fraud v2", f7_tag2: "Anti-Enumeration", f7_tag3: "Sanctum Auth",

    f8_title: "Extensible Plugins & RESTful API",
    f8_desc: "Modular plugin engine with automatic migrations, hook lifecycle system, and comprehensive REST API endpoints for seamless third-party integrations.",
    f8_tag1: "Dynamic Plugins", f8_tag2: "OAuth / REST API", f8_tag3: "Webhooks",

    // Mobile App Spotlight
    mobile_badge: "Cross-Platform Power",
    mobile_title: "Take Your Community Everywhere with <span class='gradient-text'>MYADS Mobile</span>",
    mobile_desc: "A production-ready Flutter 3.27+ mobile application built with Clean Architecture, Riverpod state management, and high-performance native rendering.",
    m_feat1_title: "100% Social Feed & Clips Parity",
    m_feat1_desc: "Enjoy the full web experience on mobile, including expandable posts, audio waveforms, and TikTok-style vertical clips with zero lag.",
    m_feat2_title: "Hardware-Backed Security",
    m_feat2_desc: "Sanctum Bearer tokens and API keys are safeguarded by Android Keystore and iOS Keychain AES-256 encryption.",
    m_feat3_title: "Comprehensive 24-Guide Wiki",
    m_feat3_desc: "Every architecture layer, network interceptor, and release pipeline is thoroughly documented on our official wiki.",
    btn_view_app_wiki: "Read Mobile App Wiki 📖",
    btn_app_github: "Mobile App Repo 📱",

    // Architecture
    arch_badge: "Built for Scale",
    arch_title: "Engineered on Modern, Robust Foundations",
    arch_subtitle: "Zero bloat. Built from the ground up using industry-standard enterprise frameworks and architectural best practices.",
    arch_card1_title: "Backend Core",
    arch_card1_b: "Laravel 12",
    arch_card1_i1: "PHP 8.2+ Typed Architecture",
    arch_card1_i2: "Laravel Sanctum Authentication",
    arch_card1_i3: "Real-Time Server-Sent Events (SSE)",
    arch_card1_i4: "Single-Pass SQL Optimization",

    arch_card2_title: "Frontend Stack",
    arch_card2_b: "Modern Blade",
    arch_card2_i1: "Bootstrap 5 Responsive Grid",
    arch_card2_i2: "Vanilla ES6+ Dynamic Modules",
    arch_card2_i3: "Zero-Reload AJAX Management",
    arch_card2_i4: "Dark & Light Glassmorphism UI",

    arch_card3_title: "Mobile Client",
    arch_card3_b: "Flutter 3.27+",
    arch_card3_i1: "Dart 3.10+ Clean Architecture",
    arch_card3_i2: "Riverpod State Management",
    arch_card3_i3: "Dio HTTP Client & Interceptors",
    arch_card3_i4: "Hardware Keystore Encryption",

    arch_card4_title: "Data & Scaling",
    arch_card4_b: "High Performance",
    arch_card4_i1: "MySQL / MariaDB (InnoDB Engine)",
    arch_card4_i2: "Selective Cache Warmup Engine",
    arch_card4_i3: "Fingerprint Anti-Fraud Shield",
    arch_card4_i4: "Multi-Gateway Billing System",

    // Quick Start
    qs_badge: "Rapid Deployment",
    qs_title: "Up and Running in Under 5 Minutes",
    qs_subtitle: "Choose between our intuitive graphical web wizard or command-line developer installation.",
    tab_gui: "Web GUI Wizard",
    tab_cli: "Composer & Artisan CLI",
    tab_reqs: "System Requirements",

    gui_step1_title: "1. Download & Extract",
    gui_step1_desc: "Download the latest v4.5.6 release archive from GitHub and extract it to your web server root directory.",
    gui_step2_title: "2. Open Web Installer",
    gui_step2_desc: "Navigate to your domain in the browser (e.g., https://yourdomain.com/install) to launch the step-by-step setup wizard.",
    gui_step3_title: "3. Connect Database & Launch",
    gui_step3_desc: "Enter your database credentials, set up the administrator account, and click Install. MYADS takes care of the rest!",

    cli_step1_title: "1. Clone Repository & Install Dependencies",
    cli_step2_title: "2. Configure Environment & App Key",
    cli_step3_title: "3. Run Migrations & Start Server",

    req1: "PHP 8.2 or Higher",
    req2: "MySQL 5.7+ or MariaDB 10.3+",
    req3: "BCMath & Ctype PHP Extensions",
    req4: "cURL & DOM PHP Extensions",
    req5: "Fileinfo & JSON Extensions",
    req6: "Mbstring & OpenSSL Extensions",
    req7: "PDO & PDO_MySQL Drivers",
    req8: "XML & Tokenizer Extensions",
    req9: "Apache (mod_rewrite) or Nginx",

    // Documentation Hub
    docs_badge: "Knowledgebase",
    docs_title: "Comprehensive Documentation for Every Need",
    docs_subtitle: "Explore in-depth guides, API specifications, and extension tutorials designed for site owners and developers alike.",
    doc1_title: "Complete Web Documentation",
    doc1_desc: "Everything from initial setup and SMTP mail servers to Smart Ads configuration and PTS point economy.",
    doc1_link: "Explore Web Docs ➔",

    doc2_title: "Mobile App 24-Guide Wiki",
    doc2_desc: "Full architectural breakdown of the Flutter client: Dio networking, FCM notifications, and release pipelines.",
    doc2_link: "Explore App Wiki ➔",

    doc3_title: "RESTful API Reference",
    doc3_desc: "Detailed endpoint documentation with Sanctum Bearer token authentication for third-party client integration.",
    doc3_link: "View API Specs ➔",

    doc4_title: "Plugin & Theme Guides",
    doc4_desc: "Build custom plugins, hooks, widgets, and themes using the Zero-Reload Superdesign theme engine.",
    doc4_link: "Read Developer Guide ➔",

    doc5_title: "Anti-Fraud & Security Shield",
    doc5_desc: "How MYADS prevents bot clicks, traffic fraud, user enumeration, and unauthorized token tampering.",
    doc5_link: "View Security Specs ➔",

    doc6_title: "Changelog & Release Notes",
    doc6_desc: "Detailed version history tracking every feature, fix, and performance boost from v1.0.0 to v4.5.6.",
    doc6_link: "Read Changelogs ➔",

    // Community & Sponsors
    comm_title: "Join the Growing MYADS Community",
    comm_desc: "MYADS is an independent, community-driven open-source project. Star our repository, contribute code, or support our ongoing development!",
    btn_sponsor_kofi: "Sponsor on Ko-Fi ☕",
    btn_sponsor_patreon: "Sponsor on Patreon ❤️",
    btn_contribute: "Contribute on GitHub 🐙",

    // FAQ
    faq_badge: "FAQ",
    faq_title: "Frequently Asked Questions",
    faq_subtitle: "Got questions? We've got answers. Here are the most common inquiries about MYADS.",
    faq1_q: "Is MYADS completely free and open source?",
    faq1_a: "Yes! MYADS is licensed under the permissive MIT License. You are free to use, modify, self-host, and customize it for both personal and commercial projects without any licensing fees.",
    faq2_q: "Does MYADS include the mobile app source code?",
    faq2_a: "Absolutely. The official native Flutter mobile client is open-sourced under the mrghozzi/myads_app repository with full documentation, state management setup, and CI/CD pipelines.",
    faq3_q: "Can I monetize my platform with real payments?",
    faq3_a: "Yes. MYADS supports multiple billing gateways, paid VIP subscriptions, PTS point purchases, paid advertisement deposits, and digital store downloads with seller commission splits.",
    faq4_q: "How does the Anti-Fraud Shield protect traffic exchange?",
    faq4_a: "MYADS uses browser fingerprinting, unique session tokens, active window focus verification, proxy/VPN detection, and rate limiting to ensure only genuine, human traffic is rewarded with PTS points.",

    // Footer
    footer_desc: "The open-source traffic exchange, social network, and monetization platform built with Laravel 12 and Flutter.",
    footer_col1: "Ecosystem",
    footer_col2: "Developers",
    footer_col3: "Community",
    footer_credits: "Developed with passion by <a href='https://github.com/mrghozzi' target='_blank'><strong>Zoubair Ghozzi (@mrghozzi)</strong></a>.",
    footer_mit: "Released under the <a href='https://github.com/mrghozzi/myads/blob/main/LICENSE' target='_blank'>MIT License</a>.",
    back_to_top: "Back to top ↑"
  },

  ar: {
    // Navigation
    nav_brand: "MYADS",
    nav_features: "المميزات",
    nav_mobile: "تطبيق الجوال",
    nav_arch: "البنية التقنية",
    nav_quickstart: "التشغيل السريع",
    nav_docs: "التوثيق",
    nav_community: "المجتمع",
    nav_star: "نجمة",
    lang_btn: "English 🌐",

    // Hero Section
    badge_release: "الإصدار v4.5.6 جاهز",
    badge_tech: "Laravel 12 + Flutter 3.27+",
    badge_mit: "مفتوح المصدر 100% ومجاني (MIT)",
    hero_title: "المنصة المتكاملة مفتوحة المصدر <span class='gradient-text'>لتبادل الزيارات، شبكات التواصل الاجتماعي والربح الرقمي</span>",
    hero_desc: "امنح مجتمعك الرقمي منصة فائقة القوة تجمع بين شبكة إعلانات متطورة، تبادل الزيارات والمشاهدات بنظام يوتيوب وتيك توك، مجتمع تفاعلي، متجر رقمي، وتطبيق جوال فلاتر أصلي.",
    btn_get_started: "دليل البدء السريع 🚀",
    btn_github: "مستودع GitHub",
    btn_mobile_app: "استكشف تطبيق الجوال 📱",
    terminal_title: "التثبيت السريع عبر Composer",
    btn_copy: "نسخ",
    copied_toast: "تم نسخ أمر التثبيت إلى الحافظة!",

    // Stats
    stat_engines_num: "+6",
    stat_engines_label: "محركات وأنظمة مدمجة",
    stat_open_num: "100%",
    stat_open_label: "مفتوح المصدر بالكامل (MIT)",
    stat_speed_num: "+95%",
    stat_speed_label: "تسريع استعلامات قواعد البيانات",
    stat_langs_num: "+14",
    stat_langs_label: "لغة مجهزة مسبقاً",

    // Features Section
    features_badge: "إمكانيات فائقة ومتقدمة",
    features_title: "نظام بيئي موحد بإمكانيات لا محدودة",
    features_subtitle: "تخلّص من عشرات السكربتات والإضافات المتفرقة واستمتع بنظام متكامل ومحسّن لتحمل الزيارات العالية وتحقيق أقصى ربحية ممكنة.",
    filter_all: "جميع المميزات",
    filter_ads: "شبكة الإعلانات",
    filter_video: "الفيديو والكليبس",
    filter_social: "المجتمع التفاعلي",
    filter_mobile: "تطبيق الجوال",
    filter_store: "المتجر والخدمات",
    filter_security: "الحماية والأمان",

    // Feature Cards
    f1_title: "شبكة الإعلانات وتبادل الزيارات",
    f1_desc: "منظومة إعلانية متكاملة تدعم البانرات التفاعلية، الروابط النصية، تبادل الزيارات التلقائي (Auto-Surf)، ومشاهدات يوتيوب مع تسوية يومية لنقاط PTS.",
    f1_tag1: "تصفح تلقائي", f1_tag2: "إعلانات ذكية", f1_tag3: "نقاط PTS",

    f2_title: "مركز الفيديو ومقاطع الكليبس القصيرة",
    f2_desc: "تجربة فيديو عصرية مع فيديو مميز في الواجهة بنسبة 16:9، مشغل مصغر عائم (Picture-in-Picture)، ومقاطع قصيرة 9:16 بإيماءات التمرير كالتيك توك.",
    f2_tag1: "مشغل عائم PIP", f2_tag2: "فيديوهات قصيرة", f2_tag3: "بث عالي السرعة",

    f3_title: "الخلاصة والمجتمع التفاعلي الحديث",
    f3_desc: "خط زمني تفاعلي محسن بدون استعلامات فائضة، يدعم المنشورات المتعددة (موجات صوتية، فيديوهات، معرض صور شبكي، ملفات)، التفاعلات والإشعارات اللحظية.",
    f3_tag1: "موجات صوتية", f3_tag2: "تفاعلات المنشورات", f3_tag3: "بث SSE المباشر",

    f4_title: "تطبيق الجوال الأصلي بتقنية Flutter",
    f4_desc: "تطبيق متكامل لنظامي أندرويد وآيفون (myads_app) بتطابق كامل مع الويب، مشغل كليبس، إطارات الناشرين السداسية، وتشفير الأجهزة AES-256.",
    f4_tag1: "فلاتر 3.27+", f4_tag2: "أندرويد وآبل", f4_tag3: "Riverpod + Dio",

    f5_title: "متجر المنتجات الرقمية والوساطة للخدمات",
    f5_desc: "سوق رقمي متعدد البائعين لبيع السكربتات والإضافات والتصاميم بنقاط PTS، مع توثيق ويكي مستقل لكل منتج، ومنصة وساطة وضمان لمزايدات الخدمات المصغرة.",
    f5_tag1: "تحميلات رقمية", f5_tag2: "وساطة الخدمات", f5_tag3: "ويكي المنتجات",

    f6_title: "لوحة تحكم إدارية Superdesign Duralux",
    f6_desc: "لوحة تحكم فائقة السرعة بتصميم داكن مريح للعين، تقارير مجمعة فورية، إدارة الإضافات والقوالب بتقنية AJAX بدون إعادة تحميل، ومخصص المظهر المباشر.",
    f6_tag1: "AJAX بدون إعادة تحميل", f6_tag2: "مخصص المظهر", f6_tag3: "مراقب النظام",

    f7_title: "درع مكافحة الاحتيال v2 وحماية الخصوصية",
    f7_desc: "نظام أمني متعدد الطبقات مع فحص بصمة المتصفح، كشف البروكسي وVPN، معرفات عامة مشفرة لمنع استكشاف الحسابات، ومصادقة Sanctum الثنائية.",
    f7_tag1: "مكافحة الاحتيال v2", f7_tag2: "حظر الاستكشاف", f7_tag3: "مصادقة Sanctum",

    f8_title: "نظام الإضافات المرن وواجهة REST API",
    f8_desc: "محرك إضافات ديناميكي يثبّت الجداول تلقائياً، مع نظام خطافات (Hooks)، وواجهات برمجية REST API كاملة لربط أي تطبيقات خارجية بسهولة.",
    f8_tag1: "إضافات ديناميكية", f8_tag2: "OAuth / REST API", f8_tag3: "Webhooks",

    // Mobile App Spotlight
    mobile_badge: "قوة عبر جميع الأنظمة",
    mobile_title: "انطلق بمجتمعك في كل مكان مع <span class='gradient-text'>تطبيق MYADS للجوال</span>",
    mobile_desc: "تطبيق جوال جاهز للإنتاج مبني على Flutter 3.27+ بأحدث معايير Clean Architecture وإدارة الحالة عبر Riverpod وأداء native فائق.",
    m_feat1_title: "تطابق 100% مع الخلاصة ومقاطع الكليبس",
    m_feat1_desc: "عش تجربة الويب الكاملة على هاتفك، بما يشمل المنشورات القابلة للتوسيع، مشغل الصوت المدمج، ومقاطع الفيديو الرأسية بسلاسة تامة.",
    m_feat2_title: "أمان مدعوم بعتاد الهاتف التشفيري",
    m_feat2_desc: "رموز الدخول Sanctum ومفاتيح الـ API مشفرة عتادياً داخل Android Keystore و iOS Keychain بتشفير AES-256 المتين.",
    m_feat3_title: "ويكي شامل يضم 24 دليلاً تقنياً",
    m_feat3_desc: "كل طبقة معمارية، وكل معترض شبكي، وخطوات التجميع وتوقيع التطبيق موثقة بالتفصيل في الويكي الرسمي للتطبيق.",
    btn_view_app_wiki: "قراءة ويكي تطبيق الجوال 📖",
    btn_app_github: "مستودع تطبيق الجوال 📱",

    // Architecture
    arch_badge: "مصمم للتحمل والأداء العالي",
    arch_title: "مبني على أسس برمجية عصرية ومتينة",
    arch_subtitle: "خالٍ من الحشو البرمجي. بُني من الصفر باستخدام أحدث معايير ومكتبات هندسة البرمجيات الاحترافية.",
    arch_card1_title: "النواة الخلفية (Backend)",
    arch_card1_b: "Laravel 12",
    arch_card1_i1: "بنية برمجية قوية مع PHP 8.2+",
    arch_card1_i2: "مصادقة آمنة عبر Laravel Sanctum",
    arch_card1_i3: "بث أحداث مباشر Server-Sent Events (SSE)",
    arch_card1_i4: "استعلامات SQL مجمعة عالية السرعة",

    arch_card2_title: "الواجهة الأمامية (Frontend)",
    arch_card2_b: "Modern Blade",
    arch_card2_i1: "شبكة متجاوبة مع Bootstrap 5",
    arch_card2_i2: "وحدات تفاعلية Vanilla ES6+ سريعة",
    arch_card2_i3: "تحديثات AJAX بدون إعادة تحميل",
    arch_card2_i4: "واجهة زجاجية Glassmorphism داكنة ومضيئة",

    arch_card3_title: "تطبيق الجوال (Mobile)",
    arch_card3_b: "Flutter 3.27+",
    arch_card3_i1: "بنية برمجية نظيفة مع Dart 3.10+",
    arch_card3_i2: "إدارة حالة متطورة عبر Riverpod",
    arch_card3_i3: "عميل شبكي ذكي Dio مع معترضات",
    arch_card3_i4: "تشفير عتادي في Keystore / Keychain",

    arch_card4_title: "البيانات والتوسع (Data)",
    arch_card4_b: "أداء فائق",
    arch_card4_i1: "قواعد بيانات MySQL / MariaDB (محرك InnoDB)",
    arch_card4_i2: "نظام تسخين الذاكرة المؤقتة التلقائي",
    arch_card4_i3: "درع فحص البصمة ضد الاحتيال",
    arch_card4_i4: "بوابات دفع متعددة ونظام اشتراكات",

    // Quick Start
    qs_badge: "تشغيل سريع وبسيط",
    qs_title: "انطلق بموقعك خلال أقل من 5 دقائق",
    qs_subtitle: "اختر بين معالج التثبيت المرئي السهل عبر المتصفح، أو التثبيت البرمجي عبر سطر الأوامر.",
    tab_gui: "المعالج المرئي (Web GUI)",
    tab_cli: "سطر الأوامر (Composer & CLI)",
    tab_reqs: "متطلبات السيرفر",

    gui_step1_title: "1. تنزيل واستخراج الملفات",
    gui_step1_desc: "قم بتنزيل أحدث حزمة إصدار v4.5.6 من GitHub واستخرج محتوياتها داخل المجلد الرئيسي لسيرفر الويب الخاص بك.",
    gui_step2_title: "2. فتح معالج التثبيت في المتصفح",
    gui_step2_desc: "افتح الرابط في متصفحك (مثلاً: https://yourdomain.com/install) لبدء المعالج التفاعلي خطوة بخطوة.",
    gui_step3_title: "3. ربط قاعدة البيانات والانطلاق",
    gui_step3_desc: "أدخل بيانات الاتصال بقاعدة البيانات، وأنشئ حساب المدير، ثم اضغط على زر التثبيت. سيتولى السكربت إعداد كل شيء تلقائياً!",

    cli_step1_title: "1. استنساخ المستودع وتثبيت الحزم",
    cli_step2_title: "2. ضبط ملف الإعدادات وتوليد المفتاح",
    cli_step3_title: "3. تشغيل التهجيرات وبدء السيرفر",

    req1: "إصدار PHP 8.2 أو أحدث",
    req2: "قواعد بيانات MySQL 5.7+ أو MariaDB 10.3+",
    req3: "إضافات BCMath و Ctype",
    req4: "إضافات cURL و DOM",
    req5: "إضافات Fileinfo و JSON",
    req6: "إضافات Mbstring و OpenSSL",
    req7: "مشغلات PDO و PDO_MySQL",
    req8: "إضافات XML و Tokenizer",
    req9: "سيرفر Apache (mod_rewrite) أو Nginx",

    // Documentation Hub
    docs_badge: "مركز المعرفة والشروحات",
    docs_title: "توثيق شامل ومفصل لكل جزء في المنصة",
    docs_subtitle: "استكشف أدلة الإعداد، ومواصفات واجهة الـ API، وشروحات التطوير المصممة لأصحاب المواقع والمطورين على حد سواء.",
    doc1_title: "توثيق منصة الويب الشامل",
    doc1_desc: "كل ما تحتاجه بدءاً من التثبيت وضبط بريد SMTP، وصولاً لإعداد الإعلانات الذكية واقتصاد نقاط PTS.",
    doc1_link: "استكشف شروحات الويب ➔",

    doc2_title: "ويكي تطبيق الجوال (24 دليلاً)",
    doc2_desc: "تحليل معماري شامل لتطبيق Flutter: شبكة Dio، إشعارات FCM، وبناء وتوقيع النسخ النهائية.",
    doc2_link: "استكشف ويكي التطبيق ➔",

    doc3_title: "دليل واجهات برمجة التطبيقات (API)",
    doc3_desc: "توثيق نقاط النهاية البرمجية بالتفصيل مع مصادقة Sanctum Bearer لربط أي تطبيقات ومواقع خارجية.",
    doc3_link: "عرض مواصفات الـ API ➔",

    doc4_title: "دليل مطوري الإضافات والقوالب",
    doc4_desc: "برمج إضافاتك ومربعاتك الجانبية وقوالبك المخصصة بكل سهولة باستخدام محرك Superdesign بدون إعادة تحميل.",
    doc4_link: "قراءة دليل المطورين ➔",

    doc5_title: "درع الحماية ومكافحة الاحتيال",
    doc5_desc: "كيف يمنع MYADS نقرات الروبوتات، وتزييف الزيارات، واستكشاف أسماء المستخدمين، والتلاعب بالرموز.",
    doc5_link: "عرض مواصفات الأمان ➔",

    doc6_title: "سجل التغييرات والتحديثات",
    doc6_desc: "سجل تاريخي يوثق كل ميزة، وإصلاح، وتسريع للأداء منذ الإصدار الأول v1.0.0 وحتى v4.5.6.",
    doc6_link: "قراءة سجل التغييرات ➔",

    // Community & Sponsors
    comm_title: "انضم إلى مجتمع MYADS المتنامي",
    comm_desc: "سكربت MYADS هو مشروع حر ومستقل مدفوع بحب المجتمع. ضع نجمة على المستودع، أو شارك في التطوير، أو ادعم استمرار المشروع!",
    btn_sponsor_kofi: "ادعم عبر Ko-Fi ☕",
    btn_sponsor_patreon: "ادعم عبر Patreon ❤️",
    btn_contribute: "المساهمة على GitHub 🐙",

    // FAQ
    faq_badge: "الأسئلة الشائعة",
    faq_title: "كل ما تود معرفته عن MYADS",
    faq_subtitle: "إليك إجابات وافية ومباشرة عن أبرز التساؤلات حول السكربت وكيفية الاستفادة منه.",
    faq1_q: "هل سكربت MYADS مجاني ومفتوح المصدر بالكامل؟",
    faq1_a: "نعم تماماً! السكربت مرخص بموجب رخصة MIT المفتوحة والمرنة. يمكنك استخدامه، وتعديله، واستضافته لمشاريعك الشخصية أو التجارية مجاناً وبدون أي رسوم ترخيص.",
    faq2_q: "هل كود تطبيق الجوال مشمول ومتاح مجاناً؟",
    faq2_a: "نعم بكل تأكيد. الكود المصدري الرسمي لتطبيق الجوال Flutter متاح في مستودع mrghozzi/myads_app مع كامل الشروحات وخطوات البناء والإنتاج.",
    faq3_q: "هل يمكنني تحقيق دخل مالي حقيقي من المنصة؟",
    faq3_a: "نعم. يدعم MYADS بوابات دفع إلكترونية متعددة، والاشتراكات الشهرية المدفوعة للعضويات المميزة (VIP)، وشراء النقاط، وإيداعات المعلنين، وعمولات مبيعات المتجر الرقمي.",
    faq4_q: "كيف يحمي درع مكافحة الاحتيال تبادل الزيارات؟",
    faq4_a: "يعتمد السكربت على تقنيات فحص بصمة المتصفح الرقمية، وتأكيد تركيز النافذة النشطة، وكشف شبكات البروكسي وVPN، وتحديد معدل الطلبات لضمان أن الزيارات حقيقية وبشرية فقط.",

    // Footer
    footer_desc: "المنصة مفتوحة المصدر لتبادل الزيارات، شبكات التواصل الاجتماعي، والربح الرقمي المبنية على Laravel 12 و Flutter.",
    footer_col1: "المنظومة",
    footer_col2: "المطورون",
    footer_col3: "المجتمع",
    footer_credits: "تم التطوير بكل فخر وشغف بواسطة <a href='https://github.com/mrghozzi' target='_blank'><strong>زبیر الغزي (@mrghozzi)</strong></a>.",
    footer_mit: "مرخص بموجب ترخيص <a href='https://github.com/mrghozzi/myads/blob/main/LICENSE' target='_blank'>MIT License</a>.",
    back_to_top: "العودة للأعلى ↑"
  }
};

// --- State Management ---
let currentLang = localStorage.getItem('myads_lang') || 'ar';
let currentTheme = localStorage.getItem('myads_theme') || 'dark';

// --- Initialization on DOM Load ---
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initLanguage();
  initFilterTabs();
  initQuickstartTabs();
  initFaqAccordion();
  initMobileMenu();
  initCopyButtons();
});

// --- Theme Management ---
function initTheme() {
  document.documentElement.setAttribute('data-theme', currentTheme);
  updateThemeIcon();

  const themeBtn = document.getElementById('theme-toggle-btn');
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', currentTheme);
      localStorage.setItem('myads_theme', currentTheme);
      updateThemeIcon();
    });
  }
}

function updateThemeIcon() {
  const themeBtn = document.getElementById('theme-toggle-btn');
  if (themeBtn) {
    themeBtn.innerHTML = currentTheme === 'dark' ? '☀️' : '🌙';
    themeBtn.setAttribute('title', currentTheme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode');
  }
}

// --- Language Management ---
function initLanguage() {
  applyLanguage(currentLang);

  const langBtn = document.getElementById('lang-switch-btn');
  if (langBtn) {
    langBtn.addEventListener('click', () => {
      currentLang = currentLang === 'ar' ? 'en' : 'ar';
      localStorage.setItem('myads_lang', currentLang);
      applyLanguage(currentLang);
    });
  }
}

function applyLanguage(lang) {
  document.documentElement.setAttribute('lang', lang);
  document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');

  const dict = translations[lang] || translations.en;

  // Update all elements with data-i18n attribute
  document.querySelectorAll('[data-i18n]').forEach((el) => {
    const key = el.getAttribute('data-i18n');
    if (dict[key]) {
      el.innerHTML = dict[key];
    }
  });

  // Update language switch button text
  const langBtn = document.getElementById('lang-switch-btn');
  if (langBtn) {
    langBtn.innerHTML = dict.lang_btn;
  }
}

// --- Feature Filtering Tabs ---
function initFilterTabs() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.feature-card');

  filterBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      filterBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');

      const filter = btn.getAttribute('data-filter');

      cards.forEach((card) => {
        const category = card.getAttribute('data-category');
        if (filter === 'all' || category === filter) {
          card.style.display = 'flex';
          card.style.opacity = '1';
        } else {
          card.style.display = 'none';
          card.style.opacity = '0';
        }
      });
    });
  });
}

// --- Quickstart Installation Tabs ---
function initQuickstartTabs() {
  const tabBtns = document.querySelectorAll('.qs-tab-btn');
  const panels = document.querySelectorAll('.qs-content-panel');

  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      tabBtns.forEach((b) => b.classList.remove('active'));
      panels.forEach((p) => p.classList.remove('active'));

      btn.classList.add('active');
      const targetId = btn.getAttribute('data-target');
      const targetPanel = document.getElementById(targetId);
      if (targetPanel) {
        targetPanel.classList.add('active');
      }
    });
  });
}

// --- Interactive FAQ Accordion ---
function initFaqAccordion() {
  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach((item) => {
    const questionBtn = item.querySelector('.faq-question');
    if (questionBtn) {
      questionBtn.addEventListener('click', () => {
        const isOpen = item.classList.contains('active');
        // Close others
        faqItems.forEach((i) => i.classList.remove('active'));
        // Toggle clicked
        if (!isOpen) {
          item.classList.add('active');
        }
      });
    }
  });
}

// --- Mobile Hamburger Menu ---
function initMobileMenu() {
  const toggleBtn = document.getElementById('mobile-toggle-btn');
  const navLinks = document.getElementById('nav-links');

  if (toggleBtn && navLinks) {
    toggleBtn.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });

    // Close when clicking any nav link
    navLinks.querySelectorAll('.nav-link').forEach((link) => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
      });
    });
  }
}

// --- Copy to Clipboard with Toast ---
function initCopyButtons() {
  const copyButtons = document.querySelectorAll('.btn-copy-code');
  const toast = document.getElementById('toast-notification');

  copyButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const codeText = btn.getAttribute('data-code') || btn.parentElement.innerText;
      navigator.clipboard.writeText(codeText.trim()).then(() => {
        showToast();
      }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = codeText.trim();
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast();
      });
    });
  });

  function showToast() {
    if (!toast) return;
    const dict = translations[currentLang] || translations.en;
    toast.querySelector('.toast-text').innerText = dict.copied_toast || 'Copied to clipboard!';
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 2800);
  }
}
