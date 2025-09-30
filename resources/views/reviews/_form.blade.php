@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
    // Normaliza el ID por si no vino inyectado
    $postId = $postId ?? (is_object($post) ? $post->id : data_get($post, 'id'));
@endphp
<form method="POST" action="{{ route('reviews.store', ['post' => $postId]) }}" class="mb-4">

    @csrf

    <div class="mb-2">
        <label class="form-label">Puntuación</label>
        <div class="d-flex gap-2 align-items-center">
            <select name="rating" class="form-select" style="max-width:120px" required>
                <option value="">--</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} ★</option>
                @endfor
            </select>
            <small class="text-muted">5 es excelente, 1 es muy malo</small>
        </div>
    </div>

    <div class="mb-2">
        <label class="form-label">Título (opcional)</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" maxlength="120" placeholder="Ej: Buen vendedor y envío rápido">
    </div>

    <div class="mb-2">
        <label class="form-label">Comentario</label>
        <textarea name="body" rows="4" class="form-control" required minlength="10" maxlength="2000" placeholder="Cuenta tu experiencia...">{{ old('body') }}</textarea>
    </div>

    {{-- Honeypot contra bots --}}
    <input type="text" name="website" style="display:none">

    <button class="btn btn-primary">Publicar reseña</button>
</form>
