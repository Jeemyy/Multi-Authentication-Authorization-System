@extends('layouts.master-back')
@section('title', 'Update Admin')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Update Admin</h5>
            <form action="{{route('back.admins.update', $admin->id)}}" method="POST">
                @csrf
                @method("PUT")
                <div class="card-body">
                    <div class="mb-3">
                        <x-input-label for="Name" :value="__('Name')" />
                        <input id="name" class="form-control" type="text" name="name" autocomplete="name"
                            placeholder="Enter admin name" value="{{ $admin->name }}" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Email" :value="__('Email')" />
                        <input id="email" class="form-control" type="text" name="email" autocomplete="email"
                            placeholder="Enter admin email" value="{{ $admin->email }}" />
                    </div>
                    <div class="mb-3">

                        <select id="defaultSelect" class="form-select" name="role">
                          {{-- <option disabled selected>Select Role</option> --}}
                          @foreach ($roles as $item)
                          <option value="{{ $item->name }}" {{ $admin->getRoleNames()->contains($item->name) ? "selected" : "" }}>{{ $item->name }}</option>
                          @endforeach
                        </select>
                    </div>
                    <input type="submit" value="Update Admin" class="btn btn-dark">
                </div>
            </form>
        </div>
</div>@endsection
