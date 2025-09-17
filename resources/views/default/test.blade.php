@extends('panel.layout.app', ['disable_tblr' => true, 'disable_titlebar' => true])
@section('title', __('Overview'))

@section('content')
<form
	action="{{ route('test.post') }}"
	method="POST"
	enctype="multipart/form-data"
	class="form-horizontal"
	>

	{{-- submit button --}}
	<button
		type="submit"
		class="btn btn-primary mt-3"
	>
		{{ __('Submit') }}
	</button>

</form>
@endsection
