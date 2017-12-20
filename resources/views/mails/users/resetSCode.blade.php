@component('mail::message')
# Reset password prompt

# 重置安全密码

Your verification code is

您的验证码为

@component('mail::button', ['url' => config('app.url')])
	{{ $code }}
@endcomponent

@endcomponent