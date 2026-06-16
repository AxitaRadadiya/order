@extends('layouts.frontend')

@section('title', 'About Us')

@section('main_class', '')

@section('content')
<div class="about-page-wrapper">
    <!-- Hero Section -->
    <section class="about-hero text-center py-5">
        <div class="container py-5 position-relative z-index-2">
            <span class="about-hero-subtitle d-block mb-3">Who We Are</span>
            <h1 class="about-hero-title display-3 font-weight-bold">Our Story</h1>
            <p class="about-hero-desc lead">
                Delivering quality products and exceptional customer experiences.
            </p>
            <div class="scroll-indicator mt-5">
                <i class="fas fa-chevron-down fa-2x"></i>
            </div>
        </div>
    </section>

    <!-- About Company -->
    <section class="about-brand-section py-5 my-md-4">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 pr-lg-5">
                    <div class="about-brand-image-wrapper">
                        <img src="{{ asset('admin/dist/img/j1.jpg') }}"
                             class="img-fluid about-brand-img w-100"
                             alt="About Us"
                             onerror="this.onerror=null;">
                    </div>
                </div>

                <div class="col-lg-6">
                    <h2 class="about-section-tag"></h2>
                    <h2 class="about-brand-title mt-2">
                        About Live By Style
                    </h2>
                    <h5 class="about-brand-subtitle mt-2 mb-4">
                        Crafting Comfort. Delivering Style. Building Trust Since 2007.
                    </h5>

                    <p class="about-brand-p text-muted mb-3">
                        <strong>LIVE BY STYLE</strong>, a brand of <strong>LBS Trends Private Limited</strong>, is a leading manufacturer and wholesaler of premium cotton apparel in India. Since our establishment in 2007, we have been dedicated to creating garments that combine comfort, durability, quality, and contemporary style.
                    </p>

                    <p class="about-brand-p text-muted mb-3">
                        What began as a small venture with a team of just two members has grown into a trusted apparel manufacturing company with over <strong>20 full-time team members</strong>, <strong>100+ skilled workers</strong>, and a production capacity of <strong>20,000 garments per month</strong>.
                    </p>

                    <p class="about-brand-p text-muted mb-4">
                        With a strong focus on customer satisfaction, innovation, and quality craftsmanship, we proudly serve distributors, wholesalers, retailers, and business partners across multiple states in India.
                    </p>

                    <div class="brand-pill-badges mb-4">
                        <span class="brand-badge"><i class="fas fa-industry"></i> 20,000 Garments/Mo</span>
                        <span class="brand-badge"><i class="fas fa-users"></i> 100+ Skilled Workers</span>
                        <span class="brand-badge"><i class="fas fa-calendar-alt"></i> Est. 2007</span>
                    </div>

                    <a href="{{ url('/contact') }}" class="about-cta-btn mt-2">
                        Contact Us <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="about-vm-section py-5 my-md-4">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="about-section-tag">What Drives Us</span>
                <h2 class="fw-bold mt-2 text-dark">Vision & Mission</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="vm-card text-center h-100">
                        <div class="vm-icon-wrapper">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-dark">Our Vision</h3>
                        <p class="text-muted mb-0" style="font-size: 1rem; line-height: 1.7;">
                            We are committed to offering great service and real value
                            to our business partners and consumers. We strive to create
                            a pleasant and fair environment where employees, workers,
                            and associates can grow their careers.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="vm-card text-center h-100">
                        <div class="vm-icon-wrapper">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-dark">Our Mission</h3>
                        <p class="text-muted mb-0" style="font-size: 1rem; line-height: 1.7;">
                            To design clothes with great comfort and unique style for all age customers in exceptional price value.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-5 my-md-4">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="about-section-tag">Our Foundation</span>
                <h2 class="fw-bold mt-2 text-dark">Core Values</h2>
            </div>

            <div class="core-values-slider-wrapper position-relative">
                <button class="slider-btn slider-prev d-none d-md-flex shadow-sm"><i class="fas fa-chevron-left"></i></button>
                <div class="core-values-slider" id="coreValuesSlider">
                    <!-- Integrity -->
                    <div class="slider-item">
                        <div class="value-card h-100">
                            <div class="value-icon-wrapper">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Integrity</h5>
                            <p class="text-muted small mb-0">
                                Conducting business with honesty and transparency.
                            </p>
                        </div>
                    </div>

                    <!-- Respect -->
                    <div class="slider-item">
                        <div class="value-card h-100">
                            <div class="value-icon-wrapper">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Respect</h5>
                            <p class="text-muted small mb-0">
                                Valuing every customer, employee, supplier, and partner.
                            </p>
                        </div>
                    </div>

                    <!-- Innovation -->
                    <div class="slider-item">
                        <div class="value-card h-100">
                            <div class="value-icon-wrapper">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Innovation</h5>
                            <p class="text-muted small mb-0">
                                Embracing innovation and continuous improvement.
                            </p>
                        </div>
                    </div>

                    <!-- Service Excellence -->
                    <div class="slider-item">
                        <div class="value-card h-100">
                            <div class="value-icon-wrapper">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Service Excellence</h5>
                            <p class="text-muted small mb-0">
                                Delivering exceptional service and support.
                            </p>
                        </div>
                    </div>

                    <!-- Giving Back -->
                    <div class="slider-item">
                        <div class="value-card h-100">
                            <div class="value-icon-wrapper">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Giving Back</h5>
                            <p class="text-muted small mb-0">
                                Contributing positively to society and communities.
                            </p>
                        </div>
                    </div>
                </div>
                <button class="slider-btn slider-next d-none d-md-flex shadow-sm"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="about-stats-section py-5 text-white">
        <div class="container py-4">
            <div class="row g-4 text-center">
                <!-- Item 1 -->
                <div class="col-lg col-md-4 col-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-calendar-check"></i></span>
                        <h2 class="stat-number">2007</h2>
                        <p class="stat-label mb-0">Founded</p>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="col-lg col-md-4 col-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-box-open"></i></span>
                        <h2 class="stat-number">50+</h2>
                        <p class="stat-label mb-0">SKUs</p>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="col-lg col-md-4 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-map-marked-alt"></i></span>
                        <h2 class="stat-number">28</h2>
                        <p class="stat-label mb-0">States</p>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="col-lg col-md-4 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-users"></i></span>
                        <h2 class="stat-number">10K+</h2>
                        <p class="stat-label mb-0">Customers</p>
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="col-lg col-md-4 col-6 mx-auto">
                    <div class="stat-item">
                        <span class="stat-icon"><i class="fas fa-store"></i></span>
                        <h2 class="stat-number">500+</h2>
                        <p class="stat-label mb-0">Retailers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Certificates & Awards -->
    <section class="recognition-section py-5 my-md-4">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="about-section-tag">Recognition</span>
                <h2 class="fw-bold mt-2 text-dark">Certificates & Awards</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-lg-0">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-certificate"></i></span>
                        <h6 class="rec-title mb-0">ISO 9001:2015</h6>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-lg-0">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-trophy"></i></span>
                        <h6 class="rec-title mb-0">Best Brand</h6>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-lg-0">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-recycle"></i></span>
                        <h6 class="rec-title mb-0">Eco Fabric</h6>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-md-0">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-star"></i></span>
                        <h6 class="rec-title mb-0">MSME Registered</h6>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6 mb-4 mb-md-0">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-check-circle"></i></span>
                        <h6 class="rec-title mb-0">GST Compliant</h6>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="rec-badge-card">
                        <span class="rec-icon"><i class="fas fa-medal"></i></span>
                        <h6 class="rec-title mb-0">Top Rated Seller</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('coreValuesSlider');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    
    if (slider && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            const itemWidth = slider.querySelector('.slider-item').offsetWidth + 24; // width + gap
            slider.scrollBy({ left: -itemWidth, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', () => {
            const itemWidth = slider.querySelector('.slider-item').offsetWidth + 24; // width + gap
            slider.scrollBy({ left: itemWidth, behavior: 'smooth' });
        });
    }
});
</script>
@endpush