@include('theme::errors.error', [
    'code' => '500',
    'title' => __('messages.error_500_title'),
    'message' => __('messages.error_500_text')
])
