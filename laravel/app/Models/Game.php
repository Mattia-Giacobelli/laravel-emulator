<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Game extends Model
{
    use HasFactory;

    protected $table = 'games';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'title',
        'region',
        'category',
        'description',
        'file_path'
    ];

    /**
     * Restituisce il percorso completo del file ROM
     */
    public function getFilepathAttribute(): string
    {
        return config('app.storage_path') . '/roms/' . $this->file_path;
    }

    /**
     * Verifica se il file esiste fisicamente
     */
    public function existsInStorage(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        $path = config('app.storage_path') . '/roms/' . $this->file_path;
        return File::exists($path);
    }

    /**
     * Restituisce la dimensione del file
     */
    public function getFileSizeAttribute(): int
    {
        if (!$this->existsInStorage()) {
            return 0;
        }

        $path = config('app.storage_path') . '/roms/' . $this->file_path;
        return File::size($path);
    }

    /**
     * Ottiene l'URL pubblico per il download/streaming
     */
    public function getPublicUrlAttribute(): string
    {
        return '/' . $this->file_path;
    }
}
