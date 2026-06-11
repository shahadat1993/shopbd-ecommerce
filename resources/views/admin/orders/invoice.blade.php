<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice #{{ $order->order_number }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #333; background: #fff; }
  .invoice-wrapper { max-width: 780px; margin: 30px auto; padding: 40px; border: 1px solid #e0e0e0; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
  .logo-area h1 { font-size: 26px; color: #696cff; font-weight: 800; }
  .logo-area p { color: #888; font-size: 12px; }
  .invoice-meta { text-align: right; }
  .invoice-meta h2 { font-size: 22px; color: #333; margin-bottom: 6px; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
  .badge-success { background: #e8f8ee; color: #28a745; }
  .badge-warning { background: #fff8e1; color: #e65100; }
  .addresses { display: flex; gap: 40px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
  .address-block h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 8px; }
  .address-block p { line-height: 1.7; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  thead th { background: #696cff; color: white; padding: 10px 12px; text-align: left; font-size: 12px; }
  tbody td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; }
  tbody tr:nth-child(even) td { background: #fafafa; }
  tfoot td { padding: 8px 12px; font-weight: 600; }
  tfoot tr:last-child td { font-size: 16px; color: #696cff; border-top: 2px solid #696cff; padding-top: 12px; }
  .text-right { text-align: right; }
  .footer { text-align: center; color: #aaa; font-size: 11px; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none; }
    .invoice-wrapper { border: none; margin: 0; padding: 20px; }
  }
</style>
</head>
<body>
<div class="invoice-wrapper">

  <div class="header">
    <div class="logo-area">
      <h1>ShopBD</h1>
      <p>Your One-Stop Shopping Destination</p>
      <p>Dhaka, Bangladesh | info@shopbd.com</p>
    </div>
    <div class="invoice-meta">
      <h2>INVOICE</h2>
      <p style="color:#888; margin-bottom:4px">#{{ $order->order_number }}</p>
      <p style="margin-bottom:4px">Date: {{ $order->created_at->format('d M Y') }}</p>
      <span class="badge {{ $order->payment_status==='paid'?'badge-success':'badge-warning' }}">
        Payment: {{ strtoupper($order->payment_status) }}
      </span>
    </div>
  </div>

  <div class="addresses">
    <div class="address-block">
      <h4>Bill To / Ship To</h4>
      <p>
        <strong>{{ $order->shipping_name }}</strong><br>
        {{ $order->shipping_phone }}<br>
        {{ $order->shipping_email }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}{{ $order->shipping_state ? ', '.$order->shipping_state : '' }}<br>
        {{ $order->shipping_country }}
      </p>
    </div>
    <div class="address-block">
      <h4>Order Info</h4>
      <p>
        <strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
        <strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}<br>
        <strong>Order Status:</strong> {{ ucfirst($order->status) }}<br>
        @if($order->transaction_id)
        <strong>Transaction ID:</strong> {{ $order->transaction_id }}<br>
        @endif
      </p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Product</th>
        <th>SKU</th>
        <th class="text-right">Price</th>
        <th class="text-right">Qty</th>
        <th class="text-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>
          {{ $item->product_name }}
          @if($item->variant)
            <br><small style="color:#888">{{ implode(', ', array_map(fn($k,$v)=>"$k: $v", array_keys($item->variant), $item->variant)) }}</small>
          @endif
        </td>
        <td>{{ $item->product_sku ?? '—' }}</td>
        <td class="text-right">৳{{ number_format($item->price,0) }}</td>
        <td class="text-right">{{ $item->qty }}</td>
        <td class="text-right">৳{{ number_format($item->subtotal,0) }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr><td colspan="5" class="text-right">Subtotal</td><td class="text-right">৳{{ number_format($order->subtotal,0) }}</td></tr>
      @if($order->coupon_discount > 0)
      <tr style="color:green"><td colspan="5" class="text-right">Coupon ({{ $order->coupon_code }})</td><td class="text-right">-৳{{ number_format($order->coupon_discount,0) }}</td></tr>
      @endif
      <tr><td colspan="5" class="text-right">Shipping</td><td class="text-right">৳{{ number_format($order->shipping_charge,0) }}</td></tr>
      <tr><td colspan="5" class="text-right">TOTAL</td><td class="text-right">৳{{ number_format($order->total,0) }}</td></tr>
    </tfoot>
  </table>

  <div class="no-print" style="text-align:center; margin-top:20px">
    <button onclick="window.print()" style="background:#696cff;color:white;border:none;padding:10px 30px;border-radius:6px;cursor:pointer;font-size:14px">
      🖨️ Print Invoice
    </button>
    <button onclick="window.close()" style="background:#f0f0f0;color:#333;border:none;padding:10px 30px;border-radius:6px;cursor:pointer;font-size:14px;margin-left:10px">
      ✕ Close
    </button>
  </div>

  <div class="footer">
    <p>Thank you for shopping with ShopBD! For support: info@shopbd.com</p>
    <p style="margin-top:4px">This is a computer generated invoice and does not require a signature.</p>
  </div>

</div>
</body>
</html>
