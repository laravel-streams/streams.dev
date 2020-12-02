<div id="blurryscroll" aria-hidden="true"></div>
<div class="o-navbar @if($entry->intro) o-navbar__has-intro @endif">
    <div class="container mx-auto">
        <div class="o-navbar__list-wrapper">
            <ul class="c-navbar c-navbar__left">
                <li class="c-navbar__item">
                    <a href="/">
                        <x-heroicon-o-home class="c-navbar__icon" />
                    </a>
                </li>
            </ul>
            <ul class="c-navbar c-navbar__right">
                <li class="c-navbar__item">
                    <a href="/docs/introduction" data-streams="toggle-color-mode">
                        <x-heroicon-o-color-swatch class="c-navbar__icon" />
                    </a>
                </li>
                @if(Request::segment(1)=="docs" && Request::segment(2))
                <li class="c-navbar__item">
                    <a data-mobile-menu-toggle class="block md:hidden">
                        <x-heroicon-o-menu-alt-1 class="c-navbar__icon" />
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
