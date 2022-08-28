<script>
    const searchComponent = function () {
        return {
            q: null,
            results: [],
            search: async function () {

                const response = await fetch("{{ URL::to('docs.json') }}");
                
                const list = await response.json();

                const options = {
                    minMatchCharLength: 3,
                    // location: 0,
                    threshold: 0.5,
                    // distance: 100,
                    // useExtendedSearch: false,
                    // ignoreLocation: false,
                    // ignoreFieldNorm: false,
                    // fieldNormWeight: 1,
                    keys: [
                        "id",
                        "title",
                        "intro",
                        "body"
                        ]
                };

                if (!this.q || this.q.length < 3) {
                    return;
                }

                const fuse = new Fuse(list, options);

                this.results = results = fuse.search(this.q);
            },
            close: function () {
                this.q = null;
                this.results = [];
            }
        }
    }
</script>

<header>

    <div class="flex items-center justify-between px-16 py-6">

        <div class="flex items-center justify-center">

            <div class="flex items-center justify-center font-bold text-black text-3xl">
                <a href="/"><img src="/img/logo.png" width="240"></a>
            </div>

            <div class="lg:flex items-center justify-center antialiased lg:ml-20 pt-1">
                <div x-data="searchComponent()" @click.away="close()" class="relative">
                    <div>
                        <x-heroicon-o-search class="h-6 w-6 inline z-2 relative ml-2 -mr-9 -mt-0.5 opacity-50" />
                        <input type="search" x-model="q" placeholder="Search"
                            class="bg-gray-100 px-6 py-3 rounded-lg pl-10 w-96" @focus="await search()"
                            @keyup.debounce.250="search()">
                    </div>
                    <div class="absolute z-10 left-0 right-0 m-0 bg-white shadow-xl border-2 rounded-lg overflow-y-scroll"
                        style="max-height: calc(100vh - 100px)" x-show="results.length" x-cloak>
                        <ul x-model="results">
                            <template x-for="(result, index) in results" :key="index">
                                <li>
                                    <a x-bind:href="result.item.href" class="block p-3 m-3 hover:bg-gray-100 rounded-lg">
                                        <strong x-text="result.item.title"></strong>
                                        <br>
                                        <small class="font-mono" x-text="result.item.id"></small>
                                        <br>
                                        <small x-text="result.item.intro"></small>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- <nav>
            <ul class="flex">
                <li><a href="/docs" class="p-2">Documentation</a></li>
                <li><a href="/discord" class="p-2">Discord</a></li>
            </ul>
        </nav> --}}

        <div class="hidden md:flex items-center justify-center gap-x-2">
            <a href="/explore/idea"
                class="px-6 py-3 font-bold bg-gray-200 hover:bg-gray-800 hover:text-white text-black outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">Explore</a>
            <a href="/docs/installation"
                class="px-6 py-3 font-bold bg-black hover:bg-gray-800 text-white outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">Get
                Started <span>&#10141;</span></a>
        </div>

    </div>

</header>
