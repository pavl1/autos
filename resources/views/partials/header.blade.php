<header class="roof">
    <div class="container">
        <nav class="navbar">
            <a class="brand" href="{{ home_url('/') }}">
                <img class="brand-image" src="@asset('images/logo.png')" alt="">
                <div class="brand-description">Масла<br />Аксессуары<br />Автозапчасти</div>
            </a>
            <div class="search">
                {{ get_search_form() }}
            </div>
            <nav class="nav-primary">
                @if (has_nav_menu('primary_navigation'))
                    {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav nav-top', 'walker' => new Roots\Soil\Nav\NavWalker ]) !!}
                @endif
            </nav>
        </nav>
    </div>
</header>
