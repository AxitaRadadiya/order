@extends('layouts.frontend')

@section('title','Home')

@section('content')
  <section class="hero text-center">
    <div class="container">
      <h1 class="display-4">Welcome to My Store</h1>
      <p class="lead">Shop products by category or browse our featured items.</p>
      <p>
        <a class="btn btn-primary btn-lg mr-2" href="{{ route('login') }}">Login</a>
        <a class="btn btn-outline-primary btn-lg" href="{{ route('register') }}">Sign Up</a>
      </p>
    </div>
  </section>

  <section id="products" class="py-5">
    <div class="container">
      <h2>Featured Products</h2>
      <p class="text-muted">(Placeholder) Add product cards here.</p>
      <div class="row">
        <div class="col-md-4">
          <div class="card mb-3"><div class="card-body">Product 1</div></div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3"><div class="card-body">Product 2</div></div>
        </div>
        <div class="col-md-4">
          <div class="card mb-3"><div class="card-body">Product 3</div></div>
        </div>
      </div>
    </div>
  </section>

  <section id="categories" class="py-5 bg-light">
    <div class="container">
      <h2>Categories</h2>
      <p class="text-muted">(Placeholder) Link categories to category-wise product pages.</p>
    </div>
  </section>

  <section id="about" class="py-5">
    <div class="container">
      <h2>About Company</h2>
      <p class="text-muted">Short blurb about the company goes here.</p>
    </div>
  </section>

  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <h2>Contact</h2>
      <p class="text-muted">Provide contact details or a contact form here.</p>
    </div>
  </section>

@endsection
