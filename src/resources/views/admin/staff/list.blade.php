@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_list.css') }}">
@endsection

@section('content')

<div class="staff-wrapper">

    {{-- タイトル --}}
    <h1 class="page-title">スタッフ一覧</h1>

    {{-- 白カード --}}
    <div class="staff-card">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>

            <tbody>
                @foreach($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td class="detail-link">
                        <a href="{{ route('admin.attendance.staff.detail', $staff->id) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
