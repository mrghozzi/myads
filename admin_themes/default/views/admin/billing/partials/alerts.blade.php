<div id="billing-alerts-wrapper">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert" style="border-radius: 16px; background: rgba(23, 198, 102, 0.12); color: #0d6838;">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                <i class="feather-check fs-5"></i>
            </div>
            <div class="flex-grow-1 fw-semibold">{{ session('success') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert" style="border-radius: 16px; background: rgba(234, 77, 77, 0.12); color: #841818;">
            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                <i class="feather-alert-triangle fs-5"></i>
            </div>
            <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert" style="border-radius: 16px; background: rgba(61, 199, 190, 0.12); color: #166560;">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                <i class="feather-info fs-5"></i>
            </div>
            <div class="flex-grow-1 fw-semibold">{{ session('info') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 16px; background: rgba(234, 77, 77, 0.12); color: #841818;">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="feather-x-circle fs-5"></i>
                </div>
                <div class="fw-bold">{{ __('messages.error_occurred') ?? 'يرجى مراجعة الأخطاء التالية:' }}</div>
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li class="fw-medium">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

{{-- Global Billing AJAX Toast Container & Scripts --}}
<div id="billing-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 99999; max-width: 420px; pointer-events: none;"></div>

<script>
    if (typeof window.showBillingToast === 'undefined') {
        window.showBillingToast = function(message, type) {
            type = type || 'success';
            var container = document.getElementById('billing-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'billing-toast-container';
                container.className = 'position-fixed top-0 end-0 p-3';
                container.style.zIndex = '99999';
                container.style.maxWidth = '420px';
                container.style.pointerEvents = 'none';
                document.body.appendChild(container);
            }

            var toastEl = document.createElement('div');
            var isSuccess = type === 'success';
            var isWarning = type === 'warning';
            var bgGradient = isSuccess 
                ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' 
                : (isWarning ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)');
            var iconClass = isSuccess ? 'feather-check-circle' : (isWarning ? 'feather-alert-triangle' : 'feather-x-circle');

            toastEl.className = 'toast show border-0 shadow-lg mb-2 text-white';
            toastEl.style.borderRadius = '14px';
            toastEl.style.background = bgGradient;
            toastEl.style.boxShadow = '0 12px 28px rgba(0,0,0,0.18)';
            toastEl.style.pointerEvents = 'auto';
            toastEl.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
            toastEl.style.opacity = '0';
            toastEl.style.transform = 'translateY(-15px) scale(0.96)';

            toastEl.innerHTML = `
                <div class="d-flex align-items-center p-3 gap-2">
                    <i class="${iconClass} fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1 fw-bold fs-13 lh-base">${message}</div>
                    <button type="button" class="btn-close btn-close-white shadow-none ms-1" style="font-size: 0.75rem;" aria-label="Close"></button>
                </div>
            `;

            function closeToast() {
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(-15px) scale(0.96)';
                setTimeout(function () { if (toastEl.parentNode) toastEl.remove(); }, 300);
            }

            toastEl.querySelector('.btn-close').addEventListener('click', closeToast);
            container.appendChild(toastEl);

            requestAnimationFrame(function() {
                toastEl.style.opacity = '1';
                toastEl.style.transform = 'translateY(0) scale(1)';
            });

            setTimeout(closeToast, 4500);
        };
    }

    if (typeof window.copyBillingText === 'undefined') {
        window.copyBillingText = function(text, btnEl) {
            if (!navigator.clipboard) {
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                try { document.execCommand('copy'); } catch (err) {}
                document.body.removeChild(textArea);
            } else {
                navigator.clipboard.writeText(text);
            }

            if (btnEl) {
                var originalHtml = btnEl.innerHTML;
                btnEl.innerHTML = '<i class="feather-check text-success"></i>';
                btnEl.classList.add('btn-copy-success');
                setTimeout(function() {
                    btnEl.innerHTML = originalHtml;
                    btnEl.classList.remove('btn-copy-success');
                }, 1800);
            }
            if (window.showBillingToast) {
                window.showBillingToast('{{ __("messages.copied_to_clipboard") ?? "تم النسخ للحافظة بنجاح!" }}', 'success');
            }
        };
    }
</script>
