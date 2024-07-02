<div {{ $attributes->merge(['class' => 'clear-icon text-secondary cursor-pointer hover:opacity-80']) }}></div>

<style>
    .clear-icon {
        position: relative;
        width: 24px;
        height: 24px;
    }

    .clear-icon:before, .clear-icon:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 14px;
        height: 2px;
        background-color: currentColor;
    }

    .clear-icon:before {
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .clear-icon:after {
        transform: translate(-50%, -50%) rotate(-45deg);
    }
</style>
