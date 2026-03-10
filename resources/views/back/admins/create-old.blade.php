@extends('layouts.master-back')
@section('title', 'Create New Admin')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Create New Admin</h5>
            <form action="{{ route('back.admins.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <x-input-label for="Name" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" required
                            autocomplete="name" placeholder="Enter admin name" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Email" :value="__('Email')" />
                        <x-text-input id="email" class="form-control" type="text" name="email" required
                            autocomplete="email" placeholder="Enter admin email" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Password" :value="__('Password')" />
                        <x-text-input id="password" class="form-control" type="password" name="password" required
                            autocomplete="new-password" placeholder="Enter admin password" />
                    </div>
                    <div class="mb-3">

                        <select id="defaultSelect" class="form-select" name="role">
                            <option disabled selected>Select Role</option>
                            @foreach ($roles as $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="submit" value="Create Admin" class="btn btn-dark">
                </div>
            </form>
        </div>
</div>@endsection
