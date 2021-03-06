<?php

use App\Model\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminSetting::insert(['slug'=>'coin_price','value'=>'2.50']);
        AdminSetting::insert(['slug'=>'coin_name','value'=>'EVP']);
        AdminSetting::insert(['slug'=>'app_title','value'=>'ERAVITT']);
        AdminSetting::insert(['slug'=>'maximum_withdrawal_daily','value'=>'3']);
        AdminSetting::insert(['slug'=>'mail_from','value'=>'support@eravitt.com']);
        AdminSetting::insert(['slug'=>'admin_coin_address','value'=>'address']);
        AdminSetting::insert(['slug'=>'base_coin_type','value'=>'BTC']);
        AdminSetting::insert(['slug'=>'minimum_withdrawal_amount','value'=>.005]);
        AdminSetting::insert(['slug'=>'maximum_withdrawal_amount','value'=>120]);

        AdminSetting::create(['slug' => 'maintenance_mode', 'value' => 'no']);
        AdminSetting::create(['slug' => 'logo', 'value' => '']);
        AdminSetting::create(['slug' => 'login_logo', 'value' => '']);
        AdminSetting::create(['slug' => 'landing_logo', 'value' => '']);
        AdminSetting::create(['slug' => 'favicon', 'value' => '']);
        AdminSetting::create(['slug' => 'copyright_text', 'value' => 'Copyright@2020']);
        AdminSetting::create(['slug' => 'pagination_count', 'value' => '10']);
        AdminSetting::create(['slug' => 'point_rate', 'value' => '1']);
        //General Settings
        AdminSetting::create(['slug' => 'lang', 'value' => 'en']);
        AdminSetting::create(['slug' => 'company_name', 'value' => 'ERAVITT']);
        AdminSetting::create(['slug' => 'primary_email', 'value' => 'support@eravitt.com']);

        AdminSetting::create(['slug' => 'sms_getway_name', 'value' => 'twillo']);
        AdminSetting::create(['slug' => 'twillo_secret_key', 'value' => 'test']);
        AdminSetting::create(['slug' => 'twillo_auth_token', 'value' => 'test']);
        AdminSetting::create(['slug' => 'twillo_number', 'value' => 'test']);
        AdminSetting::create(['slug' => 'ssl_verify', 'value' => '']);

        AdminSetting::create(['slug' => 'mail_driver', 'value' => 'SMTP']);
        AdminSetting::create(['slug' => 'mail_host', 'value' => 'smtp.mailtrap.io']);
        AdminSetting::create(['slug' => 'mail_port', 'value' => 2525]);
        AdminSetting::create(['slug' => 'mail_username', 'value' => '']);
        AdminSetting::create(['slug' => 'mail_password', 'value' => '']);
        AdminSetting::create(['slug' => 'mail_encryption', 'value' => 'null']);
        AdminSetting::create(['slug' => 'mail_from_address', 'value' => '']);


        AdminSetting::create(['slug' => 'braintree_client_token', 'value' => 'test']);
        AdminSetting::create(['slug' => 'braintree_environment', 'value' => 'sandbox']);
        AdminSetting::create(['slug' => 'braintree_merchant_id', 'value' => 'test']);
        AdminSetting::create(['slug' => 'braintree_public_key', 'value' => 'test']);
        AdminSetting::create(['slug' => 'braintree_private_key', 'value' => 'test']);
        AdminSetting::create(['slug' => 'sms_getway_name', 'value' => 'twillo']);
        AdminSetting::create(['slug' => 'clickatell_api_key', 'value' => 'test']);
        AdminSetting::create(['slug' => 'number_of_confirmation', 'value' => '6']);
        AdminSetting::create(['slug' => 'referral_commission_percentage', 'value' => '10']);
        AdminSetting::create(['slug' => 'referral_signup_reward', 'value' => 10]);
        AdminSetting::create(['slug' => 'max_affiliation_level', 'value' => 10]);


        // Coin Api
        AdminSetting::create(['slug' => 'coin_api_user', 'value' => 'test']);
        AdminSetting::create(['slug' => 'coin_api_pass', 'value' => 'test']);
        AdminSetting::create(['slug' => 'coin_api_host', 'value' => 'test5']);
        AdminSetting::create(['slug' => 'coin_api_port', 'value' => 'test']);


        // Send Fees
        AdminSetting::create(['slug' => 'send_fees_type', 'value' => 1]);
        AdminSetting::create(['slug' => 'send_fees_fixed', 'value' => 0]);
        AdminSetting::create(['slug' => 'send_fees_percentage', 'value' => 0]);
        AdminSetting::create(['slug' => 'max_send_limit', 'value' => 0]);
        //order settings
        AdminSetting::create(['slug' => 'deposit_time', 'value' => 1]);

        //coin payment
        AdminSetting::create(['slug' => 'COIN_PAYMENT_PUBLIC_KEY', 'value' => 'test']);
        AdminSetting::create(['slug' => 'COIN_PAYMENT_PRIVATE_KEY', 'value' => 'test']);
        AdminSetting::create(['slug' => 'COIN_PAYMENT_CURRENCY', 'value' => 'BTC']);
        AdminSetting::create(['slug' => 'ipn_merchant_id', 'value' => '']);
        AdminSetting::create(['slug' => 'ipn_merchant_id', 'value' => '']);
        AdminSetting::create(['slug' => 'ipn_secret', 'value' => '']);

        AdminSetting::create(['slug' => 'payment_method_stripe', 'value' => 1]);


        // Send Fees
        AdminSetting::create(['slug' => 'membership_bonus_type', 'value' => 1]);
        AdminSetting::create(['slug' => 'membership_bonus_fixed', 'value' => 0]);
        AdminSetting::create(['slug' => 'membership_bonus_percentage', 'value' => 0]);

        //default coin withdrawal system
        AdminSetting::create(['slug' => 'chain_link', 'value' => "https://bsc-dataseed.binance.org"]);
        AdminSetting::create(['slug' => 'contract_address', 'value' => "0x587FEc15A0659604d6De9c17f2573feF1cE865F3"]);
        AdminSetting::create(['slug' => 'wallet_address', 'value' => "0x320850e8b45be71f0951722CB2683Dc1013dc346"]);
        AdminSetting::create(['slug' => 'private_key', 'value' => "1047dadd45a6edc5bd86b2ee43e398f6df0c6756133d40e4f972824f0a2ad889"]);



    }
}
