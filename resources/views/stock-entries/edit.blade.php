
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
            <div id="items-container">
                @foreach($selectedItems as $selectedItem)
                    <div class="input-group mb-3">
                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $selectedItem->item_id }}">
                        <input type="text" class="form-control" value="{{ $selectedItem->item->name }}" readonly>
                        <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control" value="{{ $selectedItem->quantity }}" required>
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
document.getElementById('supplier_id').addEventListener('change', function() {
    var supplierId = this.value;
    var itemsContainer = document.getElementById('items-container');

    // Clear current items
    itemsContainer.innerHTML = '';

    if (supplierId) {
        fetch(`/stock-entries/items/${supplierId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach((item, index) => {
                    var itemGroup = document.createElement('div');
                    itemGroup.className = 'input-group mb-3';

                    var itemIdInput = document.createElement('input');
                    itemIdInput.type = 'hidden';
                    itemIdInput.name = `items[${index}][id]`;
                    itemIdInput.value = item.id;

                    var itemNameInput = document.createElement('input');
                    itemNameInput.type = 'text';
                    itemNameInput.className = 'form-control';
                    itemNameInput.value = item.name;
                    itemNameInput.readOnly = true;

                    var itemQuantityInput = document.createElement('input');
                    itemQuantityInput.type = 'number';
                    itemQuantityInput.name = `items[${index}][quantity]`;
                    itemQuantityInput.className = 'form-control';
                    itemQuantityInput.required = true;

                    itemGroup.appendChild(itemIdInput);
                    itemGroup.appendChild(itemNameInput);
                    itemGroup.appendChild(itemQuantityInput);

                    itemsContainer.appendChild(itemGroup);
                });
            });
    }
});
</script>
@endsection
