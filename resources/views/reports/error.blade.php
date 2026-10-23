@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-danger mb-3">⚠️ Power BI Error</h2>
    <p>{{ session('error') ?? $message ?? 'Something went wrong while loading the report.' }}</p>

    <a href="{{ route('reports.show') }}" class="btn btn-primary mt-3">
        Try again
    </a>
</div>
@endsection
