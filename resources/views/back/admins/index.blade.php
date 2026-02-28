@extends('layouts.master-back')
@section('title', 'Admins')
@section('content')

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title" style="font-size: 20px; font-weight: 700">
                Admins
            </div>

            <a href="{{ route('back.admins.create') }}" class="btn btn-primary">
                Create New Admin
            </a>
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
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($admins) > 0)
                            @foreach ($admins as $item)
                                <tr>
                                    <td><strong>{{ $loop->index + 1 }}</strong></td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td>
                                        @if (count($item->getRoleNames()) > 0)
                                            <span class="badge bg-label-primary me-1">
                                                {{ $item->getRoleNames()[0] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('back.admins.edit', $item->id)}}" class="btn btn-secondary">Edit</a>
                                        <form action="{{route('back.admins.destroy', $item->id)}}" method="POST" style="display: inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <input type="submit" value="Delete" class="btn btn-danger">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <span class="alert alert-danger">
                                There are no Admins yet
                            </span>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
