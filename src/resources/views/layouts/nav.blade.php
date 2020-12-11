<nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">{{ __('messages.layout_names.dashboard') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('devices/config') }}">{{ __('messages.layout_names.config_file') }}</a>
            </li>
        </ul>
    </div>
    <div class="mx-auto order-0">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-auto" href="{{ route('home') }}">{{ Str::upper(config('app.name')) }}</a>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link disabled" href="{{ route('handwriting') }}">{{ __('messages.layout_names.handwriting') }}</a>
            </li>
        </ul>
    </div>
</nav>
