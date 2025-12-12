@extends('layouts.app')

@section('title', 'Analysis History')

@php
    $description = 'View your complete analysis history. Browse all past website security header analyses with detailed security scores and recommendations.';
@endphp

@section('meta')
    <meta name="description" content="{{ $description }}">
    @php
        $ogTitle = 'Analysis History - AnaEvaRec';
        $ogDescription = $description;
        $ogImage = url('/images/og-image.png');
    @endphp
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap');
    
    body {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 30%, #93c5fd 60%, #60a5fa 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }
    
    .history-heading {
        font-family: 'Rubik', sans-serif;
        font-weight: 700;
    }

    .recent-analyses-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
    }

    .recent-analyses-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        background-color: #fdfdff;
    }

    .recent-analyses-table thead th {
        background-color: #f3f4ff;
        color: #374151;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .recent-analyses-table tbody tr {
        background-color: #ffffff;
    }

    .recent-analyses-table tbody tr + tr td {
        border-top: 1px solid #f1f3f8;
    }

    .recent-analyses-table th,
    .recent-analyses-table td {
        padding: 16px 20px;
        border: none;
        vertical-align: middle;
        text-align: center;
    }

    .recent-analyses-table a.table-link {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    .recent-analyses-table a.table-link:hover {
        text-decoration: underline;
    }

    .delete-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #dc3545;
        background-color: #fff;
        color: #dc3545;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
        transition: all 0.2s;
        margin-right: 8px;
        cursor: pointer;
    }

    .delete-btn:hover {
        background-color: #dc3545;
        color: #fff;
        transform: scale(1.1);
    }

    .modern-success-alert {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(52, 211, 153, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideDown 0.3s ease-out;
        position: relative;
        overflow: hidden;
    }

    .modern-success-alert::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: rgba(255, 255, 255, 0.5);
    }

    .modern-success-alert-content {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.05rem;
        flex: 1;
    }

    .modern-success-alert-icon {
        width: 24px;
        height: 24px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .modern-success-alert-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 18px;
        line-height: 1;
        flex-shrink: 0;
        margin-left: 12px;
    }

    .modern-success-alert-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h3 class="mb-4 history-heading">ANALYSIS HISTORY</h3>

            @if(session('success'))
                <div class="modern-success-alert" role="alert">
                    <div class="modern-success-alert-content">
                        <span style="font-family: 'Rubik', sans-serif; font-weight: 700; letter-spacing: -0.01em;">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="modern-success-alert-close" onclick="this.parentElement.remove()" aria-label="Close">×</button>
                </div>
            @endif

            @if($analyses->count() > 0)
                <div class="recent-analyses-card bg-white shadow-sm p-4 mb-4">
                    <div class="table-responsive">
                        <table class="recent-analyses-table">
                            <thead>
                                <tr>
                                    <th><b>URL</b></th>
                                    <th>Security Score</th>
                                    <th><b>Missing Features</b></th>
                                    <th><b>Analyzed</b></th>
                                    <th><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $analysis)
                                    @php
                                        $displayHost = parse_url($analysis->url, PHP_URL_HOST) ?? $analysis->url;
                                        $isoTimestamp = $analysis->created_at
                                            ? $analysis->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y H:i:s')
                                            : '';
                                        
                                        // OWASP-style Grade Scale for badge colors
                                        if ($analysis->security_score >= 70) {
                                            $scoreBadgeClass = 'bg-success';
                                        } elseif ($analysis->security_score >= 40) {
                                            $scoreBadgeClass = 'bg-warning text-dark';
                                        } else {
                                            $scoreBadgeClass = 'bg-danger';
                                        }
                                        
                                        $missingCount = is_array($analysis->missing_security_features) ? count($analysis->missing_security_features) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ $analysis->url }}" target="_blank" class="table-link">
                                                {{ $displayHost }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-2 {{ $scoreBadgeClass }}">
                                                {{ $analysis->security_score }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-2 {{ $missingCount > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $missingCount }} Missing Headers
                                            </span>
                                        </td>
                                        <td class="text-muted"><b>{{ $isoTimestamp }}</b></td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <form action="{{ route('analysis.destroy', $analysis->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this analysis?')" title="Delete analysis">
                                                        ×
                                                    </button>
                                                </form>
                                                <a href="{{ route('analysis.show', $analysis->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                                                    <b>View Details</b>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted mb-3">No analysis history found.</h4>
                        <a href="{{ route('home') }}" class="btn btn-primary">Start Analyzing</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
