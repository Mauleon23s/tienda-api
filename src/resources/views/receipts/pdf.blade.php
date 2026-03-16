<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
body{
    font-family: DejaVu Sans, sans-serif;
    color:#333;
}

.header{
    border-bottom:2px solid #444;
    margin-bottom:20px;
}

.header h1{
    margin:0;
}

.info{
    margin-top:10px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#f2f2f2;
    padding:8px;
    text-align:left;
}

td{
    padding:8px;
    border-bottom:1px solid #ddd;
}

.totals{
    margin-top:20px;
    width:40%;
    float:right;
}

.totals td{
    border:none;
}

.total{
    font-weight:bold;
    font-size:16px;
}

</style>

</head>

<body>
    <center><h2>Online Store Receipt</h2></center>

<div class="header">
    <h1>Receipt #{{ $receipt->id }}</h1>
</div>

<div class="info">
    <p><strong>Date:</strong> {{ $receipt->generated_at }}</p>
</div>

<h3>Items</h3>

<table>
<thead>
<tr>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
<th>Subtotal</th>
</tr>
</thead>

<tbody>

@foreach($receipt->order->items as $item)

<tr>
<td>{{ $item->product->name }}</td>
<td>{{ $item->quantity }}</td>
<td>${{ number_format($item->price,2) }}</td>
<td>${{ number_format($item->quantity * $item->price,2) }}</td>
</tr>

@endforeach

</tbody>

</table>

<table class="totals">

<tr>
<td>Subtotal:</td>
<td>${{ number_format($receipt->subtotal,2) }}</td>
</tr>

<tr>
<td>Tax:</td>
<td>${{ number_format($receipt->tax,2) }}</td>
</tr>

<tr class="total">
<td>Total:</td>
<td>${{ number_format($receipt->total,2) }}</td>
</tr>

</table>

</body>
</html>