@extends('layouts.admin')
@section('page_title','Finance & revenue')
@section('content')
<div class="finance-page">
    <section class="finance-hero admin-panel">
        <div>
            <span class="eyebrow">FINANCE & REVENUE</span>
            <h2>See what the business is collecting.</h2>
            <p>Revenue reporting is based on the payment and pricing data currently recorded against service requests. Use the date range to review performance.</p>
        </div>
        <form class="finance-filter" method="GET">
            <label>From<input type="date" name="start_date" value="{{ $payload['start_date'] }}"></label>
            <label>To<input type="date" name="end_date" value="{{ $payload['end_date'] }}"></label>
            <button class="button button-yellow" type="submit">Apply range</button>
        </form>
    </section>

    <section class="finance-kpis">
        <article class="finance-kpi"><span>Gross sales</span><strong>TSh {{ number_format($payload['gross_sales'],2) }}</strong><small>Priced requests in range</small></article>
        <article class="finance-kpi accent"><span>Collected</span><strong>TSh {{ number_format($payload['collected'],2) }}</strong><small>{{ $payload['paid_orders'] }} paid requests</small></article>
        <article class="finance-kpi"><span>Refunds</span><strong>TSh {{ number_format($payload['refunds'],2) }}</strong><small>Recorded refund totals</small></article>
        <article class="finance-kpi dark"><span>Net collected</span><strong>TSh {{ number_format($payload['net_collected'],2) }}</strong><small>Paid transactions currently recorded</small></article>
    </section>

    <section class="finance-mini-grid">
        <article class="admin-panel mini-metric"><span>Orders</span><strong>{{ number_format($payload['order_count']) }}</strong><small>Total requests</small></article>
        <article class="admin-panel mini-metric"><span>Average order value</span><strong>TSh {{ number_format($payload['aov'],2) }}</strong><small>Paid requests only</small></article>
        <article class="admin-panel mini-metric"><span>Discounts</span><strong>TSh 0.00</strong><small>Not captured yet</small></article>
        <article class="admin-panel mini-metric"><span>Payment fees</span><strong>TSh 0.00</strong><small>Not captured yet</small></article>
        <article class="admin-panel mini-metric"><span>Shipping / delivery</span><strong>TSh 0.00</strong><small>Not captured yet</small></article>
    </section>

    <section class="finance-two-col">
        <article class="admin-panel">
            <div class="panel-heading"><div><span class="eyebrow">REVENUE TREND</span><h3>Daily collection</h3></div><a href="{{ route('admin.reports.index') }}">More reports →</a></div>
            <div class="finance-trend">
                @php $maxDaily=(float)max(1, collect($payload['daily'])->max('collected')); @endphp
                @forelse($payload['daily'] as $row)
                    @php $value=(float)$row->collected; $height=max(8, ($value/$maxDaily)*100); @endphp
                    <div class="trend-item" title="{{ $row->day }} · TSh {{ number_format($value,2) }}"><div class="trend-bar" style="height:{{ $height }}%"></div><small>{{ \Carbon\Carbon::parse($row->day)->format('d M') }}</small></div>
                @empty
                    <div class="finance-empty">No revenue activity in this date range.</div>
                @endforelse
            </div>
        </article>
        <article class="admin-panel">
            <div class="panel-heading"><div><span class="eyebrow">PAYMENT MIX</span><h3>How customers paid</h3></div></div>
            <div class="finance-list">
                @forelse($payload['payment_methods'] as $row)
                    <div class="finance-row"><div><strong>{{ ucfirst(str_replace('_',' ', $row->method ?: 'Not recorded')) }}</strong><small>{{ $row->count }} requests</small></div><b>TSh {{ number_format((float)$row->collected,2) }}</b></div>
                @empty <div class="finance-empty">Payment methods are not recorded yet.</div> @endforelse
            </div>
        </article>
    </section>

    <section class="finance-two-col">
        <article class="admin-panel">
            <div class="panel-heading"><div><span class="eyebrow">BY CATEGORY</span><h3>Revenue by service area</h3></div></div>
            <div class="finance-list">
                @forelse($payload['category_sales'] as $row)
                    @php $share=$payload['collected']>0 ? ((float)$row->collected/$payload['collected'])*100 : 0; @endphp
                    <div class="finance-row stacked"><div class="finance-row-top"><strong>{{ $row->name }}</strong><b>TSh {{ number_format((float)$row->collected,2) }}</b></div><div class="share-track"><span style="width:{{ min(100,$share) }}%"></span></div><small>{{ $row->orders }} requests · {{ number_format($share,1) }}% of collected revenue</small></div>
                @empty <div class="finance-empty">No category activity in this date range.</div> @endforelse
            </div>
        </article>
        <article class="admin-panel">
            <div class="panel-heading"><div><span class="eyebrow">TOP SERVICES</span><h3>Services generating collection</h3></div></div>
            <div class="finance-list">
                @forelse($payload['service_sales'] as $row)
                    <div class="finance-row"><div><strong>{{ $row->name }}</strong><small>{{ $row->orders }} requests</small></div><b>TSh {{ number_format((float)$row->collected,2) }}</b></div>
                @empty <div class="finance-empty">No paid services in this date range.</div> @endforelse
            </div>
        </article>
    </section>

    <section class="finance-two-col">
        <article class="admin-panel">
            <div class="panel-heading"><div><span class="eyebrow">ORDER STATUS</span><h3>Requests in the range</h3></div></div>
            <div class="finance-status-list">
                @forelse($payload['status_breakdown'] as $row)
                    @php $count=(int)$row->count; $share=$payload['order_count']>0 ? ($count/$payload['order_count'])*100 : 0; @endphp
                    <div><div class="finance-row-top"><strong>{{ \App\Models\Submission::statusLabel($row->status) }}</strong><b>{{ $count }}</b></div><div class="share-track"><span style="width:{{ min(100,$share) }}%"></span></div></div>
                @empty <div class="finance-empty">No requests in this date range.</div> @endforelse
            </div>
        </article>
        <article class="admin-panel finance-notice">
            <div class="panel-heading"><div><span class="eyebrow">DATA COVERAGE</span><h3>Where finance is limited</h3></div></div>
            <p>The dashboard does not invent costs that the current application data does not capture.</p>
            <div class="coverage-list">
                <div><span>Discounts</span><strong>Not recorded</strong></div>
                <div><span>Payment fees</span><strong>Not recorded</strong></div>
                <div><span>Shipping / delivery</span><strong>Not recorded</strong></div>
                <div><span>Refunds</span><strong>{{ $payload['refunds'] > 0 ? 'Recorded' : 'None in range' }}</strong></div>
            </div>
            <a class="button button-dark" href="{{ route('admin.submissions.index') }}">Review requests →</a>
        </article>
    </section>
</div>
@endsection
