@component('mail::message')
# You are invited to create an account

# 您被邀请创建了一个账户

If you do not know that you were invited to create this account, please delete this email

如果您不知道您被邀请创建了该账户，请删除改封邮件


Please log in to your application using your email address and initial password, and change your password as soon as possible

请使用您的邮箱和初始密码登录应用，并尽快修改密码

@component('mail::button', ['url' => config('app.url')])
	{{ $password }}
@endcomponent

@endcomponent