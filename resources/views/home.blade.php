@extends('layouts.appPublic')

@section('content')
    <div class="row">
        {{-- Banner Slider --}}
        <div class="col-12 mb-4">
            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $banner->image_url) }}" class="d-block w-100" alt="Banner">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>

        <!--{{-- Kategori Motor --}}
        <div class="col-12 mb-4">
            <h3 class="mb-3">Kategori Motor</h3>
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $category->name }}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>-->

        <!--{{-- Daftar Motor --}}
        <div class="col-12">
            <h3 class="mb-3">Daftar Motor</h3>
            <div class="row">
                @foreach($motors as $motor)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="{{ asset('storage/' . $motor->colors->first()->image_url ?? 'default.jpg') }}" class="card-img-top" alt="{{ $motor->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $motor->name }}</h5>
                                <p class="card-text">Rp {{ number_format($motor->price, 0, ',', '.') }}</p>
                                <a href="#" class="btn btn-primary w-100">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>-->
@endsection