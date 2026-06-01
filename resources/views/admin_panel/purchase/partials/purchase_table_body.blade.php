@foreach ($Purchase as $purchase)
    <tr class="border-bottom-0">
        <td class="ps-3 fw-bold text-muted">#{{ $purchase->id }}</td>
        <td class="text-nowrap">
            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}
        </td>
        <td class="font-monospace text-dark">{{ $purchase->invoice_no }}</td>
        <td>
            @if ($purchase->status_purchase == 'draft')
                <span
                    class="badge badge-warning text-dark border border-warning">Draft</span>
            @elseif ($purchase->status_purchase == 'Returned')
                <span
                    class="badge bg-danger text-white border border-danger">Returned</span>
            @else
                <span class="badge badge-success border border-success">Approved</span>
            @endif
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 32px; height: 32px; font-size: 14px;">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                </div>
                <span
                    class="fw-medium text-dark">{{ $purchase->vendor->name ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="text-muted small">
            {{ $purchase->warehouse->warehouse_name ?? 'N/A' }}
        </td>

        <td class="text-end fw-bold text-dark">
            @if ($purchase->total_returned > 0)
                {{-- Show original amount struck through --}}
                <div>
                    <small
                        class="text-muted text-decoration-line-through">{{ number_format($purchase->net_amount, 2) }}</small>
                </div>
                {{-- Show updated amount --}}
                <div class="text-success">
                    {{ number_format($purchase->updated_net_amount, 2) }}
                </div>
                <small
                    class="text-danger">(-{{ number_format($purchase->total_returned, 2) }})</small>
            @else
                {{ number_format($purchase->net_amount, 2) }}
            @endif
        </td>
        <td class="text-end text-success">
            {{ number_format($purchase->paid_amount, 2) }}
        </td>
        <td class="text-end">
            @php
                $displayDue =
                    $purchase->total_returned > 0
                        ? $purchase->updated_due_amount
                        : $purchase->due_amount;
            @endphp
            @if ($displayDue > 0)
                <span
                    class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">{{ number_format($displayDue, 2) }}</span>
            @else
                <span
                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Paid</span>
            @endif

            @if ($purchase->has_partial_return)
                <br><small class="badge bg-danger text-white mt-1"><i class="fas fa-undo-alt me-1"></i> Partial
                    Return</small>
            @elseif($purchase->is_fully_returned)
                <br><small class="badge bg-danger mt-1">Fully Returned</small>
            @endif
        </td>

        <td class="pe-3 text-center">
            <div class="dropdown">
                {{-- Replaced data-bs-toggle with data-toggle for Bootstrap 4 compatibility --}}
                <button class="btn btn-sm btn-light border dropdown-toggle"
                    type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small"></i> Actions
                </button>
                {{-- Replaced dropdown-menu-end (BS5) with dropdown-menu-right (BS4) --}}
                <ul
                    class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">

                    @can('purchases.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('purchase.edit', $purchase->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit
                            </a>
                        </li>
                    @endcan

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-purchase-btn"
                                    href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-check-circle fa-fw"></i> Confirm
                                    Purchase
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase != 'draft')
                        @can('purchases.view')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('purchase.invoice', $purchase->id) }}">
                                    <i class="fas fa-file-invoice text-info fa-fw"></i> View
                                    Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('purchase.receipt', $purchase->id) }}">
                                    <i class="fas fa-receipt text-secondary fa-fw"></i> View
                                    Receipt
                                </a>
                            </li>
                        @endcan
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('purchase.return.show', $purchase->id) }}">
                                    <i class="fas fa-undo text-warning fa-fw"></i> Return
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.delete')
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form
                                    action="{{ route('purchase.destroy', $purchase->id) }}"
                                    method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="dropdown-item d-flex align-items-center gap-2 py-2 delete-btn text-danger">
                                        <i class="fas fa-trash-alt fa-fw"></i> Delete
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
