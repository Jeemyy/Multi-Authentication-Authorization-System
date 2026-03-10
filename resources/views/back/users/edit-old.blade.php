@extends('layouts.master-back')
@section('title', 'Edit User')
@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <h5 class="card-header">Edit User</h5>
            <form action="{{route('back.users.update', $user->id)}}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <x-input-label for="Name" :value="__('Name')" />
                        <x-text-input id="name" class="form-control" type="text" name="name" required autocomplete="name"
                            placeholder="Enter admin name" value="{{$user->name}}" />
                    </div>
                    <div class="mb-3">
                        <x-input-label for="Email" :value="__('Email')" />
                        <x-text-input id="email" class="form-control" type="text" name="email" required autocomplete="email"
                            placeholder="Enter admin email" value="{{$user->email}}" />
                    </div>
                    <input type="submit" value="Update User" class="btn btn-dark">
                </div>
            </form>
        </div>
</div>@endsection
