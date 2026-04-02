@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm" style="border-radius: 14px;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 14px;">
        <div class="fw-semibold mb-1">{{ __('messages.seo_review_form') }}</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
