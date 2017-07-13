<!doctype html>
<html @php(language_attributes())>
@include('partials.head')
<body id="application" @php(body_class())>
    @php(do_action('get_header'))
    @include('partials.header')
    @include('partials.sidemenu')
    <div class="wrap container" role="document">
        <div class="content">
            <div class="row">
                <main class="main col">
                    @yield('content')
                </main>
                @if (App\display_sidebar())
                    <aside class="sidebar col">
                        @include('partials.sidebar')
                    </aside>
                @endif
            </div>
        </div>
    </div>
    @php(do_action('get_footer'))
    @include('partials.footer')
    @php(wp_footer())
</body>
</html>
