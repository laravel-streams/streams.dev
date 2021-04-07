---
title: Documentation
intro: Everything you need to know to get up and running with Laravel Streams.
enabled: true
sort: 14
---


<div class="mx-auto px-4">

    <h1 class="text-4xl sm:text-6xl lg:text-5xl leading-none font-extrabold tracking-tight w-3/5 text-gray-900 mt-10 sm:mt-14 sm:mb-10" style="max-width: 880px;">
        {{ $entry->title }}
    </h1>

    @if ($entry->intro)
    <p class="text-2xl tracking-tight mb-10 opacity-40">{{ $entry->intro }}</p>
    @endif

    <div class="flex">

        <div class="grid gap-8">
            <div class="col-span-3 xl:col-span-2 bg-white c-callout gap-8">
                <div class="">
                    <div class="col-span-3 xl:col-span-2 ">

                        <h2 class="text-3xl font-bold">Getting Started</h2>

                        <p>
                            Start here if you are new to the Laravel Streams platform or Laravel.
                        </p>

                        <ul class="mt-4 list-disc ml-5">
                            <li>
                                <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/introduction"><strong>Introduction:</strong> What is Laravel Streams?</a>
                            </li>
                            <li>
                                <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/installation">Installation</a>
                            </li>
                            <li>
                                <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/contributing">Contributing</a>
                            </li>
                            <li>
                                <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="https://github.com/sponsors/ryanthompson" target="_blank">Sponsors</a>
                            </li>
                        </ul>

                    </div>
                    <div class="mt-8">
                        
                        <h2 class="text-3xl font-bold">Core Packages</h2>

                        <p>
                            Know what you are looking for already? Dive right in to our core packages.
                        </p>

                        <ul class="mt-4 list-disc ml-5">
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/core/introduction">Streams Core</a></li>
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/ui/introduction">Streams UI</a></li>
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/api/introduction">Streams API</a></li>
                            <li><a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="docs/cli/introduction">Streams CLI</a></li>
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
