<x-app-layout>
    <div>
        <a href="{{ $backUrl }}" class="flex items-center font-semibold hover:underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="ml-2">All ideas (or back to chosen category with filters)</span>
        </a>
    </div>

    <livewire:idea-show :idea="$idea" :voteCount="$voteCount" />

    @can('update', $idea)
        <livewire:edit-idea :idea="$idea" />
    @endcan

    @can('delete', $idea)
        <livewire:delete-idea :idea="$idea" />
    @endcan

    @auth
        <livewire:mark-idea-as-spam :idea="$idea" />
    @endauth

    @admin
    <livewire:mark-idea-as-not-spam :idea="$idea" />
    @endadmin

    </div><!-- end ideas and button container -->

    <div class="comments-container relative space-y-6 md:ml-22 pt-4 my-8 mt-1">
        <div class="comment-container relative bg-white rounded-xl flex mt-4">
            <div class="flex flex-col md:flex-row flex-1 px-4 py-6">
                <div class="flex-none">
                    <a href="#">
                        <img src="https://source.unsplash.com/200x200/?face&crop=face&v=2" alt="avatar"
                            class="w-14 h-14 rounded-xl">
                    </a>
                </div>
                <div class="w-full md:mx-4">
                    {{-- <h4 class="text-xl font-semibold">
                        <a href="#" class="hover:underline">A random title can go here</a>
                    </h4> --}}
                    <div class="text-gray-600 mt-3 transition duration-150">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur fugiat
                        in rerum tempore natus sint cum sapiente. Facere aliquid illo ducimus optio
                        aperiam neque, dolorem iste! Perspiciatis officiis sunt molestias est
                        similique? Fuga aspernatur delectus facilis ipsa dignissimos, et numquam
                        cupiditate animi laboriosam iusto perspiciatis! Delectus repellendus
                        oloribus commodi nesciunt officiis neque aliquam! Vero eos tenetur dolor
                        deserunt ipsam quidem! Voluptatem fugiat maiores tempore, deserunt ea
                        cupiditate ex fugit! Officiis, minima! Tenetur sunt voluptatibus quas
                        expedita vel consectetur delectus, fugit eius autem sint minima vitae,
                        pariatur vero, obcaecati eum placeat optio officiis? Aut officiis
                        voluptatem deserunt excepturi, vel odit magnam.
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <div
                            class="flex items-center text-xs text-gray-400 font-semibold 
                        space-x-2">
                            <div class="font-bold text-gray-900">John Doe</div>
                            <div>&bull;</div>
                            <div>10 hours ago</div>
                        </div>
                        <div class="flex items-center space-x-2" x-data="{ isOpen: false}">
                            <div class="relative">
                                <button
                                    class="relative bg-gray-100 hover:bg-gray-200 border rounded-full 
                                h-7 transition duration-150 ease-in px-3"
                                    @click="isOpen = !isOpen">
                                    <svg fill="currentColor" width="24" height="6">
                                        <path d="M2.97.061A2.969 
                                2.969 0 000 3.031 2.968 2.968 0 002.97 6a2.97 2.97 0 100-5.94zm9.184
                                 0a2.97 2.97 0 100 5.939 2.97 2.97 0 100-5.939zm8.877 0a2.97 2.97 0 
                                 10-.003 5.94A2.97 2.97 0 0021.03.06z" style="color: rgba(163, 163, 163, .5)">
                                    </svg>
                                </button>
                                <ul class="absolute w-44 text-left font-semibold bg-white 
                                shadow-dialog rounded-xl z-10 py-3 md:ml-8 top-8 md:top-6 right-0
                                md:left-0"
                                    x-cloak x-show.transition.origin.top.left="isOpen" @click.away="isOpen = false"
                                    @keydown.escape.window="isOpen = false">
                                    <li><a href="#"
                                            class="hover:bg-gray-100 block transition 
                                    duration-150 ease-in px-5 py-3">Mark
                                            as Spam</a></li>
                                    <li><a href="#"
                                            class="hover:bg-gray-100 block transition 
                                    duration-150 ease-in px-5 py-3">Delete
                                            Post</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end of comment-container -->
    </div><!-- end of comments-container -->
</x-app-layout>
