---
title: Documentation
intro: Everything you need to know to get up and running with Laravel Streams.
path: docs
enabled: true
sort: 14
---

<!-- <div class="fancy-hero-five">
    <div class="bg-wrapper">
        <div class="container">
            <div class="text-center">
                <h1 class="heading">Find docs</h1>
                <p class="sub-heading space-xs">Find articles, help and advice for getting the most our of docall theme</p>
            </div>
            <div class="search-filter-form mt-30">
                <form action="#">
                    <input type="text" placeholder="Search Somthing..">
                    <button><img src="images/icon/54.svg" alt=""></button>
                    <select class="form-control" id="exampleFormControlSelect1">
                        <option>All</option>
                        <option>Layout</option>
                        <option>API</option>
                        <option>Doc</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div> -->


<div class="flex">

@include('partials.docs.aside')

<div class="ls-doc__content w-3/4 flex-grow pb-16 px-16">
    
    <h1 class="text-4xl sm:text-6xl lg:text-5xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-4">
        {{ $entry->title }}
    </h1>
    
    @if ($entry->intro)
    <p class="text-xl tracking-tight mb-5 opacity-40">{{ $entry->intro }}</p>
    @endif

    
    <div class="flex space-x-4 bg-white">
    
        <div class="w-1/3">
            <div class="hover:shadow-xl transition-shadow duration-1000 rounded-3xl h-full p-8 flex flex-col">
            
                <h3 class="text-2xl leading-none font-extrabold tracking-tight text-gray-900 mb-4">1.&nbsp&nbspGetting Started</h3>
                
                <p>Start here if you are new to the Laravel Streams platform or Laravel.</p>

                <div class="mt-5 space-x-4">

                    <ul class="mt-4 list-none text-xl leading-relaxed">
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/introduction">
                                <strong class="">Introduction</strong>
                                <!-- What is Laravel Streams? -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/installation">
                                <strong class="">Installation</strong>
                                <!-- Options for working with Streams. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/contributing">
                                <strong class="">Contributing</strong>
                                <!-- How to contribute to the project. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="https://github.com/sponsors/ryanthompson" target="_blank">
                                <strong class="">Sponsors</strong>
                                <!-- Support our projects. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                    </ul>

                </div>

            </div>
        </div>


        <div class="w-1/3">
            <div class="hover:shadow-xl transition-shadow duration-1000 rounded-3xl h-full p-8 flex flex-col">

                <h3 class="text-2xl leading-none font-extrabold tracking-tight text-gray-900 mb-4">2.&nbsp&nbspCore Packages</h3>
                
                <p>Know what you are looking for already? Dive right in to our core packages. </p>

                <div class="mt-5 space-x-4">

                    <ul class="mt-4 list-none text-xl leading-relaxed">
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/core/introduction">
                                <strong class="">streams/core</strong>
                                <!-- Core utilities and modeling for Streams. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/api/introduction">
                                <strong class="">streams/api</strong>
                                <!-- A universal RESTful API for Streams. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted"href="docs/ui/introduction">
                                <strong class="">streams/ui</strong>
                                <!-- A versatile UI and control panel for Streams. -->
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                    </ul>

                </div>

            </div>
        </div>
        
    
        <div class="w-1/3">
            <div class="hover:shadow-xl transition-shadow duration-1000 rounded-3xl h-full p-8 flex flex-col">

                <h3 class="text-2xl leading-none font-extrabold tracking-tight text-gray-900 mb-4">3.&nbsp&nbspMore Resources</h3>

                <p>Discover more resources from the core team and community.</p>
                
                <div class="mt-5 space-x-4">

                    <ul class="mt-4 list-none text-xl leading-relaxed">
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://discord.gg/vhz8NZC">
                                Discord
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://stackoverflow.com/search?q=laravel+streams">
                                Stack Overflow
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                        <li>
                            <a class="border-b bottom-1 border-black hover-border-solid border-dotted" href="https://github.com/laravel-streams">
                                GitHub
                            </a>
                            <span class="ml-4">&#10141;</span>
                        </li>
                    </ul>

                </div>

            </div>
        </div>

    </div>
    

</div>
</div>
