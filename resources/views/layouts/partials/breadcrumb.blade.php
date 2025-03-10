<!-- Breadcrumb -->
@php $breadcrumbs = Breadcrumbs::generate(); @endphp
@if (!empty($breadcrumbs))
    <div class="flex items-center justify-center border-b border-gray-300 py-4 dark:border-gray-800 sm:justify-end">
        <nav class="flex w-max px-0 text-gray-700 sm:px-6" aria-label="Breadcrumb">
            <ol
                class="inline-flex flex-wrap items-center justify-center gap-2 space-x-0 text-[10px] uppercase sm:gap-0 sm:text-xs md:space-x-2 rtl:space-x-reverse">
                @foreach ($breadcrumbs as $key => $breadcrumb)
                    @if ($breadcrumb->url && !$loop->last)
                        <li>
                            <div class="flex items-center gap-1 sm:gap-4">
                                <a href="{{ $breadcrumb->url }}"
                                    class="flex items-center gap-1 rounded-lg px-2 py-1 font-semibold text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-300">
                                    {!! $breadcrumb->title !!}
                                </a>
                                <x-icon icon="slash" class="mx-1 block h-5 w-5 text-gray-400 rtl:rotate-180" />
                            </div>
                        </li>
                    @else
                        <li aria-current="page">
                            <div class="flex items-center">
                                <span
                                    class="ms-1 flex items-center gap-1 rounded-lg px-2 py-1 font-semibold text-primary-500 dark:text-primary-300 md:ms-2">
                                    {!! $breadcrumb->title !!}
                                </span>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif
