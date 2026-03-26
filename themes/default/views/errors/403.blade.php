@include('theme::errors.error', [
    'code' => '403',
    'title' => __('messages.error_403_title'),
    'message' => __('messages.error_403_text')
])
