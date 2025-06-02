@extends('errors.layout')

@section('code', isset($exception) ? $exception->getStatusCode() : 'Error')
@section('title', isset($exception) ? $exception->getMessage() ?: 'Terjadi Kesalahan' : 'Terjadi Kesalahan')
@section('message', 'Maaf, terjadi kesalahan dalam memproses permintaan Anda.')