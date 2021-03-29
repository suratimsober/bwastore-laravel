<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionDetail;
use App\User;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $transactions = TransactionDetail::with(['transaction.user', 'product.galleries'])
                        ->whereHas('product', function($product){
                            $product->where('users_id', Auth::user()->id);
                        });

        $revenue = $transactions->get()->reduce(function($carry, $item){
            return $carry + $item->price;
        });

        $customer = User::count();
        return view('pages.dashboard', [
            'transactions_count' => $transactions->count(),
            'transactions_data' => $transactions->paginate(10),
            'revenue' => $revenue,
            'customer' => $customer
        ]);
    }
}
