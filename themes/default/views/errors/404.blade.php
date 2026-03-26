@include('theme::errors.error', [
    'code' => '404',
    'title' => __('messages.error_404_title'),
    'message' => __('messages.error_404_text')
])
