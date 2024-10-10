
@extends('layout')

@section('content')
<div class="container">
    <h1>Create Stock Entry</h1>
    <form action="{{ route('stock-entries.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="items">Items</label>
            <div id="items">
                <div class="item">
                    <select name="items[0][id]" class="form-control item-select" required>
                        <option value="">Select Item</option>
                    </select>
                    <input type="number" name="items[0][quantity]" placeholder="Quantity" class="form-control mt-2" required min="1">
                </div>
            </div>
            <button type="button" id="add-item" class="btn btn-secondary mt-2">Add Item</button>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Create Stock Entry</button>
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
</script>
@endsection
