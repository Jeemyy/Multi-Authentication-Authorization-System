@extends('layouts.master-back')
@section('title', 'Users')
@section('content')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title" style="font-size: 20px; font-weight: 700">
                Users
            </div>

            <a href="{{ route('back.roles.create') }}" class="btn btn-primary">
                Create New Role
            </a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card">
            <div class="table-responsive text-nowrap p-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th style="text-align: center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($roles) > 0)
                            @foreach ($roles as $item)
                                <tr>
                                    <td><strong>{{ $loop->iteration }}</strong></td>
                                    <td>{{ $item->name }}</td>
                                    <td style="text-align: center">
                                        <a href="{{ route('back.roles.show', $item->id) }}" class="btn btn-primary">Show</a>
                                        <a href="{{ route('back.roles.edit', $item->id) }}"
                                            class="btn btn-secondary">Edit</a>
                                        {{-- <a href="#" class="btn btn-danger">Delete</a> --}}
                                        <form action="{{ route('back.roles.destroy', $item->id) }}" method="POST"
                                            style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            {{-- <a href="#" class="btn btn-danger">Delete</a> --}}
                                            <button type="submit" class='btn btn-danger'>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <span class="aler alert-danger">
                                No Roles Record Found
                            </span>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
