<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\User;

Route::get('/', function () {
    return view('index_touch');
});




//Items
Route::post('/search_items', 'ItemsController@search_items');

Route::post('/update_sc_qty', 'ItemsController@update_sc_qty');

Route::post('/del_sc_item', 'ItemsController@del_sc_item');

Route::post('/check_return', 'ItemsController@check_return');

Route::post('/sc_totals', 'ItemsController@sc_totals');

Route::post('/check_sc', 'ItemsController@check_sc');

Route::post('/sc_items', 'ItemsController@sc_items');

Route::post('/xchange_only_sc_items', 'ItemsController@xchange_only_sc_items');

Route::post('/fetch_items', 'ItemsController@fetch_items');

Route::post('/add_item_to_sc', 'ItemsController@add_item_to_sc');

Route::post('/search_item_pull_up', 'ItemsController@search_item_pull_up');

Route::post('/csr_update', 'ItemsController@csr_update');

Route::post('/csr_check', 'ItemsController@csr_check');

Route::post('/update_price', 'ItemsController@update_price');

Route::post('/search_auto_comp_values', 'ItemsController@search_auto_comp_values');

Route::post('/mbox_qty_update', 'ItemsController@mbox_qty_update');

Route::post('/mbox_price_update', 'ItemsController@mbox_price_update');

Route::post('/new_goods_received', 'ItemsController@new_goods_received');

Route::post('/fetch_goods_item', 'ItemsController@fetch_goods_item');

Route::post('/goods_reports_data', 'ItemsController@goods_reports_data');

Route::post('/search_goods', 'ItemsController@search_goods');

Route::post('/check_held_sc', 'ItemsController@check_held_sc');

Route::post('/goods_transfer_reports_data', 'ItemsController@goods_transfer_reports_data');

Route::post('/transfer_goods_branch', 'ItemsController@transfer_goods_branch');

Route::post('/create_delivery_note', 'ItemsController@create_delivery_note');

Route::post('/receive_goods', 'ItemsController@receive_goods');

Route::post('/delivery_notes_reports', 'ItemsController@delivery_notes_reports');

Route::post('/delete_goods', 'ItemsController@delete_goods');

Route::post('/add_sc_acc_item', 'ItemsController@add_sc_acc_item');

Route::post('/new_add_sc_acc_item', 'ItemsController@new_add_sc_acc_item');

Route::post('/remove_acc_sc_item', 'ItemsController@remove_acc_sc_item');

Route::post('/fetch_acc_sc_items', 'ItemsController@fetch_acc_sc_items');

Route::post('/del_sc_acc_item', 'ItemsController@del_sc_acc_item');

Route::post('/sc_items_xchange', 'ItemsController@sc_items_xchange');

Route::post('/return_for_xchange', 'ItemsController@return_for_xchange');

Route::post('/remove_xchange_sc', 'ItemsController@remove_xchange_sc');

Route::post('/xchange_sc_totals', 'ItemsController@xchange_sc_totals');

Route::get('/tester', 'ItemsController@tester');

Route::post('/upload_items_xls', 'ItemsController@upload_items_xls');

Route::post('/upload_goods_xls', 'ItemsController@upload_goods_xls');

Route::get('/download_items_xls', 'ItemsController@download_items_xls');

Route::get('/download_goods_xls', 'ItemsController@download_goods_xls');

Route::post('/delete_from_goods', 'ItemsController@delete_from_goods');

Route::post('/search_goods_rprt', 'ItemsController@search_goods_rprt');






//Transactions
Route::post('/tender_trans', 'TransController@tender_trans');

Route::post('/drawings_transactions', 'TransController@drawings_transactions');

Route::post('/fetch_return_txn', 'TransController@fetch_return_txn');

Route::post('/search_trans_reports', 'TransController@search_trans_reports');

Route::post('/return_all_items', 'TransController@return_all_items');

Route::post('/fetch_return_txn_sec', 'TransController@fetch_return_txn_sec');

Route::post('/return_txns', 'TransController@return_txns');

Route::post('/accounts_txns', 'Admin@accounts_txns');

Route::post('/accounts_txns_update', 'Admin@accounts_txns_update');

Route::post('/search_accounts_txns', 'TransController@search_accounts_txns');

Route::post('/cust_acc_txns', 'TransController@cust_acc_txns');

Route::post('/remove_remaining_returns', 'TransController@remove_remaining_returns');

Route::post('/remove_remaining_sc', 'TransController@remove_remaining_sc');

Route::post('/remove_remaining_acc_sc', 'TransController@remove_remaining_acc_sc');

Route::post('/mpesa_trans', 'TransController@mpesa_trans');

Route::post('/return_trans', 'TransController@return_trans');

Route::post('/print_duplicate_receipt', 'TransController@print_duplicate_receipt');

Route::post('/pdq_trans', 'TransController@pdq_trans');

Route::post('/hold_trans', 'TransController@hold_trans');

