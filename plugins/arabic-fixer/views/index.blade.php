@extends('theme::layouts.admin')

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">إصلاح اللغة العربية</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">الإضافات</li>
            <li class="breadcrumb-item">إصلاح اللغة العربية</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-12">

            @if(session('success'))
            <div class="alert alert-success mt-3 mb-3">
                {!! nl2br(e(session('success'))) !!}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger mt-3 mb-3">
                {{ session('error') }}
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">إصلاح مشكلة الرموز الغريبة (الترميز)</h5>
                </div>
                <div class="card-body">
                    <p>
                        هذه الإضافة تقوم بفحص جميع النصوص في قاعدة البيانات والبحث عن النصوص العربية التي تظهر بشكل رموز غريبة
                        (مثل <code dir="ltr">Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©</code>)
                        بسبب مشكلة ترميز قاعدة البيانات.
                    </p>
                    <p class="text-danger">
                        <strong>تنبيه:</strong> يرجى أخذ نسخة احتياطية (Backup) لقاعدة البيانات قبل إجراء عملية الإصلاح.
                    </p>

                    <div class="d-flex gap-3 mt-3">
                        {{-- Preview Button --}}
                        <form action="{{ route('admin.arabic-fixer.preview') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-lg">
                                <i class="feather-eye me-2"></i>
                                معاينة التغييرات
                            </button>
                        </form>

                        {{-- Fix Button --}}
                        <form action="{{ route('admin.arabic-fixer.run') }}" method="POST"
                              onsubmit="return confirm('هل أنت متأكد من رغبتك في تشغيل أداة الإصلاح الآن؟ تأكد من أخذ نسخة احتياطية أولاً.')">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="feather-tool me-2"></i>
                                بدء عملية الإصلاح
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Preview Results Table --}}
            @if(session('preview'))
                @php $items = session('preview'); @endphp
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">نتائج المعاينة ({{ count($items) }} حقل سيتم إصلاحه)</h5>
                    </div>
                    <div class="card-body">
                        @if(count($items) === 0)
                            <div class="alert alert-info">
                                <i class="feather-check-circle me-2"></i>
                                لم يتم العثور على أي نصوص تحتاج إصلاح. قاعدة البيانات سليمة.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 10%">الجدول</th>
                                            <th style="width: 10%">العمود</th>
                                            <th style="width: 8%">المعرّف</th>
                                            <th style="width: 33%">قبل الإصلاح</th>
                                            <th style="width: 33%">بعد الإصلاح</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td><code>{{ $item['table'] }}</code></td>
                                            <td><code>{{ $item['column'] }}</code></td>
                                            <td>{{ $item['id'] }}</td>
                                            <td dir="ltr" class="text-start text-danger" style="word-break: break-all; font-size: 0.85em;">{{ $item['before'] }}</td>
                                            <td dir="rtl" class="text-success" style="word-break: break-all;">{{ $item['after'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning mt-3">
                                <i class="feather-alert-triangle me-2"></i>
                                إذا كانت النتائج أعلاه صحيحة، اضغط على زر <strong>"بدء عملية الإصلاح"</strong> لتطبيق التغييرات.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
