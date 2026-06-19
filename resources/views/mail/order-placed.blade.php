<x-mail::message>
# Thanks, {{ Str::before($order->name, ' ') }} — your order is confirmed.

We're already wrapping up order **{{ $order->number }}**. It ships within 24 hours, and we'll let you know the moment it's on its way.

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->name }} — {{ $item->creator }} | {{ $item->quantity }} | {{ $item->formattedLineTotal() }} |
@endforeach
| **Shipping** | | {{ $order->formattedShipping() }} |
| **Total** | | **{{ $order->formattedTotal() }}** |
</x-mail::table>

<x-mail::button :url="route('orders.show', ['order' => $order])">
View Your Order
</x-mail::button>

Questions? Just reply — a human reads every email.

— The Catalog Team
</x-mail::message>
