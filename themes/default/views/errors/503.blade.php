@include('theme::errors.error', [
    'code' => '503',
    'title' => __('messages.error_503_title'),
    'message' => __('messages.error_503_text')
])
