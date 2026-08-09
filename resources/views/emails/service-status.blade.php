<!DOCTYPE html>
<html>
<body>
    @if($serviceRequest->status === \App\Enums\ServiceStatus::APPROVED)
        <h1>Serviceforespørselen din er godkjent!</h1>
        <p>Hei {{ $serviceRequest->name }}!</p>
        <p>Vi har godkjent serviceforespørselen din for <strong>uke {{ \Illuminate\Support\Carbon::parse($serviceRequest->week_start)->isoWeek() }}</strong>.</p>
        @if($serviceRequest->wants_pickup)
            <p>Vi tar kontakt for å avtale henting og levering av sykkelen.</p>
        @else
            <p>Vi tar kontakt for å avtale innlevering av sykkelen.</p>
        @endif
    @else
        <h1>Om serviceforespørselen din</h1>
        <p>Hei {{ $serviceRequest->name }}!</p>
        <p>Dessverre kan vi ikke ta imot sykkelen din for service i ønsket uke (uke {{ \Illuminate\Support\Carbon::parse($serviceRequest->week_start)->isoWeek() }}). Ta gjerne kontakt for å finne en annen løsning.</p>
    @endif
    <p>Hilsen Han Sykkeltypen</p>
</body>
</html>
