@extends('theme::layouts.master')

@section('content')
<div class="section-header">
    <div class="section-header-info">
        <h2 class="section-title">
            {{ __('messages.updates') ?? 'Updates' }}
        </h2>
    </div>
    <div class="section-header-actions" style="display: flex; gap: 10px;">
        <a class="button primary small" role="button" href="{{ route('store.update', $product->name) }}">&nbsp;<i class="fa fa-cloud-upload"></i>&nbsp;{{ __('messages.release_new_version') }}&nbsp;</a>
        <a class="button secondary small" role="button" href="{{ route('store.show', $product->name) }}">&nbsp;<i class="fa fa-arrow-left"></i>&nbsp;{{ __('messages.back') ?? 'Back' }}&nbsp;</a>
    </div>
</div>

<div class="grid grid-12">
    @forelse($files as $file)
        @php
            $fileDownloads = \App\Models\Short::where('sh_type', 7867)->where('tp_id', $file->id)->value('clik') ?? 0;
        @endphp
        <div class="widget-box" id="update-row-{{ $file->id }}" style="display: flex; justify-content: space-between; align-items: center; padding: 22px 24px; border-radius: 16px; margin-bottom: 12px; transition: transform .2s ease, box-shadow .2s ease;">
            <div style="display: flex; gap: 18px; align-items: center; min-width: 220px;">
                <div style="background: linear-gradient(135deg, #615dfa 0%, #413dbf 100%); width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; box-shadow: 0 4px 10px rgba(97, 93, 250, 0.35); flex-shrink: 0;">
                    <i class="fa fa-code-fork"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h3 style="margin: 0; font-size: 18px; font-weight: bold; color: #fff;">{{ $file->name }}</h3>
                        <span class="badge" style="background: rgba(97, 93, 250, 0.18); color: #8c8aff; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                            <i class="fa fa-download"></i> {{ $fileDownloads }}
                        </span>
                    </div>
                    <p style="margin: 4px 0 0; color: #8f91ac; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa fa-clock-o"></i> {{ $file->created_at ? $file->created_at->diffForHumans() : '' }}
                    </p>
                </div>
            </div>
            
            <div style="flex-grow: 1; margin: 0 30px; color: #9aa4bf; line-height: 1.6; font-size: 14px;">
                {{ Str::limit(strip_tags($file->o_valuer), 160) }}
            </div>
            
            <div style="min-width: 120px; text-align: right;">
                <button type="button" class="button white btn-delete-update" data-id="{{ $file->id }}" data-url="{{ route('store.updates.destroy', ['name' => $product->name, 'file' => $file->id]) }}" style="border-radius: 8px; padding: 9px 18px; border: 1px solid #ff4242; color: #ff4242; cursor: pointer; transition: all .2s ease;">
                    <i class="fa fa-trash"></i>&nbsp;&nbsp;{{ __('messages.delete') ?? 'Delete' }}
                </button>
            </div>
        </div>
    @empty

        <div class="widget-box profile-relationships-empty" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px;">
            <div class="profile-relationships-empty__icon" style="font-size: 48px; color: #8f91ac; margin-bottom: 20px;">
                <i class="fa fa-history" aria-hidden="true"></i>
            </div>
            <p class="widget-box-title" style="font-size: 18px; color: #fff;">{{ __('messages.no_results') ?? 'No results found.' }}</p>
        </div>
    @endforelse
</div>

@if($files->hasPages())
    <div style="margin-top: 20px;">
        {{ $files->links('pagination::bootstrap-5') }}
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-update').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('{{ __('messages.confirm_delete') ?? 'Are you sure you want to delete this update?' }}')) {
                return;
            }
            
            var url = this.getAttribute('data-url');
            var id = this.getAttribute('data-id');
            var row = document.getElementById('update-row-' + id);
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting.');
            });
        });
    });
});
</script>
@endsection
