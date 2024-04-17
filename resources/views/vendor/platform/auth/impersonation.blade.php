@extends('platform::auth')
@section('title',__('Access Denied: Viewing as Another User'))

@section('content')
    <h1 class="h4 text-black mb-4">{{__('Limited Access')}}</h1>

    <form role="form"
          method="POST"
          data-controller="form"
          data-form-need-prevents-form-abandonment-value="false"
          data-action="form#submit"
          action="{{ route('platform.switch.logout') }}">
        @csrf

        <p>
            {{ __("You are currently viewing this page on behalf of a user who does not have access to it. To return to viewing as yourself, please click the 'Switch to My Account' button. It's possible that the page may be displayed correctly when viewed from your own account.") }}
        </p>

        <button id="button-login" type="submit" class="btn btn-default btn-block" tabindex="2">
            <x-orchid-icon path="bs.box-arrow-in-right" class="small me-2"/> {{__('Switch to My Account')}}
        </button>

    </form>
@endsection
