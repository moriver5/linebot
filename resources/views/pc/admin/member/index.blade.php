@extends('layouts.app')

@section('content')
<br />
<div class="container">
	@if( Auth::guard('admin')->user()->type >= 3 )
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/search">
			{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>USER LIST</b>
					@if( !empty($db_data) )
					&nbsp;&nbsp;<a href="/admin/member/create/{{ $db_data->currentPage() }}">アカウント新規作成</a>　
					@else
					&nbsp;&nbsp;<a href="/admin/member/create">アカウント新規作成</a>　
					@endif
				</div>
                <div class="panel-body">
					<center>
						<table border="1">
							<tr>
								<td style="font-weight:bold;padding:5px;text-align:center;background:wheat;">
									ID
								</td>
								<td style="font-weight:bold;padding:5px;text-align:center;background:wheat;">
									ログインID
								</td>
								<td style="font-weight:bold;padding:5px;text-align:center;background:wheat;">
									管理区分
								</td>
							</tr>
							@if( !empty($db_data) )
								@foreach($db_data as $lines)
									<tr>
										<td style="padding:5px;text-align:center;">
											{{ $lines->id }}
										</td>
										<td style="padding:5px;text-align:center;">
											<a href="/admin/member/edit/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->email }}</a>
										</td>
										<td style="padding:5px;text-align:center;">
											{{ $admin_auth_list[$lines->type] }}
										</td>
									</tr>
								@endforeach
							@endif
						</table>
						@if( !empty($db_data) )
							{{ $db_data->links() }}
						@endif
					</center>
                </div>
            </div>
			</form>
        </div>
    </div>
	@endif

	<div class="panel panel-default col-md-8 col-md-offset-2">
		<div class="panel-heading"><b>LOGIN USER</b></div>
		<div class="panel-body">
			<table border="1" width="100%">
				<tr>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						ログインID
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						日時
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						HOST
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						USER AGENT
					</td>
				</tr>
				@foreach($db_ua_data as $lines)
					<tr>
						<td style="font-size:11px;padding:5px;text-align:center;">
							{{ $lines->email }}
						</td>
						<td style="font-size:11px;padding:5px;text-align:center;">
							{{ $lines->last_login_date }}
						</td>
						<td style="font-size:11px;padding:5px;text-align:center;">
							{{ $lines->access_host }}
						</td>
						<td style="font-size:11px;padding:5px;text-align:center;">
							{{ substr($lines->user_agent,0,60) }}
						</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
	
</div>
@endsection
