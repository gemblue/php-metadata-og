<?php

/**
 * Metadata Provider
 * 
 * Small spaghetty code for serve metadata & og only.
 */

/** Define metadata & og */
$metadata['title'] = 'Website Belajar Coding Bahasa Indonesia';
$metadata['description'] = 'Website tempat belajar pemrograman berbahasa Indonesia lengkap dengan beragam format seperti kelas online, tutorial, training dan ebook. Siapa saja bisa belajar coding dan membuat program komputer. Platform belajar coding online yang dikemas secara interaktif dengan beragam media belajar.';
$metadata['image'] = 'https://cdn-cdpl.sgp1.digitaloceanspaces.com/assets/share.jpg';
$metadata['slug'] = '';

$request = request($_SERVER['REQUEST_URI']);

if ($request) {
    $metadata['title'] = $request['title'];
    $metadata['description'] = tease($request['content']);
    $metadata['image'] = $request['featured_image'];
    $metadata['slug'] = $request['slug'];
}

/**
 * Request
 * 
 * Get API data from server.
 */
function request($slug) {
    $endpoint = 'https://api.codepolitan.com/v1/posts/detail' . $slug;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
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
    Metadata OG Serve.
</body>
</html>