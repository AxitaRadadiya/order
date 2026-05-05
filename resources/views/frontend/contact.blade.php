@extends('layouts.frontend')

@section('title','Contact')

@section('content')
<div class="container mt-4 mb-5 front">
<h2>Contact Us</h2>

<form>
  <div class="form-group">
    <input type="text" class="form-control" placeholder="Your Name">
  </div>
  <div class="form-group">
    <input type="email" class="form-control" placeholder="Email">
  </div>
  <div class="form-group">
    <textarea class="form-control" placeholder="Message"></textarea>
  </div>
  <button class="btn-create">Send</button>
</form>
</div>
@endsection