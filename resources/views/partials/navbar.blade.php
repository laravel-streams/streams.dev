<div id="blurryscroll" aria-hidden="true"></div>
<div class="o-navbar @if($entry->intro) o-navbar__has-intro @endif">
    <div class="container mx-auto">
        <div class="o-navbar__list-wrapper">
            <ul class="c-navbar c-navbar__left">
                <li class="c-navbar__item">
                    <a href="/docs">
                    <x-icon-home class="c-navbar__icon" />
                    </a>
                </li>
            </ul>
            <ul class="c-navbar c-navbar__right">
                <li class="c-navbar__item">
                    <a href="/docs/introduction" data-streams="toggle-color-mode">
                        <x-icon-color class="c-navbar__icon" />
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
