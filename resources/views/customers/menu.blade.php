@php
    $items = [
        'customers.program-overview.index',
        'customers.daily-questions.index',
        'customers.history.index',
        'customers.verbatim.index',
        'customers.tests.index',
        'customers.interviews.index',
        'customers.reviews.index',
        'customers.notes.index',
        'customers.files.index',
        'customers.timeouts.index',
        'customers.show',
        'customers.edit',
    ];

    $name = \Illuminate\Support\Facades\Route::currentRouteName();
    if (!in_array($name, $items)) {
        foreach ($items as $item) {
            $exploded = explode('.', $item);
            if (count($exploded) > 2) {
                array_pop($exploded);
            }
            $exploded = implode('.', $exploded);
            if (\Illuminate\Support\Str::startsWith($name, $exploded)) {
                $name = $item;
            }
        }
    }
@endphp

<x-sidebar-links start="{{ $name }}" :customer="$customer">
    
    <x-sidebar-link href="{{ route('customers.history.index', [$customer]) }}"
                    name="customers.history.index">Repoting Dashboard</x-sidebar-link>                

    @php
    $val = url()->current() == route('customers.interviews.index', [$customer]) || url()->current() == route('customers.reviews.index', [$customer])  ? '1' : '0';
    @endphp
                    
    <div x-data="{ selected: <?php echo $val; ?> }" class="border-0" class="p-3">
    <!-- The accordion items -->
        <div >
            <!-- Accordion item 1 -->
            <div>
                <!-- The button that toggles the accordion item -->
                <button @click="selected !== 1 ? selected = 1 : selected = <?php echo $val; ?>"
                    class="w-full flex justify-between items-center p-3 ">
                    <!-- The title of the accordion item -->
                    <h3 class="text-md font-medium text-gray-600">Survey</h3>
                    <!-- The icon that indicates whether the accordion item is expanded or collapsed -->
                    <div>
                        <span class="text-lg transition-all block"
                            :class="selected === 1 ? 'rotate-45' : ''">↓</span>
                    </div>
                </button>
                <div x-cloak x-show="selected === 1" class="text-sm text-black/50 p-3"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95">
                    <ul class="p-0">
                        @can('viewAny', new App\Models\Interview())
                        <li class="{{url()->current() == route('customers.interviews.index', [$customer]) ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'}} p-2"><a href="{{ route('customers.interviews.index', [$customer]) }}" class="text-md font-medium text-gray-600" id="360-interviews"
                            name="customers.interviews.index">{{ __('360 Interviews') }}</a></li>
                        @endcan
                        <li class="{{url()->current() == route('customers.reviews.index', [$customer]) ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'}} p-2"><a href="{{ route('customers.reviews.index', [$customer]) }}" class="text-md font-medium text-gray-600"
                        name="customers.reviews.index">{{ __('Mini-survey') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>                
    <x-sidebar-link href="{{ route('customers.program-overview.index', [$customer]) }}" id="program-overview"
                    name="customers.program-overview.index">{{ __('Milestones') }}</x-sidebar-link>

    <x-sidebar-link href="{{ route('customers.daily-questions.index', [$customer]) }}" id="daily-questions"
                    name="customers.daily-questions.index">{{ __('Daily Questions') }}</x-sidebar-link>
    
    @if (current_team()->isRoot())
        <x-sidebar-link href="{{ route('customers.tests.index', [$customer]) }}"
                        name="customers.tests.index">{{ __('Tests') }}</x-sidebar-link>
    @endif

    @can('viewAny', new App\Models\Note())
        <x-sidebar-link href="{{ route('customers.notes.index', [$customer]) }}" id="notes"
                        name="customers.notes.index">{{ __('Notes') }}</x-sidebar-link>
    @endcan

    @can('viewAny', new App\Models\Asset())
        <x-sidebar-link href="{{ route('customers.files.index', [$customer]) }}"
                        name="customers.files.index">{{ __('Files') }}</x-sidebar-link>
    @endcan

    <x-sidebar-link href="{{ route('customers.timeouts.index', [$customer]) }}"
                    name="customers.timeouts.index">{{ __('Timeouts') }}</x-sidebar-link>

    @if(Auth::user()->hasCurrentTeamRole([App\Enum\Roles::COACH, App\Enum\Roles::ADMIN]))
        <x-sidebar-link href="{{ route('customers.edit', [$customer]) }}"
                        name="customers.edit">{{ __('Edit Client Account') }}</x-sidebar-link>
    @endif
</x-sidebar-links>
