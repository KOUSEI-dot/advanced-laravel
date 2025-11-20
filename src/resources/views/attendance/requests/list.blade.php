@extends('layouts.authenticated')

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction.css') }}">
@endsection

@section('content')
<div class="request-wrapper">

    <h1 class="page-title">申請一覧</h1>

    {{-- タブ --}}
    <div class="tab-header">
        <button id="pending-tab" class="tab-button active" onclick="showTab('pending')">承認待ち</button>
        <button id="approved-tab" class="tab-button" onclick="showTab('approved')">承認済み</button>
    </div>
    <div class="tab-border"></div>

    @php
        $isAdmin = Auth::user()->role === 'admin';
    @endphp

    {{-- ▼ 承認待ち --}}
    <div id="pending" class="tab-content active">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($pendingRequests as $request)
                <tr>
                    <td class="status">承認待ち</td>
                    <td>{{ $request->user->name }}</td>

                    <td>
                        {{ $request->attendanceRecord
                            ? $request->attendanceRecord->date->format('Y/m/d')
                            : '-' }}
                    </td>

                    <td>{{ $request->request_reason ?? '—' }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>

                    <td>
                        <a href="{{ $isAdmin 
                            ? route('admin.requests.detail', $request->id)
                            : route('attendance.request.usershow', $request->id)
                        }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ▼ 承認済み --}}
    <div id="approved" class="tab-content">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($approvedRequests as $request)
                <tr>
                    <td class="status">承認済み</td>
                    <td>{{ $request->user->name }}</td>

                    <td>
                        {{ $request->attendanceRecord
                            ? $request->attendanceRecord->date->format('Y/m/d')
                            : '-' }}
                    </td>

                    <td>{{ $request->request_reason ?? '—' }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>

                    <td>
                        <a href="{{ $isAdmin 
                            ? route('admin.requests.detail', $request->id)
                            : route('attendance.request.usershow', $request->id)
                        }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>


<script>
function showTab(tabId) {
    // タブ切り替え
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId + '-tab').classList.add('active');
}
</script>

@endsection
