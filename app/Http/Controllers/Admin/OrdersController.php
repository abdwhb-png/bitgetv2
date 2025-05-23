<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Enums\PermissionsEnum;
use App\Http\Controllers\BaseController;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;

class OrdersController extends BaseController
{
    protected function getParsedOrder($order)
    {
        return [
            'id' => $order->id,
            'type' => $order->type,
            'status' => $order->getStatus(),
            'ref_id' => $order->ref_id,
            'dates' => [
                // 'opened_at' => $order->opened_at,
                // 'closed_at' => $order->closed_at,
                'created' => $order->created_at->diffForHumans(),
                'updated' => $order->updated_at->diffForHumans(),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
            ],
            'detail' => [
                'base' => [
                    'user' => $order->account->user ? $order->account->user->info->fullName() : 'unknown',
                    'symbol' => $order->symbol,
                    'duration' => $order->expiration . "s",
                    'quantity' =>  number_format($order->quantity, 3),
                    'profit' => number_format($order->profit, 3),
                    'profit_type' => $order->profit_type,
                    'percentage' => ($order->percentage * 100) . '%',
                ],
                'more' => [
                    'open_price' => $order->open_price,
                    'close_price' => $order->close_price,
                    'opened_at' => $order->opened_at,
                    'closed_at' => $order->closed_at,
                ],
            ],
        ];
    }
    public function index()
    {
        return Inertia::render('Orders', $this->get());
    }
    public function get()
    {
        $user = Auth::user();
        $query = Order::query();
        $filters = FacadesRequest::all('search', 'sort', 'type', 'status');

        return [
            'filters' => $filters,
            'can' => [
                'create_order' => $user->isSuperAdmin(),
                'edit_order' => $user->isSuperAdmin(),
                'delete_order' => $user->isSuperAdmin(),
            ],
            'canEditOrder' => $user->can(PermissionsEnum::EDITORDERS->value),
            'totalCount' => $query->count(),
            'openedCount' => $query->whereNull('closed_at')->count(),
            'orders' => $query->filter($filters)
                ->latest()
                ->paginate($this->itemsPerPage(20))
                ->withQueryString()
                ->through(fn($item) => $this->getParsedOrder($item)),
        ];
    }

    public function update(Order $order, Request $request)
    {
        // Implement order update logic here
        return back(303)->with('status', 'order updated');
    }
}
