{{--
    Pagination kustom untuk tampilan produk (dan halaman lain yang mau
    pakai gaya sama). Dipakai lewat $products->links('vendor.pagination.kep').

    Kenapa tidak pakai view bawaan Bootstrap-5 Laravel: view bawaan
    membungkus semuanya dalam satu <nav class="d-flex justify-content-between">
    yang isinya teks "Showing X to Y of Z results" didorong ke kiri dan
    tombol halaman didorong ke kanan. Kalau <nav> itu sendiri dibungkus lagi
    dengan <div class="d-flex justify-content-center"> (seperti sebelumnya),
    justify-content-between di dalam tetap membentangkan kedua sisi ke ujung
    kontainer, bukan benar-benar "satu unit" yang center — hasilnya terasa
    lebar dan kosong di tengah, bukan rapi. Di sini semuanya (ringkasan +
    tombol halaman) sengaja ditumpuk vertikal dan di-center sebagai satu
    kesatuan.
--}}
@if ($paginator->hasPages())
    <nav aria-label="Navigasi halaman produk" class="kep-pagination">
        @if (method_exists($paginator, 'total') && method_exists($paginator, 'firstItem'))
            <p class="kep-pagination-summary">
                Menampilkan <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong>
                dari <strong>{{ $paginator->total() }}</strong> produk
            </p>
        @endif

        <ul class="pagination kep-pagination-list">
            {{-- Tombol halaman sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled kep-page-nav-item" aria-disabled="true">
                    <span class="page-link kep-page-nav" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item kep-page-nav-item">
                    <a class="page-link kep-page-nav" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Nomor halaman (dengan elipsis otomatis dari Laravel) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled kep-page-dots" aria-disabled="true">
                        <span class="page-link kep-page-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link kep-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link kep-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol halaman berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="page-item kep-page-nav-item">
                    <a class="page-link kep-page-nav" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled kep-page-nav-item" aria-disabled="true">
                    <span class="page-link kep-page-nav" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
