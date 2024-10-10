<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            border: 1px solid #000;
            width: 300px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .details {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Receipt</h1>
    <div class="details">
        <p><strong>Service:</strong> {{ $transaction->service->name }}</p>
        <p><strong>Amount Earned:</strong> KSh {{ number_format($transaction->amount_earned, 2) }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($transaction->payment_method) }}</p>
        <p><strong>Quantity Used:</strong> {{ $transaction->quantity_used }}</p>
        <p><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
