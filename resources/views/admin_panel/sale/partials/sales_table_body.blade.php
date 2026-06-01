@foreach ($sales as $sale)
    @php
        // Product Names
        $pNames = 'N/A';
        if ($sale->items && $sale->items->count() > 0) {
            $pNames = $sale->items
                ->map(fn($item) => optional($item->product)->item_name ?? '?')
                ->implode(', ');
        } elseif ($sale->product) {
            $pNames = $sale->product;
        }

        // Status Styling
        $statusBadge = '<span class="badge badge-warning text-dark border border-warning">Draft</span>';
        if ($sale->sale_status === 'posted') {
            if ($sale->is_booking) {
                $statusBadge = '<span class="badge badge-success border border-success"><i class="fas fa-check-circle me-1"></i>Confirmed Booking</span>';
            } else {
                $statusBadge = '<span class="badge badge-success border border-success">Posted</span>';
            }
        } elseif ($sale->sale_status === 'booked') {
            $statusBadge = '<span class="badge badge-warning text-dark border border-warning"><i class="fas fa-bookmark me-1"></i>Booked</span>';
        } elseif ($sale->sale_status === 'returned') {
            $statusBadge = '<span class="badge badge-danger border border-danger">Returned</span>';
        } elseif ($sale->sale_status == 1) {
            $statusBadge = '<span class="badge badge-danger border border-danger">Return</span>';
        } elseif ($sale->sale_status === null) {
            $statusBadge = '<span class="badge badge-success border border-success">Sale</span>';
        }

        // Check for returns
        if ($sale->returns && $sale->returns->count() > 0) {
            $statusBadge .= '<br><small class="badge badge-danger border border-danger mt-1"><i class="fas fa-undo-alt me-1"></i> Partial Return</small>';
        }
    @endphp
    <tr class="border-bottom-0">
        <td class="ps-3 fw-bold text-muted font-monospace">#{{ $sale->id }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 32px; height: 32px; font-size: 14px; background-color: #e0f2fe; color: #0369a1;">
                    {{ strtoupper(substr(optional($sale->customer_relation)->customer_name ?? 'C', 0, 1)) }}
                </div>
                <span class="fw-medium text-dark">{{ optional($sale->customer_relation)->customer_name ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="font-monospace text-dark">{{ $sale->reference ?? '-' }}</td>
        <td title="{{ $pNames }}" class="text-muted small">
            {{ \Illuminate\Support\Str::limit($pNames, 40) }}
        </td>
        <td class="text-center font-monospace">
            {{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }}
        </td>
        <td class="text-end fw-bold text-dark font-monospace">
            {{ number_format($sale->total_bill_amount > 0 ? $sale->total_bill_amount : (float) $sale->per_total, 2) }}
        </td>
        <td class="text-end text-danger font-monospace">
            {{ number_format($sale->total_extradiscount, 2) }}
        </td>
        <td class="text-end text-success fw-bold font-monospace">
            {{ number_format($sale->total_net, 2) }}
        </td>
        <td class="text-nowrap small text-muted">
            {{ $sale->created_at->format('d M, Y') }}
        </td>
        <td>{!! $statusBadge !!}</td>
        <td class="pe-3 text-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle"
                    type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @if ($sale->sale_status === 'draft' || $sale->sale_status === 'booked')
                        {{-- Draft / Booked Actions --}}
                        <li>
                            <form action="{{ route('sales.confirm', $sale->id) }}" method="POST" class="d-inline confirm-booking-form">
                                @csrf
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-booking-btn">
                                    <i class="fas fa-check-circle fa-fw text-success"></i> Confirm Booking
                                </button>
                            </form>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $sale->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $sale->id) }}" target="_blank">
                                <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', ['id' => $sale->id, 'type' => 'estimate']) }}" target="_blank">
                                <i class="fas fa-calculator text-secondary fa-fw"></i> View Estimate
                            </a>
                        </li>
                    @else
                        {{-- Posted Actions --}}
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $sale->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $sale->id) }}" target="_blank">
                                <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', ['id' => $sale->id, 'type' => 'estimate']) }}" target="_blank">
                                <i class="fas fa-calculator text-secondary fa-fw"></i> View Estimate
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc', $sale->id) }}" target="_blank">
                                <i class="fas fa-shipping-fast text-warning fa-fw"></i> View DC
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc_thermal', $sale->id) }}" target="_blank">
                                <i class="fas fa-truck text-secondary fa-fw"></i> DC Thermal
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.receipt', $sale->id) }}" target="_blank">
                                <i class="fas fa-receipt text-success fa-fw"></i> View Receipt
                            </a>
                        </li>
                        @if ($sale->sale_status !== 'returned')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="{{ route('sale.return.show', $sale->id) }}">
                                    <i class="fas fa-undo text-danger fa-fw"></i> Return Sale
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 disabled text-muted" href="#" onclick="return false;">
                                    <i class="fas fa-undo fa-fw"></i> Returned
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
