
@extends('layout')

@section('content')
    <div class="container">
        <h1>Approve Stock Entries</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
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
                @foreach ($stockEntries as $stockEntry)
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
                            <form action="{{ route('stock-entries.approveactions', ['stockEntry' => $stockEntry->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this stock entry?')">Approve</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
