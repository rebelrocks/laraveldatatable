<?php

Route::get('/sendnotification', 'HomeController@sendnotification')->name('sendnotification');
Route::get('/users/provider', 'UsersController@signup')->name('signup');
Route::put('/users/provider', 'UsersController@register');
Route::put('frontlogin', 'UsersController@login')->name('frontlogin');

Route::get('/', function () {	
	if(Route::currentRouteName() == 'password.request'){
		return redirect(route('password.request'));	
	}elseif(auth::check() && auth()->user()->role !=true){
		return redirect(route('admin_dashboard'));
	}else{
    	return redirect(route('admin_login'));
	}	
	return view('welcome');
});

Route::namespace('API\V1')->group(function () {
	Route::get('/email/verify/{access_token}/{timestamp}/{lc}', 'Users\UserHomeController@verifyEmailAddress');

});


Route::get('locale/{locale}', function ($locale){
    Session::put('locale', $locale);
    return redirect()->back();
});

//Send reset password email
Route::get('sendresetpasswordEmail', 'Auth\ForgotPasswordController@sendresetpasswordEmail');

# Clear application cache
Route::get('/refresh', function () {
	Artisan::call('cache:clear');
	Artisan::call('view:clear');
	Artisan::call('config:clear');
	Artisan::call('clear-compiled');
	Artisan::call('config:cache');
	
	return redirect(route('admin_dashboard'))->with('success', 'Cache & temp files Cleared Successfully');
})->name('cacheClear');

Route::get('/home', 'HomeController@index')->name('home');

// Authentication Routes...

