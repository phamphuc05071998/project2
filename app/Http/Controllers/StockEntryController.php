<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\StockEntry;
use App\Models\StockEntryTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockEntryController extends Controller
{
    public function index()
    {
        $pendingEntries = StockEntry::where('user_id', Auth::id())->where('status', 'pending')->get();
        $waitingApprovedEditEntries = StockEntry::where('user_id', Auth::id())->where('status', 'waiting_approved_edit')->get();
        $waitingApprovedDeleteEntries = StockEntry::where('user_id', Auth::id())->where('status', 'waiting_approved_delete')->get();

        return view('stock-entries.index', compact('pendingEntries', 'waitingApprovedEditEntries', 'waitingApprovedDeleteEntries'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('stock-entries.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $stockEntry = StockEntry::create([
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $stockEntry->items()->create([
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);

            // Cập nhật số lượng tồn kho của item
            $itemModel = Item::find($item['id']);
            $itemModel->quantity -= $item['quantity'];
            $itemModel->save();
        }

        return redirect()->route('stock-entries.index')->with('success', 'Stock entry created successfully and pending approval.');
    }

    public function edit(StockEntry $stockEntry)
    {
        $this->authorize('update', $stockEntry);

        if ($stockEntry->status == 'confirmed') {
            return redirect()->route('stock-entries.index')->with('error', 'Cannot edit a confirmed stock entry.');
        }

        $suppliers = Supplier::all();
        return view('stock-entries.edit', compact('stockEntry', 'suppliers'));
    }

    public function update(Request $request, StockEntry $stockEntry)
    {
        $this->authorize('update', $stockEntry);

        if ($stockEntry->status == 'confirmed') {
            return redirect()->route('stock-entries.index')->with('error', 'Cannot update a confirmed stock entry.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Xóa các mục tạm hiện có liên quan đến phiếu nhập kho này
        StockEntryTemp::where('stock_entry_id', $stockEntry->id)->delete();

        // Lưu yêu cầu chỉnh sửa vào bảng tạm
        foreach ($request->items as $item) {
            StockEntryTemp::create([
                'stock_entry_id' => $stockEntry->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'action' => 'edit',
            ]);
        }

        $stockEntry->status = 'waiting_approved_edit';
        $stockEntry->save();

        return redirect()->route('stock-entries.index')->with('success', 'Stock entry update requested and pending approval.');
    }

    public function destroy(StockEntry $stockEntry)
    {
        $this->authorize('delete', $stockEntry);

        if ($stockEntry->status == 'confirmed') {
            return redirect()->route('stock-entries.index')->with('error', 'Cannot delete a confirmed stock entry.');
        }

        // Xóa các mục tạm hiện có liên quan đến phiếu nhập kho này
        StockEntryTemp::where('stock_entry_id', $stockEntry->id)->delete();

        // Lưu yêu cầu xóa vào bảng tạm mà không cần item_id
        StockEntryTemp::create([
            'stock_entry_id' => $stockEntry->id,
            'action' => 'delete',
        ]);

        $stockEntry->status = 'waiting_approved_delete';
        $stockEntry->save();

        return redirect()->route('stock-entries.index')->with('success', 'Stock entry deletion requested and pending approval.');
    }

    public function approveIndex()
    {
        $stockEntries = StockEntry::whereIn('status', ['pending', 'waiting_approved_edit', 'waiting_approved_delete'])->get();
        return view('stock-entries.approve', compact('stockEntries'));
    }

    public function approve(Request $request, StockEntry $stockEntry)
    {
        $this->authorize('approve', $stockEntry);

        $tempEntries = StockEntryTemp::where('stock_entry_id', $stockEntry->id)->get();

        foreach ($tempEntries as $tempEntry) {
            if ($tempEntry->action == 'edit') {
                // Cập nhật số lượng tồn kho của item cũ
                foreach ($stockEntry->items as $oldItem) {
                    $itemModel = Item::find($oldItem->item_id);
                    $itemModel->quantity += $oldItem->quantity;
                    $itemModel->save();
                }

                // Cập nhật phiếu nhập kho
                $stockEntry->items()->delete();
                $stockEntry->items()->create([
                    'item_id' => $tempEntry->item_id,
                    'quantity' => $tempEntry->quantity,
                ]);

                // Cập nhật số lượng tồn kho của item mới
                $itemModel = Item::find($tempEntry->item_id);
                $itemModel->quantity -= $tempEntry->quantity;
                $itemModel->save();
            } elseif ($tempEntry->action == 'delete') {
                // Khôi phục số lượng tồn kho của các item
                foreach ($stockEntry->items as $item) {
                    $itemModel = Item::find($item->item_id);
                    $itemModel->quantity += $item->quantity;
                    $itemModel->save();
                }

                // Xóa phiếu nhập kho
                $stockEntry->delete();
            }
        }

        // Xóa các mục tạm
        StockEntryTemp::where('stock_entry_id', $stockEntry->id)->delete();

        // Cập nhật trạng thái phiếu nhập kho
        $stockEntry->update(['status' => 'confirmed']);

        return redirect()->route('stock-entries.approveactions')->with('success', 'Stock entry approved successfully.');
    }

    public function statistics()
    {
        $statistics = StockEntry::selectRaw('supplier_id, COUNT(*) as total_entries, SUM(stock_entry_items.quantity) as total_quantity')
            ->join('stock_entry_items', 'stock_entries.id', '=', 'stock_entry_items.stock_entry_id')
            ->groupBy('supplier_id')
            ->get();

        return view('stock-entries.statistics', compact('statistics'));
    }

    public function getItemsBySupplier($supplierId)
    {
        $items = Item::where('supplier_id', $supplierId)->get();
        return response()->json($items);
    }
}
