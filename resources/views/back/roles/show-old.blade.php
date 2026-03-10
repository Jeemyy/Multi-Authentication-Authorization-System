@extends('layouts.master-back')
@section('title', 'Create New User')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Create New Role</h5>
                <div class="card-body">
                    <div class="mb-3 col-md-10">
                        <x-input-label for="Name" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" value="{{ $role->name }}" />
                    </div>
                    <div class="form-group col-12 mt-2">
                        <div class="row">
                            @if (count($groups) > 0)
                                @foreach ($groups as $item)
                                    <div class="form-check mb-3 col-md-6">
                                        {{-- <x-text-input class="form-check-input" type="checkbox" disabled @checked($role->hasPermissionTo($item->name)) /> --}}
                                        <input type="checkbox" class="form-check-input" disabled @checked($role->hasPermissionTo($item->name))>
                                        <x-input-label for="roleId[{{$item->id}}]" id="formCheckcolor{{$item->id}}" class="form-check-label"
                                            value='{{ $item->name }}' />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
        </div>
</div>@endsection
