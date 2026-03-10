@extends('layouts.master-back')
@section('title', 'Show Role')
@section('content')
    <div class="w-full p-6 mx-auto">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-8/12 md:flex-0">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                        <div class="border-black/12.5 rounded-t-2xl border-b-0 border-solid p-6 pb-0">
                            <div class="flex items-center">
                                <p class="mb-0 dark:text-white/80">Show Role</p>
                            </div>
                        </div>
                        <div class="flex-auto p-6">
                            <p class="leading-normal uppercase dark:text-white dark:opacity-60 text-sm">User Information</p>
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-full max-w-full px-3 shrink-0 md:w-6/6 md:flex-0">
                                    <div class="mb-4">
                                        <label for="username"
                                            class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Username</label>
                                        <input type="text" name="name" placeholder="name..."
                                            value="{{ $role->name }}"
                                            class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" disabled>
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                                    </div>
                                </div>
                                <div class="form-group col-12 mt-2 ms-5">
                                    <div class="row">
                                        @if (count($groups) > 0)
                                            @foreach ($groups as $item)
                                                <div class="form-check mb-3 col-md-6">
                                                    {{-- <x-text-input id="formCheckcolor{{$item->id}}" class="form-check-input" type="checkbox"
                                            name="permissionArray[{{$item->name}}]" /> --}}
                                                    <input type="checkbox" name="permissionArray[{{ $item->name }}]"
                                                        id="formCheckcolor{{ $item->id }}" class="form-check-input"
                                                        @checked($role->hasPermissionTo($item->name)) disabled>
                                                    <x-input-label for="roleId[{{ $item->id }}]"
                                                        id="formCheckcolor{{ $item->id }}" class="form-check-label"
                                                        value='{{ $item->name }}' />
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection
