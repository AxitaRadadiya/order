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
    </section>

    <!-- Join Section -->
    <section class="join-section">
        <div class="container">
            <div class="join-box text-center">
                <h2 class="join-title">Join With Us</h2>
                <p class="join-desc mx-auto">
                    Become our dealer in your city. Fill the form below and our team will contact you within 24 hours.
                </p>

                <form class="network-form text-left mt-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" placeholder="First Name">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" placeholder="Mobile Number">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Email Address">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">City</label>
                            <select name="city_id" class="form-select form-control">
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">State</label>
                            <select name="state_id" class="form-select form-control">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label">Requirement / Message</label>
                            <textarea rows="4" class="form-control" placeholder="Tell us about your dealership requirement..."></textarea>
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="about-cta-btn">
                                Submit Inquiry <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection