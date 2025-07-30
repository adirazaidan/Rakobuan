<div class="status-card" data-call-session-id="{{ $call->session_id }}">
    <div class="receipt-details">
        <div class="receipt-header">
            <h3>Rincian Panggilan #{{ $call->call_number }}</h3>
            <div class="receipt-customer-info">
                <span><strong>Waktu:</strong> {{ $call->created_at->format('d M Y, H:i') }}</span>
                <p class="receipt-status">
                    <strong>Status:</strong> 
                    <span id="call-status-badge-{{ $call->call_number }}" class="status-badge status-{{ $call->status }}">
                        {{ $call->translated_status ?? ($call->status == 'pending' ? 'Menunggu' : 'Selesai') }}
                    </span>
                </p>
            </div>
        </div>

        <table class="receipt-table">
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">
                            <i class="fas fa-comment-dots" style="margin-right: 0.5rem; color: var(--text-muted);"></i>
                            Catatan Panggilan
                        </div>
                        <div class="receipt-item-notes">
                           {{ $call->notes ?: 'Tidak ada catatan khusus.' }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>