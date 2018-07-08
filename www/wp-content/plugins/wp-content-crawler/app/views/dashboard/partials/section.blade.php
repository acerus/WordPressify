{{-- Parent for a section in dashboard --}}
<div class="content @yield('content-class')">
    <div class="details">
        <h2>
            <span>
                {{-- Section title--}}
                @yield('title')
            </span>

            <div class="header">
                @yield('header')
            </div>
        </h2>
        <div class="inside">
            <div class="panel-wrap wcc-settings-meta-box">
                {{-- Section content --}}
                @yield('content')
            </div>
        </div>
    </div>
</div>