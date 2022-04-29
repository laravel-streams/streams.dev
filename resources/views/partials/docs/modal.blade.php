<div
    id="packages_model"
    class="hidden flex min-w-screen h-screen animated fadeIn faster  fixed  left-0 top-0 justify-center items-center inset-0 z-50 outline-none focus:outline-none bg-black">

    <button class="absolute top-8 left-8 text-white" onclick="this.parentNode.classList.toggle('hidden');">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div class="flex flex-col space-y-4 p-8 overflow-y-scroll max-h-screen">

        @foreach (Streams::entries('packages')->get() as $package)    
        <div class="flex flex-col p-8 bg-white shadow-md hover:shodow-lg rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex flex-col ml-3">
                        <div class="font-medium leading-none">{{ $package->name }}</div>
                        <p class="text-sm text-gray-600 mt-1" style="max-width: 16rem;">
                            {{ $package->description }}
                        </p>
                    </div>
                </div>
                <a href="{{ $package->decorate('docs') }}"
                    class="flex-no-shrink bg-black px-5 ml-4 py-2 text-sm shadow-sm hover:shadow-lg hover:bg-accent hover:border-accent font-medium tracking-wider border-2 border-black text-white rounded-full">Docs</a>
            </div>
        </div>
        @endforeach

    </div>

</div>
