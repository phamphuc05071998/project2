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
        $waitingApprovedEntries = StockEntry::where('user_id', Auth::id())->where('status', 'waiting_approved')->get();
        $waitingApprovedEditEntries = StockEntry::where('user_id', Auth::id())->where('status', 'waiting_approved_edit')->get();
        $waitingApprovedDeleteEntries = StockEntry::where('user_id', Auth::id())->where('status', 'waiting_approved_delete')->get();
        $approvedEntries = StockEntry::where('user_id', Auth::id())->where('status', 'approved')->get();
        $rejectedEntries = StockEntry::where('user_id', Auth::id())->where('status', 'rejected')->get();
        $completedEntries = StockEntry::where('user_id', Auth::id())->where('status', 'completed')->get();

        return view('stock-entries.index', compact('pendingEntries', 'waitingApprovedEntries', 'waitingApprovedEditEntries', 'waitingApprovedDeleteEntries', 'approvedEntries', 'rejectedEntries', 'completedEntries'));
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

    public function requestApproval($id)
    {
        $stockEntry = StockEntry::findOrFail($id);
        $this->authorize('update', $stockEntry);

        if ($stockEntry->status == 'pending') {
            $stockEntry->status = 'waiting_approved';
            $stockEntry->save();

            return back()->with('success', 'Approval requested successfully.');
        }

        return back()->with('error', 'Cannot request approval for this stock entry.');
    }

    public function confimRequestApproval($id)
    {
        $stockEntry = StockEntry::findOrFail($id);
        $this->authorize('approve', $stockEntry);

        if ($stockEntry->status == 'waiting_approved') {
            $stockEntry->status = 'approved';
            $stockEntry->save();

            return back()->with('success', 'Approval  successfully.');
        }

        return back()->with('error', 'Cannot request approval for this stock entry.');
    }

    public function rejectRequestApproval($id)
    {
        $stockEntry = StockEntry::findOrFail($id);
        $this->authorize('approve', $stockEntry);

        if ($stockEntry->status == 'waiting_approved') {
            $stockEntry->status = 'rejected';
            $stockEntry->save();

            return back()->with('success', 'Approval rejected successfully.');
        }

        return back()->with('error', 'Cannot reject approval for this stock entry.');
    }

    public function edit(StockEntry $stockEntry)
    {
        $this->authorize('update', $stockEntry);

        if ($stockEntry->status == 'approved') {
            return redirect()->route('stock-entries.index')->with('error', 'Cannot edit an approved stock entry.');
        }

        $suppliers = Supplier::all();
        $items = Item::where('supplier_id', $stockEntry->supplier_id)->get(); // Lấy các mục hàng hóa của nhà cung cấp hiện tại
        $selectedItems = $stockEntry->items; // Lấy các mục hàng hóa đã chọn

        return view('stock-entries.edit', compact('stockEntry', 'suppliers', 'items', 'selectedItems'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $stockEntry = StockEntry::findOrFail($id);
        $stockEntry->supplier_id = $request->supplier_id;
        $stockEntry->status = 'waiting_approved_edit';
        $stockEntry->save();

        // Lưu yêu cầu sửa vào bảng tạm
        foreach ($request->items as $item) {
            StockEntryTemp::create([
                'stock_entry_id' => $stockEntry->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'action' => 'edit',
            ]);
        }

        return redirect()->route('stock-entries.index')->with('success', 'Stock entry update requested and pending approval.');
    }

    public function destroy(StockEntry $stockEntry)
    {
        $this->authorize('delete', $stockEntry);

        if ($stockEntry->status == 'approved') {
            return redirect()->route('stock-entries.index')->with('error', 'Cannot delete an approved stock entry.');
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
        $editEntries = StockEntry::with(['user', 'supplier'])
            ->where('status', 'waiting_approved_edit')
            ->get();

        $deleteEntries = StockEntry::with(['user', 'supplier'])
            ->where('status', 'waiting_approved_delete')
            ->get();

        $approveEntries = StockEntry::with(['user', 'supplier'])
            ->where('status', 'waiting_approved')
            ->get();

        return view('stock-entries.approve', compact('editEntries', 'deleteEntries', 'approveEntries'));
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
                    if ($itemModel) {
                        $itemModel->quantity += $oldItem->quantity;
                        $itemModel->save();
                    }
                }

                // Xóa các mục hàng hóa cũ
                $stockEntry->items()->delete();

                // Thêm các mục hàng hóa mới
                if ($tempEntry->item_id) {
                    $stockEntry->items()->create([
                        'item_id' => $tempEntry->item_id,
                        'quantity' => $tempEntry->quantity,
                    ]);

                    // Cập nhật số lượng tồn kho của item mới
                    $itemModel = Item::find($tempEntry->item_id);
                    if ($itemModel) {
                        $itemModel->quantity -= $tempEntry->quantity;
                        $itemModel->save();
                    }
                }

                // Cập nhật trạng thái của StockEntry
                $stockEntry->status = 'pending';
                $stockEntry->save();
            } elseif ($tempEntry->action == 'delete') {
                // Khôi phục số lượng tồn kho của các item
                foreach ($stockEntry->items as $item) {
                    $itemModel = Item::find($item->item_id);
                    if ($itemModel) {
                        $itemModel->quantity += $item->quantity;
                        $itemModel->save();
                    }
                }

                // Xóa phiếu nhập kho
                $stockEntry->delete();
            }
        }

        // Xóa các mục tạm
        StockEntryTemp::where('stock_entry_id', $stockEntry->id)->delete();

        return back()->with('success', 'Stock entry approved successfully.');
    }
    public function reject(Request $request, StockEntry $stockEntry)
    {
        $this->authorize('approve', $stockEntry);

        $tempEntries = StockEntryTemp::where('stock_entry_id', $stockEntry->id)->get();

        foreach ($tempEntries as $tempEntry) {
            if ($tempEntry->action == 'edit' || $tempEntry->action == 'delete') {
                // Chỉ cần xóa các mục tạm mà không thay đổi trạng thái của phiếu nhập kho gốc
                $tempEntry->delete();
            }
        }

        $stockEntry->status = 'pending';
        $stockEntry->save();

        return back()->with('success', 'Stock entry edit request rejected successfully.');
    }
    public function statistics()
    {
        $statistics = StockEntry::selectRaw('supplier_id, user_id, COUNT(*) as total_entries, SUM(stock_entry_items.quantity) as total_quantity')
            ->join('stock_entry_items', 'stock_entries.id', '=', 'stock_entry_items.stock_entry_id')
            ->groupBy('supplier_id', 'user_id')
            ->get();

        return view('stock-entries.statistics', compact('statistics'));
    }

    public function getItemsBySupplier($supplierId)
    {
        $items = Item::where('supplier_id', $supplierId)->get();
        return response()->json($items);
    }
}
