@if(session('success'))
    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">{{ session('error') }}</div>
@endif

@if(session('info'))
    <div class="alert alert-info" role="alert" style="margin-bottom: 20px;">{{ session('info') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
