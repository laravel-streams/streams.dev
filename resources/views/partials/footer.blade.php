<footer class="bg-black" aria-labelledby="footerHeading">
    
    <h2 id="footerHeading" class="sr-only">Footer</h2>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            
            <div class="flex space-x-6 md:order-2">

                <a href="/discord" target="_blank" rel="noreferrer noopener" class="text-gray-400 hover:text-gray-300">
                    <span class="sr-only">Discord</span>
                    <x-fab-discord class="h-8"/>
                </a>

                <a href="https://www.instagram.com/RyanThePyro/" target="_blank" rel="noreferrer noopener" class="text-gray-400 hover:text-gray-300">
                    <span class="sr-only">Instagram</span>
                    <x-fab-instagram class="h-8"/>
                </a>

                <a href="https://twitter.com/Laravel_Streams" target="_blank" rel="noreferrer noopener" class="text-gray-400 hover:text-gray-300">
                    <span class="sr-only">Twitter</span>
                    <x-fab-twitter class="h-8"/>
                </a>

                <a href="https://github.com/laravel-streams" class="text-gray-400 hover:text-gray-300" target="_blank" rel="noreferrer noopener">
                    <span class="sr-only">GitHub</span>
                    <x-fab-github class="h-8"/>
                </a>

            </div>
            
            <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
            &copy; {{ now()->format('Y') }} Laravel Streams. All rights reserved.
            </p>

        </div>
    </div>
  </footer>
