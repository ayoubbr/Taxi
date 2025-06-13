@extends('client.layout')

@section('content')
    <div class="container mt-70">
        <h1>Applications for Booking #{{ substr($booking->booking_uuid, 0, 8) }}</h1>
        <p>Review the drivers who have applied for your trip and choose one.</p>

        @if ($applications->isEmpty())
            <div class="alert alert-info">No drivers have applied yet. Please check back later.</div>
        @else
            <div class="list-group">
                @foreach ($applications as $application)
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Driver: {{ $application->driver->firstname }}
                                {{ $application->driver->lastname }}</h5>
                            <form
                                action="{{ route('client.bookings.accept_application', ['booking' => $booking, 'application' => $application]) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Accept & Assign</button>
                            </form>
                        </div>
                        <p class="mb-1">Taxi: {{ $application->taxi->type }} - {{ $application->taxi->license_plate }}</p>
                        <small>Applied on: {{ $application->created_at->format('d M Y, H:i') }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
