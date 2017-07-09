<div class="sidemenu">
    @if (has_nav_menu('side_navigation'))
        {!! wp_nav_menu(['theme_location' => 'side_navigation', 'menu_class' => 'nav nav-side', 'walker' => new Roots\Soil\Nav\NavWalker ]) !!}
    @endif
</div>
