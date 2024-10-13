
@extends('layout')

@section('content')
<div class="container">
    <h1>Approve Stock Entries</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <h2>Pending Approval for Edit</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($editEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <form action="{{ route('stock-entries.approve', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                        <form action="{{ route('stock-entries.reject', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Pending Approval for Delete</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deleteEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <form action="{{ route('stock-entries.approve', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                        <form action="{{ route('stock-entries.reject', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Pending Approval</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approveEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <form action="{{ route('stock-entries.confimRequestApproval', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                        <form action="{{ route('stock-entries.rejectRequestApproval', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
