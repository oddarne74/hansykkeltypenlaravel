<form method="post" action="{{ route('contact.store') }}" class="card space-y-5">
    @csrf
    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
    <label>Navn<input name="name" value="{{ old('name') }}" required></label>
    <label>E-post eller telefon<input name="contact" value="{{ old('contact') }}" required></label>
    <label>Hva gjelder det?
        <select name="subject" required>
            <option value="bike">Jeg ser etter en sykkel</option>
            <option value="service">Sykkelreparasjon</option>
            <option value="sell">Selge eller gi bort en sykkel</option>
            <option value="other">Annet</option>
        </select>
    </label>
    <label>Melding<textarea name="message" rows="6" required>{{ old('message') }}</textarea></label>
    <label class="flex items-start gap-3">
        <input type="checkbox" name="consent" value="1" required class="mt-1 size-5">
        <span>Jeg samtykker til at opplysningene brukes til å besvare henvendelsen.</span>
    </label>
    @if($errors->any())
        <div class="rounded bg-red-100 p-3 text-sm text-red-800">Sjekk at alle felt er riktig utfylt.</div>
    @endif
    <button class="rounded-full bg-rust px-7 py-4 font-extrabold text-white">Send henvendelse</button>
</form>
