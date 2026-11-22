@extends('layouts.app')

@section('title', $title)

@section('content')
<section class="container soon">
    <p class="eyebrow">Segera hadir</p>
    <h1 class="h1">Halaman {{ $title }}</h1>
    <p class="lead">Halaman ini sedang kami siapkan. Sementara itu, kembali ke beranda untuk menemukan dessert favoritmu.</p>
    <a href="{{ route('home') }}" class="btn btn--primary">Kembali ke beranda</a>
</section>
@endsection