Route::post('/get_held_trans_data', 'TransController@get_held_trans_data');

Route::post('/sel_sc_totals', 'TransController@sel_sc_totals');

Route::post('/cheque_trans', 'TransController@cheque_trans');

Route::post('/check_customer', 'TransController@check_customer');

Route::post('/issue_acc_items', 'TransController@issue_acc_items');

Route::post('/remove_remaining_xchange_sc', 'TransController@remove_remaining_xchange_sc');

Route::post('/issue_xchange_items', 'TransController@issue_xchange_items');

Route::post('/new_tender_trans', 'TransController@new_tender_trans');

Route::post('/tender_split_trans', 'TransController@tender_split_trans');

Route::post('/new_return_trans', 'TransController@new_return_trans');
#return_all_for_xchange_txns ----- Not in use
Route::post('/return_all_for_xchange_txns', 'TransController@return_all_for_xchange_txns');

Route::post('/check_xchange', 'TransController@check_xchange');





Route::post('/return_sc_item', 'NewTransController@return_sc_item');

Route::post('/new_search_trans_reports', 'NewTransController@new_search_trans_reports');

Route::post('/search_accounts_txns', 'NewTransController@search_accounts_txns');

Route::post('/search_cust_acc_txns', 'NewTransController@search_cust_acc_txns');






//Admin
Route::post('/dash_totals', 'Admin@dash_totals');

Route::post('/todays_trans_reports', 'Admin@todays_trans_reports');

Route::post('/todays_trans_totals', 'Admin@todays_trans_totals');

Route::post('/open_drawer', 'Admin@open_drawer');

Route::post('/drawer_report', 'Admin@drawer_report');

Route::post('/search_drawer_report', 'Admin@search_drawer_report');

Route::post('/new_tax', 'Admin@new_tax');

Route::post('/tax_reports', 'Admin@tax_reports');

Route::post('/new_user', 'Admin@new_user');

Route::post('/users_reports', 'Admin@users_reports');

Route::post('/new_item', 'Admin@new_item');

Route::post('/admin_items_report', 'Admin@admin_items_report');

Route::post('/get_tax_options', 'Admin@get_tax_options');

Route::post('/hourly_graph', 'Admin@hourly_graph');

Route::post('/daily_graph', 'Admin@daily_graph');

Route::post('/search_transactions', 'Admin@search_transactions');

Route::post('/search_trans_totals', 'Admin@search_trans_totals');

Route::post('/get_trans_dets', 'Admin@get_trans_dets');

Route::post('/get_trans_dets_totals', 'Admin@get_trans_dets_totals');

Route::post('/get_single_item', 'Admin@get_single_item');

Route::post('/update_item_values', 'Admin@update_item_values');

Route::post('/login', 'Admin@login');

Route::post('/close_drawer', 'Admin@close_drawer');

Route::post('/logout', 'Admin@logout');

Route::post('/get_my_acc_dets', 'Admin@get_my_acc_dets');

Route::post('/update_acc_info', 'Admin@update_acc_info');

Route::post('/update_passd', 'Admin@update_passd');

Route::post('/check_drawer', 'Admin@check_drawer');

Route::post('/item_sales_report', 'Admin@item_sales_report');

Route::post('/catg_sales_report', 'Admin@catg_sales_report');

Route::post('/filter_items_sales', 'Admin@filter_items_sales');

Route::post('/users_logs', 'Admin@users_logs');

Route::post('/search_logs', 'Admin@search_logs');

Route::post('/admin_catg_report', 'Admin@admin_catg_report');

Route::post('/new_item_catg', 'Admin@new_item_catg');

Route::post('/catg_trans_by_items', 'Admin@catg_trans_by_items');

Route::post('/savePO', 'Admin@savePO');

Route::post('/get_catg_options', 'Admin@get_catg_options');

Route::post('/new_item_sub_catg', 'Admin@new_item_sub_catg');

Route::post('/get_sub_catg_options', 'Admin@get_sub_catg_options');

Route::post('/get_all_sub_catg_options', 'Admin@get_all_sub_catg_options');

Route::post('/todays_inventory_reports', 'Admin@todays_inventory_reports');

Route::post('/todays_inventory_totals', 'Admin@todays_inventory_totals');

Route::post('/search_todays_inventory_reports', 'Admin@search_todays_inventory_reports');

Route::post('/dets_search_todays_inventory_reports', 'Admin@dets_search_todays_inventory_reports');

Route::post('/search_customers_auto_comp', 'Admin@search_customers_auto_comp');

Route::post('/search_todays_inventory_totals', 'Admin@search_todays_inventory_totals');

Route::post('/dets_search_todays_inventory_totals', 'Admin@dets_search_todays_inventory_totals');

Route::post('/get_category_rprt_data', 'Admin@get_category_rprt_data');

Route::post('/get_sub_category_rprt_data', 'Admin@get_sub_category_rprt_data');

