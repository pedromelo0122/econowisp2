@php
    use App\Models\Review;

    // Normalizar $post (puede venir como modelo o como array)
    $isModel   = is_object($post);
    $postId    = $isModel ? $post->id : data_get($post, 'id');
    $postTitle = $isModel ? $post->title : data_get($post, 'title');

    // rating_count y rating_avg
    if ($isModel) {
        $ratingCount = $post->rating_count ?? $post->approvedReviews()->count();
        $ratingAvg   = $post->rating_avg ?? round($post->approvedReviews()->avg('rating') ?? 0, 1);
    } else {
        $ratingCount = data_get($post, 'rating_count');
        $ratingAvg   = data_get($post, 'rating_avg');
        if (is_null($ratingCount) || is_null($ratingAvg)) {
            $ratingCount = Review::approved()->where('post_id', $postId)->count();
            $ratingAvg   = round((float) Review::approved()->where('post_id', $postId)->avg('rating'), 1);
        }
    }

    // Cargar reviews aprobados (paginados)
    if ($isModel) {
        $reviews = $post->approvedReviews()->with('user:id,name')->latest()->paginate(10);
    } else {
        $reviews = Review::approved()
            ->where('post_id', $postId)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);
    }
@endphp

<div class="my-3">
    <h3 class="mb-3">
        Reseñas de: {{ $postTitle }}
        <small class="text-muted">({{ $ratingCount }} reseñas, {{ number_format($ratingAvg, 1) }}★)</small>
    </h3>

    @auth
        @include('reviews._form', ['post' => $post, 'postId' => $postId])
    @else
        <div class="alert alert-info">Inicia sesión para dejar tu reseña.</div>
    @endauth

    <hr>

    @forelse($reviews as $r)
        <div class="mb-4 border rounded p-3">
            <div class="d-flex justify-content-between align-items-center">
                <strong>{{ $r->user->name ?? 'Usuario' }}</strong>
                <span aria-label="{{ $r->rating }} estrellas">
                    {{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}
                </span>
            </div>

            @if($r->title)
                <div class="fw-semibold mt-1">{{ $r->title }}</div>
            @endif

            <p class="mb-2">{{ $r->body }}</p>
            <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>

            @auth
                @php
                    $canDelete = auth()->id() === $r->user_id || (property_exists(auth()->user(), 'is_admin') && auth()->user()->is_admin);
                @endphp
                @if($canDelete)
                    <form method="POST" action="{{ route('reviews.destroy', $r) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta reseña?')">
                            Eliminar
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    @empty
        <p>No hay reseñas todavía.</p>
    @endforelse

    {{ $reviews->links() }}
</div>
