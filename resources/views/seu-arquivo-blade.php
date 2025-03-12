<form method="POST" action="{{ route('tournaments.generate-matches', $tournament->id) }}">
    @csrf
    <button type="submit">Gerar Partidas</button>
</form> 