<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

    <!-- Styles -->
    <style>
    /* Custom styles if needed */
    </style>
</head>

<body>
    <!-- Simple Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">MyApp</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/') }}">Home <span class="sr-only">(current)</span></a>
                </li>
                @auth
                    @if (auth()->user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Manage Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Manage Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('suppliers.index') }}">Manage Suppliers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('items.index') }}">Manage Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stock-entries.statistics') }}">View Statistics</a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('manager'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Manage Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('suppliers.index') }}">Manage Suppliers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('items.index') }}">Manage Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stock-entries.statistics') }}">View Statistics</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stock-entries.approve') }}">Approve Stock Entries</a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('employee'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stock-entries.index') }}">View Stock Entries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stock-entries.create') }}">Create Stock Entry</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link block btn btn-link p-0">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endauth
            </ul>
            @auth
                @if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('manager') && !auth()->user()->hasRole('employee'))
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(name: 'roleRequests.create') }}">Request Role Upgrade</a>
                        </li>
                    </ul>
                @endif
            @endauth
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Flash Message -->
        @if (session('success'))
            <div class="alert alert-success" id="flash-message">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" id="flash-message">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Import Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript for Auto-Dismiss Flash Message -->
    <script>
    $(document).ready(function() {
        setTimeout(function() {
            $('#flash-message').fadeOut('slow');
        }, 3000); // 3 seconds
    });
    </script>
</body>

</html>
