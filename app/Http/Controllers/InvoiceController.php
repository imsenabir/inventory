<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetails;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {


        $order_id = $request->order_id;
        $customer_id = $request->customer_id;
        $user = Auth::user();
        $invoice_date = date('Y-m-d H:i:s');
        $notes = '';

        $invoice_number = 'INV-'.date('Ymd').uniqid();

        $gross_total_amount = 0;

        $invoice = Invoice::create([
            'order_id'=> $order_id,
            'customer_id'=> $customer_id,
            'user_id'=> 2,
            'invoice_number'=> $invoice_number,
            'invoice_date'=> $invoice_date,
            'total_amount'=> 0,
            'status'=> "Pending",
        ]);




        $orderDetails = OrderDetail::where('order_id', $order_id)->get();

        $total_amount = 0;
        foreach($orderDetails as $order)
        {
            $product_id = $order->product_id;
            $quantity  = $order->quantity;
            $amount  = $order->price;

            $total_amount = $quantity * $amount;
            $gross_total_amount+= $total_amount;

            $invoiceDetails = [
                'invoice_id' =>  $invoice->id,
                'product_id' =>  $product_id,
                'quantity' =>  $quantity,
                'amount' =>  $amount,
                'total_amount' =>  $total_amount,
            ];

            InvoiceDetails::create($invoiceDetails);

            // update product table:
            $product = Product::find($product_id);
            $product->quantity = $product->quantity - $quantity;
            $product->save();
        }

        $invoice->total_amount = $gross_total_amount;
        $invoice->save();

        $order = Order::find($order->id);
        $order->status = 'confirmed';
        $order->save();

        return response()->json([
            'status' => 'success',
            'data' => $invoice,
            'message' => 'Invoice Created successfully.'
        ]);

    }

    public function show(Invoice $invoice)
    {

    }
    public function print(Invoice $invoice)
    {
        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'));
        return $pdf->stream('customer_invoice.pdf');
    }
}
