@props([
    'type' => 'success',
    'message' => null,
    'title' => null,
    'dismissible' => true,
    'timeout' => 4000,
])

<x-toast :type="$type" :message="$message ?? $slot" :title="$title" :timeout="$timeout" />
