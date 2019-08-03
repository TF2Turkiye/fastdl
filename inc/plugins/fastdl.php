<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}
$codename = str_replace('.php', '', basename(__FILE__));

// $plugins->add_hook('', '');
$plugins->add_hook('misc_start', 'fastdl_index');
$plugins->add_hook('fetch_wol_activity_end', 'fastdl_fetch_wol_activity_end');
$plugins->add_hook('build_friendly_wol_location_end', 'fastdl_build_friendly_wol_location_end');

function fastdl_info()
{
	return array(
		"name"			=> "[TF2 Turkiye] FastDL indirme",
		"description"	=> "Sunucu listesini gosteren eklenti.",
		"website"		=> "https://tf2turkiye.com",
		"author"		=> "Kerem",
		"authorsite"	=> "https://kkerem.com",
		"version"		=> "1.0",
		"guid" 			=> "",
		"codename"		=> $codename,
		"compatibility" => "*"
	);
}

// WOL Support
function fastdl_fetch_wol_activity_end(&$args)
{
	if($args['activity'] != 'unknown')
	{
		return;
	}

	if(my_strpos($args['location'], 'misc.php?action=fastdl') === false)
	{
		return;
	}

	$args['activity'] = 'misc_fastdl';
}
function fastdl_build_friendly_wol_location_end(&$args)
{
	global $lang, $settings;

	if($args['user_activity']['activity'] == 'misc_fastdl')
	{
		$args['location_name'] = $lang->sprintf($lang->fastdl);
	}
}

function fastdl_index() 
{
    global $lang, $mybb, $templates, $header, $headerinclude, $footer;
    $lang->load("fastdl");

    if($mybb->get_input('action') == 'fastdl')
    {
        add_breadcrumb($lang->haritalar, $mybb->settings['bburl'] . "/haritalar");

        if($mybb->get_input('indir'))
        {
            header('Location: ' . $mybb->settings['bburl'] . '/fastdl/maps/' . $mybb->get_input('indir')  . '.bsp.bz2');
        }
        
        $array = scandir('fastdl/maps');




        $total = count($array);

    if($mybb->settings['ppp']) // Using posts per page.  tpp for threads per page
    {
    $perpage = (int) $mybb->settings['ppp'];
    }
    else
    {
    $perpage = 29; // I chose 10, but you can put what you like
    }
    $pages = ceil($total / $perpage);

    if($mybb->input['page'])
    {
    $page = $mybb->get_input('page', 1);
    }
    else
    {
    $page = 1;
    }
    if($page < 1)
    {
    $page = 1;
    }
    if($page > $pages)
    {
    $page = $pages;
    }
    $pagination = str_replace("?page=","/", multipage($total, $perpage, $page, $mybb->settings['bburl'] . '/haritalar') );
    
    if ($total > 0) {
    $offset = ($page - 1) * $perpage; // forgive the lack of indentation, mybb keeps stripping out the tab characters
    }
    else {
    $offset = 0;
    }

$str_array = array(".bsp.bz2",".res.bz2");

$slice = array_slice($array, $offset, $perpage);
foreach ($slice as $key => $value) {
    if($key == 0 || $key == 1) {
    }
    else {
        $file['name'] = str_replace($str_array,"", $value);
        $test = my_date('relative', filemtime('fastdl/maps/' . $value ) );
        $file['size'] = get_friendly_size(filesize('fastdl/maps/' . $value));
        if(filesize('fastdl/maps/' . $value) > 1) {
            $file['image'] = 'https://teamwork.tf/images/maps/' . $file['name'] . '.jpg';
            eval("\$fastdl_gorsel = \"".$templates->get("fastdl_gorsel")."\";");
        }
        else {
            $file['image'] = 'asd';
            eval("\$fastdl_gorsel = \"".$templates->get("fastdl_gorsel_yok")."\";");
        }

        eval("\$fastdl_row .= \"".$templates->get("fastdl_row")."\";");
    }
}

if ($pages > 0) {
    // Display Pagination
}

        // foreach ($array as $key => $value) {
        //     if($key == 0 || $key == 1) {
        
        //     }
        //     else {
        //         $file['name'] = str_replace(".bsp.bz2","", $value);
        //         $file['image'] = 'https://teamwork.tf/images/maps/' . $file['name'] . '.jpg';
        //         $file['size'] = get_friendly_size(filesize('fastdl/maps/' . $value));
		// 		eval("\$fastdl_row .= \"".$templates->get("fastdl_row")."\";");
        //     }
        // }
        
       
        eval('$fastdl  = "' . $templates->get('fastdl') . '";');

        output_page($fastdl);
    }
}
