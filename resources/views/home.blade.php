@extends('layouts.appPublic')

@section('content')
<div class="d-flex flex-row vh-100">
    {{-- Sidebar kiri --}}
    <aside class="sidebar bg-white p-3 d-flex flex-column align-items-center">
        <img src="/images/logo.png" alt="Logo" class="img-fluid mb-4" style="max-width: 140px;">
    </aside>

    {{-- Konten utama --}}
    <main class="flex-fill p-4 overflow-auto">
        <div class="row">
            <div class="col-12 mb-4">
                @if($banners->isNotEmpty())
                    {{-- Carousel Banner --}}
                    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach($banners as $index => $banner)
                                <button type="button"
                                        data-bs-target="#bannerCarousel"
                                        data-bs-slide-to="{{ $index }}"
                                        class="{{ $index == 0 ? 'active' : '' }}"
                                        aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                                        aria-label="Slide {{ $index+1 }}">
                                </button>
                            @endforeach
                        </div>

                        <div class="carousel-inner">
                            @foreach($banners as $index => $banner)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $banner->image_path }}"
                                         class="d-block w-100"
                                         alt="{{ $banner->title ?? 'Banner' }}"
                                         style="object-fit: cover; height: 400px; border-radius: 12px;">
                                </div>
                            @endforeach
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
                        </button>
                    </div>

                    {{-- Teks di bawah banner --}}
                    <div class="text-center mt-3">
                        <h2>Yuk, Pilih Motor Honda Favorit Versimu!</h2>
                    </div>

                    {{-- Kategori Motor --}}
                    <div class="category-container mt-4">
                        <div class="d-flex justify-content-center flex-wrap">
                            <a href="{{ route('home') }}" 
                               class="{{ request()->route()->getName() === 'home' ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
                               SEMUA
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('motors.by.category', $category->name) }}" 
                                   class="{{ request()->route()->getName() === 'motors.by.category' && request()->route('name') === $category->name ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
                                   {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p>Tidak ada banner yang tersedia.</p>
                @endif
            </div>
        </div>
    </main>
</div>

{{-- Styling tambahan --}}
<style>
.sidebar {
    width: 200px; /* fix lebar sidebar */
}
#bannerCarousel .carousel-indicators [data-bs-target] {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #dc3545;
}
#bannerCarousel .carousel-indicators .active {
    background-color: #000;
}
.category-container {
    text-align: center;
}
.custom-category-btn {
    background-color: #fff;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 8px 20px;
    font-size: 14px;
    border-radius: 20px;
    min-width: 120px;
    text-align: center;
    transition: all 0.3s ease;
}
.custom-category-btn:hover {
    background-color: #dc3545;
    color: #fff;
}
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    padding: 8px 20px;
    font-size: 14px;
    border-radius: 20px;
    min-width: 120px;
    text-align: center;
    transition: all 0.3s ease;
}
.btn-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
}
</style>
@endsection