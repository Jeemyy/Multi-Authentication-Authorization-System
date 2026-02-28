<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets-back') }}/"
  data-template="vertical-menu-template-free"
>
@include('back.auth.partials.head')
  <body>
    <!-- Content -->
        <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              @include('back.auth.partials.logo')
              <!-- /Logo -->
              <h4 class="mb-2">Reset Password? 🔒</h4>
              <form id="formAuthentication" class="mb-3" action="{{ route('back.password.store') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autofocus
                    value="{{old('email', $request->email)}}"
                  />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autofocus
                    value="{{old('password', $request->password)}}"
                  />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="mb-3">
                  <label for="confirm_password" class="form-label">Confirm Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="confirm_password"
                    name="password_confirmation"
                    placeholder="Enter your confirm password"
                    autofocus
                    value="{{old('password_confirmation', $request->password_confirmation)}}"
                  />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
              </form>
              <div class="text-center">
                <a href="{{ route('back.login') }}" class="d-flex align-items-center justify-content-center">
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                  Back to login
                </a>
              </div>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    @include('back.auth.partials.scripts')
  </body>
</html>
