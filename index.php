<?php

/**
 * Metadata Provider
 * 
 * Small spaghetty code to serve metadata & og only from REST API
 * 
 * @author Gemblue
 */

require 'bootstrap.php';

/** Dependency instantiation */
$Parsedown = new Parsedown();

/** Define metadata & og */
$metadata['title'] = 'Website Belajar Coding Bahasa Indonesia';
$metadata['description'] = 'Website tempat belajar pemrograman berbahasa Indonesia lengkap dengan beragam format seperti kelas online, tutorial, training dan ebook. Siapa saja bisa belajar coding dan membuat program komputer. Platform belajar coding online yang dikemas secara interaktif dengan beragam media belajar.';
$metadata['image'] = 'https://cdn-cdpl.sgp1.digitaloceanspaces.com/assets/share.jpg';
$metadata['slug'] = '';
$metadata['content'] = 'Website tempat belajar pemrograman berbahasa Indonesia lengkap dengan beragam format seperti kelas online, tutorial, training dan ebook. Siapa saja bisa belajar coding dan membuat program komputer. Platform belajar coding online yang dikemas secara interaktif dengan beragam media belajar.';

/** Get info from global vars */
$domain = $_SERVER['SERVER_NAME'];
$slug = $_SERVER['REQUEST_URI'];

/** If domain is localhost, get from path instead of request uri */
if ($domain == 'localhost') {
    $slug = $_SERVER['PATH_INFO'];
}

logs('Slug : ' . $slug);

/** Request data by slug */
$request = request($slug);

if ($request) {
    $metadata['title'] = $request['title'];
    $metadata['content'] = $Parsedown->text($request['content']);
    $metadata['description'] = tease($metadata['content']);
    $metadata['image'] = $request['featured_image'];
    $metadata['slug'] = $request['slug'];
}

/**
 * Request
 * 
 * Get API data from server.
 */
function request($slug) {

    $source = 'blog';
    $endpoint = 'https://api.codepolitan.com/v1/posts/detail' . $slug;

    if (preg_match("/course/i", $slug)) {
        $source = 'course';
        $slug = str_replace('/course/intro', '', $slug);
        $endpoint = 'https://api.codepolitan.com/course/detail' . $slug;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $result = json_decode($response, true);

    curl_close($ch);
    
    if ($source == 'course') {
        return [
            'title' => $result['course']['title'],
            'content' => $result['course']['long_description'],
            'description' => $result['course']['seo_description'],
            'featured_image' => $result['course']['thumbnail'],
            'slug' => $result['course']['slug']
        ];
    }

    return [
        'title' => $result['title'],
        'content' => $result['content'],
        'description' => $result['seo_description'],
        'featured_image' => $result['featured_image'],
        'slug' => $result['slug']
    ];
}

/**
 * Logs
 * 
 * Save activity to file for debug purpose
 */
function logs($message) {

    $myfile = fopen("log.txt", "a");
    fwrite($myfile, $message . "\n");
    fclose($myfile);

    return true;
}

/**
 * Tease
 * 
 * Get one or two paragraph 
 * https://stackoverflow.com/questions/4692047/php-get-first-two-sentences-of-a-text
 */
function tease($body, $sentencesToDisplay = 2) {
    $nakedBody = preg_replace('/\s+/',' ',strip_tags($body));
    $sentences = preg_split('/(\.|\?|\!)(\s)/',$nakedBody);

    if (count($sentences) <= $sentencesToDisplay)
        return $nakedBody;

    $stopAt = 0;
    foreach ($sentences as $i => $sentence) {
        $stopAt += strlen($sentence);

        if ($i >= $sentencesToDisplay - 1)
            break;
    }

    $stopAt += ($sentencesToDisplay * 2);
    return trim(substr($nakedBody, 0, $stopAt));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $metadata['title'];?></title>
    <link rel="canonical" href="https://www.codepolitan.com/">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo $metadata['title']?> - Codepolitan">
    <meta property="og:description" content="<?php echo $metadata['description'];?>">
    <meta property="og:url" content="https://www.codepolitan.com/<?php echo $metadata['slug']?>">
    <meta property="og:site_name" content="CodePolitan.com">
    <meta property="article:publisher" content="https://www.instagram.com/codepolitan">
    <meta property="og:image" content="<?php echo $metadata['image'];?>">
    <meta property="og:image:width" content="700">
    <meta property="og:image:height" content="350">
    <link rel="alternate" type="application/rss+xml" title="<?php echo $metadata['title']?> - Codepolitan" href="https://www.codepolitan.com/">
    <meta name="description" content="<?php echo $metadata['description'];?>">
    <meta property="description" content="<?php echo $metadata['description'];?>">
    <meta property="language" content="Indonesia">
    <meta property="revisit-after" content="7">
    <meta property="rating" content="general">
    <meta name="google-site-verification" content="ErdTzB1AquCqq7TiWcxi1FiXIdYxCOd-wkx66iJQ72c" />
    <meta property="webcrawlers" content="all">
    <meta property="spiders" content="all">
    <meta property="robots" content="all">
</head>
<body>
    <h1><?php echo $metadata['title'];?></h1>
    <p><?php echo $metadata['content'];?></p>

    <input type="hidden" id="slug" value="<?php echo $slug?>">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script>
    $(document).ready(function(){

        let slug = $('#slug').val();

        window.location.replace("https://codepolitan.com" + slug);
    });
    </script>
</body>
</html>