@extends('layouts.frontend')

@section('title', 'Our Network')

@section('main_class', '')

@section('content')
<div class="network-page-wrapper">
    <!-- Hero Section -->
    <section class="network-hero">
        <div class="container position-relative z-index-2">
            <span class="network-hero-subtitle d-block mb-3">Our Presence</span>
            <h1 class="network-hero-title">Our Network</h1>
            <p class="network-hero-desc lead mb-0">
                Delivering quality products through a strong nationwide network.
            </p>
        </div>
    </section>

    <!-- State Grid Section -->
    <section class="network-section py-5 my-md-4">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="network-section-tag">Our Distribution Network</span>
                <h2 class="network-heading mt-2">We Are Across India</h2>
            </div>

            <div class="state-grid">
                <div class="state-card">
                    <h5>Gujarat</h5>
                    <span>45 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Maharashtra</h5>
                    <span>38 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Rajasthan</h5>
                    <span>22 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Madhya Pradesh</h5>
                    <span>20 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Chhattisgarh</h5>
                    <span>16 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Uttar Pradesh</h5>
                    <span>28 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Delhi</h5>
                    <span>30 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Telangana</h5>
                    <span>18 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>Haryana</h5>
                    <span>25 Dealers</span>
                </div>
                <div class="state-card">
                    <h5>More States...</h5>
                    <span>Expanding</span>
                </div>
            </div>
        </div>

        <div class="inquiry-card my-5">
        <div class="inquiry-content text-center">
            <h2>JOIN WITH US</h2>
            <p>Become our dealer in your city. Register with us and our team will contact you within 24 hours.</p>

            <a href="{{ route('register') }}" class="btn inquiry-btn">
                JOIN NOW
            </a>
        </div>
        </div>
    </section>
</div>
@endsection