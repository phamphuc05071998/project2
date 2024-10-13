
@extends('layout')

@section('content')
<div class="container">
    <h1>Stock Entries</h1>
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

    <h2>Pending Stock Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <a href="{{ route('stock-entries.edit', $entry->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('stock-entries.destroy', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        <form action="{{ route('stock-entries.requestApproval', $entry->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Request Approval</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Waiting Approved Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($waitingApprovedEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-warning">Pending Approval</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Waiting Approved Edit Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($waitingApprovedEditEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-warning">Pending Approval</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Waiting Approved Delete Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($waitingApprovedDeleteEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-warning">Pending Approval</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Approved Stock Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvedEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-success">Approved</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Rejected Stock Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rejectedEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-danger">Rejected</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Completed Stock Entries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($completedEntries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->supplier->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <span class="badge badge-info">Completed</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
