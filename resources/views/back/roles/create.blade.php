@extends('layouts.master-back')
@section('title', 'Create New User')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Create New Role</h5>
            <form action="{{ route('back.roles.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3 col-md-10">
                        <x-input-label for="Name" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" required
                            autocomplete="name" placeholder="Enter name" />
                        <div>
                            <x-text-input id="defaultCheck3" class="form-check-input" type="checkbox" name="permissionArray[select_all]]" />
                            <x-input-label for="defaultCheck3" class="form-check-label" value='Select All' />
                        </div>
                    </div>
                    <div class="form-group col-12 mt-2">
                        <div class="row">
                            @if (count($groups) > 0)
                                @foreach ($groups as $item)
                                    <div class="form-check mb-3 col-md-6">
                                        <x-text-input id="formCheckcolor{{$item->id}}" class="form-check-input" type="checkbox"
                                            name="permissionArray[{{$item->name}}]" />
                                        <x-input-label for="roleId[{{$item->id}}]" id="formCheckcolor{{$item->id}}" class="form-check-label"
                                            value='{{ $item->name }}' />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <input type="submit" value="Add Role" class="btn btn-dark">
                </div>
            </form>
        </div>
</div>@endsection
