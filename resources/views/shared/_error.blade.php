{{--验证错误消息提醒--}}
@if(count($errors) > 0)
  <div class="alert alert-danger" role="alert">
    <div class="mt-2"><strong>有错误发生：</strong></div>
    <ul class="mt-2 mb-2">
      @foreach($errors->all() as $error)
        <li><i class="glyphicon gylphicon-remove"></i>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
