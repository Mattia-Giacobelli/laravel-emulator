<!DOCTYPE html>
<html lang="it">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laravel Emulator - Carica e Gioca ROM Nintendo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-controller"></i> Laravel Emulator
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ url('/roms') }}">
                                <i class="bi bi-folder-plus"></i> Carica ROM
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/api/roms') }}">
                                <i class="bi bi-list-stars"></i> Elenco Giochi
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-upload"></i> Carica una nuova ROM
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Form di caricamento -->
                            <form action="{{ url('/api/rom/upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="file" class="form-label fw-bold">
                                        <i class="bi bi-file-earmark-zip"></i> Seleziona File ROM
                                    </label>
                                    <input type="file" class="form-control form-control-lg" id="file"
                                        name="file" accept=".nes,.gb,.gbc,.nds,.swc,.z64,.v64,.iso" required>
                                    <div class="form-text">
                                        Supportati: .nes (Nintendo Entertainment System), .gba, .ds, .swc (Switch),
                                        .iso, .v64
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="title" class="form-label">Titolo</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Mario Kart DS...">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="region" class="form-label">Regione</label>
                                        <select class="form-select" id="region" name="region">
                                            <option value="">Seleziona...</option>
                                            <option value="us">USA/Europa</option>
                                            <option value="jp">Giappone</option>
                                            <option value="eu">Europa</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="category" class="form-label">Categoria</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">Tutto</option>
                                            <option value="nintendo-switch">Nintendo Switch</option>
                                            <option value="nintendo-64">Nintendo 64</option>
                                            <option value="nintendods">Nintendo DS</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" style="min-height: 55px;">
                                        <i class="bi bi-upload"></i> Carica ROM
                                    </button>
                                </div>

                                <div id="uploadStatus" class="mt-3">
                                    <!-- Stati di caricamento -->
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($games->count() > 0)
                        <div class="card shadow mt-4">
                            <div
                                class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-folder-open"></i> ROM Disponibili
                                    ({{ $games->count() }})</h5>
                                <span><i class="bi bi-clock"></i> Aggiornato: {{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Titolo</th>
                                                <th>Categoria</th>
                                                <th>Regione</th>
                                                <th>Dimensione</th>
                                                <th>Azienda</th>
                                                <th>Azioni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($games as $game)
                                                <tr>
                                                    <td>{{ $game->id }}</td>
                                                    <td><strong>{{ $game->title }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucfirst($game->category ?? 'Unknown') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $game->region ?? '-' }}</td>
                                                    <td>{{ $game->file_size_human ?? '-' }}</td>
                                                    <td><small
                                                            class="text-muted">{{ $game->description ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/storage/' . $game->file_path) }}"
                                                            class="btn btn-sm btn-success" target="_blank">
                                                            <i class="bi bi-play-circle"></i> Gioca
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <footer class="bg-dark text-white text-center py-4 mt-5">
            <div class="container">
                <p class="mb-0">
                    <i class="bi bi-controller"></i> Laravel Emulator - Backend per ROM Nintendo
                    &copy; {{ date('Y') }} |
                    <a href="{{ url('/api/roms') }}" class="text-white">Elenco Giochi</a>
                </p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Monitoramento caricamento file
            document.querySelector('form').addEventListener('submit', function(e) {
                const fileInput = document.getElementById('file');
                const statusDiv = document.getElementById('uploadStatus');

                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileName = file.name;

                    statusDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Caricamento in corso: <strong>${fileName}</strong>...
                    </div>
                `;
                }

                e.preventDefault();
                // Il form viene inviato automaticamente da Laravel
            });
        </script>
    </body>

</html>