Route::post('/disable_item', 'Admin@disable_item');

Route::post('/enable_item', 'Admin@enable_item');

Route::post('/search_item_reports', 'Admin@search_item_reports');

Route::post('/detailed_search_transactions', 'Admin@detailed_search_transactions');

Route::post('/alt_detailed_search_transactions', 'Admin@alt_detailed_search_transactions');

Route::post('/alt_detailed_total_search_transactions', 'Admin@alt_detailed_total_search_transactions');

Route::post('/get_single_item_goods', 'Admin@get_single_item_goods');

Route::post('/detailed_total_search_transactions', 'Admin@detailed_total_search_transactions');

Route::post('/new_branch', 'Admin@new_branch');

Route::post('/get_branch_rprt_data', 'Admin@get_branch_rprt_data');

Route::post('/get_user_priviledges', 'Admin@get_user_priviledges');

Route::post('/get_admin_user_priviledges', 'Admin@get_admin_user_priviledges');

Route::post('/disable_user', 'Admin@disable_user');

Route::post('/enable_user', 'Admin@enable_user');

Route::post('/update_user_priviledges', 'Admin@update_user_priviledges');

Route::post('/new_customer', 'Admin@new_customer');

Route::post('/customers_reports', 'Admin@customers_reports');

Route::post('/search_customers_reports', 'Admin@search_customers_reports');

Route::post('/new_tourn', 'Admin@new_tourn');

Route::post('/tourn_reports', 'Admin@tourn_reports');

Route::post('/fetch_customer_data', 'Admin@fetch_customer_data');

Route::post('/fetch_tournaments_data', 'Admin@fetch_tournaments_data');

Route::post('/fetch_held_data', 'Admin@fetch_held_data');

Route::post('/sel_curr_branch', 'Admin@sel_curr_branch');

Route::post('/get_active_branch_rprt_data', 'Admin@get_active_branch_rprt_data');

Route::post('/get_current_branch', 'Admin@get_current_branch');

Route::post('/drawings_report', 'Admin@drawings_report');

Route::post('/new_club', 'Admin@new_club');

Route::post('/club_reports', 'Admin@club_reports');

Route::post('/new_event', 'Admin@new_event');

Route::post('/get_active_clubs', 'Admin@get_active_clubs');

Route::post('/events_reports', 'Admin@events_reports');

Route::post('/search_events_reports', 'Admin@search_events_reports');

Route::post('/get_active_sponsors', 'Admin@get_active_sponsors');

Route::post('/get_active_events', 'Admin@get_active_events');

Route::post('/get_all_events', 'Admin@get_all_events');


Route::post('/get_active_customers', 'Admin@get_active_customers');

Route::post('/get_active_users', 'Admin@get_active_users');

Route::post('/new_acc_entry', 'Admin@new_acc_entry');

Route::post('/acc_entry_rprts', 'Admin@acc_entry_rprts');

Route::post('/del_acc_entry', 'Admin@del_acc_entry');

Route::post('/search_auto_comp_customers', 'Admin@search_auto_comp_customers');

Route::post('/search_acc_rprts', 'Admin@search_acc_rprts');

Route::post('/det_search_acc_rprts', 'Admin@det_search_acc_rprts');

Route::post('/amt_drawings_left', 'Admin@amt_drawings_left');

Route::post('/create_invoice', 'Admin@create_invoice');

Route::post('/archive_events', 'Admin@archive_events');

Route::post('/new_check_customer', 'Admin@new_check_customer');

Route::post('/get_goods_comment', 'Admin@get_goods_comment');

Route::get('/create_pdf_receipt', 'Admin@create_pdf_receipt');










//Pool
Route::post('/send_to_pool', 'PoolController@send_to_pool');

Route::post('/pool_activities', 'PoolController@pool_activities');

Route::post('/pool_reports', 'PoolController@pool_reports');

Route::post('/upload_results_xls', 'PoolController@upload_results_xls');

Route::post('/search_pool_report', 'PoolController@search_pool_report');

Route::post('/search_activities_report', 'PoolController@search_activities_report');

Route::post('/pool_reports_by_events', 'PoolController@pool_reports_by_events');

Route::post('/search_pool_event_report', 'PoolController@search_pool_event_report');

Route::post('/delete_activity_purchase', 'PoolController@delete_activity_purchase');

Route::post('/delete_activity_allocation', 'PoolController@delete_activity_allocation');

Route::post('/detailed_search_activities_report', 'PoolController@detailed_search_activities_report');





Route::post('/upload_data', 'UpDown@upload_data');

Route::post('/download_data', 'UpDown@download_data');

Route::post('/update_data', 'UpDown@update_data');

Route::post('/up_reports_check', 'UpDown@up_reports_check');

Route::get('/mail_reorder', 'UpDown@mail_reorder');

Route::get('/new_mail_reorder', 'UpDown@new_mail_reorder');












































































































































































