{{-- JQuery --}}
<script src="{{ asset('js/app.js',env('APP_ENV') !== 'local')  }}"></script>
{{-- Bootstrap --}}
<link rel="stylesheet" href={{ asset('css/app.css',env('APP_ENV') !== 'local') }}>

<!-- Stripe JS -->
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<!-- Latest compiled and minified CSS -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">-->

<script src="{{ assetUrl('js/api/api_setup.js') }}"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">
