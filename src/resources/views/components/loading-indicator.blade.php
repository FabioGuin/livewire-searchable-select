<div {{ $attributes->merge(['class' => 'loading-indicator text-secondary']) }}></div>

<style>
    .loading-indicator {
        position: relative;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        border: 2px inset #858585;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

