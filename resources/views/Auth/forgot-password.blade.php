@extends('layouts.auth')
@push('style')
    <style>
        /* * * * * General CSS * * * * */
*,
*::before,
*::after {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 16px;
  font-weight: 400;
  color: #666666;
  background: #eaeff4;
}

.wrapper {
  margin: 0 auto;
  width: 100%;
  max-width: 1140px;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.container {
  position: relative;
  width: 100%;
  max-width: 600px;
  height: auto;
  display: flex;
  background: #ffffff;
  box-shadow: 0 0 15px rgba(0, 0, 0, .1);
}

.credit {
  position: relative;
  margin: 25px auto 0 auto;
  width: 100%;
  text-align: center;
  color: #666666;
  font-size: 16px;
  font-weight: 400;
}

.credit a {
  color: #222222;
  font-size: 16px;
  font-weight: 600;
}

/* * * * * Login Form CSS * * * * */
h2 {
  margin: 0 0 15px 0;
  font-size: 30px;
  font-weight: 700;
}

h2 img {
  width: 120px;
}

p {
  margin: 0 0 20px 0;
  font-size: 16px;
  font-weight: 500;
  line-height: 22px;
}

.btn {
  display: inline-block;
  padding: 7px 20px;
  font-size: 16px;
  letter-spacing: 1px;
  text-decoration: none;
  border-radius: 5px;
  color: #ffffff;
  outline: none;
  border: 1px solid #ffffff;
  transition: .3s;
  -webkit-transition: .3s;
}

.btn:hover {
  color: #4CAF50;
  background: #ffffff;
}

.col-left,
.col-right {
  width: 55%;
  padding: 45px 35px;
  display: flex;
}

.col-left {
  width: 45%;
  background: #e2f1ef;
  -webkit-clip-path: polygon(98% 17%, 100% 34%, 98% 51%, 100% 68%, 98% 84%, 100% 100%, 0 100%, 0 0, 100% 0);
  clip-path: polygon(98% 17%, 100% 34%, 98% 51%, 100% 68%, 98% 84%, 100% 100%, 0 100%, 0 0, 100% 0);
}

@media(max-width: 575.98px) {
  .container {
    flex-direction: column;
    box-shadow: none;
  }

  .col-left,
  .col-right {
    width: 100%;
    margin: 0;
    padding: 30px;
    -webkit-clip-path: none;
    clip-path: none;
  }
}

.login-text {
  position: relative;
  width: 100%;
  color: #ffffff;
  text-align: center;
}

.login-form {
  position: relative;
  width: 100%;
  color: #666666;
}

.login-form p:last-child {
  margin: 0;
}

.login-form p a {
  color: #ee7c08;
  font-size: 14px;
  text-decoration: none;
}

.login-form p:last-child a:last-child {
  float: right;
}

.login-form label {
  display: block;
  width: 100%;
  margin-bottom: 2px;
  letter-spacing: .5px;
}

.login-form p:last-child label {
  width: 60%;
  float: left;
}

.login-form label span {
  color: #ff574e;
  padding-left: 2px;
}

.login-form input {
  display: block;
  width: 100%;
  height: 40px;
  padding: 0 10px;
  font-size: 16px;
  letter-spacing: 1px;
  outline: none;
  border: 1px solid #cccccc;
  border-radius: 5px;
}

.login-form input:focus {
  border-color: #ff574e;
}

.login-form input.btn {
  color: #ffffff;
  background: #ee7c08;
  border-color: #ee7c08;
  outline: none;
  cursor: pointer;
}

.login-form input.btn:hover {
  color: #ee7c08;
  background: #ffffff;
}
.container{
    padding-right: 0px;
    padding-left: 0px;
}
.field-icon {
    float: right;
    margin-left: -25px;
    margin-right: 7px;
    margin-top: -30px;
    position: relative;
    z-index: 2;
}
.alert-danger {
    color: #850b0b;
    background-color: #ffd0d0;
    border-color: #ffbebe;
}
.alert-success {
    color: #426e11;
    background-color: #e5f6d3;
    border-color: #dbf3c1;
}
</style>
@endpush
@section('content')
<div class="wrapper">
    <div class="container">
      <div class="col-left">
        <div class="login-text">
            <h2 style="color:#ee7c08">Trust <span style="color:#0e93cd">Wave</span></h2>
            <img src="{{ siteLogo() }}" alt="{{ env('APP_NAME') }}" style="max-width: 200px;">
        </div>
      </div>
      <div class="col-right">
        <div class="login-form">
          <h2>Forgot Password</h2>
          @if(session('status'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
          @endif
          
          @if($errors)
              @foreach ($errors->all() as $error)
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ $error }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endforeach
          @endif
          <form action="{{ routePut('password.email') }}" method="POST">
            @csrf
            <p>
              <input type="email" placeholder="Email" name= "email" required>
            </p>
            <p>
              <input class="btn" type="submit" value="Send Password Reset Link" />
            </p>
            <p>
              <a href="{{ routePut('app.login') }}">Login Now</a>
              {{-- <a href="">Create an account.</a> --}}
            </p>
          </form>
        </div>
      </div>
    </div>
    <div class="credit">
     <a href="#">&copy; Trustwave</a> and Designed by <a href="https://www.ap-group.io">AP Group</a>
    </div>
  </div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
          if($(".alert").length){
            //$(".alert").delay(4000).slideUp(200, function() {
                //$(this).alert('close');
            //});
          }
        });
    </script>
@endpush
