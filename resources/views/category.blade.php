@extends('layouts.app')

@section('content')
<input type="hidden" name="api_token" value="{{ $token }}">
<router-view></router-view>
@endsection