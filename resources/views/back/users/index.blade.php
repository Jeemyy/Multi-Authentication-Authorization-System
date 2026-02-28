@extends('layouts.master-back')
@section('title', 'Users')
@section('content')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title" style="font-size: 20px; font-weight: 700">
                Users
            </div>
            @if (permission('add_user'))
                <a href="{{ route('back.users.create') }}" class="btn btn-primary">
                    Create New User
                </a>
            @endif
        </div>
        @if (session('success'))
            <span class='alert alert-success' width='100%'>{{ session('success') }}</span>
        @endif
        <div class="card">
            <div class="table-responsive text-nowrap p-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="display: flex; align-items: center; justify-content: center; ">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($users) > 0)
                            @foreach ($users as $item)
                                <tr>
                                    <td><strong>{{ $loop->iteration }}</strong></td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td style="display: flex; gap: 5px; align-items: center; justify-content: center;">
                                        @if (permission('show_user'))
                                            <a href="#" class="btn btn-info">Show</a>
                                        @endif

                                        @if (permission('edit_user'))
                                            <a href="{{ route('back.users.edit', $item->id) }}"
                                                class="btn btn-secondary">Edit</a>
                                        @endif

                                        @if (permission('delete_user'))
                                            <form action="{{ route('back.users.destroy', $item->id) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <input type="submit" value="Delete" class="btn btn-danger">
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
