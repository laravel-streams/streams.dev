---
title: Addons
path: addons
enabled: true
sort: 14
---
<div class="flex flex-col px-16">
    
    <h1 class="w-full text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 mb-8 sm:mt-14 sm:mb-10">Addons</h1>

    <!-- <p class="text-lg text-gray-700 sm:text-2xl sm:leading-10 font-medium mb-10 sm:mb-11">Addons <a href="/mission#your-team-and-clients-will-love-it" class="font-bold text-black border-b-2 border-black border-dotted" onmouseover="this.classList.remove('border-dotted');" onmouseout="this.classList.add('border-dotted');">Your team and clients will love it.</a></p> -->

    <ul>
    @foreach (Streams::repository('addons')->all() as $addon)
    <li>
        <a class="text-red-500 font-bold" href="/addons/{{ $addon->composer?->name }}">{{ $addon->name }}</a>
        <br><small>{{ $addon->composer?->name }}</small>
    </li>
    @endforeach
    </ul>
    
</div>
