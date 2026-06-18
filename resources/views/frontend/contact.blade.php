@extends('layouts.frontend')

@section('title', 'Contact Us')

@section('main_class', '')

@section('content')
<div class="contact-page-wrapper">
    <!-- Hero Section -->
    <section class="contact-hero text-center py-5">
        <div class="container py-5 position-relative z-index-2">
            <span class="contact-hero-subtitle d-block mb-3">Get In Touch</span>
            <h1 class="contact-hero-title display-3 font-weight-bold">Contact Us</h1>
            <p class="contact-hero-desc lead">
                Have questions? We'd love to hear from you.
            </p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-5 my-md-4">
        <div class="container py-4">
            <div class="row g-4">

                <!-- Contact Info -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="contact-info-wrapper h-100">
                        <h3 class="contact-section-title mb-4">Contact Information</h3>

                        <div class="contact-info-card">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>Email</h6>
                                <p class="mb-0">Livebystyle.amd@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>Phone</h6>
                                <p class="mb-0">+91 81414 67888</p>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-icon-wrapper">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h6>Address</h6>
                                <p class="mb-0">
                                    GF, Jaisinghbhai Vadi<br>
                                    Opp. Gheekanta Metro Station<br>
                                    Gheekanta Road<br>
                                    Ahmedabad, Gujarat, India
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-form-card">
                        <h3 class="contact-section-title mb-4">Send us a Message</h3>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control premium-input" value="{{ old('name') }}" placeholder="Your Name" required>
                                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control premium-input" value="{{ old('email') }}" placeholder="Email Address">
                                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" name="mobile" class="form-control premium-input" value="{{ old('mobile') }}" placeholder="Mobile Number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" inputmode="numeric">
                                    @error('mobile')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control premium-input" value="{{ old('subject') }}" placeholder="Subject">
                                    @error('subject')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label">Message</label>
                                    <textarea name="message" rows="5" class="form-control premium-input" value="{{ old('message') }}" placeholder="Write your message here..." required></textarea>
                                    @error('message')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="contact-cta-btn w-100 justify-content-center">
                                        Send Message <i class="fas fa-paper-plane ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Map Section -->
            <div class="map-section mt-5 pt-4">
                <div class="map-frame-wrapper">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d229.4858589128592!2d72.58783041712383!3d23.032078294085856!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84464fe66f6f%3A0x9a9ee7a27a68754e!2sLIVE%20BY%20STYLE%20%7C%20PM%20Creation%20%7C%20Cottan%20track%20pent%20%7C%20Cottan%20Cargo%20pent%20%7C%20Trousers!5e0!3m2!1sen!2sin!4v1781593972891!5m2!1sen!2sin"
                        width="100%"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
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