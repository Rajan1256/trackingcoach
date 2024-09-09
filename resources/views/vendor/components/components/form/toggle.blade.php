<x-slot name="input">
    <div class="pt-2" x-data="{ buttonDisabled: false }">
        <button x-on:click="buttonDisabled = !buttonDisabled"
                x-bind:class="buttonDisabled ? 'bg-indigo-600' : 'bg-gray-200'"
                type="button"
                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                role="switch" aria-checked="false">
            <span class="sr-only">Use setting</span>
            <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
            <span
                    x-bind:class="buttonDisabled ? 'translate-x-5' : 'translate-x-0'"
                    aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
        </button>
    </div>
</x-slot>
