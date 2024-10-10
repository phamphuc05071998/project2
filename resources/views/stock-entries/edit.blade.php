
@extends('layout')

@section('content')
<div class="container">
    <h1>Edit Stock Entry</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('stock-entries.update', $stockEntry->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $supplier->id == $stockEntry->supplier_id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="items">Items</label>
            <div id="items">
                @foreach($stockEntry->items as $index => $item)
                    <div class="item mt-2">
                        <select name="items[{{ $index }}][id]" class="form-control item-select" required>
                            <option value="">Select Item</option>
                            @foreach($allItems as $allItem)
                                <option value="{{ $allItem->id }}" {{ $allItem->id == $item->item_id ? 'selected' : '' }}>
                                    {{ $allItem->name }} (In Stock: {{ $allItem->quantity }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="items[{{ $index }}][quantity]" placeholder="Quantity" class="form-control mt-2" required min="1" value="{{ $item->quantity }}">
                        <button type="button" class="btn btn-danger remove-item mt-2">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-item" class="btn btn-secondary mt-2">Add Item</button>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update Stock Entry</button>
    </form>
</div>

<script>
    document.getElementById('supplier_id').addEventListener('change', function() {
        var supplierId = this.value;
        fetch(`/items-by-supplier/${supplierId}`)
            .then(response => response.json())
            .then(data => {
                var itemSelects = document.querySelectorAll('.item-select');
                itemSelects.forEach(select => {
                    select.innerHTML = '<option value="">Select Item</option>';
                    data.forEach(item => {
                        select.innerHTML += `<option value="${item.id}" data-quantity="${item.quantity}">${item.name} (In Stock: ${item.quantity})</option>`;
                    });
                });
            });
    });

    document.getElementById('add-item').addEventListener('click', function() {
        var itemsDiv = document.getElementById('items');
        var itemCount = itemsDiv.getElementsByClassName('item').length;
        var newItemDiv = document.createElement('div');
        newItemDiv.classList.add('item', 'mt-2');
        newItemDiv.innerHTML = `
            <select name="items[${itemCount}][id]" class="form-control item-select" required>
                <option value="">Select Item</option>
            </select>
            <input type="number" name="items[${itemCount}][quantity]" placeholder="Quantity" class="form-control mt-2" required min="1">
            <button type="button" class="btn btn-danger remove-item mt-2">Remove</button>
        `;
        itemsDiv.appendChild(newItemDiv);

        // Fetch items for the new select element
        var supplierId = document.getElementById('supplier_id').value;
        fetch(`/items-by-supplier/${supplierId}`)
            .then(response => response.json())
            .then(data => {
                var newItemSelect = newItemDiv.querySelector('.item-select');
                newItemSelect.innerHTML = '<option value="">Select Item</option>';
                data.forEach(item => {
                    newItemSelect.innerHTML += `<option value="${item.id}" data-quantity="${item.quantity}">${item.name} (In Stock: ${item.quantity})</option>`;
                });
            });
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-item')) {
            e.target.parentElement.remove();
        }
    });
</script>
@endsection
