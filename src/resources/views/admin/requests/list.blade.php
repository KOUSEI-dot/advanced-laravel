@extends('layouts.admin')

@section('title', '勤怠申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction.css') }}">
@endsection

@section('content')

<div class="request-wrapper">

    {{-- タイトル --}}
    <h1 class="page-title">申請一覧</h1>

    {{-- タブヘッダー --}}
    <div class="tab-header">
        <button class="tab-button active" id="pending-tab" onclick="showTab('pending')">承認待ち</button>
        <button class="tab-button" id="approved-tab" onclick="showTab('approved')">承認済み</button>
    </div>

    <div class="tab-border"></div>

    {{-- ▼ 承認待ち --}}
    <div class="tab-content active" id="pending">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingRequests as $request)
                <tr>
                    <td class="status">承認待ち</td>
                    <td>{{ $request->user->name }}</td>
                    <td>
                        {{ optional($request->attendanceRecord)->date
                            ? \Carbon\Carbon::parse($request->attendanceRecord->date)->format('Y/m/d')
                            : 'N/A'
                        }}
                    </td>
                    <td>{{ $request->request_reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.requests.detail', $request->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ▼ 承認済み --}}
    <div class="tab-content" id="approved">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($approvedRequests as $request)
                <tr>
                    <td class="status">承認済み</td>
                    <td>{{ $request->user->name }}</td>
                    <td>
                        {{ optional($request->attendanceRecord)->date
                            ? \Carbon\Carbon::parse($request->attendanceRecord->date)->format('Y/m/d')
                            : 'N/A'
                        }}
                    </td>
                    <td>{{ $request->request_reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.requests.detail', $request->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    document.getElementById(tabId).classList.add('active');

    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(tabId + '-tab').classList.add('active');
}
</script>

@endsection
