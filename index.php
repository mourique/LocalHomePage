<!DOCTYPE html>

<?php require('config.php'); ?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>FFMEMEX - Local Websites</title>
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="<?= $projroot ?>css/main.css">
    </head>

    <body>

	    <div class="canvas">
	<span class="current-time">
		    		<?php echo date(DATE_RSS)."\n"; ?>
		    	</span>
		    <header>

			    <h1>Local Websites</h1>

			    <nav>
			        <ul>
<?php
			            foreach ( $devtools as $tool ) {
			            	printf( '<li><a href="%1$s">%2$s</a></li>', $tool['url'], $tool['name'] );
			            }
?>
			        </ul>
			    </nav>

		    </header>

		    <content class="cf">
		    
<?php
		    foreach ( $dir as $d ) {
			    $dirsplit = explode('/', $d);
			    $dirname = $dirsplit[count($dirsplit)-2];

				printf( '<ul class="sites %1$s">', $dirname );

		        foreach( glob( $d ) as $file )  {

		        	$project = basename($file);

		        	if ( in_array( $project, $hiddensites ) ) continue;

		            echo '<li>';

		            $siteroot = sprintf( 'http://%1$s.%2$s.%3$s', $project, $dirname, $tld );

		            // Display an icon for the site
		            $icon_output = '<span class="no-img"></span>';
		            foreach( $icons as $icon ) {

		            	if ( file_exists( $file . '/' . $icon ) ) {
		            		$icon_output = sprintf( '<img src="%1$s/%2$s">', $siteroot, $icon );
		            		break;
		            	} // if ( file_exists( $file . '/' . $icon ) )

		            } // foreach( $icons as $icon )
		            echo $icon_output;

		            // Display a link to the site
		            $displayname = $project;
		            if ( array_key_exists( $project, $siteoptions ) ) {
		            	if ( is_array( $siteoptions[$project] ) )
		            		$displayname = array_key_exists( 'displayname', $siteoptions[$project] ) ? $siteoptions[$project]['displayname'] : $project;
		            	else
		            		$displayname = $siteoptions[$project];
		            }
		            printf( '<a class="site" href="%1$s">%2$s</a>', $siteroot, $displayname );


					// Display an icon with a link to the admin area
					$adminurl = '';
					// We'll start by checking if the site looks like it's a WordPress site
					if ( is_dir( $file . '/wp-admin' ) )
						$adminurl = sprintf( 'http://%1$s/wp-admin', $siteroot );

					if ( is_dir( $file . '/kirby' ) )
						$adminurl = sprintf( 'http://%1$s/panel', $siteroot );


					// If the user has defined an adminurl for the project we'll use that instead
		            if (isset($siteoptions[$project]) &&  is_array( $siteoptions[$project] ) && array_key_exists( 'adminurl', $siteoptions[$project] ) )
		            	$adminurl = $siteoptions[$project]['adminurl'];

		            // If there's an admin url then we'll show it - the icon will depend on whether it looks like WP or not
		            if ( ! empty( $adminurl ) )
			            printf( '<a class="%2$s icon" href="%1$s">Admin</a>', $adminurl, is_dir( $file . '/wp-admin' ) ? 'wp' : 'admin' );


		            echo '</li>';

				} // foreach( glob( $d ) as $file )

		        echo '</ul>';

		   	} // foreach ( $dir as $d )
?>
<hr />
<h2>Remote Websites</h2>
<?

$sitelist = array(
    "https://www.das-plats.de",
    "https://www.gruenekuriere.de",
    "https://www.kinderaerztesachsenhausen.de",
    "https://www.mari-babic.de",
    "https://www.felixf.de",
    "thisisafailcheck.com"
);

$filename = "/Users/ff/Documents/plaintext/hosted-websites.txt";

// Open the file
$fp = @fopen($filename, 'r'); 

// Add each line to an array
if ($fp) {
    $sitelist = explode("\n", fread($fp, filesize($filename)));
}

$errormsg = "There is an error with the following sites: \n\n";
$error = False;
echo '<ul class="sites Websites">';
foreach ($sitelist as $i => $site) {
echo '<li>';
        $crl = curl_init();
        $timeout = 10;
        curl_setopt ($crl, CURLOPT_URL, $site);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($crl);
        echo $site;
        if( curl_errno($crl) )
        {
                $error = True;
                echo " (curl error: ".curl_error($crl).")\n";
        } else {
        	echo '<span class="checked">✓</span>';
        }

        $status = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        $status_range =  (string)$status;
        $status_range = $status_range[0]; // status starts as an integer and has to be converted to string to get the first digit

        if( !( $status_range == '2' || $status_range == '3' ) ) // 200's are a good response and 300's are redirects
        {
                $error = True;
                echo " [Code ".$status."]\n";
        } else {
        	echo '<span class="checked">✓</span>';
        }

        $length = strlen( $content );
        if( $length < 2 )
        {
                $error = True;
                echo "[No Content]\n";
        } else {
        	echo '<span class="checked">✓</span>';
        }

        curl_close($crl);
        echo '</li>';

}
echo '</ul>';
// echo '<ul>';

// if( $error ) {
// 		echo '<li>';
//         echo $errormsg;
// 		echo '</li>';

//         $to      = 'address1@email.com,address2@email.com';
//         $subject = '## Websites are down ##';
//         $message = $errormsg;
//         $headers = 'From: noreply@mywebsite.com' . "\r\n" .
//         'X-Mailer: PHP/' . phpversion();

//         mail($to, $subject, $message, $headers);
// } else {
// 	echo '<li>';
//     echo "No errors encountered.\n";
//     echo '</li>';
// }

// echo '</ul>';


?>


			</content>



		    <footer class="cf">
		    <p></p>
		    </footer>

	    </div>
    </body>
</html>
