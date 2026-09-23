{{--
    Location icon for a punch in / punch out. Opens the shared location popup
    (admin.partials.location-modal). Shows nothing when no GPS was sent.

    @param $kind      'in' or 'out'
    @param $lat, $lng           this punch's coordinates
    @param $otherLat, $otherLng the other punch's coordinates (for distance)
    @param $name, $date, $time, $shift, $photo  details shown in the popup
--}}
@if( $lat !== null && $lng !== null && $lat !== '' && $lng !== '' )
    <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline show-location"
        title="Show punch {{ $kind }} location"
        data-kind="{{ $kind }}"
        data-lat="{{ $lat }}" data-lng="{{ $lng }}"
        data-other-lat="{{ $otherLat }}" data-other-lng="{{ $otherLng }}"
        data-name="{{ $name }}" data-date="{{ $date }}" data-time="{{ $time }}"
        data-shift="{{ $shift }}" data-photo="{{ $photo }}">
        <i class="fa-solid fa-location-dot {{ $kind == 'in' ? 'text-success' : 'text-danger' }}"></i>
    </button>
@endif
