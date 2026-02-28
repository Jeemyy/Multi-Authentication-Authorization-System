@extends('layouts.master-back')
@section('title', 'Create New User')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Create New User</h5>
            <form action="{{route('back.users.store')}}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <x-input-label for="Name" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" required autocomplete="name"
                            placeholder="Enter admin name" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Email" :value="__('Email')" />
                        <x-text-input id="email" class="form-control" type="text" name="email" required autocomplete="email"
                            placeholder="Enter admin email" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Password" :value="__('Password')" />
                        <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="new-password"
                            placeholder="Enter admin password" />
                    </div>
                    <input type="submit" value="Create User" class="btn btn-dark">
                </div>
            </form>
        </div>
</div>@endsection
