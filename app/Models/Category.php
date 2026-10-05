<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relasi dengan products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Count products dalam kategori
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    // Accessor untuk status text
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    // Mutator untuk generate slug + guard hapus kategori yang masih punya produk
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        // Cegah hapus kategori kalau masih ada produk di dalamnya.
        // withTrashed() disengaja: constraint FK restrict di DB tetap menghitung
        // baris produk yang soft-deleted (masih fisik ada di tabel), jadi guard
        // ini harus konsisten sama apa yang bakal ditolak DB.
        static::deleting(function ($category) {
            if ($category->products()->withTrashed()->exists()) {
                throw new \RuntimeException(
                    'Kategori "' . $category->name . '" masih memiliki produk dan tidak bisa dihapus. Pindahkan atau hapus produknya terlebih dahulu.'
                );
            }
        });
    }

    // Scope untuk mencari berdasarkan nama
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    // Get active products dari kategori
    public function activeProducts()
    {
        return $this->products()->where('status', 'active');
    }
}
