@extends('layouts.app')

@section('title', 'Birthday Cake')

@section('content')
    <section class="birthday-page">
        <div class="container">
            <header class="birthday-head">
                <p class="eyebrow">Momen spesial, kue istimewa</p>
                <h1 class="h1">Birthday Cake</h1>
                <p class="lead">Lihat inspirasi kue ulang tahun yang dibuat dengan penuh perhatian oleh Byuss Cakery.</p>
            </header>

            <section class="birthday-gallery" aria-label="Galeri Birthday Cake">
                <div class="birthday-empty">
                    <svg class="birthday-empty__icon" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M10 24h28v17H10zM7 41h34M24 24v17M10 31h28M24 8v9M18 13c0-2 2-3 2-5 0-1-.5-2-1-3M30 13c0-2-2-3-2-5 0-1 .5-2 1-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 24a3 3 0 0 1 3-3h28a3 3 0 0 1 3 3v0H7v0Z" fill="currentColor" opacity=".14"/>
                    </svg>
                    <h2 class="h2">Galeri segera hadir</h2>
                    <p>Foto-foto Birthday Cake buatan admin akan ditampilkan di sini.</p>
                </div>
            </section>

            <section class="birthday-custom" aria-labelledby="birthday-custom-title">
                <div>
                    <p class="eyebrow">Punya ide sendiri?</p>
                    <h2 class="h2" id="birthday-custom-title">Buat kue sesuai keinginanmu</h2>
                    <p class="lead" id="birthday-custom-note">Hubungi kami melalui WhatsApp untuk mulai merancang Birthday Cake custom.</p>
                </div>
                <a class="btn btn--primary birthday-custom__button"
                    href="https://wa.me/6285979754307?text={{ rawurlencode('Halo, saya ingin custom Birthday Cake.') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-describedby="birthday-custom-note">
                    Custom Cake
                </a>
            </section>
        </div>
    </section>
@endsection

@push('head')
    <link rel="stylesheet" href="{{ asset('css/birthday-cake.css') }}">
@endpush
