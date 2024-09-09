<section aria-labelledby="profile-overview-title">
    <div class="bg-white overflow-hidden shadow p-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:space-x-5">
                <div class="flex-shrink-0">
                    <img class="mx-auto h-20 w-20 rounded-full"
                         src="{{ \Creativeorange\Gravatar\Facades\Gravatar::get(auth()->user()->email) }}"
                         alt="">
                </div>
                <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                    <p class="text-sm font-medium text-gray-600">Welcome back,</p>
                    <p class="text-xl font-bold text-gray-900 sm:text-2xl">{{ Auth::user()->name }}</p>
                    <p class="text-sm font-medium text-gray-600">{{ current_team()->company }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