Route::prefix('admin')->group(function () {
	Route::get('/', 'Admin\Auth\AdminloginController@showLoginForm');
	Route::get('login', 'Admin\Auth\AdminloginController@showLoginForm')->name('admin_login');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'admin'], function() {

	//Admin dashboard
	Route::get('/', 'DashboardController@index')->name('admin_dashboard');

	Route::get('dashboard', 'DashboardController@index')->name('admin_dashboard');

	//Admin profile
	Route::get('users/edit_profile', 'AdminprofileController@edit')->name('admin.edit_profile');
	Route::put('users/edit_profile', 'AdminprofileController@update');
	//Change admin password
	Route::get('users/changepassword', 'AdminprofileController@editpassword')->name('admin.changepassword');
	Route::put('users/changepassword', 'AdminprofileController@changepassword');
	
	
	
	#slide route group

	
	Route::namespace('Faq')->group(function () {
		
		Route::get('faq', 'FaqController@index')->name('faqs');		
		Route::get('faqlist/{id}', 'FaqController@listing')->name('faqlisting');
		
		Route::get('faq/add', 'FaqController@create')->name('create_faqs');
		
		Route::post('faq/store', 'FaqController@store')->name('store_faqs');
		
		Route::get('faq/edit/{id}', 'FaqController@show')->name('edit_faqs');

		Route::put('faq/edit', 'FaqController@update')->name('update_faqs');
		
		Route::get('faq/delete/{id}', 'FaqController@destroy')->name('delete_faqs');

		Route::post('faqs/status', 'FaqController@updatestatus')->name('updatefaqstatus');
		
	});
		
	 
	Route::namespace('Faqcategory')->group(function () {
		 
		
		 
		Route::get('faqcategories/create', 'FaqcategoriesController@create')->name('add_faqcategory');
		Route::post('faqcategories/store', 'FaqcategoriesController@store')->name('store_faqcategory');;
		 
		Route::get('faqcategories/{id?}', 'FaqcategoriesController@index')->name('faqcategories');
		Route::post('faqcategories/status/{id}', 'FaqcategoriesController@status')->name('update_faqcategory_status');
		 
		Route::get('faqcategories/edit/{id}', 'FaqcategoriesController@edit')->name('update_faqcategory');
		 	
		Route::put('faqcategories/edit/{id}', 'FaqcategoriesController@update');
		 
		Route::get('faqcategories/delete/{id}', 'FaqcategoriesController@destroy')->name('delete_faqcategory');
		 
		Route::post('faqcategories/ajax-sorting', 'FaqcategoriesController@sortTable')->name('faqcategories_sortTable');
	});


	Route::namespace('Constituency')->group(function () {
		
		 
		Route::get('constituency/create', 'ConstituenciesController@create')->name('add_constituency');
		Route::post('constituency/store', 'ConstituenciesController@store')->name('store_constituency');;
		 
		Route::get('constituency/{id?}', 'ConstituenciesController@index')->name('constituency');
		Route::post('constituency/status/{id}', 'ConstituenciesController@updatestatus')->name('update_constituency_status');
		 
		Route::get('constituency/edit/{id}', 'ConstituenciesController@edit')->name('update_constituency');
		 	
		Route::put('constituency/edit/{id}', 'ConstituenciesController@update');
		 
		Route::get('constituency/delete/{id}', 'ConstituenciesController@destroy')->name('delete_constituency');
		 
		Route::post('constituency/ajax-sorting', 'ConstituenciesController@sortTable')->name('constituency_sortTable');

		Route::post('ajax_get_constituency', 'ConstituenciesController@ajax_get_constituency')->name('ajax_get_constituency');
	});

	Route::namespace('Booth')->group(function () {
		
		 
		Route::get('booth/create', 'BoothsController@create')->name('add_booth');
		Route::post('booth/store', 'BoothsController@store')->name('store_booth');;
		 
		Route::get('booth/{id?}', 'BoothsController@index')->name('booths');
		Route::post('booth/status/{id}', 'BoothsController@updatestatus')->name('update_booth_status');
		 
		Route::get('booth/edit/{id}', 'BoothsController@edit')->name('update_booth');
		 	
		Route::put('booth/edit/{id}', 'BoothsController@update');
		 
		Route::get('booth/delete/{id}', 'BoothsController@destroy')->name('delete_booth');
		 
		Route::post('booth/ajax-sorting', 'BoothsController@sortTable')->name('booth_sortTable');

		Route::post('ajax_get_booth', 'BoothsController@ajax_get_constituency')->name('ajax_get_booth');
	});

	Route::namespace('Block')->group(function () {
		
		#Subcategory route code
		
		Route::get('sub_blocks/create', 'SubBlocksController@create')->name('add_sub_block');
		Route::post('sub_blocks/store', 'SubBlocksController@store')->name('store_sub_block');;
		  
		Route::get('sub_blocks/edit/{id}', 'SubBlocksController@edit')->name('update_sub_block');
		 	
		Route::put('sub_blocks/edit/{id}', 'SubBlocksController@update');
		
		 
		Route::get('blocks/create', 'BlocksController@create')->name('add_block');
		Route::post('blocks/store', 'BlocksController@store')->name('store_block');;
		 
		Route::get('blocks/{id?}', 'BlocksController@index')->name('blocks');
		Route::post('blocks/status/{id}', 'BlocksController@updatestatus')->name('update_category_status');
		 
		Route::get('blocks/edit/{id}', 'BlocksController@edit')->name('update_block');
		 	
		Route::put('blocks/edit/{id}', 'BlocksController@update');
		 
		Route::get('blocks/delete/{id}', 'BlocksController@destroy')->name('delete_block');
		 
		Route::post('blocks/ajax-sorting', 'BlocksController@sortTable')->name('blocks_sortTable');

		Route::post('ajax_get_blocks', 'BlocksController@ajax_get_blocks')->name('ajax_get_blocks');

		Route::get('sub_blocks/{id?}', 'BlocksController@sub_blocks')->name('sub_blocks');
		
		Route::post('ajax_get_sub_blocks/{id?}', 'BlocksController@ajax_get_sub_blocks')->name('ajax_get_sub_blocks');
 
		
	});
    
    Route::namespace('Database')->group(function () {

	});


	# User routes list
	Route::namespace('User')->group(function () {
		//Get all users
		Route::get('users/{type?}', 'UsersController@index')->name('users');

		Route::post('ajax_get_users/{type?}', 'UsersController@users')->name('ajax_get_users');
		
        Route::get('user/add', 'UsersController@create')->name('adduser');
		
		Route::post('user/store', 'UsersController@store')->name('storeuser');
		
		Route::get('user/edit/{id}', 'UsersController@edit')->name('edituser');
		
		Route::get('user/details/{id}', 'UsersController@details')->name('viewuser');
		
		//Update user detail	
		Route::put('user/edit/{id}', 'UsersController@update');
		//Delete user detial
		Route::get('users/delete/{id}', 'UsersController@destroy')->name('delete_user');
		
		Route::get('users/{user_id}/{id}/delete-image', 'UsersController@deleteuserimage')->name('deleteuserimage');
		Route::get('users/{user_id}/{id}/delete-image-restaturant', 'UsersController@deleterestaturantimage')->name('deleterestaturantimage');

		Route::post('users/status', 'UsersController@updatestatus');
	
	});	
	
	Route::namespace('Pages')->group(function () {
		//Cms pages
		Route::get('pages', 'PagesController@index')->name('cms_pages');
		//Update page status
		Route::post('pages/status/{id}', 'PagesController@updatestatus')->name('update_page_status');
		//Edit page detail
		Route::get('pages/edit/{id}', 'PagesController@edit')->name('editpage');
		//Update page detail
		Route::put('pages/edit/{id}', 'PagesController@update');
	});
		
	//Social links update
	Route::get('social_links', 'Socialinks\SocialinksController@index')->name('social_links');
	Route::put('social_links', 'Socialinks\SocialinksController@update');

	//Web setting
	Route::get('settings', 'Settings\SettingsController@index')->name('web_settings');
	Route::put('settings', 'Settings\SettingsController@update');

	//Email templates routes list
	Route::namespace('Emailtemplate')->group(function () {
		//Email templates
		Route::get('emailtemplates', 'EmailtemplatesController@index')->name('email_templates');
		//Add new email template
		Route::get('emailtemplates/create', 'EmailtemplatesController@create')->name('add_email_template');
		Route::post('emailtemplates/create', 'EmailtemplatesController@store');

		//Edit template detail
		Route::get('emailtemplates/edit/{id}', 'EmailtemplatesController@edit')->name('edit_email_template');
		//Update template detail
		Route::put('emailtemplates/edit/{id}', 'EmailtemplatesController@update');
		//Update template status
		Route::post('emailtemplates/status/{id}', 'EmailtemplatesController@updatestatus')->name('updateemailtemplatestatus');
		//Send test email
		Route::get('emailtemplates/test_email/{id}', 'EmailtemplatesController@sendtestemail')->name('sendtestemail');
	});

	//Notification routes list
	Route::namespace('Notifications')->group(function () {
		//Notification
		Route::get('notifications', 'NotificationsController@index')->name('notifications');
		//Add new notification
		Route::get('notifications/create', 'NotificationsController@create')->name('create_notification');
		Route::post('notifications/create', 'NotificationsController@store');
		//Notification receive users
		Route::get('notifications/userlist/{id}', 'NotificationsController@userlist')->name('notification_receive_users');
		//Notification History
		Route::get('notifications/view/{id}', 'NotificationsController@history')->name('notification_history');
		//Delete Notifications
		Route::get('notifications/delete/{id}', 'NotificationsController@destroy')->name('delete_notifications');
		//Notification user count
		Route::post('notifications/usercount', 'NotificationsController@usercount')->name('ajax_user_count');

		//Send email's
		Route::get('notifications/sendnotification', 'NotificationsController@sendnotificationEmail')->name('admin.sendnotificationemail');
		//Send push notification
		Route::get('notifications/sendpushnotification', 'NotificationsController@sendpushnotification')->name('admin.sendpushnotification');
	});

	//Language routes list
	Route::namespace('Language')->group(function () {
		//Language codes
		Route::get('languages', 'LanguagecodesController@index')->name('languages');
		//Add language code
		Route::get('languages/create', 'LanguagecodesController@create')->name('addlanguagecode');
		Route::post('languages/create', 'LanguagecodesController@store');
		//Edit language code
		Route::get('languages/edit/{id}', 'LanguagecodesController@edit')->name('editlanguagecode');
		Route::put('languages/edit/{id}', 'LanguagecodesController@update');
		//Delete language code
		Route::get('languages/delete/{id}', 'LanguagecodesController@destroy')->name('delete_language_code');
		//Update language code status
		Route::post('languages/status/{id}', 'LanguagecodesController@updatestatus')->name('language_status');
		//Update default language
		Route::get('languages/set_default_language/{new_local}/{default_language}', 'LanguagecodesController@updatedefaultlanguage')->name('set_default_language');
	});
	
	//Manager routes list	
	Route::namespace('Managers')->group(function () { 	
		//Managers
		Route::get('managers', 'ManagersController@index')->name('managers');
		//Add new manager
		Route::get('managers/create', 'ManagersController@create')->name('add_manager');
		Route::post('managers/create', 'ManagersController@store');
		//Edit manager detail
		Route::get('managers/edit/{id}', 'ManagersController@edit')->name('edit_manager');
		Route::put('managers/edit/{id}', 'ManagersController@update');
		//Delete manager list
		Route::get('manager/delete/{id}', 'ManagersController@destroy')->name('delete_manager');
	});
	
	//Site permissions route list
	Route::namespace('Sitepermissions')->group(function (){	
		//Site permissions
		Route::get('sitepermissions/role/{role_id}', 'SitepermissionsController@index')->name('sitepermissions');
		//Add site permission
		Route::get('sitepermissions/{role_id}/create', 'SitepermissionsController@create')->name('add_site_permissions');
		Route::post('sitepermissions/{role_id}/create', 'SitepermissionsController@store');
		//Edit site permission
		Route::get('sitepermissions/{role_id}/edit/{id}', 'SitepermissionsController@edit')->name('edit_site_permission'); 
		Route::put('sitepermissions/{role_id}/edit/{id}', 'SitepermissionsController@update');
		//Delete site permission
		Route::get('sitepermissions/{role_id}/delete/{id}', 'SitepermissionsController@destroy')->name('delete_site_permission');
		//Update site permission status
		Route::post('sitepermissions/site_permissionstatus', 'SitepermissionsController@permissionstatus')->name('site_permissionstatus');
		Route::post('sitepermissions/status', 'SitepermissionsController@updatestatus')->name('site_permission_status');

		//Update all status one time action
		Route::get('sitepermissions/update_all_status/{status}/{role_id}', 'SitepermissionsController@updateallstatus')->name('admin.updateallsitepermissionstatus');
	});

	//Notificationtexts
	Route::namespace('Notificationtext')->group(function (){
		//All notifications text
		Route::get('notificationtext', 'NotificationtextsController@index')->name('notificationtext');
		//Add notification text
		Route::get('notificationtext/create', 'NotificationtextsController@create')->name('add_notification_text');
		Route::post('notificationtext/create', 'NotificationtextsController@store');
		//Edit notification text
		Route::get('notificationtext/edit/{id}', 'NotificationtextsController@edit')->name('edit_notification_text');
		Route::put('notificationtext/edit/{id}', 'NotificationtextsController@update');
		//Delete notification text
		Route::get('notificationtext/delete/{id}', 'NotificationtextsController@destroy')->name('delete_notification_text');
	});

	//Words lists
	Route::namespace('Wordlists')->group(function () {
		//Show all word lists
		Route::get('wordlists', 'WordlistController@index')->name('wordlists');		
		Route::post('ajax_get_wordlists', 'WordlistController@wordlists')->name('ajax_get_wordlists');
		//Add new word list detail
		Route::get('wordlists/create', 'WordlistController@create')->name('create_new_word_list');	
		Route::post('wordlists/create', 'WordlistController@store');
		//Edit word detial
		Route::get('wordlists/edit/{id}', 'WordlistController@edit')->name('edit_word_detial');	
		Route::put('wordlists/edit/{id}', 'WordlistController@update');
		//Delete 
		Route::get('wordlists/delete/{id}', 'WordlistController@destroy')->name('delete_wordlist');
		//Status update
		//Route::get('wordlists/status/{id}', 'WordlistController@status')->name('wordlist_status');	
		Route::post('wordlists/status/{id}', 'WordlistController@status')->name('wordlist_status');	
	});


	//Newsletter
	Route::namespace('Newsletter')->group(function () {
		
		//Create new newsletter
		Route::get('newsletters/create', 'NewslettersController@create')->name('create_newsletter');
		Route::post('newsletters/create', 'NewslettersController@store');
		//Newsletter history
		Route::get('newsletters/history', 'NewslettersController@newshistory')->name('newsletterhistory');
		//Newsletter user list
		Route::get('newsletters/userlist/{id}', 'NewslettersController@userlist')->name('newsletteruserlist');
		//View newsletter
		Route::get('newsletters/detail/{id}', 'NewslettersController@viewnewsstatus')->name('viewnewsstatus');
		//User count
		Route::post('newsletters/usercount', 'NewslettersController@usercount')->name('ajax_newsletter_user_count');
		
		//Delete newsletter list
		Route::get('newsletters/delete/{id}', 'NewslettersController@destroy')->name('delete_newsletter');

		//Send email's
		Route::get('newsletters/sendemails', 'NewslettersController@sendnewsletterEmail')->name('sendemails');

		//Ajax send test newsletter email
		Route::post('newsletter/send_test_newsletter_email', 'NewslettersController@sendtestnewsletterEmail')->name('ajax_send_test_email_newsletter');



	});

	//Roles lists
	Route::namespace('Roles')->group(function () {
		//Show all role lists
		Route::get('roles', 'RolesController@index')->name('roles');
		//Add new role list detail
		Route::get('roles/create', 'RolesController@create')->name('create_new_role');	
		Route::post('roles/create', 'RolesController@store');
		//Edit role detial
		Route::get('roles/edit/{id}', 'RolesController@edit')->name('edit_role');	
		Route::put('roles/edit/{id}', 'RolesController@update');
		//Delete role
		Route::get('roles/delete/{id}', 'RolesController@destroy')->name('delete_role');
		
	});

	
	//Show all admin user lists
	Route::get('adminusers', 'AdminusersController@index')->name('admin_users');
	//Add new admin user detail
	Route::get('adminusers/create', 'AdminusersController@create')->name('create_new_admin_user');	
	Route::post('adminusers/create', 'AdminusersController@store');
	//Edit admin user detial
	Route::get('adminusers/edit/{id}', 'AdminusersController@edit')->name('edit_admin_user');	
	Route::put('adminusers/edit/{id}', 'AdminusersController@update');
	Route::get('adminusers/delete/{id}', 'AdminusersController@destroy')->name('delete_admin_user');

	//Ads
	Route::namespace('Ads')->group(function () {
		//Show all ads lists
		Route::get('ads', 'AdsController@index')->name('manage_ads');
		//Add new ads list detail
		Route::get('ads/create', 'AdsController@create')->name('create_new_ad');	
		Route::post('ads/create', 'AdsController@store');
		//Edit ads detial
		Route::get('ads/edit/{id}', 'AdsController@edit')->name('edit_ad');	
		Route::put('ads/edit/{id}', 'AdsController@update');
		//Delete ads code
		Route::get('ads/delete/{id}', 'AdsController@destroy')->name('delete_ads');
		//Update ads code status
		Route::post('ads/status/{id}', 'AdsController@status')->name('ads_status_update');
		
	});
	
	//Ads
	Route::namespace('Ads')->group(function () {
		//Show all ads lists
		Route::get('ads', 'AdsController@index')->name('manage_ads');
		//Add new ads list detail
		Route::get('ads/create', 'AdsController@create')->name('create_new_ad');	
		Route::post('ads/create', 'AdsController@store');
		//Edit ads detial
		Route::get('ads/edit/{id}', 'AdsController@edit')->name('edit_ad');	
		Route::put('ads/edit/{id}', 'AdsController@update');
		//Delete ads code
		Route::get('ads/delete/{id}', 'AdsController@destroy')->name('delete_ads');
		//Update ads code status
		Route::post('ads/status/{id}', 'AdsController@status')->name('ads_status_update');
		
	});

});	

Route::get('pages/{slug}/{language}', function ($slug, $language){
	
	$lng_detail = \DB::table('languagecodes')->where('code', $language)->first();
	if($lng_detail){
		$page_detail = \DB::table('pages')
						->where('languagecode_id', $lng_detail->id)
						->where('slug', $slug)
						->first();
		
		if(!$page_detail){			
			return abort(404);			
		}
		
	    return view('page', compact('page_detail'));
	}    
	return abort(404);
	
})->name('page_detail');


Route::get('cms/pages/{slug}', function ($slug){
	
	if($slug){
		$page_detail = \DB::table('preparations')
						->where('status', 1)
						->where('title', $slug)
						->first();
		
		if(!$page_detail){			
			return abort(404);			
		}
		
	    return view('preparation_page', compact('page_detail'));
	}    
	return abort(404);
	
})->name('cms_page_detail');


Route::get('importExportView','ExcelController@importExportView')->name('importExportView');
Route::post('import','ExcelController@import')->name('import');
Route::get('export','ExcelController@export')->name('export');


Auth::routes(['register' => false]);
