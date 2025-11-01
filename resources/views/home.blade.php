@extends('layouts.app')

@section('content')
<!-- Analyzer Section -->
    
<div class="container">
    <div class="row justify-content-center">
        <div>
        <h4>Web Security Header Analyzer</h4>
        <p>Scan any website you want — enter the website URL below:</p>

        <form action="{{ route('analyze') }}" method="POST">
            @csrf
            <input type="text" name="url" placeholder="Enter website URL">
            <button type="submit">Analyze</button>
        </form>
    </div>
    </div>
</div>
@endsection
