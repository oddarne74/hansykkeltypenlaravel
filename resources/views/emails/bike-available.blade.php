<!DOCTYPE html>
<html>
<body>
    <h1>Sykkelen {{ $bike->name }} er tilgjengelig igjen!</h1>
    <p>Hei!</p>
    <p>Du meldte din interesse for sykkelen <strong>{{ $bike->name }}</strong> som tidligere var reservert. Den er nå tilgjengelig for salg igjen.</p>
    <p>Du kan se sykkelen her: <a href="{{ route('bikes.show', $bike->slug) }}">{{ route('bikes.show', $bike->slug) }}</a></p>
    <p>Hilsen Han Sykkeltypen</p>
</body>
</html>
