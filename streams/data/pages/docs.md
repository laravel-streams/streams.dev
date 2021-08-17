---
title: Documentation
intro: Everything you need to know to get up and running with Laravel Streams.
path: docs
enabled: true
sort: 14
---


<h1 class="text-center block text-4xl sm:text-6xl lg:text-5xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 sm:mt-14 sm:mb-10">
    {{ $entry->title }}
</h1>

@if ($entry->intro)
<p class="text-center block text-2xl tracking-tight mb-10 opacity-40">{{ $entry->intro }}</p>
@endif



<div class="flex p-20 bg-white">
    
    <div class="flex-grow-1 self-center">
        <h1 class="text-4xl sm:text-6xl lg:text-6xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 mb-8 sm:mt-14 sm:mb-10">1.&nbsp&nbspGetting Started</h1>
        
        <p class="max-w-screen-lg text-lg text-gray-700 sm:text-2xl sm:leading-10 font-medium mb-10 sm:mb-11">Start here if you are new to the Laravel Streams platform or Laravel. {{-- <a href="/mission#your-team-and-clients-will-love-it" class="font-bold text-black border-b-2 border-black border-dotted" onmouseover="this.classList.remove('border-dotted');" onmouseout="this.classList.add('border-dotted');">Your team and clients will love it.</a> --}}</p>    

        <div class="mt-10 space-x-4">

            <ul class="mt-4 list-none text-3xl leading-relaxed">
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/introduction">
                        <strong class="">Introduction:</strong> What is Laravel Streams?
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/installation">
                        <strong class="">Installation:</strong> Options for working with Streams.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/contributing">
                        <strong class="">Contributing:</strong> How to contribute to the project.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="https://github.com/sponsors/ryanthompson" target="_blank">
                        <strong class="">Sponsors:</strong> Support our projects.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
            </ul>

        </div>
    </div>

    <div class="w-5/12">
        <img src="/img/undraw_celebrating.svg" class="w-7/12 m-auto">
    </div>
    
</div>



<div class="flex p-20 bg-white">
    
    <div class="self-center w-4/12">
        <img src="/img/undraw_adventure_map.svg" class="w-8/12 m-auto">
    </div>

    <div class="flex-grow-1 self-center">
        <h1 class="text-4xl sm:text-6xl lg:text-6xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 mb-8 sm:mt-14 sm:mb-10">2.&nbsp&nbspCore Packages</h1>
        
        <p class="max-w-screen-lg text-lg text-gray-700 sm:text-2xl sm:leading-10 font-medium mb-10 sm:mb-11">Know what you are looking for already? Dive right in to our core packages. {{-- <a href="/mission#your-team-and-clients-will-love-it" class="font-bold text-black border-b-2 border-black border-dotted" onmouseover="this.classList.remove('border-dotted');" onmouseout="this.classList.add('border-dotted');">Your team and clients will love it.</a> --}}</p>    

        <div class="mt-10 space-x-4">

            <ul class="mt-4 list-none text-3xl leading-relaxed">
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/core/introduction">
                        <strong class="">streams/core:</strong> Core utilities and modeling for Streams.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/api/introduction">
                        <strong class="">streams/api:</strong> A universal RESTful API for Streams.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
                <li>
                    <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/ui/introduction">
                        <strong class="">streams/ui:</strong> A versatile UI and control panel for Streams.
                    </a>
                    <span class="ml-4">&#10141;</span>
                </li>
            </ul>

        </div>
    </div>
    
</div>


<div class="mx-auto px-4">

    <div class="flex pb-24">

        <div class="grid gap-8">


            <div class="col-span-3 xl:col-span-2 c-callout gap-8">
                <div class="">                   
                    
                    <div class="mt-8">
                        
                        <h2 class="text-3xl font-bold">Core Packages</h2>

                        <p>
                            Know what you are looking for already? Dive right in to our core packages.
                        </p>

                        <ul class="mt-4 list-disc ml-5">
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/core/introduction">Streams Core</a> <span class="text-xs font-mono">(alhpa)</span></li>
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/api/introduction">Streams API</a> <span class="text-xs font-mono">(alhpa)</span></li>
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/ui/introduction">Streams UI</a></li>
                            <!-- <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/cli/introduction">Streams CLI</a></li> -->
                        </ul>

                    </div>

                </div>
            </div>

            <div class="">
                <div>
                    <h2 class="text-3xl font-bold">Community Resources</h2>
                    <p>
                        The core team and community work together.
                    </p>
                    <ul class="mt-4 list-disc ml-5">
                        <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://discord.gg/vhz8NZC">Discord</a></li>
                        <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://stackoverflow.com/search?q=laravel+streams">Stack Exchange</a></li>
                        <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://github.com/laravel-streams">GitHub</a></li>
                        <!-- <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://www.youtube.com/channel/UC4a-uVtWOHNCduY5T7_Q4wA">YouTube</a></li> -->
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
