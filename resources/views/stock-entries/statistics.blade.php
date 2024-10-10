@extends('layout')

@section('content')
    <div class="container">
        <h1>Stock Entry Statistics</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Total Entries</th>
                    <th>Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statistics as $stat)
                    <tr>
                        <td>{{ $stat->supplier->name }}</td>
                        <td>{{ $stat->total_entries }}</td>
                        <td>{{ $stat->total_quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
