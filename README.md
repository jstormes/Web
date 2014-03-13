Web_Scraper
===========

A simple library for scraping tables that are behind a login form.

I needed a simple and lightweight web page table scraper that could scrape Asp.Net pages.

## Examples:

### Login to a forms page:

    // Page with login form
    login_url='https://secure.somesite.com/Account/Login.aspx';
		
    $scraper = new Web_Scraper();
		
    // Get login page
    $login_page = $scraper->get_page($login_url);

    // Set login field values
    $login_page->form_fields['TextBox1']='SomeUser';
    $login_page->form_fields['TextBox2']='SomePassword';

		
    // post login page
    print($scraper->get_page($login_url, $login_page)->html);
		
		
### Parse a the first table on a page into an array:

	// URL to scrape
    $url = "https://secure.somesite.com/Account/stuff.aspx?IDX=1234";
    
    $scraper = new Web_Scraper();
		
    // Get page
    $page = $scraper->get_page($url);		

    // Parse table to array
    $table = $scraper->get_table_by_idx($page,0);
    
    print_r($table);
    

### Post to a page a scrape the results:

    // URL to scrape
    $url = "https://secure.somesite.com/Account/stuff.aspx?IDX=1234";
    $scraper = new Web_Scraper();
		
    // Get page
    $page = $scraper->get_page($url);
		
    // Simulate checking the "ShowAll" checkbox.
    // Set event values (show all)
    $page->form_fields['__EVENTTARGET']='ctl00$MainContent3$chkShowAll';
    $page->form_fields['__EVENTARGUMENT']='';
    $page->form_fields['ctl00$MainContent3$chkShowAll']='checked';
		
    // Postbak to page.
    $page = $scraper->get_page($url, $page);
		
    // Pull the second table on the page into an array.
    $table = $scraper->get_table_by_idx($page,1);
    
    print_r($table);    