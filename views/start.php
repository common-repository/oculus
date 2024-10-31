<div>
<p><?php esc_html_e('Alibaba Yundun AntiSpam Service eliminates the comment spam you get on your site. Apply for appkey below to get started.', 'oculus'); ?></p>
<div >
	<div >
		<strong><?php esc_html_e( 'Activate Alibaba Yundun Antispam' , 'oculus');?></strong>
		<p><?php esc_html_e('Log in or create an account to get your Appkey and secret.', 'oculus'); ?></p>
	</div>
    <a href="http://www.aliyun.com/product/antifraud" target="_blank">Get your Appkey</a>
</div>
	<div >
		<strong><?php esc_html_e('Manually enter an Appkey', 'oculus'); ?></strong>
	</div>
	<form action="<?php echo esc_url( Oculus_Admin::get_page_url() ); ?>" method="post" >
        <p><strong>Get from Email.</strong></p>
        <p>AppKey<input type="text" name="appkey" /></p>
        <p>AppSecret<input type="text" name="appsecret" /></p>
        <p><strong>Get from this <a href="https://ak-console.aliyun.com/#/accesskey">location</a>.</strong></p>
        <p>Access Key ID<input type="text" name="accesskeyid" /><p>
        <p>Access Key Secret<input type="text" name="accesskeysecret" /></p>
		<input type="hidden" name="action" value="oc-appkey">
		<input type="submit" name="submit" id="submit"  value="<?php esc_attr_e('Use this key', 'oculus');?>">
	</form>

</div>
