
@extends('layout')

@section('content')
    <div class="container">
        <h1>Stock Entries</h1>
        <a href="{{ route('stock-entries.create') }}" class="btn btn-primary mb-3">Create Stock Entry</a>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Pending Entries -->
        <h2>Pending Entries</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingEntries as $stockEntry)
                    <tr>
                        <td>{{ $stockEntry->supplier->name }}</td>
                        <td>
                            <ul>
                                @foreach ($stockEntry->items as $item)
                                    <li>{{ $item->item->name }} ({{ $item->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($stockEntry->status) }}</td>
                        <td>
                            <a href="{{ route('stock-entries.edit', $stockEntry->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('stock-entries.destroy', $stockEntry->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to request deletion of this stock entry?')">Request Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Waiting Approved Edit Entries -->
        <h2>Waiting Approved Edit Entries</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitingApprovedEditEntries as $stockEntry)
                    <tr>
                        <td>{{ $stockEntry->supplier->name }}</td>
                        <td>
                            <ul>
                                @foreach ($stockEntry->items as $item)
                                    <li>{{ $item->item->name }} ({{ $item->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($stockEntry->status) }}</td>
                        <td>
                            <span class="text-muted">No actions available</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Waiting Approved Delete Entries -->
        <h2>Waiting Approved Delete Entries</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitingApprovedDeleteEntries as $stockEntry)
                    <tr>
                        <td>{{ $stockEntry->supplier->name }}</td>
                        <td>
                            <ul>
                                @foreach ($stockEntry->items as $item)
                                    <li>{{ $item->item->name }} ({{ $item->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($stockEntry->status) }}</td>
                        <td>
                            <span class="text-muted">No actions available</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
