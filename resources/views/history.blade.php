@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4 class="mb-4">Analysis History</h4>

            @if($analyses->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Security Score</th>
                                        <th>Missing Features</th>
                                        <th>Analyzed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analyses as $analysis)
                                        <tr>
                                            <td>
                                                <a href="{{ $analysis->url }}" target="_blank" class="text-decoration-none">
                                                    {{ $analysis->url }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($analysis->security_score >= 80) bg-success
                                                    @elseif($analysis->security_score >= 50) bg-warning
                                                    @else bg-danger
                                                    @endif">
                                                    {{ $analysis->security_score }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if(is_array($analysis->missing_security_features) && count($analysis->missing_security_features) > 0)
                                                    <span class="badge bg-danger">{{ count($analysis->missing_security_features) }} missing</span>
                                                @else
                                                    <span class="badge bg-success">All present</span>
                                                @endif
                                            </td>
                                            <td>{{ $analysis->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('analysis.show', $analysis->id) }}" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $analyses->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-muted mb-3">No analysis history found.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Start Analyzing</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
