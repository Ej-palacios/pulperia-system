@extends('layouts.app', ['title' => 'Punto de Venta'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Punto de Venta</li>
@endsection

@section('page-title')
    Punto de Venta (POS)
@endsection

@section('styles')
    <!-- React y Babel para el componente POS -->
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
@endsection

@section('content')
<div id="pos-react"></div>
@endsection

@section('scripts')
<script type="text/babel" src="{{ asset('js/ventas-pos.jsx') }}"></script>
@endsection