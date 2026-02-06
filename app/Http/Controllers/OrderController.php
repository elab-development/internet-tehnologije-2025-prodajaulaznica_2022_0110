<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::all();

        return view('order.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        return view('order.show', [
            'order' => $order,
        ]);
    }

    public function store(OrderStoreRequest $request): Response
    {
        $order = Order::create($request->validated());

        return redirect()->route('order.show', ['order' => $order]);
    }
}
