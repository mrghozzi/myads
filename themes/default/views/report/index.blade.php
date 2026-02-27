@extends('theme::layouts.master')

@section('content')
<div class="content">
    <div class="grid grid-12">
        <div class="grid-column">
            <div class="widget-box">
                <!-- TITLE -->
                <p class="widget-box-title"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                <hr>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif

                <!-- ITEM PREVIEW -->
                <div class="report-item" style="padding: 20px; text-align: center;">
                    @if($type == 'link')
                        <a href="{{ $item->url }}" class="button primary" target="_blank">{{ $item->name }}&nbsp;<b><i class="fa fa-external-link"></i></b></a>
                    @elseif($type == 'banner')
                        <div style="background-image: url('{{ $item->img }}'); height: {{ $item->px == 1 ? '90' : ($item->px == 2 ? '250' : '60') }}px; width: {{ $item->px == 1 ? '728' : ($item->px == 2 ? '300' : '468') }}px; background-size: cover; background-position: center; margin: 0 auto; border: 1px solid #ddd;"></div>
                    @endif
                </div>

                <!-- FORM -->
                <form action="{{ route('report.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="s_type" value="{{ $typeId }}">
                    <input type="hidden" name="tp_id" value="{{ $item->id }}">
                    
                    <div class="form-item">
                        <div class="form-input small full">
                            <label for="txt">{{ __('messages.reason') }}</label>
                            <textarea id="txt" name="txt" rows="4" required placeholder="{{ __('messages.reason_desc') }}"></textarea>
                        </div>
                    </div>
                    
                    <br>
                    <div class="form-actions" style="text-align: center;">
                        <button type="submit" class="button secondary">{{ __('messages.submit_report') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
