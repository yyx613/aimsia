<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Models\CombinedOrder;
use Log;
use Illuminate\Http\Request;
use Session;

class ProductWalletController extends Controller
{
    public function pay(Request $request){
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
        foreach ($combined_order->orders as $order) {
            $request->merge(['order_id' => $order->id]);
            $request->merge(['status' => 'paid']);

            (new OrderController)->update_payment_status($request);
        }

        flash(translate("Your order has been placed successfully"))->success();
        return redirect()->route('order_confirmed');
    }
}
