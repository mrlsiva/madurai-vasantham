{{--
    Receipt + WhatsApp buttons for one staff salary.
    @param $links   result of SalaryController::whatsapp(), null when the salary can't be calculated yet
    @param $reason  shown on the greyed-out buttons when $links is null
--}}
@if($links)
    <a href="{{ $links['receiptUrl'] }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Open receipt"><i class="fa-solid fa-receipt"></i></a>
    @if($links['whatsappUrl'])
        <a href="{{ $links['whatsappUrl'] }}" target="_blank" rel="noopener" class="btn btn-success btn-sm" title="Send receipt on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
    @else
        <span class="btn btn-outline-secondary btn-sm disabled" title="No valid mobile number"><i class="fa-brands fa-whatsapp"></i></span>
    @endif
@else
    <span class="d-inline-block" title="{{ $reason ?? 'Salary not ready' }}">
        <span class="btn btn-outline-secondary btn-sm disabled"><i class="fa-solid fa-receipt"></i></span>
        <span class="btn btn-outline-secondary btn-sm disabled"><i class="fa-brands fa-whatsapp"></i></span>
    </span>
@endif
